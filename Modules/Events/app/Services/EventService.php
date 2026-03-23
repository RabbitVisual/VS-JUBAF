<?php

namespace Modules\Events\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Events\App\Models\EventCoupon;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventBatch;
use Modules\Events\App\Models\EventPriceRule;
use Modules\Events\App\Models\EventRegistrationSegment;
use Modules\Events\App\Models\Participant;
use Modules\Events\App\Models\EventRegistration;

class EventService
{
    /**
     * Single source of truth: calculate total for a registration.
     * With batch: base = batch.price, then at most one registration-level rule (by priority) applies.
     * Without batch: sum per-participant of first matching rule (by priority) or 0.
     *
     * Field → rule mapping (configure "Inscrição e vagas" form field names accordingly):
     * - Código promocional: form field name discount_code or codigo_promocional → registrationData['discount_code'].
     * - Tipo de participante / almoço: form field → participantData['participant_type'] (custom_responses per participant).
     * - Localização: form field → participantData['location'] (custom_responses per participant).
     *
     * @param  array  $participants  Array of participant data (name, email, birth_date, etc.; participant_type, location from custom_responses)
     * @param  array  $registrationData  discount_code, created_at (optional); participant_count is set from count($participants)
     */
    public function calculateRegistrationTotal(array $participants, Event $event, array $registrationData = [], ?int $batchId = null): float
    {
        $registrationData['participant_count'] = count($participants);
        $registrationData['created_at'] = $registrationData['created_at'] ?? now();

        if ($batchId !== null) {
            $batch = EventBatch::where('event_id', $event->id)->findOrFail($batchId);
            $basePrice = (float) $batch->price;

            return $this->applyRegistrationLevelRules($event, $basePrice, $registrationData);
        }

        $event->load('registrationSegments');
        $rules = $event->priceRules()->active()->ordered()->get();
        $total = 0.0;
        foreach ($participants as $participantData) {
            $total += $this->calculatePriceForParticipant($participantData, $rules, $registrationData, $event);
        }

        return round($total, 2);
    }

    /**
     * When using a batch, apply at most one registration-level rule (by priority) to the base price.
     */
    protected function applyRegistrationLevelRules(Event $event, float $basePrice, array $registrationData): float
    {
        $rules = $event->priceRules()->active()->ordered()->get();
        $registrationLevelTypes = [
            EventPriceRule::RULE_TYPE_DISCOUNT_CODE,
            EventPriceRule::RULE_TYPE_EARLY_BIRD,
            EventPriceRule::RULE_TYPE_LAST_MINUTE,
            EventPriceRule::RULE_TYPE_GROUP_SIZE,
            EventPriceRule::RULE_TYPE_BULK_DISCOUNT,
            EventPriceRule::RULE_TYPE_REGISTRATION_DATE,
        ];

        foreach ($rules as $rule) {
            $type = $rule->effective_rule_type;
            if (! in_array($type, $registrationLevelTypes, true)) {
                continue;
            }
            if ($rule->matchesParticipant([], $registrationData)) {
                return round($rule->calculatePrice($basePrice), 2);
            }
        }

        return round($basePrice, 2);
    }

    /**
     * Calculate price for a specific participant: segment price (when set), then segment rules, then global rules.
     */
    public function calculatePriceForParticipant(array $participantData, $rules, array $registrationData = [], ?Event $event = null): float
    {
        $segmentId = $participantData['registration_segment_id'] ?? null;
        if ($segmentId && $event && $event->relationLoaded('registrationSegments')) {
            $segment = $event->registrationSegments->firstWhere('id', (int) $segmentId);
            if ($segment !== null && $segment->price !== null) {
                return round((float) $segment->price, 2);
            }
        }

        if (isset($participantData['birth_date']) && ! isset($participantData['age'])) {
            try {
                $participantData['age'] = \Carbon\Carbon::parse($participantData['birth_date'])->age;
            } catch (\Throwable) {
                $participantData['age'] = null;
            }
        }

        $rulesToApply = $rules;
        if ($segmentId !== null && $rules instanceof \Illuminate\Support\Collection) {
            $segmentRules = $rules->where('registration_segment_id', $segmentId);
            $globalRules = $rules->whereNull('registration_segment_id');
            $rulesToApply = $segmentRules->concat($globalRules);
        }

        foreach ($rulesToApply as $rule) {
            if ($rule->matchesParticipant($participantData, $registrationData)) {
                return (float) $rule->calculatePrice(0.0);
            }
        }

        return 0.0;
    }

    /**
     * Return total and per-participant breakdown for validation/preview.
     *
     * @return array{total: float, per_participant: array<int, float>}
     */
    public function calculateRegistrationBreakdown(array $participants, Event $event, array $registrationData = [], ?int $batchId = null): array
    {
        $registrationData['participant_count'] = count($participants);
        $registrationData['created_at'] = $registrationData['created_at'] ?? now();

        if ($batchId !== null) {
            $batch = EventBatch::where('event_id', $event->id)->findOrFail($batchId);
            $basePrice = (float) $batch->price;
            $total = $this->applyRegistrationLevelRules($event, $basePrice, $registrationData);
            $n = count($participants);
            $perParticipant = $n > 0 ? array_fill(0, $n, $total / $n) : [];

            return ['total' => round($total, 2), 'per_participant' => array_map(fn ($p) => round($p, 2), $perParticipant)];
        }

        $event->load('registrationSegments');
        $rules = $event->priceRules()->active()->ordered()->get();
        $perParticipant = [];
        foreach ($participants as $participantData) {
            $perParticipant[] = $this->calculatePriceForParticipant($participantData, $rules, $registrationData, $event);
        }
        $total = array_sum($perParticipant);

        return ['total' => round($total, 2), 'per_participant' => array_map(fn ($p) => round((float) $p, 2), $perParticipant)];
    }

    /**
     * Get registration form config: segments (with quantity, form_fields, documents_requested) or legacy (single form_fields).
     *
     * @return array{use_segments: bool, segments?: array, form_fields?: array}
     */
    public function getRegistrationConfig(Event $event): array
    {
        $event->load('registrationSegments');
        if ($event->registrationSegments->isNotEmpty()) {
            // Get the event's effective required_fields to pass to the wizard
            $eventRequiredFields = $event->getEffectiveRequiredFields();
            return [
                'use_segments'        => true,
                'required_fields'     => $eventRequiredFields,
                'segments'            => $event->registrationSegments->map(fn (EventRegistrationSegment $s) => [
                    'id'                  => $s->id,
                    'label'               => $s->label,
                    'description'         => $s->description,
                    'gender'              => $s->gender ?? 'all',
                    'min_age'             => $s->min_age,
                    'max_age'             => $s->max_age,
                    'quantity'            => $s->quantity,
                    'price'               => $s->price !== null ? (float) $s->price : null,
                    'price_rule_type'     => $s->price_rule_type,
                    'price_rule_types'    => $s->getPriceRuleTypes(),
                    'form_fields'         => $s->form_fields ?? [],
                    'documents_requested' => $s->documents_requested ?? [],
                    'ask_phone'           => $s->ask_phone ?? false,
                    // If segment has its own required_fields, merge with event's; else use event's
                    'required_fields'     => $s->getEffectiveRequiredFields() ?? $eventRequiredFields,
                ])->values()->all(),
            ];
        }

        return [
            'use_segments'    => false,
            'required_fields' => $event->getEffectiveRequiredFields(),
            'form_fields'     => $event->form_fields ?? [],
        ];
    }

    /**
     * Validate if event has capacity available for additional participants
     */
    public function validateCapacity(Event $event, int $additionalParticipants = 1): bool
    {
        if ($event->capacity === null) {
            return true; // Unlimited capacity
        }

        $currentParticipants = $event->confirmedRegistrations()
            ->with(['participants'])
            ->get()
            ->sum(function ($registration) {
                return $registration->participants->count();
            });

        return ($currentParticipants + $additionalParticipants) <= $event->capacity;
    }

    /**
     * Create a registration with participants.
     *
     * @param  array  $registrationData  custom_responses, discount_code, batch_id (optional)
     */
    public function createRegistration(Event $event, array $participantsData, ?int $userId = null, array $registrationData = []): EventRegistration
    {
        if (! $this->validateCapacity($event, count($participantsData))) {
            throw new \Exception('Capacidade máxima do evento atingida.');
        }

        $discountCode = $registrationData['discount_code'] ?? null;
        $coupon = null;
        if (is_string($discountCode) && $discountCode !== '') {
            $normalizedCode = mb_strtoupper(trim($discountCode));
            $coupon = $this->validateCoupon($normalizedCode, $event, $participantsData, $userId);
            $registrationData['discount_code'] = $normalizedCode;
        }

        $batchId = isset($registrationData['batch_id']) ? (int) $registrationData['batch_id'] : null;
        $total = $this->calculateRegistrationTotal($participantsData, $event, $registrationData, $batchId);

        if ($coupon) {
            $total = $this->applyCouponToTotal($total, $coupon);
        }

        DB::beginTransaction();

        try {
            if ($batchId !== null) {
                $batch = EventBatch::where('event_id', $event->id)->where('id', $batchId)->lockForUpdate()->first();
                if (! $batch) {
                    throw new \Exception('Lote não encontrado ou não pertence a este evento.');
                }
                if ($batch->quantity_available < 1) {
                    throw new \Exception('Este lote está esgotado.');
                }
                $batch->decrement('quantity_available');
            }

            $registration = EventRegistration::create([
                'uuid' => (string) Str::uuid(),
                'event_id' => $event->id,
                'batch_id' => $batchId,
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => EventRegistration::STATUS_PENDING,
                'custom_responses' => $registrationData['custom_responses'] ?? null,
                'discount_code' => $registrationData['discount_code'] ?? null,
            ]);

            // Create participants
            foreach ($participantsData as $participantData) {
                $customResponses = $participantData['custom_responses'] ?? [];
                $document = $participantData['document'] ?? $customResponses['doc_cpf'] ?? null;
                if ($document && empty($customResponses['doc_cpf'])) {
                    $customResponses['doc_cpf'] = $document;
                }
                Participant::create([
                    'registration_id' => $registration->id,
                    'registration_segment_id' => $participantData['registration_segment_id'] ?? null,
                    'name' => $participantData['name'],
                    'email' => $participantData['email'],
                    'birth_date' => $participantData['birth_date'],
                    'document' => $document,
                    'phone' => $participantData['phone'] ?? null,
                    'custom_responses' => $customResponses ?: null,
                ]);
            }

            DB::commit();

            return $registration->load('participants');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating registration: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirm a registration (after payment confirmation)
     */
    public function confirmRegistration(EventRegistration $registration): EventRegistration
    {
        $updates = [
            'status' => EventRegistration::STATUS_CONFIRMED,
            'paid_at' => now(),
        ];
        $uuid = $registration->uuid ?: (string) Str::uuid();
        if (empty($registration->uuid)) {
            $updates['uuid'] = $uuid;
        }
        if (empty($registration->ticket_hash)) {
            $updates['ticket_hash'] = hash('sha256', $uuid.$registration->created_at->timestamp);
        }
        $registration->update($updates);

        // Dispatch event for Treasury and Notifications
        event(new RegistrationConfirmed($registration));

        return $registration->fresh();
    }

    /**
     * Cancel a registration
     */
    public function cancelRegistration(EventRegistration $registration, ?string $reason = null): EventRegistration
    {
        $registration->update([
            'status' => EventRegistration::STATUS_CANCELLED,
            'notes' => $reason ? ($registration->notes."\n".$reason) : $registration->notes,
        ]);

        return $registration->fresh();
    }

    /**
     * Validate a coupon for the given event and context.
     */
    protected function validateCoupon(string $discountCode, Event $event, array $participantsData, ?int $userId = null): ?EventCoupon
    {
        $code = mb_strtoupper(trim($discountCode));
        if ($code === '') {
            return null;
        }

        $coupon = EventCoupon::where('event_id', $event->id)
            ->where('code', $code)
            ->first();

        if (! $coupon || ! $coupon->isCurrentlyActive()) {
            throw new \Exception('Código promocional inválido ou inativo.');
        }

        $statusesToCount = [
            EventRegistration::STATUS_PENDING,
            EventRegistration::STATUS_CONFIRMED,
        ];

        if ($coupon->max_uses !== null && $coupon->max_uses > 0) {
            $used = EventRegistration::where('event_id', $event->id)
                ->where('discount_code', $code)
                ->whereIn('status', $statusesToCount)
                ->count();

            if ($used >= $coupon->max_uses) {
                throw new \Exception('Este código promocional já atingiu o número máximo de utilizações.');
            }
        }

        if ($coupon->max_uses_per_user !== null && $coupon->max_uses_per_user > 0 && $userId) {
            $userUsed = EventRegistration::where('event_id', $event->id)
                ->where('discount_code', $code)
                ->where('user_id', $userId)
                ->whereIn('status', $statusesToCount)
                ->count();

            if ($userUsed >= $coupon->max_uses_per_user) {
                throw new \Exception('Você já utilizou este código promocional no limite permitido.');
            }
        }

        return $coupon;
    }

    /**
     * Apply a coupon discount to the current total.
     */
    protected function applyCouponToTotal(float $total, ?EventCoupon $coupon): float
    {
        if (! $coupon || $total <= 0) {
            return round($total, 2);
        }

        $discount = 0.0;

        if ($coupon->discount_type === EventCoupon::TYPE_PERCENT) {
            $discount = $total * ((float) $coupon->discount_value / 100);
        } elseif ($coupon->discount_type === EventCoupon::TYPE_FIXED) {
            $discount = (float) $coupon->discount_value;
        }

        $newTotal = max(0, $total - $discount);

        return round($newTotal, 2);
    }
}
