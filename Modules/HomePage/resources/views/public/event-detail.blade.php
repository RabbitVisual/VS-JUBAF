@extends('homepage::components.layouts.master')

@push('meta')
<meta property="og:title" content="{{ $event->title }}">
<meta property="og:description" content="{{ Str::limit($event->description ?? 'Participe deste evento especial da Igreja Batista Avenida', 160) }}">
<meta property="og:type" content="event">
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[50vh] flex items-center justify-center overflow-hidden">
        <!-- Parallax/Static Background -->
        <div class="absolute inset-0 bg-gray-900 z-0">
            @php
                // Use event image if available (future proofing), else standard gradient
                $bgClass = 'bg-linear-to-br from-blue-900 via-indigo-900 to-gray-900';
            @endphp
            <div class="{{ $bgClass }} w-full h-full opacity-90"></div>
            <!-- Pattern -->
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <a href="{{ route('events.public.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors mb-6 font-medium tracking-wide bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white/20">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar para Eventos
            </a>

            <h1 class="text-4xl md:text-7xl font-black text-white mb-6 tracking-tight leading-tight">
                {{ $event->title }}
            </h1>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 text-lg sm:text-2xl text-blue-100 font-light">
                <div class="flex items-center backdrop-blur-md bg-white/5 px-6 py-3 rounded-2xl border border-white/10">
                    <x-icon name="calendar-days" style="duotone" class="w-6 h-6 mr-3 text-blue-400" />
                    <span class="font-semibold mr-2">{{ $event->formatted_date }}</span>
                    @if($event->formatted_time)
                        <span>• {{ $event->formatted_time }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Decorative Bottom Curve -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
            <svg class="relative block w-full h-12 md:h-24 text-white dark:text-gray-950 fill-current" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"></path>
            </svg>
        </div>
    </section>

    <!-- Content Section -->
    <section class="pb-20 pt-10 bg-white dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12 -mt-20 relative z-20">

                <!-- Main Content (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 border border-gray-100 dark:border-gray-800 p-8 md:p-12">
                        @if($event->description)
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-l-4 border-blue-500 pl-4">Sobre o Evento</h2>
                            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed font-sans">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-10">Descrição não disponível para este evento.</p>
                        @endif

                        <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Compartilhe</h3>
                            <div class="flex flex-wrap gap-4">
                                <button onclick="shareOnFacebook()"
                                    class="flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <x-icon name="facebook" style="brands" class="w-5 h-5 mr-3" />
                                    Facebook
                                </button>

                                <button onclick="shareOnWhatsApp()"
                                    class="flex items-center px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <x-icon name="whatsapp" style="brands" class="w-5 h-5 mr-3" />
                                    WhatsApp
                                </button>

                                <button onclick="copyEventLink()"
                                    class="flex items-center px-5 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                    <x-icon name="link" style="duotone" class="w-5 h-5 mr-3" />
                                    Copiar Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Info Card -->
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800 p-6 sticky top-24">
                        <h3 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6">Detalhes</h3>

                        <ul class="space-y-6">
                            <li class="flex items-start">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="clock" style="duotone" class="w-5 h-5" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Quando</p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $event->start_date->translatedFormat('d \d\e F') }}</p>
                                    @if($event->formatted_time)
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $event->formatted_time }}</p>
                                    @endif
                                </div>
                            </li>

                            @if($event->location)
                            <li class="flex items-start">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="location-dot" style="duotone" class="w-5 h-5" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Onde</p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $event->location }}</p>
                                </div>
                            </li>
                            @endif
                        </ul>

                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 space-y-3">
                            @php
                                $eventSlug = $event->slug ?? \Illuminate\Support\Str::slug($event->title);
                            @endphp
                            <a href="{{ route('events.public.landing', $eventSlug) }}?openRegistration=1" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-colors">
                                <x-icon name="circle-check" style="duotone" class="w-5 h-5" />
                                {{ __('events::messages.register') ?? 'Inscreva-se' }}
                            </a>
                            <button onclick="window.print()" class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 transition-colors">
                                <x-icon name="print" style="duotone" class="w-4 h-4 mr-2" />
                                Imprimir Detalhes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent("{{ $event->title }} - Igreja Batista Avenida");
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent("{{ $event->title }} - {{ $event->formatted_date }} {{ $event->formatted_time ? 'às ' . $event->formatted_time : '' }}\n\n{{ route('events.public.show', $event) }}");
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function copyEventLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;

                button.innerHTML = `
                    <x-icon name="check" style="duotone" class="w-5 h-5 mr-3 text-green-500" />
                    Copiado!
                `;
                button.classList.add('bg-green-50', 'text-green-700', 'dark:bg-green-900/20', 'dark:text-green-400');

                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('bg-green-50', 'text-green-700', 'dark:bg-green-900/20', 'dark:text-green-400');
                }, 2000);
            });
        }
    </script>
@endsection

