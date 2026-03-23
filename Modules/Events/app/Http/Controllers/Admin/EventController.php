<?php

namespace Modules\Events\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Events\App\Http\Requests\StoreEventRequest;
use Modules\Events\App\Http\Requests\UpdateEventRequest;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventPriceRule;
use Modules\Events\App\Models\EventCoupon;
use Modules\Events\App\Models\EventBatch;
use Modules\Events\App\Models\EventCertificate;
use Modules\Events\App\Models\EventSpeaker;
use Modules\Events\App\Services\EventService;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);
        $query = Event::with(['creator', 'priceRules', 'eventType'])->withCount('batches');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        if ($request->filled('event_type_id')) {
            $query->where('event_type_id', $request->event_type_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        foreach ($events as $event) {
            $event->total_participants = $event->confirmedRegistrations()
                ->with(['participants'])
                ->get()
                ->sum(fn ($r) => $r->participants->count());
        }

        $eventTypes = \Modules\Events\App\Models\EventType::orderBy('order')->get();

        return view('events::admin.events.index', compact('events', 'eventTypes'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create(): View
    {
        $this->authorize('create', Event::class);
        $eventTypes = \Modules\Events\App\Models\EventType::orderBy('order')->get();
        return view('events::admin.events.create', compact('eventTypes'));
    }

    /**
     * Store a newly created event
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);
        $validated = $request->validated();
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['created_by'] = auth()->id();

        $validated['location_data'] = $this->buildLocationData($request);

        // Build options with boolean cast
        if ($request->has('options') && is_array($request->options)) {
            $defaults = Event::defaultOptions();
            $opts = $request->options;
            foreach (array_keys($defaults) as $k) {
                if (array_key_exists($k, $opts)) {
                    $opts[$k] = filter_var($opts[$k], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $validated['options'] = array_merge($defaults, $opts);
        }

        // Normalize target_audience
        if (isset($validated['target_audience']) && is_array($validated['target_audience'])) {
            $validated['target_audience'] = array_values(array_filter($validated['target_audience']));
        }

        // Normalize default_required_fields — remove empty values
        if (isset($validated['default_required_fields']) && is_array($validated['default_required_fields'])) {
            $validated['default_required_fields'] = array_filter(
                $validated['default_required_fields'],
                fn ($v) => in_array($v, ['required', 'optional', 'disabled'])
            );
        }

        if ($request->hasFile('banner')) {
            $validated['banner_path'] = $request->file('banner')->store('events/banners', 'public');
        }
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('events/logos', 'public');
        }

        $event = Event::create($validated);

        // Handle global price rules (registration_segment_id = null)
        if ($request->has('price_rules')) {
            foreach ($request->price_rules as $ruleData) {
                if (! empty($ruleData['label']) || ! empty($ruleData['rule_type'])) {
                    $event->priceRules()->create(array_merge(
                        $this->priceRuleAttributesFromRequest($ruleData),
                        ['registration_segment_id' => null]
                    ));
                }
            }
        }

        // Registration segments (faixas de inscrição) and their inline price rules
        if ($request->has('registration_segments')) {
            $segmentsInput = $request->input('registration_segments', []);
            foreach ($segmentsInput as $index => $row) {
                if (empty($row['label']) || empty((int) ($row['quantity'] ?? 0))) {
                    continue;
                }
                $segment = $event->registrationSegments()->create(
                    $this->buildSegmentData($row, $index)
                );
                $this->syncSegmentPriceRules($segment, $row['segment_price_rules'] ?? []);
            }
        }

        // Coupons
        if ($request->has('coupons')) {
            $this->syncCoupons($event, $request->input('coupons', []));
        }

        // Se exige aprovação do conselho e foi criado como publicado, envia para aprovação e mantém em \"aguardando conselho\"
        if ($event->requires_diretoria_approval && $event->status === Event::STATUS_PUBLISHED && class_exists(\Modules\Diretoria\App\Models\diretoriaApproval::class)) {
            \Modules\Diretoria\App\Models\diretoriaApproval::create([
                'approvable_type' => Event::class,
                'approvable_id' => $event->id,
                'approval_type' => \Modules\Diretoria\App\Models\diretoriaApproval::TYPE_EVENT_CREATION,
                'status' => \Modules\Diretoria\App\Models\diretoriaApproval::STATUS_PENDING,
                'request_details' => "Publicação do evento: {$event->title}",
                'requested_by' => auth()->id(),
                'submitted_at' => now(),
            ]);
            $event->update(['status' => Event::STATUS_WAITING_APPROVAL]);
        }

        return redirect()->route('admin.events.events.show', $event)
            ->with('success', __('events::messages.event_created_success') ?? 'Evento criado com sucesso!');
    }

    /**
     * Display the specified event
     */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        $event->load(['priceRules', 'registrations.participants', 'creator']);

        // Calculate statistics for chart
        $confirmedRegistrations = $event->registrations()->where('status', 'confirmed')->get();
        $totalArrecadado = $confirmedRegistrations->sum('total_amount');
        $totalParticipantes = $confirmedRegistrations->sum(function ($registration) {
            return $registration->participants->count();
        });

        // Prepare data for revenue chart (last 30 days)
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayRevenue = $event->registrations()
                ->where('status', 'confirmed')
                ->whereDate('paid_at', $date)
                ->sum('total_amount');
            $revenueData[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'revenue' => $dayRevenue,
            ];
        }

        // Age distribution
        $ageDistribution = [];
        $participants = \Modules\Events\App\Models\Participant::whereHas('registration', function ($q) use ($event) {
            $q->where('event_id', $event->id)->where('status', 'confirmed');
        })->get();

        foreach ($participants as $participant) {
            if ($participant->birth_date) {
                $age = \Carbon\Carbon::parse($participant->birth_date)->age;
                $ageGroup = $this->getAgeGroup($age);
                $ageDistribution[$ageGroup] = ($ageDistribution[$ageGroup] ?? 0) + 1;
            }
        }

        $caravanaRanking = DB::table('event_registrations')
            ->join('users', 'users.id', '=', 'event_registrations.user_id')
            ->leftJoin('igrejas', 'igrejas.id', '=', 'users.igreja_id')
            ->where('event_registrations.event_id', $event->id)
            ->selectRaw("COALESCE(igrejas.nome, 'Sem igreja') as igreja_nome, COUNT(event_registrations.id) as total_inscritos")
            ->groupBy('users.igreja_id', 'igrejas.nome')
            ->orderByDesc('total_inscritos')
            ->get();

        return view('events::admin.events.show', compact(
            'event',
            'totalArrecadado',
            'totalParticipantes',
            'revenueData',
            'ageDistribution',
            'caravanaRanking'
        ));
    }

    /**
     * Build location_data array from request (address, lat, lng, maps_url).
     */
    private function buildLocationData(Request $request): array
    {
        $ld = $request->input('location_data', []);
        if (! is_array($ld)) {
            return [];
        }
        $address = trim($ld['address'] ?? '');
        $lat = isset($ld['lat']) && $ld['lat'] !== '' ? (float) $ld['lat'] : null;
        $lng = isset($ld['lng']) && $ld['lng'] !== '' ? (float) $ld['lng'] : null;
        $result = [];
        if ($address !== '') {
            $result['address'] = $address;
            $result['maps_url'] = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($address);
        }
        if ($lat !== null) {
            $result['lat'] = $lat;
        }
        if ($lng !== null) {
            $result['lng'] = $lng;
        }
        return $result;
    }

    /**
     * Get age group label
     */
    private function getAgeGroup(int $age): string
    {
        if ($age < 13) {
            return __('events::messages.children') ?? 'Crianças (0-12)';
        }
        if ($age < 18) {
            return __('events::messages.teenagers') ?? 'Adolescentes (13-17)';
        }
        if ($age < 30) {
            return __('events::messages.young_adults') ?? 'Jovens (18-29)';
        }
        if ($age < 50) {
            return __('events::messages.adults') ?? 'Adultos (30-49)';
        }
        if ($age < 65) {
            return __('events::messages.middle_aged') ?? 'Meia Idade (50-64)';
        }

        return __('events::messages.seniors') ?? 'Idosos (65+)';
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        $event->load(['priceRules', 'speakers', 'certificates', 'badges', 'registrationSegments.priceRules', 'eventType', 'ministry']);
        $eventTypes = \Modules\Events\App\Models\EventType::orderBy('order')->get();
        $speakersForEditor = $event->speakers->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'role' => $s->role,
            'order' => $s->order,
            'photo_url' => $s->photo_url ?? null,
        ])->values()->all();

        return view('events::admin.events.edit', compact('event', 'eventTypes', 'speakersForEditor'));
    }

    /**
     * Update the specified event
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);
        $validated = $request->validated();

        if ($request->has('location_data')) {
            $validated['location_data'] = $this->buildLocationData($request);
        }

        if ($request->boolean('remove_banner')) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $validated['banner_path'] = null;
        } elseif ($request->hasFile('banner')) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $validated['banner_path'] = $request->file('banner')->store('events/banners', 'public');
        }

        if ($request->boolean('remove_logo')) {
            if ($event->logo_path) {
                Storage::disk('public')->delete($event->logo_path);
            }
            $validated['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($event->logo_path) {
                Storage::disk('public')->delete($event->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('events/logos', 'public');
        }

        unset($validated['remove_banner'], $validated['remove_logo']);

        $certificateTemplate = $validated['certificate_template_html'] ?? null;
        $certificateReleaseAfter = $validated['certificate_release_after'] ?? null;
        $badgeTemplateHtml = $validated['badge_template_html'] ?? null;
        $badgeOrientation = $validated['badge_orientation'] ?? 'portrait';
        $badgePaperSize = $validated['badge_paper_size'] ?? 'A4';
        $badgePerPage = $validated['badge_per_page'] ?? 8;
        $speakersInput = $request->has('speakers_submitted')
            ? (array) ($request->input('speakers') ?? [])
            : ($validated['speakers'] ?? []);
        unset(
            $validated['certificate_template_html'],
            $validated['certificate_release_after'],
            $validated['badge_template_html'],
            $validated['badge_orientation'],
            $validated['badge_paper_size'],
            $validated['badge_per_page'],
            $validated['speakers']
        );

        $options = array_key_exists('options', $validated) ? $validated['options'] : null;
        unset($validated['options']);

        // Persist form_fields only when in single mode; avoid overwriting with stale data when in segments mode
        $registrationMode = $request->input('registration_mode');
        if ($registrationMode === 'segments') {
            unset($validated['form_fields']);
        } elseif ($registrationMode === 'single') {
            $validated['form_fields'] = array_values($request->input('form_fields', []) ?? []);
        }

        $event->update($validated);

        if ($options !== null) {
            $event->update([
                'options' => array_merge(Event::defaultOptions(), array_filter($options, fn ($v) => $v !== null)),
            ]);
        }

        // Certificate: first or create, update template and release_after
        if ($certificateTemplate !== null || $certificateReleaseAfter !== null) {
            $cert = $event->certificates()->first();
            if (! $cert) {
                $cert = $event->certificates()->create([
                    'template_html' => $certificateTemplate ?? '',
                    'release_after' => $certificateReleaseAfter,
                ]);
            } else {
                $cert->update([
                    'template_html' => $certificateTemplate ?? $cert->template_html,
                    'release_after' => $certificateReleaseAfter ?? $cert->release_after,
                ]);
            }
        }

        // Badge: first or create, update template and settings
        if ($badgeTemplateHtml !== null) {
            $badge = $event->badges()->first();
            if (! $badge) {
                $event->badges()->create([
                    'template_html' => $badgeTemplateHtml,
                    'orientation' => $badgeOrientation,
                    'paper_size' => $badgePaperSize,
                    'badges_per_page' => (int) $badgePerPage,
                ]);
            } else {
                $badge->update([
                    'template_html' => $badgeTemplateHtml,
                    'orientation' => $badgeOrientation,
                    'paper_size' => $badgePaperSize,
                    'badges_per_page' => (int) $badgePerPage,
                ]);
            }
        }

        // Speakers: sync (create/update/delete)
        $keptIds = [];
        foreach ($speakersInput as $index => $row) {
            if (empty($row['name'])) {
                continue;
            }
            $photoPath = null;
            if ($request->hasFile("speakers.{$index}.photo")) {
                $file = $request->file("speakers.{$index}.photo");
                $photoPath = $file->store('events/speakers', 'public');
            }
            if (! empty($row['id'])) {
                $speaker = $event->speakers()->find($row['id']);
                if ($speaker) {
                    $speaker->update([
                        'name' => $row['name'],
                        'role' => $row['role'] ?? null,
                        'order' => (int) ($row['order'] ?? 0),
                    ]);
                    if ($photoPath) {
                        if ($speaker->photo_path) {
                            Storage::disk('public')->delete($speaker->photo_path);
                        }
                        $speaker->update(['photo_path' => $photoPath]);
                    }
                    $keptIds[] = $speaker->id;
                    continue;
                }
            }
            $speaker = $event->speakers()->create([
                'name' => $row['name'],
                'role' => $row['role'] ?? null,
                'order' => (int) ($row['order'] ?? 0),
                'photo_path' => $photoPath,
            ]);
            $keptIds[] = $speaker->id;
        }
        $event->speakers()->whereNotIn('id', $keptIds)->each(function (EventSpeaker $s) {
            if ($s->photo_path) {
                Storage::disk('public')->delete($s->photo_path);
            }
            $s->delete();
        });

        // Handle global price rules only (registration_segment_id = null)
        if ($request->has('price_rules')) {
            $existingGlobalRuleIds = $event->priceRules()->global()->pluck('id')->toArray();
            $updatedRuleIds = [];

            foreach ($request->price_rules as $ruleData) {
                if (! empty($ruleData['label']) && isset($ruleData['price'])) {
                    $attrs = $this->priceRuleAttributesFromRequest($ruleData);
                    if (isset($ruleData['id']) && in_array((int) $ruleData['id'], $existingGlobalRuleIds, true)) {
                        $rule = $event->priceRules()->global()->find($ruleData['id']);
                        if ($rule) {
                            $rule->update($attrs);
                            $updatedRuleIds[] = $rule->id;
                        }
                    } else {
                        $newRule = $event->priceRules()->create(array_merge($attrs, ['registration_segment_id' => null]));
                        $updatedRuleIds[] = $newRule->id;
                    }
                }
            }

            $event->priceRules()->global()->whereNotIn('id', $updatedRuleIds)->delete();
        }

        // Registration segments (faixas de inscrição): only sync when mode is "segments"
        if ($request->input('registration_mode') === 'single') {
            $event->priceRules()->whereNotNull('registration_segment_id')->delete();
            $event->registrationSegments()->delete();
        } elseif ($request->has('registration_segments')) {
            $segmentsInput = $request->input('registration_segments', []);
            $existingSegmentIds = $event->registrationSegments()->pluck('id')->toArray();
            $keptSegmentIds = [];

            foreach ($segmentsInput as $index => $row) {
                if (empty($row['label']) || empty((int) ($row['quantity'] ?? 0))) {
                    continue;
                }
                $data = $this->buildSegmentData($row, $index);
                if (! empty($row['id']) && in_array((int) $row['id'], $existingSegmentIds, true)) {
                    $segment = $event->registrationSegments()->find($row['id']);
                    if ($segment) {
                        $segment->update($data);
                        $keptSegmentIds[] = $segment->id;
                        $this->syncSegmentPriceRules($segment, $row['segment_price_rules'] ?? []);
                    }
                } else {
                    $segment = $event->registrationSegments()->create($data);
                    $keptSegmentIds[] = $segment->id;
                    $this->syncSegmentPriceRules($segment, $row['segment_price_rules'] ?? []);
                }
            }

            $deletedSegmentIds = array_diff($existingSegmentIds, $keptSegmentIds);
            if (! empty($deletedSegmentIds)) {
                $event->priceRules()->whereIn('registration_segment_id', $deletedSegmentIds)->delete();
            }
            $event->registrationSegments()->whereNotIn('id', $keptSegmentIds)->delete();
        } else {
            $event->priceRules()->whereNotNull('registration_segment_id')->delete();
            $event->registrationSegments()->delete();
        }

        // Coupons
        if ($request->has('coupons')) {
            $this->syncCoupons($event, $request->input('coupons', []));
        } else {
            $this->syncCoupons($event, []);
        }

        // Se exige aprovação do conselho e foi alterado para publicado, envia para aprovação e mantém em \"aguardando conselho\"
        if ($event->requires_diretoria_approval && $event->status === Event::STATUS_PUBLISHED && class_exists(\Modules\Diretoria\App\Models\diretoriaApproval::class)) {
            $existing = \Modules\Diretoria\App\Models\diretoriaApproval::where('approvable_type', Event::class)
                ->where('approvable_id', $event->id)
                ->where('approval_type', \Modules\Diretoria\App\Models\diretoriaApproval::TYPE_EVENT_CREATION)
                ->whereIn('status', ['pending', 'requires_revision'])
                ->exists();
            if (! $existing) {
                \Modules\Diretoria\App\Models\diretoriaApproval::create([
                    'approvable_type' => Event::class,
                    'approvable_id' => $event->id,
                    'approval_type' => \Modules\Diretoria\App\Models\diretoriaApproval::TYPE_EVENT_CREATION,
                    'status' => \Modules\Diretoria\App\Models\diretoriaApproval::STATUS_PENDING,
                    'request_details' => "Publicação do evento: {$event->title}",
                    'requested_by' => auth()->id(),
                    'submitted_at' => now(),
                ]);
                $event->update(['status' => Event::STATUS_WAITING_APPROVAL]);
            }
        }

        return redirect()->route('admin.events.events.show', $event)
            ->with('success', __('events::messages.event_updated_success') ?? 'Evento atualizado com sucesso!');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()->route('admin.events.events.index')
            ->with('success', __('events::messages.event_removed_success') ?? 'Evento removido com sucesso!');
    }

    /**
     * Duplicate event (copy without registrations).
     */
    public function duplicate(Event $event): RedirectResponse
    {
        $this->authorize('create', Event::class);
        $new = $event->replicate(['banner_path']);
        $new->title = $event->title.' (cópia)';
        $new->slug = Str::slug($new->title).'-'.Str::random(4);
        $new->status = Event::STATUS_DRAFT;
        $new->save();

        foreach ($event->priceRules()->global()->get() as $rule) {
            $new->priceRules()->create(array_merge(
                $rule->only([
                    'label', 'min_age', 'max_age', 'price', 'order', 'rule_type', 'member_status',
                    'participant_type', 'date_from', 'date_to', 'min_participants', 'max_participants',
                    'location', 'discount_code', 'discount_percentage', 'discount_fixed', 'is_active', 'priority',
                ]),
                ['registration_segment_id' => null]
            ));
        }
        foreach ($event->batches as $batch) {
            $new->batches()->create($batch->only(['name', 'price', 'quantity_available', 'start_date', 'end_date']));
        }
        $event->load('registrationSegments.priceRules');
        foreach ($event->registrationSegments as $seg) {
            $newSegment = $new->registrationSegments()->create(array_merge(
                $seg->only(['label', 'min_age', 'max_age', 'quantity', 'price', 'form_fields', 'documents_requested', 'ask_phone', 'order']),
                ['price_rule_types' => $seg->getPriceRuleTypes()]
            ));
            foreach ($seg->priceRules as $rule) {
                $newSegment->priceRules()->create(array_merge(
                    $rule->only([
                        'label', 'min_age', 'max_age', 'price', 'order', 'rule_type', 'member_status',
                        'participant_type', 'date_from', 'date_to', 'min_participants', 'max_participants',
                        'location', 'discount_code', 'discount_percentage', 'discount_fixed', 'is_active', 'priority',
                    ]),
                    ['event_id' => $new->id]
                ));
            }
        }

        return redirect()->route('admin.events.events.edit', $new)
            ->with('success', __('events::messages.event_duplicated_success') ?? 'Evento duplicado. Edite e publique quando desejar.');
    }

    /**
     * Store a batch for the event.
     */
    public function storeBatch(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $event->batches()->create($validated);

        return back()->with('success', __('events::messages.batch_created_success') ?? 'Lote criado com sucesso.');
    }

    /**
     * Update a batch.
     */
    public function updateBatch(Request $request, Event $event, EventBatch $batch): RedirectResponse
    {
        $this->authorize('update', $event);
        if ($batch->event_id !== (int) $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'auto_switch_to_batch_id' => 'nullable|exists:event_batches,id',
        ]);

        $batch->update($validated);

        return back()->with('success', __('events::messages.batch_updated_success') ?? 'Lote atualizado com sucesso.');
    }

    /**
     * Delete a batch.
     */
    public function destroyBatch(Event $event, EventBatch $batch): RedirectResponse
    {
        $this->authorize('update', $event);
        if ($batch->event_id !== (int) $event->id) {
            abort(404);
        }

        $batch->delete();

        return back()->with('success', __('events::messages.batch_removed_success') ?? 'Lote removido com sucesso.');
    }

    /**
     * Sync price rules for a segment (create, update, delete).
     *
     * @param  \Modules\Events\App\Models\EventRegistrationSegment  $segment
     * @param  array  $rulesInput  Array of segment_price_rules from request
     */
    private function syncSegmentPriceRules($segment, array $rulesInput): void
    {
        if (! is_array($rulesInput)) {
            $rulesInput = [];
        }
        $existingIds = $segment->priceRules()->pluck('id')->toArray();
        $keptIds = [];
        foreach ($rulesInput as $ruleData) {
            $attrs = $this->priceRuleAttributesFromRequest($ruleData);
            $ruleType = $ruleData['rule_type'] ?? null;
            if (! $ruleType) {
                continue;
            }
            if (isset($ruleData['id']) && in_array((int) $ruleData['id'], $existingIds, true)) {
                $rule = $segment->priceRules()->find($ruleData['id']);
                if ($rule) {
                    $rule->update($attrs);
                    $keptIds[] = $rule->id;
                }
            } else {
                $rule = $segment->priceRules()->create(array_merge($attrs, [
                    'event_id' => $segment->event_id,
                ]));
                $keptIds[] = $rule->id;
            }
        }
        $segment->priceRules()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * Derive segment price_rule_types from segment_price_rules or legacy fields.
     */
    private function derivePriceRuleTypesFromSegmentRules(array $row): array
    {
        $segmentRules = $row['segment_price_rules'] ?? [];
        if (is_array($segmentRules) && ! empty($segmentRules)) {
            $types = array_values(array_unique(array_filter(array_column($segmentRules, 'rule_type'))));

            return $types;
        }
        if (isset($row['price_rule_types']) && is_array($row['price_rule_types'])) {
            return array_values(array_filter($row['price_rule_types']));
        }
        if (! empty($row['price_rule_type'])) {
            return [$row['price_rule_type']];
        }

        return [];
    }

    /**
     * Build EventPriceRule attributes from request array (for create/update).
     */
    private function priceRuleAttributesFromRequest(array $ruleData): array
    {
        return [
            'label'               => $ruleData['label'] ?? '',
            'rule_type'           => $ruleData['rule_type'] ?? null,
            'min_age'             => isset($ruleData['min_age']) && $ruleData['min_age'] !== '' ? (int) $ruleData['min_age'] : null,
            'max_age'             => isset($ruleData['max_age']) && $ruleData['max_age'] !== '' ? (int) $ruleData['max_age'] : null,
            'member_status'       => $ruleData['member_status'] ?? null,
            'church_membership'   => $ruleData['church_membership'] ?? null,
            'participant_type'    => $ruleData['participant_type'] ?? null,
            'gender'              => isset($ruleData['gender']) && $ruleData['gender'] !== 'all' ? $ruleData['gender'] : null,
            'discount_code'       => isset($ruleData['discount_code']) && $ruleData['discount_code'] !== '' ? mb_strtoupper(trim($ruleData['discount_code'])) : null,
            'date_from'           => isset($ruleData['date_from']) && $ruleData['date_from'] !== '' ? $ruleData['date_from'] : null,
            'date_to'             => isset($ruleData['date_to']) && $ruleData['date_to'] !== '' ? $ruleData['date_to'] : null,
            'min_participants'    => isset($ruleData['min_participants']) && $ruleData['min_participants'] !== '' ? (int) $ruleData['min_participants'] : null,
            'max_participants'    => isset($ruleData['max_participants']) && $ruleData['max_participants'] !== '' ? (int) $ruleData['max_participants'] : null,
            'location'            => $ruleData['location'] ?? null,
            'discount_percentage' => isset($ruleData['discount_percentage']) && $ruleData['discount_percentage'] !== '' ? (float) $ruleData['discount_percentage'] : null,
            'discount_fixed'      => isset($ruleData['discount_fixed']) && $ruleData['discount_fixed'] !== '' ? (float) $ruleData['discount_fixed'] : null,
            'is_active'           => filter_var($ruleData['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'priority'            => (int) ($ruleData['priority'] ?? 0),
            'price'               => isset($ruleData['price']) && $ruleData['price'] !== '' ? (float) $ruleData['price'] : null,
        ];
    }

    /**
     * Build segment data array (shared between store and update).
     */
    private function buildSegmentData(array $row, int $index): array
    {
        $documentsRequested = $row['documents_requested'] ?? [];
        if (is_string($documentsRequested)) {
            $documentsRequested = $documentsRequested ? [$documentsRequested] : [];
        }
        $formFields = $row['form_fields'] ?? [];
        if (is_array($formFields)) {
            $formFields = array_values(array_filter($formFields, fn ($f) => ! empty($f['name'] ?? $f['label'] ?? null)));
        }
        $requiredFields = isset($row['required_fields']) && is_array($row['required_fields'])
            ? array_filter($row['required_fields'], fn ($v) => in_array($v, ['required', 'optional', 'disabled']))
            : null;

        return [
            'label'               => $row['label'],
            'description'         => $row['description'] ?? null,
            'gender'              => $row['gender'] ?? 'all',
            'min_age'             => isset($row['min_age']) && $row['min_age'] !== '' ? (int) $row['min_age'] : null,
            'max_age'             => isset($row['max_age']) && $row['max_age'] !== '' ? (int) $row['max_age'] : null,
            'quantity'            => (int) ($row['quantity'] ?? 1),
            'price'               => isset($row['price']) && $row['price'] !== '' ? (float) $row['price'] : null,
            'price_rule_type'     => $row['price_rule_type'] ?? null,
            'price_rule_types'    => $this->derivePriceRuleTypesFromSegmentRules($row),
            'documents_requested' => array_values(array_unique((array) $documentsRequested)),
            'ask_phone'           => filter_var($row['ask_phone'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'form_fields'         => $formFields,
            'required_fields'     => $requiredFields ?: null,
            'order'               => (int) ($row['order'] ?? $index),
        ];
    }

    /**
     * Sync coupons for an event (create, update, delete).
     */
    private function syncCoupons(Event $event, array $couponsInput): void
    {
        if (! is_array($couponsInput)) {
            $couponsInput = [];
        }

        $existingIds = $event->coupons()->pluck('id')->toArray();
        $keptIds = [];

        foreach ($couponsInput as $row) {
            $code = isset($row['code']) ? trim((string) $row['code']) : '';
            if ($code === '') {
                continue;
            }

            $data = [
                'code' => mb_strtoupper($code),
                'description' => $row['description'] ?? null,
                'discount_type' => $row['discount_type'] ?? EventCoupon::TYPE_PERCENT,
                'discount_value' => isset($row['discount_value']) && $row['discount_value'] !== '' ? (float) $row['discount_value'] : 0,
                'max_uses' => isset($row['max_uses']) && $row['max_uses'] !== '' ? (int) $row['max_uses'] : null,
                'max_uses_per_user' => isset($row['max_uses_per_user']) && $row['max_uses_per_user'] !== '' ? (int) $row['max_uses_per_user'] : null,
                'starts_at' => $row['starts_at'] ?? null,
                'ends_at' => $row['ends_at'] ?? null,
                'is_active' => filter_var($row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ];

            if (! empty($row['id']) && in_array((int) $row['id'], $existingIds, true)) {
                $coupon = $event->coupons()->find($row['id']);
                if ($coupon) {
                    $coupon->update($data);
                    $keptIds[] = $coupon->id;
                }
            } else {
                $coupon = $event->coupons()->create($data);
                $keptIds[] = $coupon->id;
            }
        }

        if (! empty($existingIds)) {
            $event->coupons()->whereNotIn('id', $keptIds)->delete();
        }
    }
}
