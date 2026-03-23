<?php

namespace Modules\Events\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId          = $this->route('event')->id ?? $this->route('event');
        $ruleTypeValues   = implode(',', array_keys(\Modules\Events\App\Models\EventPriceRule::getRuleTypes()));
        $audienceValues   = implode(',', array_keys(\Modules\Events\App\Models\Event::getAudienceOptions()));

        return [
            // Basic
            'title'       => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:events,slug,' . $eventId,
            'description' => 'nullable|string',
            'banner'      => 'nullable|image|max:4096',
            'logo'        => 'nullable|image|max:2048',
            'remove_banner' => 'nullable|boolean',
            'remove_logo'   => 'nullable|boolean',

            // Dates
            'start_date'            => 'required|date',
            'end_date'              => 'nullable|date|after:start_date',
            'registration_deadline' => 'nullable|date',

            // Location
            'location'              => 'nullable|string|max:255',
            'location_data'         => 'nullable|array',
            'location_data.address' => 'nullable|string|max:500',
            'location_data.lat'     => 'nullable|numeric',
            'location_data.lng'     => 'nullable|numeric',

            // Status & Visibility
            'status'     => 'required|in:draft,published,closed',
            'visibility' => 'required|in:public,members,both',

            // Classification
            'event_type_id'     => 'nullable|exists:event_types,id',
            'ministry_id'       => 'nullable|exists:ministries,id',
            'setlist_id'        => 'nullable|exists:worship_setlists,id',
            'is_featured'       => 'nullable|boolean',
            'target_audience'   => 'nullable|array',
            'target_audience.*' => 'string|in:' . $audienceValues,

            // Restrictions
            'capacity'             => 'nullable|integer|min:1',
            'max_per_registration' => 'nullable|integer|min:1|max:100',
            'min_age_restriction'  => 'nullable|integer|min:0|max:120',
            'max_age_restriction'  => 'nullable|integer|min:0|max:120',
            'dress_code'           => 'nullable|string|max:100',
            'recurrence_type'      => 'nullable|in:weekly,monthly,yearly',

            // Contact
            'contact_name'     => 'nullable|string|max:150',
            'contact_email'    => 'nullable|email|max:150',
            'contact_phone'    => 'nullable|string|max:30',
            'contact_whatsapp' => 'nullable|string|max:30',

            // Default required fields
            'default_required_fields'   => 'nullable|array',
            'default_required_fields.*' => 'nullable|in:required,optional,disabled',

            // Theme Configuration
            'theme_config'                 => 'nullable|array',
            'theme_config.theme'           => 'nullable|string|in:modern,classic,minimal,corporate',
            'theme_config.primary_color'   => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'theme_config.secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',

            // Page options
            'options'                 => 'nullable|array',
            'options.show_about'      => 'nullable|boolean',
            'options.show_location'   => 'nullable|boolean',
            'options.show_map'        => 'nullable|boolean',
            'options.show_capacity'   => 'nullable|boolean',
            'options.show_cover'      => 'nullable|boolean',
            'options.show_schedule'   => 'nullable|boolean',
            'options.show_speakers'   => 'nullable|boolean',
            'options.show_contact'    => 'nullable|boolean',
            'options.show_audience'   => 'nullable|boolean',
            'options.has_badge'       => 'nullable|boolean',
            'options.has_certificate' => 'nullable|boolean',
            'options.has_checkin'     => 'nullable|boolean',
            'options.has_ticket'      => 'nullable|boolean',

            // Integrations
            'requires_council_approval' => 'nullable|boolean',
            'treasury_campaign_id'      => 'nullable|exists:campaigns,id',

            // Schedule
            'schedule'               => 'nullable|array',
            'schedule.*.time'        => 'nullable|string|max:50',
            'schedule.*.title'       => 'nullable|string|max:255',
            'schedule.*.description' => 'nullable|string',

            // Certificate / Badge (edit only)
            'certificate_template_html' => 'nullable|string',
            'certificate_release_after' => 'nullable|date',
            'badge_template_html'       => 'nullable|string',
            'badge_orientation'         => 'nullable|in:portrait,landscape',
            'badge_paper_size'          => 'nullable|in:A4,Letter',
            'badge_per_page'            => 'nullable|integer|in:4,6,8,10',

            // Speakers (edit only)
            'speakers'           => 'nullable|array',
            'speakers.*.id'      => 'nullable|exists:event_speakers,id',
            'speakers.*.name'    => 'nullable|string|max:255',
            'speakers.*.role'    => 'nullable|string|max:255',
            'speakers.*.order'   => 'nullable|integer|min:0',
            'speakers.*.photo'   => 'nullable|image|max:2048',

            // Single form fields
            'form_fields'               => 'nullable|array',
            'form_fields.*.type'        => 'required_with:form_fields|in:text,textarea,select,checkbox,radio,date,number,email,phone,url',
            'form_fields.*.label'       => 'required_with:form_fields|string|max:255',
            'form_fields.*.name'        => 'required_with:form_fields|string|max:255',
            'form_fields.*.required'    => 'nullable|boolean',
            'form_fields.*.options'     => 'nullable|array',
            'form_fields.*.placeholder' => 'nullable|string|max:255',
            'form_fields.*.help_text'   => 'nullable|string|max:500',

            // Registration segments
            'registration_segments'                     => 'nullable|array',
            'registration_segments.*.id'                => 'nullable|exists:event_registration_segments,id',
            'registration_segments.*.label'             => 'required_with:registration_segments|string|max:255',
            'registration_segments.*.description'       => 'nullable|string|max:500',
            'registration_segments.*.gender'            => 'nullable|in:all,male,female',
            'registration_segments.*.required_fields'   => 'nullable|array',
            'registration_segments.*.required_fields.*' => 'nullable|in:required,optional,disabled',
            'registration_segments.*.min_age'           => 'nullable|integer|min:0|max:120',
            'registration_segments.*.max_age'           => 'nullable|integer|min:0|max:120',
            'registration_segments.*.quantity'          => 'required_with:registration_segments|integer|min:1',
            'registration_segments.*.price'             => 'nullable|numeric|min:0',
            'registration_segments.*.price_rule_type'   => 'nullable|string|in:' . $ruleTypeValues,
            'registration_segments.*.price_rule_types'  => 'nullable|array',
            'registration_segments.*.price_rule_types.*' => 'string|in:' . $ruleTypeValues,
            'registration_segments.*.documents_requested'    => 'nullable|array',
            'registration_segments.*.documents_requested.*'  => 'string|in:cpf,rg,titulo_eleitor,passaporte',
            'registration_segments.*.ask_phone'              => 'nullable|boolean',
            'registration_segments.*.form_fields'            => 'nullable|array',
            'registration_segments.*.form_fields.*.type'     => 'nullable|in:text,textarea,select,checkbox,radio,date,number,email,phone,url',
            'registration_segments.*.form_fields.*.label'    => 'nullable|string|max:255',
            'registration_segments.*.form_fields.*.name'     => 'nullable|string|max:255',
            'registration_segments.*.form_fields.*.required' => 'nullable|boolean',
            'registration_segments.*.form_fields.*.options'  => 'nullable|array',
            'registration_segments.*.form_fields.*.placeholder' => 'nullable|string|max:255',
            'registration_segments.*.form_fields.*.help_text'   => 'nullable|string|max:500',
            'registration_segments.*.segment_price_rules'                    => 'nullable|array',
            'registration_segments.*.segment_price_rules.*.id'               => 'nullable|integer',
            'registration_segments.*.segment_price_rules.*.rule_type'        => 'nullable|string|in:' . $ruleTypeValues,
            'registration_segments.*.segment_price_rules.*.label'            => 'nullable|string|max:255',
            'registration_segments.*.segment_price_rules.*.priority'         => 'nullable|integer|min:0',
            'registration_segments.*.segment_price_rules.*.min_age'          => 'nullable|integer|min:0',
            'registration_segments.*.segment_price_rules.*.max_age'          => 'nullable|integer|min:0',
            'registration_segments.*.segment_price_rules.*.price'            => 'nullable|numeric|min:0',
            'registration_segments.*.segment_price_rules.*.discount_code'    => 'nullable|string|max:100',
            'registration_segments.*.segment_price_rules.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'registration_segments.*.segment_price_rules.*.discount_fixed'   => 'nullable|numeric|min:0',
            'registration_segments.*.segment_price_rules.*.date_from'        => 'nullable|date',
            'registration_segments.*.segment_price_rules.*.date_to'          => 'nullable|date',
            'registration_segments.*.segment_price_rules.*.min_participants'  => 'nullable|integer|min:0',
            'registration_segments.*.segment_price_rules.*.max_participants'  => 'nullable|integer|min:0',
            'registration_segments.*.segment_price_rules.*.member_status'    => 'nullable|string|max:50',
            'registration_segments.*.segment_price_rules.*.church_membership' => 'nullable|string|max:50',
            'registration_segments.*.segment_price_rules.*.participant_type' => 'nullable|string|max:50',
            'registration_segments.*.segment_price_rules.*.gender'           => 'nullable|in:male,female,all',
            'registration_segments.*.segment_price_rules.*.location'         => 'nullable|string|max:255',
            'registration_segments.*.segment_price_rules.*.is_active'        => 'nullable|boolean',

            // Coupons
            'coupons'                     => 'nullable|array',
            'coupons.*.id'                => 'nullable|integer|exists:event_coupons,id',
            'coupons.*.code'              => 'required_with:coupons|string|max:100',
            'coupons.*.description'       => 'nullable|string|max:255',
            'coupons.*.discount_type'     => 'required_with:coupons|string|in:percent,fixed',
            'coupons.*.discount_value'    => 'required_with:coupons|numeric|min:0',
            'coupons.*.max_uses'          => 'nullable|integer|min:1',
            'coupons.*.max_uses_per_user' => 'nullable|integer|min:1',
            'coupons.*.starts_at'         => 'nullable|date',
            'coupons.*.ends_at'           => 'nullable|date',
            'coupons.*.is_active'         => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('slug') && $this->has('title')) {
            $this->merge(['slug' => Str::slug($this->title)]);
        }
        if ($this->has('certificate_release_after') && $this->certificate_release_after === '') {
            $this->merge(['certificate_release_after' => null]);
        }
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'O título do evento é obrigatório.',
            'start_date.required'    => 'A data de início é obrigatória.',
            'end_date.after'         => 'A data de término deve ser posterior à data de início.',
            'status.required'        => 'O status do evento é obrigatório.',
            'visibility.required'    => 'A visibilidade do evento é obrigatória.',
            'slug.unique'            => 'Este slug já está em uso. Escolha outro.',
            'contact_email.email'    => 'O e-mail de contato deve ser válido.',
        ];
    }
}
