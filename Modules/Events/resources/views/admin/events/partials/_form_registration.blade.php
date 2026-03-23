{{--
    Partial: _form_registration.blade.php
    Seção "Formulário de Inscrição" — com campos padrão configuráveis e suporte a segmentos.
    $ev = $event ?? null
--}}
@php
    $ev = $event ?? null;
    $availableFields = \Modules\Events\App\Models\Event::getAvailableFormFields();
    $effectiveRequired = $ev ? $ev->getEffectiveRequiredFields() : \Modules\Events\App\Models\Event::defaultRequiredFields();
    $segments = old('registration_segments', $ev?->registrationSegments?->toArray() ?? []);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6"
     x-data="{
         mode: '{{ old('registration_mode', $ev?->hasRegistrationSegments() ? 'segments' : 'single') }}',
         segments: {{ Js::from($segments) }},
         segmentIdx: 0,
         addSegment() {
             this.segments.push({
                 label: '',
                 description: '',
                 gender: 'all',
                 min_age: '',
                 max_age: '',
                 quantity: '',
                 price: '0',
                 required_fields: {},
                 form_fields: [],
                 segment_price_rules: []
             });
         },
         removeSegment(idx) {
             this.segments.splice(idx, 1);
         }
     }">

    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="clipboard-list" style="duotone" class="w-5 h-5 text-violet-600 dark:text-violet-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Formulário de Inscrição</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure os campos e categorias de participação</p>
        </div>
    </div>

    {{-- ═══════════════════════════════ CAMPOS PADRÃO ═══════════════════════════════ --}}
    <div>
        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1 flex items-center gap-2">
            <x-icon name="toggle-on" style="duotone" class="w-4 h-4 text-violet-500" />
            Campos do Participante
        </h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Defina quais campos devem ser coletados no formulário de inscrição e se são obrigatórios ou opcionais.</p>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">Campo</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Obrigatório</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Opcional</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Desabilitado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($availableFields as $fieldKey => $fieldLabel)
                    @php
                        $currentVal = $effectiveRequired[$fieldKey] ?? 'disabled';
                        $isFixed = in_array($fieldKey, ['name']); // name is always required
                    @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50/30 dark:bg-gray-700/20' : '' }}">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                            {{ $fieldLabel }}
                            @if($isFixed)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">fixo</span>
                            @endif
                        </td>
                        @foreach(['required' => 'Obrigatório', 'optional' => 'Opcional', 'disabled' => 'Desabilitado'] as $val => $valLabel)
                        <td class="px-4 py-3 text-center">
                            @if($isFixed && $val !== 'required')
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @else
                                <input type="radio"
                                    name="default_required_fields[{{ $fieldKey }}]"
                                    value="{{ $val }}"
                                    {{ $currentVal === $val ? 'checked' : '' }}
                                    {{ $isFixed ? 'disabled' : '' }}
                                    class="focus:ring-violet-500 h-4 w-4 text-violet-600 border-gray-300 dark:border-gray-500 dark:bg-gray-700">
                                @if($isFixed)
                                    <input type="hidden" name="default_required_fields[{{ $fieldKey }}]" value="required">
                                @endif
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            <x-icon name="lightbulb" style="duotone" class="w-4 h-4 text-violet-500 inline mr-1" /> <strong>Obrigatório</strong>: campo obrigatório no formulário. <strong>Opcional</strong>: aparece mas não é obrigatório. <strong>Desabilitado</strong>: campo oculto.
        </p>
    </div>

    {{-- ═══════════════════════════════ MODO DE INSCRIÇÃO ═══════════════════════════════ --}}
    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
            <x-icon name="list-radio" style="duotone" class="w-4 h-4 text-violet-500" />
            Modo de Inscrição
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            <label :class="mode === 'single' ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-gray-200 dark:border-gray-600'"
                class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors">
                <input type="radio" name="registration_mode" value="single" x-model="mode" class="mt-0.5 text-violet-600 focus:ring-violet-500">
                <div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">Formulário Único</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Um único formulário e preço para todos os participantes do evento.</div>
                </div>
            </label>
            <label :class="mode === 'segments' ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-gray-200 dark:border-gray-600'"
                class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors">
                <input type="radio" name="registration_mode" value="segments" x-model="mode" class="mt-0.5 text-violet-600 focus:ring-violet-500">
                <div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                        Categorias / Faixas
                        <span class="text-xs font-normal text-violet-600 dark:text-violet-400">(Recomendado)</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Permite criar faixas de inscrição com preços, idades e gêneros diferentes. Ex: Adultos, Jovens, Crianças.</div>
                </div>
            </label>
        </div>

        {{-- ─── Single mode: custom extra fields ─── --}}
        <div x-show="mode === 'single'" x-transition>
            @include('events::admin.events.partials._form_fields_builder', ['fieldPrefix' => 'form_fields', 'existingFields' => old('form_fields', $ev?->form_fields ?? [])])
        </div>

        {{-- ─── Segments mode ─── --}}
        <div x-show="mode === 'segments'" x-transition class="space-y-4">
            <template x-for="(seg, idx) in segments" :key="idx">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Segment header --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <x-icon name="ticket" style="duotone" class="w-4 h-4 text-violet-500" />
                            <span x-text="seg.label || 'Nova Faixa'"></span>
                        </span>
                        <button type="button" @click="removeSegment(idx)"
                            class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 flex items-center gap-1 transition-colors">
                            <x-icon name="trash" class="w-3.5 h-3.5" /> Remover
                        </button>
                    </div>

                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Segment label --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Faixa <span class="text-red-500">*</span></label>
                            <input type="text" :name="`registration_segments[${idx}][label]`" x-model="seg.label" required
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Ex: Adultos, Jovens, Crianças">
                        </div>

                        {{-- Gender --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Restrição de Gênero</label>
                            <select :name="`registration_segments[${idx}][gender]`" x-model="seg.gender"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white">
                                <option value="all">Todos os gêneros</option>
                                <option value="male">Apenas Homens</option>
                                <option value="female">Apenas Mulheres</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição da Faixa</label>
                            <input type="text" :name="`registration_segments[${idx}][description]`" x-model="seg.description"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Ex: Para participantes a partir de 18 anos">
                        </div>

                        {{-- Ages --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Idade Mínima</label>
                            <input type="number" :name="`registration_segments[${idx}][min_age]`" x-model="seg.min_age" min="0" max="120"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Sem limite">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Idade Máxima</label>
                            <input type="number" :name="`registration_segments[${idx}][max_age]`" x-model="seg.max_age" min="0" max="120"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Sem limite">
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Vagas <span class="text-red-500">*</span></label>
                            <input type="number" :name="`registration_segments[${idx}][quantity]`" x-model="seg.quantity" min="1" required
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="Ex: 50">
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Preço Base (R$)</label>
                            <input type="number" :name="`registration_segments[${idx}][price]`" x-model="seg.price" min="0" step="0.01"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm text-sm dark:bg-gray-700 dark:text-white"
                                placeholder="0.00">
                            <p class="mt-1 text-xs text-gray-400">0 = gratuito</p>
                        </div>

                        {{-- Required fields override --}}
                        <div class="md:col-span-2">
                            <div class="p-3 rounded-lg bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700">
                                <p class="text-xs font-semibold text-violet-700 dark:text-violet-300 mb-2">
                                    <x-icon name="sliders" class="w-3.5 h-3.5 inline mr-1" />
                                    Override de Campos (opcional)
                                </p>
                                <p class="text-xs text-violet-600 dark:text-violet-400 mb-3">Deixe vazio para herdar as configurações do evento. Sobrescreva apenas o que for diferente para esta faixa.</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($availableFields as $fieldKey => $fieldLabel)
                                    <div class="flex items-center gap-2">
                                        <select :name="`registration_segments[${idx}][required_fields][{{ $fieldKey }}]`"
                                            class="flex-1 rounded-md border-violet-300 dark:border-violet-600 text-xs dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">{{ $fieldLabel }} (herdar)</option>
                                            <option value="required">Obrigatório</option>
                                            <option value="optional">Opcional</option>
                                            <option value="disabled">Desabilitado</option>
                                        </select>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Documents --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Documentos Solicitados</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach(\Modules\Events\App\Models\EventRegistrationSegment::getDocumentTypes() as $docKey => $docLabel)
                                <label class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox"
                                        :name="`registration_segments[${idx}][documents_requested][]`"
                                        value="{{ $docKey }}"
                                        class="rounded border-gray-300 text-violet-600 focus:ring-violet-500 text-xs dark:border-gray-600 dark:bg-gray-700">
                                    {{ $docLabel }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addSegment()"
                class="w-full py-3 rounded-xl border-2 border-dashed border-violet-300 dark:border-violet-700 text-sm font-medium text-violet-600 dark:text-violet-400 hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors flex items-center justify-center gap-2">
                <x-icon name="plus" class="w-4 h-4" />
                Adicionar Faixa de Inscrição
            </button>
        </div>
    </div>
</div>
