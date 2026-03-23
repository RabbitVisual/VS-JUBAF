@extends('admin::components.layouts.master')

@section('title', __('events::messages.events') . ' - Administração')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('events::messages.events') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('events::messages.manage_church_events') }}</p>
            </div>
            <a href="{{ route('admin.events.events.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
                <x-icon name="plus" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                {{ __('events::messages.new_event') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 p-5">
            <form method="GET" action="{{ route('admin.events.events.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500"
                        placeholder="{{ __('events::messages.search_events') }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="magnifying-glass" style="duotone" class="h-5 w-5 text-gray-400" />
                    </div>
                </div>


                <select name="status"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                    <option value="">{{ __('events::messages.all_statuses') }}</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('events::messages.status_draft') }}</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>{{ __('events::messages.status_published') }}</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('events::messages.status_closed') }}</option>
                </select>


                <select name="visibility"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                    <option value="">{{ __('events::messages.all_visibilities') }}</option>
                    <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>{{ __('events::messages.visibility_public') }}</option>
                    <option value="members" {{ request('visibility') === 'members' ? 'selected' : '' }}>{{ __('events::messages.visibility_members') }}</option>
                    <option value="both" {{ request('visibility') === 'both' ? 'selected' : '' }}>{{ __('events::messages.visibility_both') }}</option>
                </select>

                @if(isset($eventTypes))
                <select name="event_type_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                    <option value="">{{ __('events::messages.all_types') }}</option>
                    @foreach($eventTypes as $type)
                        <option value="{{ $type->id }}" {{ request('event_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @endif

                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500"
                    placeholder="{{ __('events::messages.date_from') ?? 'Data de' }}">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500"
                    placeholder="{{ __('events::messages.date_to') ?? 'Data até' }}">

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-slate-800 dark:bg-slate-700 text-white rounded-xl hover:bg-slate-700 dark:hover:bg-slate-600 font-medium transition-colors">
                        {{ __('events::messages.filter') }}
                    </button>
                    @if (request()->hasAny(['search', 'status', 'visibility', 'event_type_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.events.events.index') }}"
                            class="px-4 py-2.5 bg-gray-200 dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-300 dark:hover:bg-slate-700 font-medium transition-colors">
                            <x-icon name="xmark" style="duotone" class="h-5 w-5" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($events as $event)
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
                    <!-- Banner -->
                    <div class="relative h-48 bg-gray-200 dark:bg-slate-800">
                        @if ($event->banner_path)
                            <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-linear-to-br from-slate-800 to-slate-900">
                                <x-icon name="image" style="duotone" class="h-16 w-16 text-slate-700" />
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm
                                @if ($event->status === 'published') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-300
                                @elseif($event->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700/80 dark:text-gray-300
                                @else bg-red-100 text-red-800 dark:bg-red-900/80 dark:text-red-300 @endif">
                                {{ $event->status_display }}
                            </span>
                        </div>
                    </div>


                    <div class="p-6">
                        <div class="flex items-center gap-2 text-xs font-medium text-amber-600 dark:text-amber-500 mb-2">
                            <x-icon name="calendar-days" style="duotone" class="h-4 w-4" />
                            {{ $event->start_date->format('d/m/Y \à\s H:i') }}
                            @if($event->registration_deadline && !$event->registration_deadline->isPast())
                                <span class="ml-1 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 rounded-full px-1.5 py-0.5">
                                    Prazo: {{ $event->registration_deadline->format('d/m') }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-1" title="{{ $event->title }}">
                            {{ $event->title }}
                        </h3>

                        @if($event->eventType)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-400 mb-3">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $event->eventType->color ?? '#6B7280' }}"></span>
                                {{ $event->eventType->name }}
                            </span>
                        @endif

                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mt-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center gap-1">
                                <x-icon name="location-dot" style="duotone" class="h-4 w-4" />
                                <span class="truncate max-w-[100px]">{{ $event->location ?? 'A definir' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($event->dress_code)
                                    <div class="flex items-center gap-1" title="Traje">
                                        <x-icon name="shirt" style="duotone" class="h-4 w-4" />
                                        <span class="hidden sm:inline text-xs">{{ Str::title(str_replace('_', ' ', $event->dress_code)) }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1" title="Inscritos / Capacidade">
                                    <x-icon name="users" style="duotone" class="h-4 w-4" />
                                    <span>{{ $event->total_participants ?? 0 }} / {{ $event->capacity ?? '∞' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-6">
                            <a href="{{ route('admin.events.events.edit', $event) }}" class="flex items-center justify-center px-3 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                                Editar
                            </a>
                            <a href="{{ route('admin.events.events.registrations.index', $event) }}" class="flex items-center justify-center px-3 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 font-medium text-sm transition-colors">
                                Inscrições
                            </a>
                            <form action="{{ route('admin.events.events.duplicate', $event) }}" method="POST" class="inline" onsubmit="return confirm('Duplicar este evento?');">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 font-medium text-sm transition-colors">
                                    Duplicar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="flex flex-col items-center justify-center p-12 bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-gray-300 dark:border-slate-700">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                            <x-icon name="calendar-days" style="duotone" class="h-8 w-8 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('events::messages.no_events') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1 mb-6 text-center max-w-sm">{{ __('events::messages.start_creating_first_event') }}</p>
                        <a href="{{ route('admin.events.events.create') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-amber-600 hover:bg-amber-700 transition-all">
                            <x-icon name="plus" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                            {{ __('events::messages.new_event') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($events->hasPages())
            <div class="mt-6">
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endsection
