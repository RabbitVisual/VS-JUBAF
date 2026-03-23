@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.events') . ' - ' . __('memberpanel::messages.member_panel'))
@section('page-title', __('events::messages.events'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6 sm:space-y-8">
        <!-- Header alinhado ao dashboard -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
                    <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                    <span class="text-gray-900 dark:text-white font-medium">{{ __('events::messages.events') }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('events::messages.events') }}</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm max-w-xl">{{ __('events::messages.check_available_events') }}</p>
            </div>
            <a href="{{ route('memberpanel.events.my-registrations') }}" data-tour="events-my-registrations-link"
               class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shrink-0 touch-manipulation active:scale-[0.98]">
                <x-icon name="ticket" class="w-5 h-5" />
                {{ __('events::messages.my_registrations') }}
            </a>
        </div>

        <!-- Hero card (estilo dashboard) -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-500 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-500 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-4 sm:px-6 md:px-8 py-8 sm:py-10 flex flex-col md:flex-row items-center justify-between gap-6 z-10">
                <div class="flex-1 text-center md:text-left space-y-1">
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ __('events::messages.communion_and_learning') }}</span>
                    <p class="text-gray-500 dark:text-slate-400 text-sm max-w-xl">{{ __('events::messages.check_available_events') }}</p>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" data-tour="events-list">
            @forelse($events as $event)
            <article class="group bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:border-indigo-200 dark:hover:border-slate-700 transition-all duration-300 hover:-translate-y-0.5 flex flex-col">
                <div class="relative h-44 sm:h-48 overflow-hidden bg-gray-100 dark:bg-slate-800">
                    @if($event->banner_path)
                        <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <x-icon name="calendar-days" style="duotone" class="w-14 h-14 sm:w-16 sm:h-16 text-white/50" />
                        </div>
                    @endif
                    <div class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2.5 py-1 sm:px-3 rounded-lg text-xs font-bold text-gray-900 dark:text-white shadow-md">
                        {{ $event->start_date->format('d/m') }}
                    </div>
                </div>
                <div class="p-4 sm:p-6 flex flex-col flex-1 space-y-4">
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $event->title }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ Str::limit($event->description, 100) }}
                        </p>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-100 dark:border-slate-800">
                        <div class="flex items-center text-sm text-gray-500 dark:text-slate-400">
                            <x-icon name="clock" class="w-4 h-4 mr-2 text-indigo-500 shrink-0" />
                            <span class="truncate">{{ $event->start_date->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($event->location)
                            <div class="flex items-center text-sm text-gray-500 dark:text-slate-400">
                                <x-icon name="location-marker" class="w-4 h-4 mr-2 text-purple-500 shrink-0" />
                                <span class="truncate">{{ Str::limit($event->location, 28) }}</span>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('memberpanel.events.show', $event) }}"
                        class="block w-full text-center px-4 py-3 bg-gray-50 dark:bg-slate-800/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 text-gray-900 dark:text-white hover:text-white rounded-xl font-bold transition-all duration-200 mt-2 group-hover:shadow-lg group-hover:shadow-indigo-500/20 touch-manipulation active:scale-[0.98]">
                        {{ __('events::messages.details') }}
                    </a>
                </div>
            </article>
            @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                        <x-icon name="calendar" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 dark:text-slate-500" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('events::messages.no_events') }}</h3>
                    <p class="text-gray-500 dark:text-slate-400 text-sm max-w-md mx-auto">
                        {{ __('events::messages.no_events_at_moment') }}
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        @if($events->hasPages())
            <div class="mt-6 sm:mt-8">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

