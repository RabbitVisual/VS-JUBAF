<?php

namespace Modules\Events\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Events\App\Models\Event;

class RegisterEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public events can be registered by anyone. Member-only events require authentication.
        $event = $this->route('event');
        if ($event instanceof Event) {
            if ($event->visibility === Event::VISIBILITY_PUBLIC) {
                return true;
            }

            return auth()->check(); // Members or both visibility requires authentication
        }

        return false; // Should not happen if route is correctly defined
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $event = $this->route('event');
        $rules = [
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.email' => 'required|email|max:255',
            'participants.*.birth_date' => 'required|date|before:today',
            'participants.*.document' => 'nullable|string|max:50',
            'participants.*.phone' => 'nullable|string|max:20',
        ];

        if ($event instanceof Event && $event->hasBatches()) {
            $rules['batch_id'] = [
                'required',
                Rule::exists('event_batches', 'id')->where('event_id', $event->id),
            ];
        }

        if ($event instanceof Event && $event->hasRegistrationSegments()) {
            $rules['participants.*.registration_segment_id'] = [
                'required',
                Rule::exists('event_registration_segments', 'id')->where('event_id', $event->id),
            ];
            $segments = $event->registrationSegments;
            foreach ($segments as $segment) {
                foreach ($segment->form_fields ?? [] as $field) {
                    $fieldName = 'participants.*.custom_responses.'.$field['name'];
                    if (! isset($rules[$fieldName])) {
                        $rule = ($field['required'] ?? false) ? 'nullable' : 'nullable';
                        $rules[$fieldName] = $this->ruleForFieldType($field, $rule);
                    }
                }
                foreach ($segment->documents_requested ?? [] as $docKey) {
                    $key = 'doc_'.$docKey;
                    $rules['participants.*.custom_responses.'.$key] = 'nullable|string|max:50';
                }
            }
        } elseif ($event instanceof Event && ! empty($event->form_fields)) {
            foreach ($event->form_fields as $field) {
                $fieldName = 'participants.*.custom_responses.'.$field['name'];
                $rule = ($field['required'] ?? false) ? 'required' : 'nullable';
                $rules[$fieldName] = $this->ruleForFieldType($field, $rule);
            }
        }

        return $rules;
    }

    private function ruleForFieldType(array $field, string $prefix): string
    {
        switch ($field['type'] ?? 'text') {
            case 'text':
            case 'textarea':
                return $prefix.'|string|max:1000';
            case 'select':
            case 'radio':
                $options = implode(',', $field['options'] ?? []);
                return $options ? $prefix.'|string|in:'.$options : $prefix.'|string';
            case 'checkbox':
                return 'nullable|boolean';
            case 'date':
                return $prefix.'|date';
            case 'number':
                return $prefix.'|numeric';
            case 'email':
                return $prefix.'|email';
            case 'phone':
                return $prefix.'|string|max:20';
            default:
                return $prefix.'|string';
        }
    }

    /**
     * Configure the validator after the built-in rules.
     */
    public function withValidator(Validator $validator): void
    {
        $event = $this->route('event');
        if (! $event instanceof Event) {
            return;
        }

        $validator->after(function (Validator $validator) use ($event) {
            $participants = $this->input('participants', []);
            $maxPerRegistration = (int) ($event->max_per_registration ?? 10);
            if ($maxPerRegistration > 0 && count($participants) > $maxPerRegistration) {
                $validator->errors()->add(
                    'participants',
                    __('events::messages.max_participants_per_registration_exceeded', ['max' => $maxPerRegistration])
                        ?: "O número máximo de participantes por inscrição é {$maxPerRegistration}."
                );
            }
        });

        if (! $event->hasRegistrationSegments()) {
            return;
        }

        $validator->after(function (Validator $validator) use ($event) {
            $participants = $this->input('participants', []);
            $segments = $event->registrationSegments;
            $bySegment = [];
            foreach ($participants as $i => $p) {
                $segId = $p['registration_segment_id'] ?? null;
                if ($segId) {
                    $bySegment[$segId][] = ['index' => $i, 'data' => $p];
                }
            }
            foreach ($segments as $segment) {
                $count = count($bySegment[$segment->id] ?? []);
                if ($count !== $segment->quantity) {
                    $validator->errors()->add(
                        'participants',
                        "A faixa \"{$segment->label}\" exige {$segment->quantity} participante(s), mas foram informados {$count}."
                    );
                }
                foreach ($bySegment[$segment->id] ?? [] as $item) {
                    $p = $item['data'];
                    $birthDate = $p['birth_date'] ?? null;
                    if ($birthDate) {
                        $age = \Carbon\Carbon::parse($birthDate)->age;
                        if (! $segment->matchesAge($age)) {
                            $validator->errors()->add(
                                'participants.'.$item['index'].'.birth_date',
                                "A idade ({$age} anos) não está na faixa \"{$segment->label}\" (".($segment->min_age ?? 0).' a '.($segment->max_age ?? '∞').' anos).'
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'participants.required' => 'É necessário adicionar pelo menos um participante.',
            'participants.*.name.required' => 'O nome do participante é obrigatório.',
            'participants.*.email.required' => 'O email do participante é obrigatório.',
            'participants.*.email.email' => 'O email do participante deve ser válido.',
            'participants.*.birth_date.required' => 'A data de nascimento é obrigatória.',
            'participants.*.birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
        ];
    }
}
