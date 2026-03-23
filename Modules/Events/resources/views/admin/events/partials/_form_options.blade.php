{{--
    Partial: _form_options.blade.php
    Seção "Opções da Página, Badge, Certificado e Coupons"
    $ev = $event ?? null
--}}
@php
    $ev  = $event ?? null;
    $opt = $ev ? $ev->options : \Modules\Events\App\Models\Event::defaultOptions();
    $coupons = old('coupons', $ev?->coupons?->toArray() ?? []);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
            <x-icon name="gear" style="duotone" class="w-5 h-5 text-slate-600 dark:text-slate-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Opções da Página e Recursos</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure o que aparece na página pública e recursos extras</p>
        </div>
    </div>

    {{-- Page display options --}}
    <div>
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Exibição na Página do Evento</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
                $displayOpts = [
                    'show_cover'    => ['icon' => 'image',         'label' => 'Banner/Capa'],
                    'show_about'    => ['icon' => 'circle-info',   'label' => 'Sobre o Evento'],
                    'show_speakers' => ['icon' => 'microphone',    'label' => 'Palestrantes'],
                    'show_schedule' => ['icon' => 'list-timeline', 'label' => 'Programação'],
                    'show_location' => ['icon' => 'location-dot',  'label' => 'Local'],
                    'show_map'      => ['icon' => 'map',           'label' => 'Mapa Interativo'],
                    'show_capacity' => ['icon' => 'users',         'label' => 'Vagas Disponíveis'],
                    'show_contact'  => ['icon' => 'phone',         'label' => 'Contato'],
                    'show_audience' => ['icon' => 'person-circle-question', 'label' => 'Público-Alvo'],
                ];
            @endphp
            @foreach($displayOpts as $optKey => $optInfo)
            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <input type="hidden" name="options[{{ $optKey }}]" value="0">
                <input type="checkbox" name="options[{{ $optKey }}]" id="opt_{{ $optKey }}" value="1"
                    {{ old("options.$optKey", $opt[$optKey] ?? true) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-slate-600 focus:ring-slate-500 dark:bg-gray-700">
                <div class="flex items-center gap-2 flex-1">
                    <x-icon name="{{ $optInfo['icon'] }}" style="duotone" class="w-4 h-4 text-slate-500 dark:text-slate-400" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $optInfo['label'] }}</span>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Resources --}}
    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Recursos Extras</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @php
                $resOpts = [
                    'has_ticket'      => ['icon' => 'ticket',          'color' => 'blue',   'label' => 'Ingressos Digitais',     'desc' => 'Gera um ingresso PDF com QR Code'],
                    'has_checkin'     => ['icon' => 'qrcode',          'color' => 'green',  'label' => 'Check-in por QR Code',   'desc' => 'Permite check-in via leitura de QR Code'],
                    'has_badge'       => ['icon' => 'id-card-clip',    'color' => 'indigo', 'label' => 'Credenciais (Badge)',     'desc' => 'Gera credenciais para impressão'],
                    'has_certificate' => ['icon' => 'award',           'color' => 'amber',  'label' => 'Certificados',           'desc' => 'Gera certificado de participação'],
                ];
            @endphp
            @foreach($resOpts as $resKey => $resInfo)
            <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-{{ $resInfo['color'] }}-400 transition-colors group">
                <input type="hidden" name="options[{{ $resKey }}]" value="0">
                <input type="checkbox" name="options[{{ $resKey }}]" id="res_{{ $resKey }}" value="1"
                    {{ old("options.$resKey", $opt[$resKey] ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-{{ $resInfo['color'] }}-600 focus:ring-{{ $resInfo['color'] }}-500 dark:bg-gray-700 mt-0.5">
                <div>
                    <div class="flex items-center gap-2">
                        <x-icon name="{{ $resInfo['icon'] }}" style="duotone" class="w-4 h-4 text-{{ $resInfo['color'] }}-500" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-{{ $resInfo['color'] }}-700 dark:group-hover:text-{{ $resInfo['color'] }}-400 transition-colors">{{ $resInfo['label'] }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $resInfo['desc'] }}</p>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Coupons section --}}
    <div class="pt-4 border-t border-gray-100 dark:border-gray-700"
         x-data="{
             coupons: {{ Js::from($coupons) }},
             addCoupon() {
                 this.coupons.push({ code: '', description: '', discount_type: 'percent', discount_value: '', max_uses: '', is_active: true });
             },
             removeCoupon(idx) { this.coupons.splice(idx, 1); }
         }">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                <x-icon name="tag" style="duotone" class="w-4 h-4 text-rose-500" />
                Cupons de Desconto
            </h4>
        </div>

        <div class="space-y-3">
            <template x-for="(coupon, idx) in coupons" :key="idx">
                <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Código</label>
                        <input type="text" :name="`coupons[${idx}][code]`" x-model="coupon.code"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm font-mono uppercase"
                            placeholder="PROMO2025">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
                        <select :name="`coupons[${idx}][discount_type]`" x-model="coupon.discount_type"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                            <option value="percent">% Percentual</option>
                            <option value="fixed">R$ Fixo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Valor</label>
                        <input type="number" :name="`coupons[${idx}][discount_value]`" x-model="coupon.discount_value" min="0" step="0.01"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Máx. Usos</label>
                        <input type="number" :name="`coupons[${idx}][max_uses]`" x-model="coupon.max_uses" min="1"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                            placeholder="Sem limite">
                    </div>
                    <div class="flex items-end gap-2 pb-1">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" :name="`coupons[${idx}][is_active]`" x-model="coupon.is_active" value="1"
                                class="rounded border-gray-300 text-rose-500 focus:ring-rose-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-xs text-gray-600 dark:text-gray-400">Ativo</span>
                        </label>
                        <button type="button" @click="removeCoupon(idx)" class="ml-auto text-red-400 hover:text-red-600 text-xs flex items-center gap-1">
                            <x-icon name="trash" class="w-3.5 h-3.5" /> Remover
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addCoupon()"
                class="w-full py-2.5 rounded-lg border border-dashed border-rose-300 dark:border-rose-700 text-xs font-medium text-rose-500 dark:text-rose-400 hover:border-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors flex items-center justify-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5" />
                Adicionar Cupom
            </button>
        </div>
    </div>
</div>
