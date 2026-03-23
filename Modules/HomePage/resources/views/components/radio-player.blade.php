@php
    $variant = $variant ?? 'full';
@endphp

@if (!empty($embedUrl))
    @if ($variant === 'compact')
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-lg">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-linear-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-md">
                        <x-icon name="tower-broadcast" style="duotone" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.3em] text-blue-400 font-semibold">Ao vivo</p>
                        <h3 class="text-sm font-bold text-white leading-tight">Rádio 3:16</h3>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-300">
                    <x-icon name="circle-play" style="duotone" class="w-4 h-4 text-green-400" />
                    <span>Ouça agora</span>
                </div>
            </div>

            <div class="mt-4 rounded-xl overflow-hidden bg-black/20">
                <iframe
                    src="{{ $embedUrl }}"
                    title="Rádio 3:16"
                    border="0"
                    scrolling="no"
                    frameborder="0"
                    allow="autoplay; clipboard-write"
                    allowtransparency="true"
                    style="width: 100%; height: 165px; background-color: transparent;"
                    loading="lazy">
                </iframe>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                        <x-icon name="tower-broadcast" style="duotone" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300 font-semibold">
                            Ao vivo
                        </p>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">
                            Rádio 3:16
                        </h2>
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 mt-1">
                            Acompanhe nossa programação ao vivo enquanto navega pelo site.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-300">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                        </span>
                        No ar
                    </span>
                </div>
            </div>

            <div class="mt-6 rounded-2xl overflow-hidden bg-black/5 dark:bg-black/20">
                <iframe
                    src="{{ $embedUrl }}"
                    title="Rádio 3:16"
                    border="0"
                    scrolling="no"
                    frameborder="0"
                    allow="autoplay; clipboard-write"
                    allowtransparency="true"
                    style="width: 100%; height: 165px; background-color: transparent;"
                    loading="lazy">
                </iframe>
            </div>
        </div>
    @endif
@endif

