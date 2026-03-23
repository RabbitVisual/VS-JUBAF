@extends('liderancapanel::components.layouts.master')

@section('title', __('churchcouncil::messages.meetings'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('pastor.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('churchcouncil::messages.council') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ __('churchcouncil::messages.meetings') }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="calendar-days" class="w-7 h-7 text-amber-500" />
                    {{ __('churchcouncil::messages.meetings') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('churchcouncil::messages.view_meetings') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if (Route::has('admin.churchcouncil.meetings.create'))
                    <a href="{{ route('admin.churchcouncil.meetings.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="plus" class="w-5 h-5" /> {{ __('churchcouncil::messages.new_meeting') }}
                    </a>
                @endif
                <a href="{{ route('pastor.conselho.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('churchcouncil::messages.back') }}
                </a>
            </div>
        </div>

        @if (request()->hasAny(['status', 'type']) || request()->has('date_from'))
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('pastor.conselho.meetings.index') }}"
                    class="flex flex-wrap items-center gap-3">
                    <select name="status"
                        class="rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                        <option value="">{{ __('churchcouncil::messages.all_statuses') }}</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Agendada
                        </option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em
                            Andamento</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluída
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada
                        </option>
                    </select>
                    <select name="type"
                        class="rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                        <option value="">Tipo</option>
                        <option value="ordinary" {{ request('type') === 'ordinary' ? 'selected' : '' }}>Ordinária</option>
                        <option value="extraordinary" {{ request('type') === 'extraordinary' ? 'selected' : '' }}>
                            Extraordinária</option>
                        <option value="emergency" {{ request('type') === 'emergency' ? 'selected' : '' }}>Emergencial
                        </option>
                    </select>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-slate-200 dark:bg-slate-600 text-gray-800 dark:text-white text-sm font-medium">{{ __('churchcouncil::messages.filter') }}</button>
                </form>
                <a href="{{ route('pastor.conselho.meetings.index') }}"
                    class="text-sm text-amber-500 hover:underline">{{ __('churchcouncil::messages.clear_filters') }}</a>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($meetings as $meeting)
                    <a href="{{ route('pastor.conselho.meetings.show', $meeting) }}"
                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $meeting->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $meeting->scheduled_date ? $meeting->scheduled_date->format('d/m/Y H:i') : '' }}
                                    @if ($meeting->location)
                                        · {{ $meeting->location }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @if ($meeting->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($meeting->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($meeting->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $meeting->status_display ?? $meeting->status }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300">
                                    {{ $meeting->meeting_type_display ?? $meeting->meeting_type }}
                                </span>
                                <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="calendar-days" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>Nenhuma reunião encontrada.</p>
                    </div>
                @endforelse
            </div>
            @if ($meetings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
