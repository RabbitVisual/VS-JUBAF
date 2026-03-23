{{--
    Partial: _form_appearance.blade.php
    Seção "Visual & Aparência" - Configuração dinâmica de cores e template da Landing Page
    $ev = $event ?? null
--}}
@php
    $ev = $event ?? null;
    $themeConfig = fn($key, $default) => old("theme_config.{$key}", $ev?->theme_config[$key] ?? $default);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6"
     x-data="{
         theme: '{{ $themeConfig('theme', 'modern') }}',
         primaryColor: '{{ $themeConfig('primary_color', '#4F46E5') }}',
         secondaryColor: '{{ $themeConfig('secondary_color', '#111827') }}'
     }">

    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="palette" style="duotone" class="w-5 h-5 text-pink-600 dark:text-pink-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Visual & Aparência da Página</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Defina a identidade visual, cores e o formato da landing page do evento</p>
        </div>
    </div>

    {{-- Layout/Template --}}
    <div>
        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
            <x-icon name="browser" style="duotone" class="w-4 h-4 text-pink-500" />
            Estrutura da Landing Page
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-4 gap-4">
            {{-- Modern --}}
            <label :class="theme === 'modern' ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-600 dark:bg-gray-700'"
                class="relative p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-pink-300">
                <input type="radio" name="theme_config[theme]" value="modern" x-model="theme" class="sr-only">
                <div class="mb-3 w-full h-24 bg-gray-100 dark:bg-gray-800 rounded flex flex-col items-center justify-center gap-1 border border-gray-200 dark:border-gray-600 overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-600/20"></div>
                    <div class="w-3/4 h-3 rounded-full bg-gray-300 dark:bg-gray-600 mt-2 z-10"></div>
                    <div class="w-1/2 h-2 rounded-full bg-gray-200 dark:bg-gray-600 z-10"></div>
                    <div class="w-1/3 h-5 rounded bg-pink-500 mt-3 z-10"></div>
                </div>
                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">Moderno (Padrão)</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Vibrante e focado em chamadas à ação, utiliza mais gradientes.</div>

                <div x-show="theme === 'modern'" class="absolute -top-2 -right-2 bg-pink-500 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm text-xs">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                </div>
            </label>

            {{-- Minimal --}}
            <label :class="theme === 'minimal' ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-600 dark:bg-gray-700'"
                class="relative p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-pink-300">
                <input type="radio" name="theme_config[theme]" value="minimal" x-model="theme" class="sr-only">
                <div class="mb-3 w-full h-24 bg-white dark:bg-gray-800 rounded flex flex-col items-start p-3 gap-2 border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <div class="w-1/2 h-4 rounded bg-gray-800 dark:bg-gray-200"></div>
                    <div class="w-full h-1.5 rounded bg-gray-100 dark:bg-gray-700 mt-1"></div>
                    <div class="w-3/4 h-1.5 rounded bg-gray-100 dark:bg-gray-700"></div>
                    <div class="w-1/4 h-5 rounded border border-gray-300 dark:border-gray-600 mt-auto"></div>
                </div>
                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">Minimalista</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Limpo e elegante, muito espaço em branco e foco na tipografia.</div>

                <div x-show="theme === 'minimal'" class="absolute -top-2 -right-2 bg-pink-500 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm text-xs">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                </div>
            </label>

            {{-- Corporate --}}
            <label :class="theme === 'corporate' ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-600 dark:bg-gray-700'"
                class="relative p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-pink-300">
                <input type="radio" name="theme_config[theme]" value="corporate" x-model="theme" class="sr-only">
                <div class="mb-3 w-full h-24 bg-gray-100 dark:bg-gray-800 rounded flex flex-col border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <div class="w-full h-8 bg-slate-800 dark:bg-slate-900"></div>
                    <div class="flex-1 flex p-2 gap-2">
                        <div class="w-1/3 h-full bg-white dark:bg-gray-700 shadow-sm rounded"></div>
                        <div class="w-2/3 h-full flex flex-col gap-1">
                            <div class="w-full h-3 bg-white dark:bg-gray-700 rounded shadow-sm"></div>
                            <div class="w-full h-3 bg-white dark:bg-gray-700 rounded shadow-sm"></div>
                        </div>
                    </div>
                </div>
                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">Corporativo / Imersão</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Estruturado em blocos e formato mais denso para conferências.</div>

                <div x-show="theme === 'corporate'" class="absolute -top-2 -right-2 bg-pink-500 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm text-xs">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                </div>
            </label>
        </div>
    </div>

    {{-- Color Pickers --}}
    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
            <x-icon name="fill-drip" style="duotone" class="w-4 h-4 text-pink-500" />
            Personalização de Cores
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Cor Primária --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Cor Primária</label>
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden shadow-sm flex-shrink-0 cursor-pointer focus-within:ring-2 focus-within:ring-pink-500 focus-within:ring-offset-2">
                        <input type="color" name="theme_config[primary_color]" x-model="primaryColor"
                            class="absolute -inset-2 w-16 h-16 cursor-pointer opacity-0 text-transparent bg-transparent">
                        <div class="w-full h-full" :style="`background-color: ${primaryColor}`"></div>
                    </div>
                    <div class="flex-1">
                        <input type="text" x-model="primaryColor" pattern="^#[a-fA-F0-9]{6}$"
                            class="block w-full font-mono text-sm rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-700 dark:text-white uppercase"
                            placeholder="#4F46E5">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Usada em botões de ação e destaques pricipais.</p>
                    </div>
                </div>
            </div>

            {{-- Cor Secundária --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Cor Secundária (Background e Rodapé)</label>
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden shadow-sm flex-shrink-0 cursor-pointer focus-within:ring-2 focus-within:ring-pink-500 focus-within:ring-offset-2">
                        <input type="color" name="theme_config[secondary_color]" x-model="secondaryColor"
                            class="absolute -inset-2 w-16 h-16 cursor-pointer opacity-0 text-transparent bg-transparent">
                        <div class="w-full h-full" :style="`background-color: ${secondaryColor}`"></div>
                    </div>
                    <div class="flex-1">
                        <input type="text" x-model="secondaryColor" pattern="^#[a-fA-F0-9]{6}$"
                            class="block w-full font-mono text-sm rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-700 dark:text-white uppercase"
                            placeholder="#111827">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Apoia o design em blocos e seções escuras.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 items-center sm:items-stretch">
            <div class="flex-1 w-full text-center sm:text-left flex flex-col justify-center">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Prévia Genérica</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Veja como os botões principais se comportarão na landing page.</p>
            </div>
            <div class="flex gap-3 justify-center">
                <button type="button" class="px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm text-white transition-opacity hover:opacity-90"
                    :style="`background-color: ${primaryColor}`">
                    Botão Primário
                </button>
                <button type="button" class="px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm text-white transition-opacity hover:opacity-90"
                    :style="`background-color: ${secondaryColor}`">
                    Botão Secundário
                </button>
            </div>
        </div>
    </div>
</div>
