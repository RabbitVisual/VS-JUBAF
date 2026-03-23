{{--
    Partial: _form_price_rules.blade.php
    Seção "Regras de Preço" — Gerenciamento de preços inteligentes por tipo de regra.
    $ev = $event ?? null
--}}
@php
    $ev = $event ?? null;
    $ruleTypes = \Modules\Events\App\Models\EventPriceRule::getRuleTypes();
    $ruleData  = \Modules\Events\App\Models\EventPriceRule::getRuleRequiredData();
    $globalRules = old('price_rules', $ev ? $ev->priceRules()->whereNull('registration_segment_id')->get()->toArray() : []);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5"
    x-data="{
        rules: {{ Js::from($globalRules) }},
        addRule() {
            this.rules.push({
                rule_type: 'age_range',
                label: '',
                price: '',
                discount_percentage: '',
                discount_fixed: '',
                min_age: '',
                max_age: '',
                member_status: '',
                church_membership: '',
                participant_type: '',
                gender: 'all',
                discount_code: '',
                date_from: '',
                date_to: '',
                min_participants: '',
                max_participants: '',
                location: '',
                priority: 0,
                is_active: true
            });
        },
        removeRule(idx) {
            this.rules.splice(idx, 1);
        }
    }">

    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="tags" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Regras de Preço Globais</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Regras aplicadas ao evento inteiro. Para regras por faixa, configure nos segmentos acima.</p>
        </div>
    </div>

    {{-- Rule type legend --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
        @foreach($ruleData as $typeKey => $info)
        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
            <span class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0" style="background-color: {{ $info['color'] }}20;">
                <x-icon name="{{ $info['icon'] }}" class="w-3 h-3" style="color: {{ $info['color'] }}" />
            </span>
            <span>{{ $info['label'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Rules list --}}
    <div class="space-y-3">
        <template x-for="(rule, idx) in rules" :key="idx">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Rule header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" :name="`price_rules[${idx}][is_active]`" x-model="rule.is_active"
                                value="1" class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Ativa</span>
                        </label>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                            <x-icon name="tag" style="duotone" class="w-4 h-4 text-amber-500" />
                            <span x-text="rule.label || 'Nova Regra'"></span>
                        </span>
                    </div>
                    <button type="button" @click="removeRule(idx)" class="text-xs text-red-400 hover:text-red-600 flex items-center gap-1">
                        <x-icon name="trash" class="w-3.5 h-3.5" /> Remover
                    </button>
                </div>

                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    {{-- Rule type --}}
                    <div class="sm:col-span-2 md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo de Regra</label>
                        <select :name="`price_rules[${idx}][rule_type]`" x-model="rule.rule_type"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                            @foreach($ruleTypes as $rtKey => $rtLabel)
                                <option value="{{ $rtKey }}">{{ $rtLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Label --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Descrição da Regra</label>
                        <input type="text" :name="`price_rules[${idx}][label]`" x-model="rule.label"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="Ex: Desconto jovens">
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Prioridade (maior = aplicado primeiro)</label>
                        <input type="number" :name="`price_rules[${idx}][priority]`" x-model="rule.priority" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>

                    {{-- Price (override) --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Preço Fixo (R$) <span class="text-gray-400 text-xs font-normal">ou use desconto abaixo</span></label>
                        <input type="number" :name="`price_rules[${idx}][price]`" x-model="rule.price" min="0" step="0.01"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="Sobrescreve o preço base">
                    </div>

                    {{-- Discount % --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Desconto % <span class="text-gray-400 text-xs font-normal">(sobre o preço base)</span></label>
                        <input type="number" :name="`price_rules[${idx}][discount_percentage]`" x-model="rule.discount_percentage" min="0" max="100" step="0.01"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="0 a 100">
                    </div>

                    {{-- Discount Fixed --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Desconto Fixo (R$)</label>
                        <input type="number" :name="`price_rules[${idx}][discount_fixed]`" x-model="rule.discount_fixed" min="0" step="0.01"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="0.00">
                    </div>

                    {{-- Conditional fields per rule type --}}
                    {{-- Age Range --}}
                    <div x-show="rule.rule_type === 'age_range'">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Idade Mínima</label>
                        <input type="number" :name="`price_rules[${idx}][min_age]`" x-model="rule.min_age" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>
                    <div x-show="rule.rule_type === 'age_range'">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Idade Máxima</label>
                        <input type="number" :name="`price_rules[${idx}][max_age]`" x-model="rule.max_age" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>

                    {{-- Member Status --}}
                    <div x-show="rule.rule_type === 'member_status'" class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status de Membro</label>
                        <select :name="`price_rules[${idx}][member_status]`" x-model="rule.member_status"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                            <option value="">— Selecionar —</option>
                            <option value="ativo">Membro Ativo</option>
                            <option value="visitante">Visitante</option>
                        </select>
                    </div>

                    {{-- Church Membership --}}
                    <div x-show="rule.rule_type === 'church_membership'" class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo de Membresia Batista</label>
                        <select :name="`price_rules[${idx}][church_membership]`" x-model="rule.church_membership"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                            <option value="">— Selecionar —</option>
                            @foreach(\Modules\Events\App\Models\EventPriceRule::getMemberStatusOptions() as $mKey => $mLabel)
                                <option value="{{ $mKey }}">{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Gender --}}
                    <div x-show="rule.rule_type === 'gender'" class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Gênero</label>
                        <select :name="`price_rules[${idx}][gender]`" x-model="rule.gender"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                            <option value="all">Todos</option>
                            <option value="male">Homens</option>
                            <option value="female">Mulheres</option>
                        </select>
                    </div>

                    {{-- Discount Code --}}
                    <div x-show="rule.rule_type === 'discount_code'" class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Código Promocional</label>
                        <input type="text" :name="`price_rules[${idx}][discount_code]`" x-model="rule.discount_code"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm font-mono"
                            placeholder="EX: JOVEM2025">
                    </div>

                    {{-- Date Range (early_bird / last_minute / registration_date) --}}
                    <div x-show="['early_bird','last_minute','registration_date'].includes(rule.rule_type)">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Início</label>
                        <input type="datetime-local" :name="`price_rules[${idx}][date_from]`" x-model="rule.date_from"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>
                    <div x-show="['early_bird','last_minute','registration_date'].includes(rule.rule_type)">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Fim</label>
                        <input type="datetime-local" :name="`price_rules[${idx}][date_to]`" x-model="rule.date_to"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>

                    {{-- Group size / bulk --}}
                    <div x-show="['group_size','bulk_discount'].includes(rule.rule_type)">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Mín. Participantes</label>
                        <input type="number" :name="`price_rules[${idx}][min_participants]`" x-model="rule.min_participants" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>
                    <div x-show="['group_size','bulk_discount'].includes(rule.rule_type)">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Máx. Participantes</label>
                        <input type="number" :name="`price_rules[${idx}][max_participants]`" x-model="rule.max_participants" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>

                    {{-- Location --}}
                    <div x-show="rule.rule_type === 'location'" class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Localização (cidade/estado)</label>
                        <input type="text" :name="`price_rules[${idx}][location]`" x-model="rule.location"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="Ex: São Paulo/SP">
                    </div>
                </div>
            </div>
        </template>

        <button type="button" @click="addRule()"
            class="w-full py-3 rounded-xl border-2 border-dashed border-amber-300 dark:border-amber-700 text-sm font-medium text-amber-600 dark:text-amber-400 hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors flex items-center justify-center gap-2">
            <x-icon name="plus" class="w-4 h-4" />
            Adicionar Regra de Preço
        </button>

        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-600">
            <x-icon name="lightbulb" style="duotone" class="w-4 h-4 text-emerald-500 inline mr-1" /> Regras são avaliadas por prioridade. A primeira regra satisfeita é aplicada. O valor 'Menor Preço' ou 'Maior Preço' será considerado caso o candidato acumule múltiplas regras válidas simultaneamente.
        </p>
    </div>
</div>
