{{-- Registration modal: 3-step wizard. Parent must have x-data="{ registrationModalOpen: false }". --}}
<div x-show="registrationModalOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @keydown.escape.window="registrationModalOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col"
         x-data="eventRegistrationWizard()"
         data-event-id="{{ $event->id }}">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 border-b border-gray-200 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('events::messages.registration') ?? 'Inscrição' }} — {{ $event->title }}</h2>
                <span class="text-sm text-gray-500 dark:text-slate-400" x-show="step >= 1 && step <= 3" x-text="'Etapa ' + step + ' de 3'"></span>
            </div>
            <button type="button" @click="saveDraft(); registrationModalOpen = false" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors" title="{{ __('events::messages.close') ?? 'Fechar' }}">
                <x-icon name="xmark" class="w-5 h-5" />
            </button>
        </div>

        @php
            $initialStep = 1;
            if ($errors->any()) {
                $errorKeys = array_keys($errors->toArray());
                $hasParticipantError = false;
                $hasPaymentError = false;
                foreach ($errorKeys as $key) {
                    if (str_starts_with($key, 'participants')) {
                        $hasParticipantError = true;
                    }
                    if (in_array($key, ['payment_gateway_id', 'payment_method', 'brick_payload'], true)) {
                        $hasPaymentError = true;
                    }
                    if ($key === 'batch_id') {
                        $initialStep = 1;
                        break;
                    }
                }
                if ($hasPaymentError) {
                    $initialStep = 3;
                } elseif ($hasParticipantError) {
                    $initialStep = 2;
                }
            }
        @endphp

        <script type="application/json" id="event-registration-wizard-data">
            {
                "config": @json($registrationConfig ?? ['use_segments' => false, 'form_fields' => $event->form_fields ?? []]),
                "isFree": @json($isFree ?? true),
                "hasBatches": @json($hasBatches ?? false),
                "batches": @json($batches ?? []),
                "defaultParticipant": @json($defaultParticipant ?? null),
                "initialStep": {{ $initialStep }},
                "hasServerErrors": @json($errors->any())
            }
        </script>

        <form id="registration-wizard-form" action="{{ $registrationAction ?? route('events.public.register', $event) }}" method="POST"
              class="flex flex-col flex-1 min-h-0 overflow-hidden"
              data-loading-message="{{ __('events::messages.processing_registration') ?? 'Processando inscrição...' }}"
              @submit="clearDraft(); window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: $event.target.dataset.loadingMessage || 'Processando...' } }))">
            @csrf

            <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-4 sm:p-6">
                @if($errors->any())
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3">
                        <p class="font-semibold mb-1">Encontramos algumas informações que precisam de atenção:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Friendly step-level validation message --}}
                <div x-show="stepError"
                     x-transition
                     class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3">
                    <p class="font-semibold mb-1">Antes de continuar, revise os campos abaixo:</p>
                    <p x-text="stepError"></p>
                </div>

                {{-- Step 1: Lote / Quantidade --}}
                <div x-show="step === 1" x-cloak class="space-y-6">
                    @if($hasBatches)
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-icon name="ticket" style="duotone" class="w-5 h-5 text-amber-500" />
                            {{ __('events::messages.choose_batch') ?? 'Escolha o lote' }}
                        </h3>
                        <div class="space-y-3">
                            @foreach($batches as $batch)
                                @php $isSoldOut = $batch->quantity_available <= 0; @endphp
                                <label class="relative block cursor-pointer {{ $isSoldOut ? 'opacity-60 cursor-not-allowed' : '' }}">
                                    <input type="radio" name="batch_id" value="{{ $batch->id }}" class="peer sr-only" {{ $isSoldOut ? 'disabled' : '' }} required
                                           x-model="selectedBatchId">
                                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-slate-700 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 transition-all">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $batch->name }}</span>
                                            <span class="text-lg font-bold text-amber-600 dark:text-amber-400">R$ {{ number_format($batch->price, 2, ',', '.') }}</span>
                                        </div>
                                        @if($batch->end_date)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('events::messages.sales_until') ?? 'Vendas até' }} {{ $batch->end_date->format('d/m/Y') }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @if($isFree && !$hasBatches)
                        <div x-show="!useSegments" class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-icon name="ticket" style="duotone" class="w-5 h-5 text-amber-500" />
                                {{ __('events::messages.number_of_spots') ?? 'Quantidade de vagas' }}
                            </h3>
                            <div class="flex items-center gap-4">
                                <button type="button" @click="freeTickets = Math.max(1, freeTickets - 1)" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                    <x-icon name="minus" class="w-5 h-5" />
                                </button>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white w-16 text-center" x-text="freeTickets"></span>
                                <button type="button" @click="freeTickets++" class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                                    <x-icon name="plus" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    @endif

                    <div x-show="useSegments && segments.length" class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-icon name="users" style="duotone" class="w-5 h-5 text-amber-500" />
                            {{ __('events::messages.choose_quantity_per_segment') ?? 'Quantidade por faixa' }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('events::messages.choose_quantity_per_segment_help') ?? 'Escolha quantas vagas deseja em cada faixa.' }}</p>
                        <template x-for="seg in segments" :key="seg.id">
                            <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="seg.label"></div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="setSegmentQuantity(seg.id, Math.max(0, (segmentQuantities[seg.id] || 0) - 1))" class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors disabled:opacity-50" :disabled="(segmentQuantities[seg.id] || 0) <= 0">
                                        <x-icon name="minus" class="w-4 h-4" />
                                    </button>
                                    <span class="w-12 text-center font-bold text-gray-900 dark:text-white" x-text="segmentQuantities[seg.id] || 0"></span>
                                    <button type="button" @click="setSegmentQuantity(seg.id, Math.min(seg.quantity, (segmentQuantities[seg.id] || 0) + 1))" class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors disabled:opacity-50" :disabled="(segmentQuantities[seg.id] || 0) >= seg.quantity">
                                        <x-icon name="plus" class="w-4 h-4" />
                                    </button>
                                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="'máx. ' + seg.quantity"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Step 2: Participantes --}}
                <div x-show="step === 2" x-cloak class="space-y-6" data-step="2-top">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="users" style="duotone" class="w-5 h-5 text-[var(--color-main)]" />
                        {{ __('events::messages.participants_data') ?? 'Dados dos participantes' }}
                    </h3>
                    <template x-for="(participant, index) in participants" :key="index">
                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-gray-900 dark:text-white">
                                    <template x-if="useSegments && getSegmentForParticipant(index)">
                                        <span x-text="getSegmentForParticipant(index).label + ' — ' + (getParticipantSlotInSegment(index) + 1)"></span>
                                    </template>
                                    <template x-if="!useSegments || !getSegmentForParticipant(index)">
                                        <span>Participante <span x-text="index + 1"></span></span>
                                    </template>
                                </h4>
                                <div x-show="user" class="flex items-center gap-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm">
                                        <input type="checkbox" @change="toggleMe(index, $el.checked)" :checked="participant.isMe" class="rounded border-gray-300 dark:border-slate-600">
                                        <span class="text-gray-700 dark:text-gray-300">Sou eu</span>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" :name="'participants['+index+'][registration_segment_id]'" :value="participant.registration_segment_id || ''">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome completo</label>
                                    <input type="text" :name="'participants['+index+'][name]'" x-model="participant.name" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                                    <input type="email" :name="'participants['+index+'][email]'" x-model="participant.email" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm" required>
                                </div>
                                <div x-show="!useSegments">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF</label>
                                    <input type="text" :name="'participants['+index+'][document]'" x-model="participant.document" x-mask="999.999.999-99" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="___.___.___-__">
                                </div>
                                <template x-for="docKey in (useSegments && getSegmentForParticipant(index) ? getSegmentForParticipant(index).documents_requested : [])" :key="docKey">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="docLabels[docKey] || docKey"></label>
                                        <input type="text" :name="'participants['+index+'][custom_responses][doc_'+docKey+']'" x-model="participant.custom_responses['doc_'+docKey]" :x-mask="docKey === 'cpf' ? '999.999.999-99' : undefined" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" :placeholder="docKey === 'cpf' ? '___.___.___-__' : ''">
                                    </div>
                                </template>
                                <div x-show="!useSegments || (getSegmentForParticipant(index) && getSegmentForParticipant(index).ask_phone)">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                                    <input type="text" :name="'participants['+index+'][phone]'" x-model="participant.phone" x-mask="(99) 99999-9999" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="(__) _____-____">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de nascimento</label>
                                    <input type="text" x-model="participant.birth_date" x-mask="99/99/9999" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="dd/mm/aaaa" maxlength="10" required>
                                    <input type="hidden" :name="'participants['+index+'][birth_date]'" :value="formatBirthDateForSubmit(participant.birth_date)">
                                </div>
                                <template x-for="(field, fi) in (useSegments && getSegmentForParticipant(index) ? getSegmentForParticipant(index).form_fields : formFields)" :key="field.name + '_' + index">
                                    <div :class="field.type === 'textarea' ? 'sm:col-span-2' : ''">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="field.label"></label>
                                        <template x-if="field.type === 'textarea'">
                                            <textarea :name="'participants['+index+'][custom_responses]['+field.name+']'" x-model="participant.custom_responses[field.name]" :required="field.required" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm" rows="2"></textarea>
                                        </template>
                                        <template x-if="field.type !== 'textarea'">
                                            <input :type="field.type === 'number' ? 'number' : (field.type === 'email' ? 'email' : 'text')" :name="'participants['+index+'][custom_responses]['+field.name+']'" x-model="participant.custom_responses[field.name]" :required="field.required" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm">
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Step 3: Resumo e pagamento --}}
                <div x-show="step === 3" x-cloak class="space-y-6">
                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-xl p-6 border border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('events::messages.summary') ?? 'Resumo' }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Evento</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ Str::limit($event->title, 30) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Participantes</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="participants.length"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-slate-700">
                                <span class="font-bold text-gray-900 dark:text-white">{{ __('events::messages.total') ?? 'Total' }}</span>
                                <span class="text-lg font-bold text-[var(--color-main)]" x-text="formatCurrency(totalAmount)"></span>
                            </div>
                        </div>
                    </div>

                    @if(!$isFree && count($gateways ?? []) > 0)
                        <div class="space-y-4" x-show="totalAmount > 0">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-icon name="credit-card" style="duotone" class="w-5 h-5 text-amber-500" />
                                {{ __('events::messages.payment_method') ?? 'Forma de pagamento' }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($gateways as $gateway)
                                    @if($gateway->isConfigured())
                                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-700 hover:border-amber-500 dark:hover:border-amber-500 cursor-pointer transition-colors gateway-option-public">
                                            <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}" data-name="{{ $gateway->name }}" class="sr-only peer gateway-input-public" :required="totalAmount > 0" @change="updateGatewayInterfacePublic">
                                            @if($gateway->logo_url)
                                                <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->display_name }}" class="w-10 h-10 object-contain">
                                            @else
                                                <x-icon name="credit-card" style="duotone" class="w-8 h-8 text-gray-500" />
                                            @endif
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $gateway->display_name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            <div id="public_registration_brick_wrapper" class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700" style="display: none;">
                                <div id="public_registration_brick_container" class="w-full" style="min-height: 360px;"></div>
                            </div>
                            <input type="hidden" name="payment_method" id="public_method_input" value="">
                            <input type="hidden" name="brick_payload" id="public_brick_payload" value="">
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('events::messages.agree_terms') ?? 'Ao inscrever-se você concorda com os termos do evento.' }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-slate-700 shrink-0 bg-gray-50 dark:bg-slate-800/50">
                <div>
                    <button type="button" x-show="step > 1" @click="step--" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        {{ __('events::messages.back') ?? 'Voltar' }}
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" x-show="step < 3" @click="goNext()" class="px-6 py-3 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors">
                        {{ __('events::messages.next') ?? 'Próximo' }}
                    </button>
                    <button type="submit" x-show="step === 3" :disabled="participants.length === 0" class="px-6 py-3 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors disabled:opacity-50">
                        @if($isFree)
                            {{ __('events::messages.confirm_free_registration') ?? 'Confirmar inscrição gratuita' }}
                        @else
                            {{ __('events::messages.finish_and_pay') ?? 'Finalizar e pagar' }}
                        @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://sdk.mercadopago.com/js/v2"></script>
        <script>
document.addEventListener('alpine:init', () => {
    const docLabels = { cpf: 'CPF', rg: 'RG', titulo_eleitor: 'Título de Eleitor' };

    Alpine.data('eventRegistrationWizard', () => {
        const el = document.getElementById('event-registration-wizard-data');
        const data = el ? JSON.parse(el.textContent) : {};
        const config = data.config || {};
        const isFree = data.isFree !== false;
        const hasBatches = data.hasBatches === true;
        const batches = Array.isArray(data.batches) ? data.batches : [];
        const defaultParticipant = data.defaultParticipant || null;
        const useSegments = !!(config.use_segments && (config.segments || []).length);
        const segments = useSegments ? (config.segments || []) : [];
        const formFields = (!useSegments && config.form_fields) ? config.form_fields : [];
        const initialStep = (typeof data.initialStep === 'number' && data.initialStep >= 1 && data.initialStep <= 3) ? data.initialStep : 1;
        const segmentQuantitiesInit = {};
        if (useSegments && segments.length) {
            segments.forEach(seg => { segmentQuantitiesInit[seg.id] = 0; });
        }

        return {
            step: initialStep,
            stepError: '',
            formFields: Array.isArray(formFields) ? formFields : [],
            useSegments,
            segments,
            segmentQuantities: segmentQuantitiesInit,
            docLabels,
            isFree,
            hasBatches,
            batches,
            selectedBatchId: (hasBatches && batches[0]) ? batches[0].id : null,
            freeTickets: 1,
            user: defaultParticipant ? Object.assign(
                { name: '', email: '', document: '', phone: '', birth_date: '' },
                defaultParticipant
            ) : null,
            participants: [],
            paymentBrickController: null,
            isBrickLoading: false,

            init() {
                if (hasBatches && batches.length) this.selectedBatchId = batches[0].id;
                if (!this.useSegments) this.$watch('freeTickets', () => this.syncParticipants());
                this.$watch('segmentQuantities', () => this.syncParticipants(), { deep: true });
                this.syncParticipants();
                this.restoreDraft();
                if (defaultParticipant && this.participants.length && !this.useSegments) {
                    const first = this.participants[0];
                    if (!first.name && !first.email) {
                        first.name = defaultParticipant.name || '';
                        first.email = defaultParticipant.email || '';
                        first.document = defaultParticipant.document || '';
                        first.phone = defaultParticipant.phone || '';
                        first.birth_date = this.toDisplayBirthDate(defaultParticipant.birth_date || '');
                    }
                }
            },

            toDisplayBirthDate(val) {
                if (!val) return '';
                const m = String(val).match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (m) return m[3] + '/' + m[2] + '/' + m[1];
                return val;
            },

            formatBirthDateForSubmit(val) {
                if (!val) return '';
                const s = String(val).trim();
                const dmY = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                if (dmY) {
                    const d = dmY[1].padStart(2, '0'), M = dmY[2].padStart(2, '0'), y = dmY[3];
                    return y + '-' + M + '-' + d;
                }
                if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
                return '';
            },

            getDraftKey() {
                const id = this.$el && this.$el.getAttribute && this.$el.getAttribute('data-event-id');
                return id ? 'event-registration-draft-' + id : null;
            },

            saveDraft() {
                const key = this.getDraftKey();
                if (!key) return;
                try {
                    sessionStorage.setItem(key, JSON.stringify({
                        step: this.step,
                        participants: JSON.parse(JSON.stringify(this.participants)),
                        selectedBatchId: this.selectedBatchId,
                        segmentQuantities: { ...this.segmentQuantities },
                        freeTickets: this.freeTickets
                    }));
                } catch (e) {}
            },

            clearDraft() {
                const key = this.getDraftKey();
                if (key) try { sessionStorage.removeItem(key); } catch (e) {}
            },

            restoreDraft() {
                const key = this.getDraftKey();
                if (!key) return;
                try {
                    const raw = sessionStorage.getItem(key);
                    if (!raw) return;
                    const draft = JSON.parse(raw);
                    if (draft.step != null) this.step = Math.min(3, Math.max(1, parseInt(draft.step, 10) || 1));
                    if (draft.selectedBatchId != null && this.hasBatches) this.selectedBatchId = draft.selectedBatchId;
                    if (draft.segmentQuantities && typeof draft.segmentQuantities === 'object') this.segmentQuantities = { ...this.segmentQuantities, ...draft.segmentQuantities };
                    if (draft.freeTickets != null && !this.useSegments) this.freeTickets = Math.max(1, parseInt(draft.freeTickets, 10) || 1);
                    this.syncParticipants();
                    if (Array.isArray(draft.participants) && draft.participants.length) {
                        draft.participants.forEach((p, i) => {
                            if (this.participants[i]) {
                                const merged = { ...this.participants[i], ...p };
                                if (merged.birth_date && /^\d{4}-\d{2}-\d{2}$/.test(String(merged.birth_date))) merged.birth_date = this.toDisplayBirthDate(merged.birth_date);
                                this.participants[i] = merged;
                            }
                        });
                    }
                } catch (e) {}
            },

            get totalAmount() {
                if (this.hasBatches && this.selectedBatchId) {
                    const b = this.batches.find(x => x.id == this.selectedBatchId);
                    return b ? parseFloat(b.price) || 0 : 0;
                }
                if (this.useSegments && this.segments.length) {
                    return this.participants.reduce((sum, p) => {
                        const seg = this.segments.find(s => s.id == p.registration_segment_id);
                        return sum + (seg && seg.price != null ? parseFloat(seg.price) || 0 : 0);
                    }, 0);
                }
                return this.isFree ? 0 : 0;
            },

            goNext() {
                this.stepError = '';

                if (this.step === 1) {
                    this.syncParticipants();
                    if (this.participants.length === 0) {
                        this.stepError = 'Escolha pelo menos 1 ingresso ou participante para continuar.';
                        return;
                    }
                } else if (this.step === 2) {
                    if (!this.validateParticipantsStep()) {
                        return;
                    }
                }

                if (this.step < 3) {
                    this.step++;
                }
                if (this.step === 3) {
                    this.$nextTick(() => this.updateGatewayInterfacePublic());
                }
            },

            setSegmentQuantity(segmentId, n) {
                this.segmentQuantities = { ...this.segmentQuantities, [segmentId]: n };
                this.syncParticipants();
            },

            getSegmentForParticipant(participantIndex) {
                if (!this.useSegments || !this.segments.length) return null;
                const p = this.participants[participantIndex];
                if (!p || p.registration_segment_id == null) return null;
                return this.segments.find(s => s.id == p.registration_segment_id) || null;
            },

            getParticipantSlotInSegment(participantIndex) {
                const seg = this.getSegmentForParticipant(participantIndex);
                if (!seg) return 0;
                let idx = 0;
                for (let i = 0; i < participantIndex; i++) {
                    if (this.participants[i].registration_segment_id == seg.id) idx++;
                }
                return idx;
            },

            syncParticipants() {
                if (this.useSegments && this.segments.length) {
                    this.participants = [];
                    this.segments.forEach(seg => {
                        const qty = Math.max(0, parseInt(this.segmentQuantities[seg.id], 10) || 0);
                        const maxQty = Math.max(1, parseInt(seg.quantity, 10) || 1);
                        const actual = Math.min(qty, maxQty);
                        for (let i = 0; i < actual; i++) {
                            this.participants.push({
                                registration_segment_id: seg.id,
                                name: '', email: '', document: '', phone: '', birth_date: '',
                                ticketName: seg.label, isMe: false,
                                custom_responses: {}
                            });
                        }
                    });
                    return;
                }
                const n = this.hasBatches ? 1 : Math.max(1, parseInt(this.freeTickets, 10) || 1);
                while (this.participants.length > n) this.participants.pop();
                while (this.participants.length < n) {
                    this.participants.push({
                        name: '', email: '', document: '', phone: '', birth_date: '',
                        ticketName: 'Ingresso', isMe: false,
                        custom_responses: {}
                    });
                }
            },

            validateParticipantsStep() {
                if (!this.participants.length) {
                    this.stepError = 'Adicione pelo menos um participante para continuar.';
                    return false;
                }

                const missing = [];

                this.participants.forEach((p, index) => {
                    const num = index + 1;
                    const prefix = 'Participante ' + num + ': ';

                    const name = (p.name || '').trim();
                    const email = (p.email || '').trim();
                    const birth = (p.birth_date || '').trim();

                    if (!name) missing.push(prefix + 'nome completo');
                    if (!email) missing.push(prefix + 'e-mail');
                    if (!birth) missing.push(prefix + 'data de nascimento');

                    const seg = this.getSegmentForParticipant(index);
                    const fields = (this.useSegments && seg && Array.isArray(seg.form_fields))
                        ? seg.form_fields
                        : (Array.isArray(this.formFields) ? this.formFields : []);

                    const custom = p.custom_responses || {};
                    fields.forEach(field => {
                        if (!field) return;
                        const isRequired = !!field.required;
                        if (!isRequired) return;
                        const key = field.name;
                        const label = field.label || key;
                        const value = (custom[key] || '').toString().trim();
                        if (!value) {
                            missing.push(prefix + label);
                        }
                    });
                });

                if (missing.length) {
                    this.stepError = missing.join('; ');
                    this.$nextTick(() => {
                        const top = this.$el.querySelector('[data-step=\"2-top\"]');
                        if (top && typeof top.scrollIntoView === 'function') {
                            top.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                    return false;
                }

                return true;
            },

            toggleMe(index, isChecked) {
                this.participants.forEach((p, i) => { p.isMe = i === index && isChecked; });
                if (isChecked && this.user) {
                    const u = this.user;
                    this.participants[index].name       = u.name       || '';
                    this.participants[index].email      = u.email      || '';
                    this.participants[index].document   = u.document   || u.cpf || '';
                    this.participants[index].phone      = u.phone      || '';
                    this.participants[index].birth_date = this.toDisplayBirthDate(u.birth_date || '');

                    // Auto-preenche campos customizados / custom_responses com dados do perfil do membro
                    if (!this.participants[index].custom_responses) {
                        this.participants[index].custom_responses = {};
                    }
                    const cr = this.participants[index].custom_responses;
                    // Mapeia CPF para campos de documento de segmento
                    if (u.cpf || u.document) {
                        cr['doc_cpf'] = u.cpf || u.document || '';
                        cr['cpf']     = u.cpf || u.document || '';
                    }
                    // Demais campos demográficos do perfil
                    const extras = { gender: 'gender', city: 'city', state: 'state',
                                     address: 'address', zip_code: 'zip_code',
                                     neighborhood: 'neighborhood' };
                    Object.entries(extras).forEach(([key, crKey]) => {
                        if (u[key]) cr[crKey] = u[key];
                    });

                } else if (!isChecked) {
                    this.participants[index].name       = '';
                    this.participants[index].email      = '';
                    this.participants[index].document   = '';
                    this.participants[index].phone      = '';
                    this.participants[index].birth_date = '';
                    this.participants[index].custom_responses = {};
                }
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
            },

            updateGatewayInterfacePublic() {
                const selected = document.querySelector('.gateway-input-public:checked');
                const wrapper = document.getElementById('public_registration_brick_wrapper');
                const container = document.getElementById('public_registration_brick_container');
                if (!wrapper || !container) return;
                const name = selected ? selected.dataset.name : null;
                if (name === 'mercado_pago' && this.totalAmount > 0) {
                    wrapper.style.display = 'block';
                    this.$nextTick(() => this.initPaymentBrickPublic());
                } else {
                    wrapper.style.display = 'none';
                    container.innerHTML = '';
                }
            },

            async initPaymentBrickPublic() {
                if (this.isBrickLoading) return;
                this.isBrickLoading = true;
                const container = document.getElementById('public_registration_brick_container');
                const payloadInput = document.getElementById('public_brick_payload');
                const form = document.getElementById('registration-wizard-form');
                if (!container || !payloadInput || !form) return;
                if (this.paymentBrickController) {
                    await this.paymentBrickController.unmount();
                    this.paymentBrickController = null;
                }
                container.innerHTML = '<div class="flex items-center justify-center p-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div></div>';
                await new Promise(r => setTimeout(r, 300));
                container.innerHTML = '';
                @php
                    $mpGateway = ($gateways ?? collect())->firstWhere('name', 'mercado_pago');
                    $mpPublicKey = $mpGateway ? ($mpGateway->getDecryptedCredentials()['public_key'] ?? null) : null;
                @endphp
                const mpPublicKey = @json($mpPublicKey);
                if (!mpPublicKey || typeof MercadoPago === 'undefined') {
                    container.innerHTML = '<p class="text-sm text-gray-500">Carregue a página novamente para ver as opções de pagamento.</p>';
                    this.isBrickLoading = false;
                    return;
                }
                const mp = new MercadoPago(mpPublicKey, { locale: 'pt-BR' });
                const bricksBuilder = mp.bricks();
                let payerEmail = 'guest@vertex.com';
                if (this.participants.length && this.participants[0].email) payerEmail = this.participants[0].email;
                const settings = {
                    initialization: { amount: this.totalAmount, payer: { email: payerEmail, entityType: 'individual' } },
                    customization: { paymentMethods: { bankTransfer: 'all', ticket: 'all', creditCard: 'all', debitCard: 'all' }, visual: { style: { theme: 'default' } } },
                    callbacks: {
                        onSubmit: ({ selectedPaymentMethod, formData }) => {
                            payloadInput.value = JSON.stringify({ ...formData, payment_method_id: selectedPaymentMethod });
                            form.submit();
                        }
                    }
                };
                try {
                    this.paymentBrickController = await bricksBuilder.create('payment', 'public_registration_brick_container', settings);
                } catch (e) {
                    container.innerHTML = '<p class="text-sm text-red-600">Erro ao carregar pagamento. Tente novamente.</p>';
                }
                this.isBrickLoading = false;
            }
        };
    });
});
</script>
@endpush
