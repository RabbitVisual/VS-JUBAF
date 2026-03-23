@extends('homepage::components.layouts.master')

@section('content')
    <section class="relative py-12 sm:py-16 md:py-24 overflow-hidden bg-gradient-to-b from-slate-50 to-white dark:from-gray-900 dark:to-gray-900 transition-colors duration-300 min-h-screen">
        <div class="absolute inset-0 opacity-30 dark:opacity-10 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 dark:bg-blue-500/10 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-indigo-400/20 dark:bg-indigo-500/10 rounded-full blur-3xl animate-float-slow" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Hero --}}
            <div class="text-center mb-10 md:mb-14">
                <span class="inline-block text-blue-600 dark:text-blue-400 font-bold uppercase tracking-[0.2em] text-xs sm:text-sm mb-3 animate-fade-in" style="animation-delay: 0.1s;">Transmissão ao vivo</span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white mb-3 md:mb-4 tracking-tight animate-fade-in-up" style="animation-delay: 0.2s;">Rádio Rede 3.16</h1>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full mx-auto mb-5 animate-fade-in" style="animation-delay: 0.3s;"></div>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-8 animate-fade-in-up" style="animation-delay: 0.35s;">24 horas compartilhando o amor de Deus</p>
                <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-green-500/10 dark:bg-green-500/20 border border-green-400/30 shadow-lg shadow-green-500/10 animate-fade-in" style="animation-delay: 0.45s;">
                    <div class="flex items-end gap-1 h-5" aria-hidden="true">
                        @foreach ([8, 14, 10, 18, 12, 16, 9] as $i => $h)
                            <span class="radio-bar w-1 rounded-full bg-green-500 dark:bg-green-400" style="height: {{ $h }}px; animation: radio-eq 0.6s ease-in-out {{ $i * 0.05 }}s infinite;"></span>
                        @endforeach
                    </div>
                    <span class="text-sm font-bold text-green-700 dark:text-green-300 uppercase tracking-wider">Ao vivo</span>
                </div>
            </div>

            @if ($showRadio && !empty($embedUrlForPage))
                {{-- Card do player: logo oficial da rádio + equalizer animado + iframe --}}
                <div class="relative rounded-3xl shadow-2xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden mb-8 md:mb-10 animate-fade-in-up player-card-glow" style="animation-delay: 0.5s;">
                    <div class="absolute inset-0 bg-gradient-to-br from-white to-gray-50/80 dark:from-gray-800 dark:to-gray-800/80"></div>
                    <div class="absolute top-0 right-0 w-72 h-72 bg-blue-500/10 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-56 h-56 bg-indigo-500/10 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                    {{-- Faixa superior escura: logo (clara) e equalizer legíveis no modo claro --}}
                    <div class="relative flex flex-col sm:flex-row items-center justify-between gap-4 px-6 sm:px-8 pt-6 pb-4 bg-gray-800 dark:bg-gray-800 border-b border-gray-700 dark:border-gray-600">
                        <img src="{{ asset('images/logo-rede-316-player-degrade.png') }}" alt="Rede 3.16" class="h-10 sm:h-12 md:h-14 w-auto object-contain opacity-95" onerror="this.style.display='none'">
                        <div class="flex items-center gap-3">
                            <div class="flex items-end gap-1 h-6 sm:h-7" aria-hidden="true">
                                @foreach ([6, 12, 8, 16, 10, 14, 7, 18, 9] as $i => $h)
                                    <span class="radio-bar player-eq-bar rounded-full bg-green-400 dark:bg-green-400" style="height: {{ $h }}px; animation: radio-eq 0.5s ease-in-out {{ $i * 0.06 }}s infinite;"></span>
                                @endforeach
                            </div>
                            <span class="text-xs font-bold text-green-300 dark:text-green-400 uppercase tracking-wider whitespace-nowrap">No ar</span>
                        </div>
                    </div>

                    {{-- Iframe do player (autoplay via URL: auto_play=1 e autoplay=1) --}}
                    <div class="relative rounded-b-3xl overflow-hidden bg-gray-100 dark:bg-gray-900/50" style="min-height: 280px;">
                        <iframe
                            src="{{ $embedUrlForPage }}"
                            title="Rádio Rede 3.16 - Ao vivo"
                            border="0"
                            scrolling="no"
                            frameborder="0"
                            allow="autoplay; clipboard-write"
                            allowtransparency="true"
                            class="w-full block"
                            style="height: 280px; min-height: 260px; background-color: transparent;"
                            loading="eager">
                        </iframe>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-800 dark:text-gray-400 text-center px-4 pt-2 pb-1">A reprodução pode iniciar automaticamente. Se não iniciar, use o botão play do player.</p>
                </div>

                {{-- Versículo do momento (API Bíblia local) --}}
                @if(!empty($randomVerse))
                    <div class="relative rounded-2xl bg-amber-50/90 dark:bg-stone-800/80 border border-amber-200/60 dark:border-amber-900/40 p-6 sm:p-8 mb-8 animate-fade-in" style="animation-delay: 0.55s;">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 mb-2">Versículo do momento</p>
                        <blockquote class="font-serif text-lg sm:text-xl text-stone-800 dark:text-stone-200 italic leading-relaxed">"{{ $randomVerse['text'] }}"</blockquote>
                        <cite class="block text-sm font-semibold text-amber-800 dark:text-amber-300 not-italic mt-2">{{ $randomVerse['reference'] }}</cite>
                    </div>
                @endif

                {{-- Grid: dica + site oficial --}}
                <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-10">
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-blue-50/80 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40 animate-fade-in" style="animation-delay: 0.6s;">
                        <div class="shrink-0 w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-800/50 flex items-center justify-center">
                            <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Ouça enquanto navega</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Abra esta página em uma nova aba para manter a transmissão tocando enquanto você acessa a Bíblia, eventos e outras seções do site.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-5 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 animate-fade-in" style="animation-delay: 0.65s;">
                        <div class="shrink-0 w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <x-icon name="globe" style="duotone" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Site oficial</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Programação, podcasts e mais na Rede 3.16.</p>
                            <a href="https://rede316.com.br/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                <span>Visitar rede316.com.br</span>
                                <x-icon name="arrow-up-right-from-square" style="duotone" class="w-4 h-4 shrink-0" />
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CTA nova aba --}}
                <div class="text-center mb-14 animate-fade-in" style="animation-delay: 0.7s;">
                    <a href="{{ route('homepage.radio') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium text-sm border border-gray-200 dark:border-gray-600 transition-all duration-200">
                        <x-icon name="square-arrow-up-right" style="duotone" class="w-4 h-4" />
                        Abrir em nova aba para ouvir sem interrupção
                    </a>
                </div>
            @else
                {{-- Rádio desativada --}}
                <div class="max-w-md mx-auto text-center py-16 px-8 rounded-3xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 shadow-xl">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <x-icon name="tower-broadcast" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Rádio indisponível</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">A transmissão não está configurada no momento. Volte mais tarde ou acesse o site oficial da Rede 3.16.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('homepage.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                            <x-icon name="house" style="duotone" class="w-5 h-5" /> Voltar ao início
                        </a>
                        <a href="https://rede316.com.br/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <x-icon name="arrow-up-right-from-square" style="duotone" class="w-5 h-5" /> Site oficial
                        </a>
                    </div>
                </div>
            @endif

            {{-- Rodapé da página: nossa logo + Congregação Batista Avenida + Desenvolvido por Reinan Rodrigues --}}
            <footer class="relative pt-8 pb-4 border-t border-gray-200 dark:border-gray-700/80 animate-fade-in" style="animation-delay: 0.75s;">
                <div class="flex flex-col items-center text-center gap-4">
                    <img src="{{ asset(\App\Models\Settings::get('logo_path', 'storage/image/logo_oficial.png')) }}" alt="Congregação Batista Avenida" class="h-12 sm:h-14 w-auto object-contain opacity-90 dark:opacity-85" onerror="this.style.display='none'">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-300">Congregação Batista Avenida</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Desenvolvido por <span class="font-semibold text-gray-800 dark:text-gray-300">Reinan Rodrigues</span></p>
                </div>
            </footer>
        </div>
    </section>

    <style>
        @keyframes radio-eq {
            0%, 100% { transform: scaleY(0.4); opacity: 0.85; }
            50% { transform: scaleY(1); opacity: 1; }
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(10px, -15px); }
        }
        .animate-fade-in { animation: fade-in 0.6s ease-out forwards; opacity: 0; }
        .animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; opacity: 0; }
        .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
        .radio-bar { transform-origin: bottom; }
        .player-eq-bar { min-width: 4px; }
        .player-card-glow { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02); }
        .dark .player-card-glow { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05); }
        @media (max-width: 640px) {
            .radio-bar { min-width: 4px; }
        }
    </style>
@endsection
