@extends('homepage::components.layouts.master')

@section('content')
@if ($importantNotifications->count() > 0)
    {{-- Notification Ticker Bar: Shows one item at a time with auto-rotation --}}
    <div id="notification-bar" class="relative z-60 bg-blue-600 dark:bg-blue-700 border-b border-blue-500/50 shadow-md hidden" style="min-height: 44px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center gap-3 py-2">

            {{-- Live dot icon --}}
            <span class="shrink-0 flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-200 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-full w-full bg-white"></span>
            </span>

            {{-- Label --}}
            <span class="shrink-0 text-[10px] font-black uppercase tracking-[0.15em] text-white/60 hidden sm:block">Aviso</span>

            {{-- Animated notification items --}}
            <div class="flex-1 min-w-0 relative overflow-hidden" style="height: 32px;">
                @foreach($importantNotifications as $i => $notification)
                    <div class="notif-item absolute inset-0 flex items-center gap-3 transition-all duration-500 {{ $i === 0 ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0' }}"
                         data-index="{{ $i }}">
                        <span class="font-bold text-white text-sm truncate shrink-0">{{ $notification->title }}</span>
                        @if($notification->message)
                            <span class="text-blue-100/90 text-xs line-clamp-1 hidden md:block border-l border-white/20 pl-3 leading-relaxed">{{ Str::limit($notification->message, 80) }}</span>
                        @endif
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}"
                               class="shrink-0 bg-white text-blue-700 hover:bg-blue-50 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full transition whitespace-nowrap">
                                {{ $notification->action_text ?: 'Ver mais' }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Counter e navegação --}}
            @if($importantNotifications->count() > 1)
                <div class="shrink-0 flex items-center gap-1.5">
                    <button id="notif-prev" class="w-5 h-5 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition" aria-label="Anterior">
                        <x-icon name="chevron-left" class="w-3 h-3" />
                    </button>
                    <span id="notif-counter" class="text-[10px] font-black text-white/70 w-8 text-center tabular-nums">1/{{ $importantNotifications->count() }}</span>
                    <button id="notif-next" class="w-5 h-5 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition" aria-label="Próximo">
                        <x-icon name="chevron-right" class="w-3 h-3" />
                    </button>
                </div>
            @endif

            {{-- Close button --}}
            <button id="close-notif-bar" class="shrink-0 ml-1 w-7 h-7 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition" aria-label="Fechar">
                <x-icon name="xmark" class="w-4 h-4" />
            </button>
        </div>
    </div>

    <script>
    (function() {
        const KEY = 'notif_bar_dismissed_v2';
        const bar = document.getElementById('notification-bar');
        const items = Array.from(document.querySelectorAll('.notif-item'));
        const counter = document.getElementById('notif-counter');
        const prevBtn = document.getElementById('notif-prev');
        const nextBtn = document.getElementById('notif-next');
        const closeBtn = document.getElementById('close-notif-bar');
        let current = 0;

        if (!localStorage.getItem(KEY)) {
            bar.classList.remove('hidden');
        }

        function showItem(index, direction) {
            const outgoing = items[current];
            const incoming = items[index];

            // Animate out
            outgoing.style.transition = 'none';
            outgoing.style.transform = 'translateY(0)';
            outgoing.style.opacity = '1';

            requestAnimationFrame(() => {
                outgoing.style.transition = 'all 0.38s cubic-bezier(0.4,0,0.2,1)';
                outgoing.style.transform = direction === 'next' ? 'translateY(-100%)' : 'translateY(100%)';
                outgoing.style.opacity = '0';

                incoming.style.transition = 'none';
                incoming.style.transform = direction === 'next' ? 'translateY(100%)' : 'translateY(-100%)';
                incoming.style.opacity = '0';

                requestAnimationFrame(() => {
                    incoming.style.transition = 'all 0.38s cubic-bezier(0.4,0,0.2,1)';
                    incoming.style.transform = 'translateY(0)';
                    incoming.style.opacity = '1';
                });
            });

            current = index;
            if (counter) counter.textContent = (current + 1) + '/{{ $importantNotifications->count() }}';
        }

        if (nextBtn) nextBtn.addEventListener('click', () => {
            showItem((current + 1) % items.length, 'next');
        });
        if (prevBtn) prevBtn.addEventListener('click', () => {
            showItem((current - 1 + items.length) % items.length, 'prev');
        });

        {{-- Auto-rotate every 5 seconds if more than 1 notification --}}
        @if($importantNotifications->count() > 1)
        setInterval(() => {
            if (!document.hidden) showItem((current + 1) % items.length, 'next');
        }, 5000);
        @endif

        closeBtn.addEventListener('click', () => {
            bar.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            bar.style.opacity = '0';
            bar.style.transform = 'translateY(-8px)';
            setTimeout(() => {
                bar.style.display = 'none';
                localStorage.setItem(KEY, '1');
            }, 300);
        });
    })();
    </script>
@endif


    @if ($carouselEnabled && $carouselSlides->count() > 0)
        <!-- Carousel Section -->
        <section class="relative w-full mb-8">
            <div id="homepage-carousel" class="relative w-full"
                data-carousel="{{ $homepageSettings['carousel_autoplay'] ? 'slide' : 'static' }}"
                @if ($homepageSettings['carousel_autoplay']) data-carousel-interval="{{ $homepageSettings['carousel_interval'] }}" @endif>
                <!-- Carousel wrapper -->
                <div class="relative {{ $homepageSettings['carousel_height'] ?? 'h-96' }} overflow-hidden rounded-lg">
                    @foreach ($carouselSlides as $index => $slide)
                        @php
                            $transitionClass = match ($slide->transition_type ?? 'fade') {
                                'slide' => 'transition-transform duration-700 ease-in-out',
                                'zoom' => 'transition-transform duration-700 ease-in-out scale-100',
                                default => 'transition-opacity duration-700 ease-in-out',
                            };
                            $duration = $slide->transition_duration ?? 700;
                            $overlayOpacity = ($slide->overlay_opacity ?? 50) / 100;
                            $overlayColor = $slide->overlay_color ?? '#000000';
                            $textColor = $slide->text_color ?? '#ffffff';
                            $textPosition = $slide->text_position ?? 'center';
                            $textAlignment = $slide->text_alignment ?? 'center';

                            // Convert hex to rgba
                            $hex = str_replace('#', '', $overlayColor);
                            if (strlen($hex) == 3) {
                                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                            }
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            $overlayStyle = "background-color: rgba({$r}, {$g}, {$b}, {$overlayOpacity});";

                            // Position classes
                            $positionClasses = match ($textPosition) {
                                'top' => 'items-start justify-center pt-8',
                                'bottom' => 'items-end justify-center pb-8',
                                'left' => 'items-center justify-start pl-8',
                                'right' => 'items-center justify-end pr-8',
                                default => 'items-center justify-center',
                            };

                            // Alignment classes
                            $alignmentClasses = match ($textAlignment) {
                                'left' => 'text-left',
                                'right' => 'text-right',
                                default => 'text-center',
                            };

                            // Button style classes
                            $buttonClasses = match ($slide->button_style ?? 'primary') {
                                'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
                                'outline'
                                    => 'bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900',
                                default => 'bg-blue-600 hover:bg-blue-700 text-white',
                            };
                        @endphp
                        <div class="{{ $index === 0 ? '' : 'hidden' }} {{ $transitionClass }}" data-carousel-item
                            style="transition-duration: {{ $duration }}ms;">
                            @if ($slide->image_url)
                                <img src="{{ $slide->image_url }}"
                                    alt="{{ $slide->alt_text ?? ($slide->title ?? 'Slide ' . ($index + 1)) }}"
                                    class="absolute block w-full h-full object-cover">
                            @endif

                            <!-- Custom Overlay -->
                            <div class="absolute inset-0" style="{{ $overlayStyle }}"></div>

                            <!-- Content -->
                            @if ($slide->title || $slide->description || ($slide->link && $slide->link_text) || $slide->logo_url)
                                <div class="absolute inset-0 flex {{ $positionClasses }} p-6 md:p-8">
                                    <div class="max-w-4xl w-full {{ $alignmentClasses }} relative">
                                        @if ($slide->logo_url)
                                            @php
                                                // Logo Position Logic
                                                $logoPos = $slide->logo_position ?? 'top_center';
                                                $scale = ($slide->logo_scale ?? 100) / 100;

                                                // Use flex for container to control alignment reliably
                                                $logoClasses = 'mb-6 flex w-full relative';
                                                $logoStyle = "transform: scale({$scale}); transform-origin: center;";

                                                if (str_contains($logoPos, 'center')) {
                                                    $logoClasses .= ' justify-center';
                                                } elseif (str_contains($logoPos, 'left')) {
                                                    $logoClasses .= ' justify-start';
                                                } elseif (str_contains($logoPos, 'right')) {
                                                    $logoClasses .= ' justify-end';
                                                }

                                                $isBottom = str_contains($logoPos, 'bottom');
                                            @endphp

                                            @if(!$isBottom)
                                                <div class="{{ $logoClasses }}">
                                                    <img src="{{ $slide->logo_url }}" alt="Logo" class="h-16 md:h-24 lg:h-32 object-contain drop-shadow-md" style="{{ $logoStyle }}">
                                                </div>
                                            @endif
                                        @endif

                                        @if ($slide->title)
                                            <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-3 md:mb-4"
                                                style="color: {{ $textColor }}; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                {{ $slide->title }}
                                            </h2>
                                        @endif
                                        @if ($slide->description)
                                            <p class="text-base md:text-lg lg:text-xl mb-4 md:mb-6"
                                                style="color: {{ $textColor }};">
                                                {{ $slide->description }}
                                            </p>
                                        @endif

                                        @if ($slide->logo_url && $isBottom)
                                            <div class="{{ $logoClasses }} mt-6">
                                                <img src="{{ $slide->logo_url }}" alt="Logo" class="h-16 md:h-24 lg:h-32 object-contain drop-shadow-md origin-top" style="{{ $logoStyle }}">
                                            </div>
                                        @endif

                                        @if ($slide->link && $slide->link_text)
                                            <a href="{{ $slide->link }}"
                                                class="inline-block {{ $buttonClasses }} px-6 py-3 rounded-lg font-semibold transition-colors mt-2">
                                                {{ $slide->link_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

                <!-- Slider indicators -->
                @if ($homepageSettings['carousel_indicators'] ?? true)
                    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                        @foreach ($carouselSlides as $index => $slide)
                            @if ($slide->show_indicators !== false)
                                <button type="button"
                                    class="w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50 hover:bg-white' }} transition-colors"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"
                                    data-carousel-slide-to="{{ $index }}"></button>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Slider controls -->
                @if ($homepageSettings['carousel_controls'] ?? true)
                    @php
                        $showControls = true;
                        foreach ($carouselSlides as $slide) {
                            if ($slide->show_controls === false) {
                                $showControls = false;
                                break;
                            }
                        }
                    @endphp
                    @if ($showControls)
                        <button type="button"
                            class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                            data-carousel-prev>
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none transition-colors">
                                <x-icon name="chevron-left" class="w-5 h-5 text-white" />
                                <span class="sr-only">Previous</span>
                            </span>
                        </button>
                        <button type="button"
                            class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                            data-carousel-next>
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none transition-colors">
                                <x-icon name="chevron-right" class="w-5 h-5 text-white" />
                                <span class="sr-only">Next</span>
                            </span>
                        </button>
                    @endif
                @endif
                <!-- Scroll Down Button (Carousel) -->
                @if ($homepageSettings['show_scroll_to_bottom'] ?? false)
                    @php
                        $scrollDownPosClasses = match($homepageSettings['scroll_down_position'] ?? 'center') {
                            'left' => 'left-8',
                            'right' => 'right-8',
                            default => 'left-1/2 transform -translate-x-1/2',
                        };
                        $scrollDownSizeClasses = match($homepageSettings['scroll_down_size'] ?? 'medium') {
                            'small' => 'w-8 h-8',
                            'large' => 'w-12 h-12',
                            default => 'w-10 h-10',
                        };
                    @endphp
                    <div class="absolute bottom-4 {{ $scrollDownPosClasses }} z-30 animate-bounce transition-all duration-300">
                        <a href="#sobre" class="text-white hover:text-blue-400 transition-colors drop-shadow-lg block">
                            <x-icon name="arrow-down-long" class="{{ $scrollDownSizeClasses }}" />
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Hero Section -->
    @if (!($carouselEnabled && $carouselSlides->count() > 0))
    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-900">
        <!-- Background Layer -->
        <div class="absolute inset-0 z-0">
            @if($homepageSettings['hero_bg_image'])
                <img src="{{ asset($homepageSettings['hero_bg_image']) }}" class="w-full h-full object-cover scale-105 animate-slow-zoom" alt="Hero Background">
                <div class="absolute inset-0 bg-linear-to-b from-slate-900/60 via-slate-900/40 to-slate-900/80"></div>
            @else
                <div class="absolute inset-0 bg-linear-to-br from-blue-900 via-indigo-900 to-slate-900"></div>
                <!-- Abstract Background Shapes (Only if no image) -->
                <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl animate-pulse"></div>
                <div class="absolute top-1/2 -left-20 w-72 h-72 rounded-full bg-purple-500/10 blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            @endif

            <!-- Floating Particles/Elements (Always present for premium feel) -->
            <div class="absolute inset-0 opacity-30 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xNSkiLz48L3N2Zz4=')]"></div>
        </div>

        <!-- Animated Shapes -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-400/5 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col items-center text-center">
            <!-- Logo with Glass Effect -->
            <div class="mb-12 relative group">
                <div class="absolute -inset-4 bg-white/5 backdrop-blur-md rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-90 group-hover:scale-110 border border-white/10 shadow-2xl"></div>
                <img src="{{ asset(\App\Models\Settings::get('logo_path', 'logo_oficial.svg')) }}"
                    alt="Logo Oficial"
                    class="relative h-44 md:h-60 w-auto object-contain drop-shadow-[0_0_35px_rgba(255,255,255,0.2)] transform transition-transform duration-1000 group-hover:scale-105"
                    onerror="this.style.display='none';">
            </div>

            <div class="space-y-6 max-w-4xl mx-auto">
                <div class="inline-flex items-center px-5 py-2 rounded-full bg-blue-600/20 border border-blue-400/30 text-amber-400 text-sm font-black tracking-[0.2em] uppercase animate-fade-in-up opacity-0 shadow-[0_0_20px_rgba(251,191,36,0.3)] backdrop-blur-md" style="animation-delay: 0.2s; animation-fill-mode: forwards;">
                    SOMOS UM
                </div>

                <h1 class="text-7xl md:text-8xl lg:text-9xl font-black tracking-tighter text-white leading-none drop-shadow-[0_0_30px_rgba(37,99,235,0.4)]">
                    <span class="block animate-reveal-up opacity-0" style="animation-delay: 0.4s; animation-fill-mode: forwards;">
                        {{ $homepageSettings['hero_title'] }}
                    </span>
                </h1>

                <p class="text-xl md:text-3xl text-blue-100/90 font-medium tracking-wide uppercase leading-relaxed max-w-2xl mx-auto animate-fade-in-up opacity-0 drop-shadow-md" style="animation-delay: 0.8s; animation-fill-mode: forwards;">
                    {{ $homepageSettings['hero_subtitle'] }}
                </p>

                <div class="flex flex-col sm:flex-row gap-5 justify-center pt-8 animate-fade-in-up opacity-0" style="animation-delay: 1s; animation-fill-mode: forwards;">
                    @if ($homepageSettings['hero_button_1_text'])
                        <a href="{{ $homepageSettings['hero_button_1_link'] }}"
                            class="group relative px-10 py-5 bg-white text-slate-900 rounded-full font-black text-lg hover:bg-blue-50 transition-all duration-500 shadow-[0_0_40px_rgba(255,255,255,0.2)] hover:shadow-white/40 transform hover:-translate-y-1 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-3">
                                {{ $homepageSettings['hero_button_1_text'] }}
                                <x-icon name="arrow-right" class="w-6 h-6 transition-transform group-hover:translate-x-2" />
                            </span>
                        </a>
                    @endif

                    @if ($homepageSettings['hero_button_2_text'])
                        <a href="{{ $homepageSettings['hero_button_2_link'] }}"
                            class="px-10 py-5 bg-white/10 backdrop-blur-xl border border-white/20 text-white rounded-full font-black text-lg hover:bg-white/20 transition-all duration-500 transform hover:-translate-y-1 flex items-center justify-center">
                            {{ $homepageSettings['hero_button_2_text'] }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Scroll Down (Premium Indicator) -->
        @if ($homepageSettings['show_scroll_to_bottom'] ?? false)
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-3 animate-bounce-slow">
                <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-white/40">Descobrir</span>
                <div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white/60 hover:text-white hover:border-white/40 transition-all bg-white/5 backdrop-blur-sm">
                    <x-icon name="arrow-down-long" class="w-5 h-5" />
                </div>
            </div>
        @endif

        <!-- Dynamic Bottom Decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-linear-to-t from-slate-900 to-transparent z-10 pointer-events-none"></div>
    </section>

    <!-- Essential Hero Animations -->
    <style>
        @keyframes reveal-up {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes fade-in-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes slow-zoom {
            from { transform: scale(1.05); }
            to { transform: scale(1); }
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translate(-50%, 0); }
            50% { transform: translate(-50%, 15px); }
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0); }
            33% { transform: translate(30px, -50px) rotate(5deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        }
        .animate-reveal-up { animation: reveal-up 1.2s cubic-bezier(0.16, 1, 0.3, 1); }
        .animate-fade-in-up { animation: fade-in-up 1s ease-out; }
        .animate-slow-zoom { animation: slow-zoom 15s ease-out forwards; }
        .animate-bounce-slow { animation: bounce-slow 3s infinite ease-in-out; }
        .animate-float { animation: float 15s infinite ease-in-out; }
    </style>
    @endif

    <!-- About Section -->
    <section id="sobre" class="py-24 bg-gray-50 dark:bg-gray-950 transition-colors duration-200 relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 dark:bg-blue-900/10 blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-purple-100 dark:bg-purple-900/10 blur-3xl opacity-50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20">
                <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm mb-2 block">Bem-vindo</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6">
                    {{ $homepageSettings['about_title'] }}
                </h2>
                <div class="w-16 h-1 bg-linear-to-r from-blue-600 to-indigo-600 mx-auto rounded-full"></div>
                @if ($homepageSettings['about_description'])
                    <p class="text-gray-600 dark:text-gray-400 mt-6 max-w-3xl mx-auto text-lg leading-relaxed">
                        {{ $homepageSettings['about_description'] }}</p>
                @endif
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-start">
                <div class="space-y-12">
                     <!-- Mission -->
                    <div class="group">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mr-5 group-hover:scale-110 transition-transform duration-300">
                                <x-icon name="bolt-lightning" class="w-6 h-6" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Nossa Visão</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-lg pl-16 border-l-2 border-blue-100 dark:border-gray-800">
                             Despertar e conectar a juventude da região para viverem intensamente o propósito do Reino de Deus em nossa geração.
                        </p>
                    </div>

                     <!-- Identity -->
                    <div class="group">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mr-5 group-hover:scale-110 transition-transform duration-300">
                                <x-icon name="users" style="duotone" class="w-6 h-6" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Nossa Identidade</h3>
                        </div>
                         <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-lg pl-16 border-l-2 border-amber-100 dark:border-gray-800">
                            Uma associação vibrante, focada na união, amizade e no fortalecimento espiritual de jovens comprometidos com Cristo.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-8 pl-16">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 text-center transform hover:-translate-y-1 transition-transform">
                            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-indigo-600 mb-2">
                                {{ number_format($statistics['members']) }}
                            </div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Membros</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 text-center transform hover:-translate-y-1 transition-transform">
                            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-linear-to-r from-amber-500 to-orange-500 mb-2">
                                {{ $statistics['years'] }}
                            </div>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Anos</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 bg-linear-to-tr from-blue-600 to-amber-500 rounded-3xl transform rotate-3 opacity-20 blur-lg"></div>
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-10 shadow-2xl relative border border-gray-100 dark:border-gray-700">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 flex items-center">
                            <span class="w-2 h-8 bg-blue-500 rounded-full mr-4"></span>
                            Nossos Valores
                        </h3>
                        <ul class="space-y-6">
                            @foreach([
                                ['icon' => 'book-bible', 'text' => 'Fidelidade às Escrituras', 'desc' => 'A Bíblia como única regra de fé e conduta'],
                                ['icon' => 'heart', 'text' => 'Comunhão', 'desc' => 'Vivendo em amor e unidade'],
                                ['icon' => 'globe', 'text' => 'Evangelização', 'desc' => 'Compartilhando o amor de Cristo'],
                                ['icon' => 'hand-holding-heart', 'text' => 'Serviço', 'desc' => 'Comprometidos com a comunidade']
                            ] as $item)
                                <li class="flex items-start group">
                                    <div class="shrink-0 w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center mt-1 mr-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                        <x-icon :name="$item['icon']" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $item['text'] }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['desc'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Daily Verse Section (scripture / parchment aesthetic) -->
    @if ($homepageSettings['show_daily_verse'] && $dailyVerse)
        <section class="py-20 bg-amber-50/90 dark:bg-stone-900/95 transition-colors duration-200 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="text-amber-700 dark:text-amber-400 font-bold uppercase tracking-wider text-sm mb-2 block">Palavra</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-stone-900 dark:text-stone-100 mb-4">
                        {{ $homepageSettings['daily_verse_title'] }}
                    </h2>
                    <div class="w-16 h-1 bg-amber-600/80 dark:bg-amber-500/70 mx-auto rounded-full"></div>
                </div>

                <div class="max-w-4xl mx-auto">
                    <div class="relative bg-amber-50/95 dark:bg-stone-800/95 rounded-2xl p-8 md:p-12 border-2 border-amber-200/80 dark:border-amber-900/50 shadow-xl">
                        <!-- Bible Icon -->
                        <div class="flex justify-center mb-8">
                            <div class="rounded-full p-3 bg-amber-100/90 dark:bg-amber-900/30 border border-amber-200/60 dark:border-amber-800/40">
                                <x-icon name="book-bible" style="duotone" class="w-10 h-10 text-amber-800 dark:text-amber-300" />
                            </div>
                        </div>

                        <!-- Verse Text (serif, book-like) -->
                        <blockquote class="font-serif text-xl md:text-2xl text-stone-800 dark:text-stone-200 font-medium mb-6 italic text-center leading-relaxed">
                            "{{ $dailyVerse['text'] }}"
                        </blockquote>

                        <!-- Reference -->
                        <cite class="block text-lg text-amber-800 dark:text-amber-200 font-semibold not-italic text-center">
                            {{ $dailyVerse['reference'] }}
                        </cite>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
                            <a href="{{ route('verse.context', [
                                'book_id' => $dailyVerse['book_id'] ?? 1,
                                'chapter' => $dailyVerse['chapter'] ?? 1,
                                'verse' => $dailyVerse['verse'] ?? 1
                            ]) }}"
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-amber-600 dark:border-amber-500 text-amber-800 dark:text-amber-200 font-semibold rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                                <x-icon name="circle-info" style="duotone" class="w-5 h-5 mr-2" />
                                Ver Contexto
                            </a>
                            <a href="{{ route('bible.public.index') }}"
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-stone-400 dark:border-stone-500 text-stone-700 dark:text-stone-300 font-semibold rounded-lg hover:bg-stone-100 dark:hover:bg-stone-700/50 transition-colors">
                                <x-icon name="book-open" style="duotone" class="w-5 h-5 mr-2" />
                                Bíblia Online
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Ministries Section -->
    @if ($homepageSettings['show_ministries'] && $activeMinistries->count() > 0)
        <section id="ministerios" class="py-24 bg-white dark:bg-gray-900 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm mb-2 block">Crescimento & Serviço</span>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">
                        {{ $homepageSettings['ministries_title'] }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-lg">
                        Cada membro tem um lugar especial para servir e crescer espiritualmente
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($activeMinistries as $ministry)
                        @php
                            $colors = ['blue','emerald','indigo','violet','rose','cyan'];
                            $colorIndex = $loop->index % count($colors);
                            $color = $colors[$colorIndex];
                        @endphp
                        <div class="group bg-gray-50 dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                            <div class="h-32 bg-linear-to-r from-{{ $color }}-500 to-{{ $color }}-600 relative overflow-hidden">
                                <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4yKSIvPjwvc3ZnPg==')]"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                     @if ($ministry->icon)
                                        <div class="w-16 h-16 text-white opacity-90 drop-shadow-lg scale-100 group-hover:scale-110 transition-transform duration-300">
                                            {!! $ministry->icon !!}
                                        </div>
                                    @else
                                        <x-icon name="church-alt" class="w-16 h-16 text-white opacity-90 drop-shadow-lg scale-100 group-hover:scale-110 transition-transform duration-300" />
                                    @endif
                                </div>
                            </div>

                            <div class="p-8 relative">
                                <div class="absolute -top-10 right-6 bg-white dark:bg-gray-700 shadow-md rounded-full px-4 py-1 text-xs font-bold text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                                    {{ $ministry->active_members_count }} membros
                                </div>

                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-{{ $color }}-600 transition-colors">{{ $ministry->name }}</h3>

                                @if ($ministry->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-3 leading-relaxed">
                                        {{ Str::limit($ministry->description, 120) }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-4">
                                    @if ($ministry->leader)
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-medium mr-1">Líder:</span> {{ $ministry->leader->name }}
                                        </div>
                                    @endif
                                    <a href="#" class="text-{{ $color }}-600 hover:text-{{ $color }}-700 font-semibold text-sm flex items-center">
                                        Saiba mais <x-icon name="chevron-right" style="duotone" class="w-4 h-4 ml-1" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (Route::has('ministries.index'))
                    <div class="text-center mt-16">
                        <a href="{{ route('ministries.index') }}"
                            class="inline-flex items-center px-8 py-4 bg-gray-900 dark:bg-gray-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-300 transform hover:-translate-y-1">
                            <span>Explorar Todos Ministérios</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Events Section -->
    @if ($homepageSettings['show_events'] && $upcomingEvents->count() > 0)
        <section id="eventos" class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                    <div class="text-center md:text-left">
                        <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm mb-2 block">Agenda</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
                            {{ $homepageSettings['events_title'] }}
                        </h2>
                    </div>
                     @if ($upcomingEvents->count() > 3)
                        <div class="hidden md:block">
                            <a href="{{ route('events.public.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold flex items-center">
                                Ver calendário completo <x-icon name="arrow-right" class="w-4 h-4 ml-1" />
                            </a>
                        </div>
                    @endif
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @foreach ($upcomingEvents->take(3) as $event)
                        @php
                            $colors = ['blue', 'indigo', 'purple'];
                            $colorIndex = $loop->index % count($colors);
                            $color = $colors[$colorIndex];
                            $month = strtoupper(substr($event->start_date->translatedFormat('F'), 0, 3));
                            $day = $event->start_date->format('d');
                        @endphp
                        <a href="{{ route('events.public.show', $event->slug) }}" class="block group bg-white dark:bg-gray-900 rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900" aria-label="{{ __('events::messages.details_and_register') ?? 'Ver detalhes e inscrever-se' }}: {{ $event->title }}">
                            <!-- Date Badge -->
                            <div class="p-6 pb-0 flex items-start justify-between">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-3 text-center min-w-16">
                                    <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ $month }}</div>
                                    <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $day }}</div>
                                </div>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full uppercase tracking-wider">
                                    {{ $event->formatted_time }}
                                </span>
                            </div>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                                    {{ $event->title }}
                                </h3>
                                @if ($event->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 text-sm leading-relaxed">
                                        {{ Str::limit($event->description, 100) }}
                                    </p>
                                @endif

                                <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <x-icon name="location-dot" style="duotone" class="w-4 h-4 mr-2" />
                                    {{ $event->location ?? 'JUBAF - Juventude Batista Feirense' }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($upcomingEvents->count() > 3)
                    <div class="mt-8 text-center md:hidden">
                        <a href="{{ route('events.public.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-colors">Ver Todos os Eventos</a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Mural JUBAF Section -->
    @if(isset($ultimasNoticias) && $ultimasNoticias->count() > 0)
        <section class="bg-gray-50 dark:bg-gray-950 transition-colors duration-200 py-24">
            <div class="px-4 mx-auto max-w-7xl">
                <div class="text-center mb-16">
                    <span class="text-amber-500 dark:text-amber-400 font-bold uppercase tracking-widest text-sm mb-2 block">Novidades da JUBAF</span>
                    <h2 class="text-3xl lg:text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white mb-4">Mural da Associação</h2>
                    <div class="w-16 h-1 bg-linear-to-r from-blue-600 to-amber-500 mx-auto rounded-full mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Últimos comunicados, avisos e notícias para todas as juventudes conectadas.</p>
                </div>
                
                <div class="grid gap-8 lg:grid-cols-3">
                    @foreach($ultimasNoticias as $noticia)
                        <article class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
                            <div class="flex justify-between items-center mb-5 text-gray-500">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold inline-flex items-center px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300 uppercase tracking-widest">
                                    <x-icon name="newspaper" class="mr-1 w-3 h-3" /> Comunicado
                                </span>
                                <span class="text-sm font-medium">{{ $noticia->created_at->diffForHumans() }}</span>
                            </div>
                            <h2 class="mb-3 text-xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $noticia->titulo }}
                            </h2>
                            <p class="mb-5 text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($noticia->conteudo), 120) }}
                            </p>
                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 mt-auto">
                                <a href="{{ route('login') }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700">
                                    Ler notícia completa <x-icon name="chevron-right" style="duotone" class="w-4 h-4 ml-1" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Campaigns Section -->
    @if ($activeCampaigns->count() > 0)
        <section id="campanhas"
            class="py-20 bg-linear-to-br from-green-50 via-blue-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-900 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Campanhas em Andamento
                    </h2>
                    <div class="w-24 h-1 bg-linear-to-r from-green-500 via-blue-500 to-purple-500 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Participe das nossas campanhas e ajude a fazer a diferença na vida de muitas pessoas
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($activeCampaigns as $campaign)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 dark:border-gray-700">
                            <!-- Campaign Image -->
                            @if ($campaign->image)
                                <div class="relative h-48 overflow-hidden">
                                    <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}"
                                        class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-4 left-4 right-4">
                                        <h3 class="text-xl font-bold text-white mb-1">{{ $campaign->name }}</h3>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="relative h-48 bg-linear-to-br from-green-500 via-blue-500 to-purple-500 flex items-center justify-center">
                                    <div class="text-center p-6">
                                        <x-icon name="hand-holding-dollar" class="w-16 h-16 text-white mx-auto mb-3" />
                                        <h3 class="text-xl font-bold text-white">{{ $campaign->name }}</h3>
                                    </div>
                                </div>
                            @endif

                            <div class="p-6">
                                <!-- Description -->
                                @if ($campaign->description)
                                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                        {{ Str::limit($campaign->description, 120) }}
                                    </p>
                                @endif

                                <!-- Progress Bar -->
                                @if ($campaign->target_amount)
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300">Progresso</span>
                                            <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                                {{ number_format($campaign->progress_percentage, 1) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                            <div class="bg-linear-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                                                style="width: {{ min(100, $campaign->progress_percentage) }}%">
                                                @if ($campaign->progress_percentage > 20)
                                                    <span
                                                        class="text-xs font-semibold text-white">{{ number_format($campaign->progress_percentage, 0) }}%</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span>R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                                            <span>Meta: R$
                                                {{ number_format($campaign->target_amount, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300">Arrecadado</span>
                                            <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                                R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Dates -->
                                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    @if ($campaign->start_date)
                                        <div class="flex items-center gap-1">
                                            <x-icon name="calendar-days" style="duotone" class="w-4 h-4" />
                                            <span>Início: {{ $campaign->start_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($campaign->end_date)
                                        <div class="flex items-center gap-1">
                                            <x-icon name="calendar-days" style="duotone" class="w-4 h-4" />
                                            <span>Fim: {{ $campaign->end_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Donate Button -->
                                @if ($hasActiveGateways)
                                    <a href="{{ route('donation.create', ['campaign' => $campaign->id]) }}"
                                        class="w-full bg-linear-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <x-icon name="hand-holding-dollar" class="w-5 h-5" />
                                        Fazer Doação
                                    </a>
                                @else
                                    <button disabled
                                        class="w-full bg-gray-400 dark:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 opacity-60">
                                        <x-icon name="hand-holding-dollar" class="w-5 h-5" />
                                        Doação Indisponível
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- View All Campaigns Link -->
                @if ($hasActiveGateways)
                    <div class="text-center mt-12">
                        <a href="{{ route('donation.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all border-2 border-gray-300 dark:border-gray-600 hover:border-green-500 dark:hover:border-green-500">
                            <span>Ver Todas as Campanhas</span>
                            <x-icon name="chevron-right" style="duotone" class="w-5 h-5 ml-2" />
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Loja Missionária (Marketplace) – CTA para página completa -->
    @if(($homepageSettings['show_marketplace'] ?? false) && Route::has('marketplace.storefront.index'))
        <section id="loja" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 transition-colors duration-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 mb-6">
                    <x-icon name="hand-holding-heart" style="duotone" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm block mb-2">{{ __('marketplace::messages.store') }}</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    {{ $homepageSettings['marketplace_title'] ?? __('marketplace::messages.name') }}
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-8">
                    Apoie as campanhas da igreja comprando na nossa loja missionária. Sua compra destina-se a causas que fazem a diferença.
                </p>
                <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                    <x-icon name="store" style="duotone" class="w-6 h-6 mr-2" /> Ver Loja Completa
                </a>
                <p class="mt-4">
                    <a href="{{ route('marketplace.storefront.index') }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">Apoiar Missões</a>
                </p>
            </div>
        </section>
    @endif

    <!-- Testimonials Section -->
    @if ($homepageSettings['show_testimonials'] && $activeTestimonials->count() > 0)
        <section
            class="py-20 bg-linear-to-br from-gray-50 via-white to-gray-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ $homepageSettings['testimonials_title'] }}</h2>
                    <div class="w-24 h-1 bg-linear-to-r from-blue-500 via-purple-500 to-pink-500 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Veja o que nossos membros têm a dizer sobre sua experiência na igreja
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @foreach ($activeTestimonials as $testimonial)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                            <!-- Quote Icon -->
                            <div class="flex justify-center mb-6">
                                <div class="bg-linear-to-br from-blue-500 to-purple-600 rounded-full p-3">
                                    <x-icon name="quote-left" class="w-8 h-8 text-white" />
                                </div>
                            </div>

                            <!-- Testimonial Text -->
                            <blockquote class="text-gray-700 dark:text-gray-300 text-center mb-6 italic leading-relaxed">
                                "{{ Str::limit($testimonial->testimonial, 200) }}"
                            </blockquote>

                            <!-- Author Info -->
                            <div class="text-center">
                                @if ($testimonial->photo)
                                    <div
                                        class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border-4 border-blue-100 dark:border-blue-900">
                                        <img src="{{ Storage::url($testimonial->photo) }}"
                                            alt="{{ $testimonial->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div
                                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span
                                            class="text-white font-bold text-xl">{{ substr($testimonial->name, 0, 1) }}</span>
                                    </div>
                                @endif

                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $testimonial->name }}</h4>
                                @if ($testimonial->position)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $testimonial->position }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Ver Todos os Testemunhos -->
                <div class="text-center mt-12">
                    <a href="{{ route('testimonials.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-linear-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
                    <span>Ver Todos os Testemunhos</span>
                    <x-icon name="chevron-right" style="duotone" class="w-5 h-5 ml-2" />
                </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Gallery Section -->
    @if ($homepageSettings['show_gallery'] && $galleryImages->count() > 0)
        <section class="py-20 bg-white dark:bg-gray-900 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ $homepageSettings['gallery_title'] }}</h2>
                    <div class="w-24 h-1 bg-linear-to-r from-green-500 via-blue-500 to-purple-500 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Momentos especiais da nossa comunidade
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($galleryImages->take(8) as $image)
                        <div
                            class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <div class="aspect-square overflow-hidden">
                                <img src="{{ $image->image_url }}" alt="{{ $image->title ?: 'Imagem da galeria' }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            </div>
                            @if ($image->title || $image->description)
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-70 transition-all duration-300 flex items-center justify-center">
                                    <div
                                        class="text-white text-center p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        @if ($image->title)
                                            <h4 class="font-bold text-lg mb-2">{{ $image->title }}</h4>
                                        @endif
                                        @if ($image->description)
                                            <p class="text-sm">{{ Str::limit($image->description, 100) }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Ver Toda a Galeria -->
                <div class="text-center mt-12">
                <a href="{{ route('gallery.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-linear-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
                    <span>Ver Toda a Galeria</span>
                    <x-icon name="chevron-right" style="duotone" class="w-5 h-5 ml-2" />
                </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Statistics Section -->
    @if ($homepageSettings['show_statistics'])
        <section class="py-20 bg-linear-to-br from-blue-600 via-purple-600 to-blue-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $homepageSettings['statistics_title'] }}</h2>
                    <div class="w-24 h-1 bg-white mx-auto mb-4"></div>
                    <p class="text-blue-100 max-w-2xl mx-auto">
                        Números que refletem o crescimento e o impacto da nossa comunidade
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <!-- Members -->
                    <div class="text-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <x-icon name="people-group" class="w-16 h-16 text-white mx-auto mb-4" />
                            <div class="text-4xl font-bold mb-2">{{ number_format($statistics['members']) }}</div>
                            <div class="text-blue-100">Membros Ativos</div>
                        </div>
                    </div>

                    <!-- Ministries -->
                    <div class="text-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <x-icon name="church-alt" class="w-16 h-16 text-white mx-auto mb-4" />
                            <div class="text-4xl font-bold mb-2">{{ $statistics['ministries'] }}</div>
                            <div class="text-blue-100">Igrejas Associadas</div>
                        </div>
                    </div>

                    <!-- Campaigns -->
                    <div class="text-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <x-icon name="hand-holding-dollar" class="w-16 h-16 text-white mx-auto mb-4" />
                            <div class="text-4xl font-bold mb-2">{{ $statistics['campaigns'] }}</div>
                            <div class="text-blue-100">Campanhas</div>
                        </div>
                    </div>

                    <!-- Years -->
                    <div class="text-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <x-icon name="calendar-days" style="duotone" class="w-16 h-16 text-white mx-auto mb-4" />
                            <div class="text-4xl font-bold mb-2">{{ $statistics['years'] }}</div>
                            <div class="text-blue-100">Anos de História</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Newsletter Section -->
    @if ($homepageSettings['show_newsletter'])
    <!-- Newsletter Section -->
    @if ($homepageSettings['show_newsletter'])
        <section class="py-20 bg-blue-600 dark:bg-blue-900 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] opacity-30"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <x-icon name="envelope-open-text" class="w-16 h-16 mx-auto mb-6 text-white/80" />

                <h2 class="text-3xl md:text-5xl font-extrabold mb-6 tracking-tight">{{ $homepageSettings['newsletter_title'] }}</h2>
                <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
                    Receba nossas novidades, estudos bíblicos e informações sobre eventos diretamente no seu e-mail.
                </p>

                <form class="max-w-xl mx-auto" id="newsletter-form">
                    <div class="flex flex-col sm:flex-row gap-4 p-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                        <input type="email" placeholder="Seu melhor e-mail"
                            class="flex-1 px-6 py-4 rounded-xl bg-white/10 border-none text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-white/50"
                            required>
                        <button type="submit"
                            class="px-8 py-4 bg-white text-blue-600 hover:bg-blue-50 font-bold rounded-xl transition-colors shadow-lg">
                            Inscrever-se
                        </button>
                    </div>
                    <p class="text-xs text-blue-200 mt-4">
                        Respeitamos sua privacidade. Você pode cancelar a inscrição a qualquer momento.
                    </p>
                </form>
            </div>
        </section>
    @endif
    @endif

    <!-- Contact Section -->
    <section id="contato" class="py-24 bg-gray-50 dark:bg-gray-950 transition-colors duration-200 relative overflow-hidden">
        <div class="absolute inset-0 dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-20"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-linear-to-l from-blue-100/50 to-transparent dark:from-blue-900/20 dark:to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Contact Info -->
                <div class="space-y-10">
                    <div>
                        <span class="text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm mb-2 block">Fale Conosco</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6">Estamos aqui para servir você</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed max-w-xl">
                            Entre em contato conosco para acompanhamento pastoral, aconselhamento ou para saber mais sobre nossa comunidade.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition-all">
                            <div class="bg-blue-100 dark:bg-blue-600 rounded-xl p-3 mr-5 dark:shadow-lg dark:shadow-blue-900/50 shrink-0">
                                <x-icon name="location-dot" style="duotone" class="w-6 h-6 text-blue-600 dark:text-white" />
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Visite-nos</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $homepageSettings['contact_address'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition-all">
                            <div class="bg-blue-100 dark:bg-blue-600 rounded-xl p-3 mr-5 dark:shadow-lg dark:shadow-blue-900/50 shrink-0">
                                <x-icon name="phone-volume" class="w-6 h-6 text-blue-600 dark:text-white" />
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Ligue-nos</h4>
                                <p class="text-gray-600 dark:text-gray-400 font-mono">{{ $homepageSettings['contact_phone'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition-all">
                            <div class="bg-blue-100 dark:bg-blue-600 rounded-xl p-3 mr-5 dark:shadow-lg dark:shadow-blue-900/50 shrink-0">
                                <x-icon name="envelope-dot" class="w-6 h-6 text-blue-600 dark:text-white" />
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Escreva-nos</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $homepageSettings['contact_email'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 md:p-10 shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Envie uma Mensagem</h3>
                    <form class="space-y-5" id="contact-form">
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome</label>
                                <input type="text" id="name" name="name" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="Seu nome">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Telefone</label>
                                <input type="tel" id="phone" name="phone" data-mask="phone" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="(__) _____-____">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">E-mail</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="seu@email.com">
                        </div>

                        <!-- Endereço com CEP (Colapsável ou mais discreto) -->
                         <div class="pt-2">
                             <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Endereço (Opcional)</h4>
                             @include('homepage::components.address-fields', [
                                 'prefix' => 'contact',
                                 'required' => false,
                                 'showLabels' => true,
                                 'class' => 'grid md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300',
                             ])
                         </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mensagem</label>
                            <textarea id="message" name="message" rows="4" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="Como podemos ajudar?"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            Enviar Mensagem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter & Contact JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Newsletter Form
            const newsletterForm = document.getElementById('newsletter-form');
            if (newsletterForm) {
                handleAjaxForm(newsletterForm, '{{ route('newsletter.subscribe') }}', 'newsletter-message');
            }

            // Contact Form
            const contactForm = document.getElementById('contact-form');
            if (contactForm) {
                handleAjaxForm(contactForm, '{{ route('contact.send') }}', 'contact-message');
            }

            function handleAjaxForm(form, url, messageClass) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;

                    // Disable button and show loading
                    submitButton.disabled = true;
                    submitButton.textContent = 'Enviando...';

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showMessage(form, data.message, 'success', messageClass);
                                form.reset();
                            } else {
                                showMessage(form, data.message, 'error', messageClass);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(form, 'Ocorreu um erro ao processar sua solicitação.', 'error', messageClass);
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                        });
                });
            }

            function showMessage(form, message, type, messageClass) {
                // Remove existing message
                const existingMessage = document.querySelector('.' + messageClass);
                if (existingMessage) {
                    existingMessage.remove();
                }

                // Create new message
                const messageDiv = document.createElement('div');
                messageDiv.className = `${messageClass} p-4 rounded-lg mb-4 text-center ${
                    type === 'success'
                        ? 'bg-green-100 text-green-800 border border-green-200'
                        : 'bg-red-100 text-red-800 border border-red-200'
                }`;
                messageDiv.textContent = message;

                // Insert before form content (inside the form or before it)
                form.insertBefore(messageDiv, form.firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 5000);
            }
        });
    </script>

    <!-- Homepage Settings for JavaScript -->
    <script>
        window.homepageSettings = @json($homepageSettings);
    </script>



@endsection
