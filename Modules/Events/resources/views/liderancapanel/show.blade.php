@extends('liderancapanel::components.layouts.master')

@section('title', $event->title)

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.eventos.index') }}"
                        class="hover:text-white transition-colors">{{ __('events::messages.events') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ $event->title }}</span>
                </nav>
                @if ($event->banner_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($event->banner_path) }}" alt="{{ $event->title }}"
                        class="mb-4 h-40 w-full object-cover rounded-2xl border border-gray-200 dark:border-slate-700">
                @endif
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $event->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $event->start_date ? $event->start_date->format('d/m/Y H:i') : '' }}
                    @if ($event->end_date)
                        — {{ $event->end_date->format('d/m/Y H:i') }}
                    @endif
                    @if ($event->location)
                        · {{ $event->location }}
                    @endif
                </p>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2
                @if ($event->status === 'published') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                @elseif($event->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @endif">
                    {{ $event->status_display ?? $event->status }}
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if (\Illuminate\Support\Facades\Gate::allows('manageRegistrations', $event))
                    <a href="{{ route('lideranca.eventos.registrations.index', $event) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="clipboard-list" class="w-5 h-5" />
                        {{ __('events::messages.registrations') }}
                    </a>
                @endif
                @if (\Illuminate\Support\Facades\Gate::allows('checkin', $event))
                    <a href="{{ route('lideranca.eventos.checkin.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition-colors">
                        <x-icon name="camera" class="w-5 h-5" />
                        Check-in
                    </a>
                @endif
                @if ($event->status === 'published' && Route::has('events.public.show'))
                    <a href="{{ route('events.public.show', $event->slug) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-gray-800 dark:text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-up-right-from-square" class="w-5 h-5" />
                        {{ __('events::messages.view_public_page') ?? 'Ver página pública' }}
                    </a>
                @endif
                <a href="{{ route('lideranca.eventos.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('events::messages.back') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.registered') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $event->total_participants ?? 0 }} /
                    {{ $event->capacity ?? '∞' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('events::messages.registrations_count') ?? 'Inscrições' }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $event->registrations->count() }}
                </p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('events::messages.visibility') ?? 'Visibilidade' }}</p>
                <p class="mt-1 text-lg font-semibold"><span
                        class="px-2 py-0.5 rounded-full text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $event->visibility_display ?? $event->visibility }}</span>
                </p>
            </div>
        </div>

        @if ($event->description)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.description') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $event->description }}</p>
            </div>
        @endif
    </div>
@endsection
