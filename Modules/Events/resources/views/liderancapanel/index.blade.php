@extends('liderancapanel::components.layouts.master')

@section('title', __('events::messages.events'))

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

        <div
            class="relative overflow-hidden rounded-2xl bg-slate-800 dark:bg-slate-900 border border-amber-900/30 text-white p-6 md:p-8">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <x-icon name="calendar-days" class="w-7 h-7 text-amber-400" />
                {{ __('events::messages.events') }}
            </h1>
            <p class="text-slate-300 mt-1">
                {{ __('events::messages.manage_church_events') ?? 'Acompanhe eventos, inscrições e check-in.' }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                @if (Route::has('lideranca.eventos.checkin.index'))
                    <a href="{{ route('lideranca.eventos.checkin.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="camera" class="w-5 h-5" />
                        Check-in
                    </a>
                @endif
                @if (Route::has('admin.events.events.create'))
                    <a href="{{ route('admin.events.events.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="plus" class="w-5 h-5" />
                        {{ __('events::messages.new_event') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
            <form method="GET" action="{{ route('lideranca.eventos.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('events::messages.search_events') ?? 'Buscar' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white">
                <select name="status"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white">
                    <option value="">{{ __('events::messages.all_statuses') ?? 'Todos' }}</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                        {{ __('events::messages.status_draft') }}</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                        {{ __('events::messages.status_published') }}</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>
                        {{ __('events::messages.status_closed') }}</option>
                </select>
                @if (isset($eventTypes))
                    <select name="event_type_id"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('events::messages.all_types') ?? 'Todos os tipos' }}</option>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ request('event_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium">{{ __('events::messages.filter') ?? 'Filtrar' }}</button>
                    @if (request()->hasAny(['search', 'status', 'event_type_id', 'date_from', 'date_to']))
                        <a href="{{ route('lideranca.eventos.index') }}"
                            class="px-4 py-2.5 bg-slate-200 dark:bg-slate-600 text-gray-800 dark:text-white rounded-xl font-medium">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($events as $event)
                    <a href="{{ route('lideranca.eventos.show', $event) }}"
                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $event->start_date ? $event->start_date->format('d/m/Y H:i') : '' }}
                                    @if ($event->location)
                                        · {{ $event->location }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if ($event->status === 'published') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($event->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @endif">
                                    {{ $event->status_display ?? $event->status }}
                                </span>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ $event->total_participants ?? 0 }}
                                    / {{ $event->capacity ?? '∞' }} {{ __('events::messages.registered') }}</span>
                                <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="calendar-days" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>{{ __('events::messages.no_events_found') ?? 'Nenhum evento encontrado.' }}</p>
                    </div>
                @endforelse
            </div>
            @if ($events->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
