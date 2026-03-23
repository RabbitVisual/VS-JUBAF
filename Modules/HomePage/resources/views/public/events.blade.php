@extends('homepage::components.layouts.master')

@section('title', 'Eventos - Igreja Batista Avenida')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[40vh] flex items-center justify-center overflow-hidden">
        <!-- Background with Overlay -->
        <div class="absolute inset-0 bg-linear-to-br from-blue-900 to-gray-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>
        <div class="absolute inset-0 bg-linear-to-t from-gray-900 via-transparent to-transparent z-10"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl translate-x-1/2 translate-y-1/2 z-0 animate-pulse delay-1000"></div>

        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-sm font-semibold tracking-wider uppercase mb-4 backdrop-blur-sm">
                Vida na Igreja
            </span>
            <h1 class="text-4xl md:text-7xl font-bold text-white mb-6 tracking-tight">
                Nossos <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-purple-400">Eventos</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto mb-10 leading-relaxed font-light">
                Participe conosco dos nossos cultos, reuniões e atividades especiais. Há sempre um lugar para você.
            </p>

            <!-- Modern Search Form -->
            <div class="max-w-2xl mx-auto">
                <form method="GET" action="{{ route('events.public.index') }}" class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-r from-blue-600 to-purple-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                    <div class="relative flex items-center bg-white/10 dark:bg-gray-900/50 backdrop-blur-md border border-white/20 dark:border-gray-700/50 rounded-xl p-2 shadow-2xl">
                        <x-icon name="magnifying-glass" style="duotone" class="w-6 h-6 text-gray-400 ml-3" />
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar eventos por nome..."
                            class="w-full bg-transparent border-none text-white placeholder-gray-400 focus:ring-0 px-4 py-2 text-lg">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg shadow-blue-500/30">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Events Grid Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @if($events->count() > 0)
                <!-- Results Info -->
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Próximos Eventos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 px-4 py-2 rounded-full shadow-sm border border-gray-100 dark:border-gray-800">
                        @if(request('search'))
                            Encontrados <span class="font-bold text-blue-600 dark:text-blue-400">{{ $events->total() }}</span> resultado(s)
                        @else
                            Mostrando <span class="font-bold text-blue-600 dark:text-blue-400">{{ $events->count() }}</span> de {{ $events->total() }} eventos
                        @endif
                    </p>
                </div>

                <!-- Events Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    @foreach($events as $event)
                        @php
                            $colors = ['blue', 'indigo', 'violet', 'fuchsia', 'cyan'];
                            $colorIndex = $loop->index % count($colors);
                            $color = $colors[$colorIndex];
                            // Map for Tailwind classes safelist equivalent logic
                            $gradientFrom = 'from-' . $color . '-500';
                            $gradientTo = 'to-' . $color . '-600';
                            $bgLight = 'bg-' . $color . '-50';
                            $textDark = 'text-' . $color . '-700';

                            $dayOfWeek = strtoupper(substr($event->start_date->translatedFormat('l'), 0, 3));
                            $dayNumber = $event->start_date->format('d');
                            $month = strtoupper($event->start_date->translatedFormat('M'));
                        @endphp

                        <article class="group relative flex flex-col h-full bg-white dark:bg-gray-900 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 border border-gray-100 dark:border-gray-800 overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                            <!-- Card Header / Date -->
                            <div class="relative h-48 overflow-hidden">
                                <div class="absolute inset-0 bg-linear-to-br {{ $gradientFrom }} {{ $gradientTo }} opacity-90 transition-transform duration-500 group-hover:scale-110"></div>
                                <!-- Decorative Circles -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-xl"></div>

                                <div class="relative z-10 h-full flex flex-col justify-center items-center text-white p-6 text-center">
                                    <span class="text-sm font-semibold tracking-widest uppercase opacity-80 mb-1">{{ $dayOfWeek }}</span>
                                    <span class="text-6xl font-black tracking-tighter leading-none">{{ $dayNumber }}</span>
                                    <span class="text-lg font-bold tracking-widest uppercase mt-1 opacity-90">{{ $month }}</span>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="flex-1 p-8 flex flex-col">
                                <div class="flex items-center text-xs font-semibold text-{{ $color }}-600 uppercase tracking-wider mb-3">
                                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500 mr-2 animate-pulse"></span>
                                    {{ $event->formatted_time ?: 'Horário a definir' }}
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    <a href="{{ route('events.public.show', $event) }}" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        {{ $event->title }}
                                    </a>
                                </h3>

                                @if($event->location)
                                    <div class="flex items-start text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        <x-icon name="location-dot" style="duotone" class="w-5 h-5 mr-1.5 shrink-0 text-gray-400" />
                                        <span class="line-clamp-1">{{ $event->location }}</span>
                                    </div>
                                @endif

                                @if($event->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-6 flex-1 leading-relaxed">
                                        {{ Str::limit($event->description, 100) }}
                                    </p>
                                @endif

                                <div class="mt-auto pt-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500">
                                        {{ $event->start_date->year }}
                                    </span>
                                    <span class="inline-flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                                        Saiba mais
                                        <x-icon name="arrow-right" style="duotone" class="w-4 h-4 ml-1" />
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Events State -->
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="relative w-40 h-40 mb-8">
                        <div class="absolute inset-0 bg-blue-100 dark:bg-blue-900/30 rounded-full animate-ping opacity-20 delay-300"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-full p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                            <x-icon name="calendar-days" style="duotone" class="w-full h-full text-blue-500/50" />
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Nenhum evento encontrado</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        @if(request('search'))
                            Não encontramos nada relacionado a "<span class="text-blue-600 font-semibold">{{ request('search') }}</span>". Tente outro termo.
                        @else
                            Estamos planejando grandes coisas! Volte em breve para conferir nossa agenda.
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('events.public.index') }}"
                            class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg shadow-blue-500/30 font-semibold transition-all hover:-translate-y-1">
                            Ver Todos os Eventos
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection

