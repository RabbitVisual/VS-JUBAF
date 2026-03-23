@extends('homepage::components.layouts.master')

@section('title', __('events::messages.events'))

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[40vh] flex items-center justify-center overflow-hidden pt-32 pb-12">
        <div class="absolute inset-0 bg-linear-to-br from-indigo-900 to-purple-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2 z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-200 text-sm font-semibold tracking-wider uppercase mb-4 backdrop-blur-sm">
                {{ __('events::messages.events') }}
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">
                Próximos <span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-400 to-purple-400">Eventos</span>
            </h1>
            <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto font-light leading-relaxed">
                {{ __('events::messages.check_upcoming_events') ?? 'Confira nossos próximos eventos e faça sua inscrição' }}
            </p>
        </div>
    </section>

    <!-- Events Grid -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950 min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($events as $event)
                <div class="group relative bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-800 flex flex-col h-full">
                    <!-- Image Container -->
                    <div class="relative h-64 overflow-hidden">
                        @if($event->banner_path)
                            <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <x-icon name="calendar-days" style="duotone" class="w-20 h-20 text-white/30" />
                            </div>
                        @endif

                        <!-- Date Badge -->
                        <div class="absolute top-4 left-4 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl border border-white/20 min-w-[75px] h-20 flex flex-col items-center justify-center">
                            <span class="block text-3xl font-black text-indigo-600 dark:text-indigo-400 leading-none mb-1">
                                {{ $event->start_date->format('d') }}
                            </span>
                            <span class="block text-[0.7rem] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest text-center leading-tight">
                                {{ $event->start_date->locale('pt_BR')->shortMonthName }}
                            </span>
                        </div>

                        <!-- Status/Capacity Badge -->
                        @if($event->total_participants >= $event->capacity && $event->capacity > 0)
                            <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg">
                                Esgotado
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-8 flex flex-col flex-1 relative">
                        <!-- Location -->
                        @if($event->location)
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <x-icon name="location-dot" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                                {{ Str::limit($event->location, 30) }}
                            </div>
                        @endif

                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $event->title }}
                        </h3>

                        @if($event->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-3 text-sm leading-relaxed flex-1">
                                {{ Str::limit($event->description, 120) }}
                            </p>
                        @endif

                        <a href="{{ route('events.public.show', $event->slug) }}"
                            class="block w-full text-center px-6 py-4 bg-gray-50 dark:bg-gray-800 hover:bg-indigo-600 hover:text-white text-gray-900 dark:text-white rounded-xl transition-all duration-300 font-bold tracking-wide border border-gray-200 dark:border-gray-700 hover:border-indigo-600 group-hover:shadow-lg">
                            {{ __('events::messages.details_and_register') ?? 'Ver Detalhes e Inscrever-se' }}
                            <x-icon name="arrow-right" style="solid" class="w-4 h-4 inline-block ml-2 transform group-hover:translate-x-1 transition-transform" />
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="calendar-days" style="duotone" class="w-12 h-12 text-indigo-300 dark:text-indigo-700" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('events::messages.no_events') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        {{ __('events::messages.no_events_at_moment') ?? 'Não há eventos disponíveis no momento. Volte em breve!' }}
                    </p>
                </div>
                @endforelse
            </div>

            @if($events->hasPages())
                <div class="mt-16 flex justify-center">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

