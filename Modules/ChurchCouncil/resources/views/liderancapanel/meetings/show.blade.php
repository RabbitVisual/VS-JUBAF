@extends('liderancapanel::components.layouts.master')

@section('title', $meeting->title . ' - ' . __('churchcouncil::messages.meeting'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('pastor.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('churchcouncil::messages.council') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('pastor.conselho.meetings.index') }}"
                        class="hover:text-white transition-colors">{{ __('churchcouncil::messages.meetings') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ $meeting->title }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $meeting->title }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ $meeting->scheduled_date ? $meeting->scheduled_date->format('d/m/Y \à\s H:i') : '' }}
                    </span>
                    @if ($meeting->location)
                        <span class="flex items-center gap-1.5">
                            <x-icon name="location-dot" class="w-4 h-4" />
                            {{ $meeting->location }}
                        </span>
                    @endif
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
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if (Route::has('pastor.conselho.meetings.minutes-pdf'))
                    <a href="{{ route('pastor.conselho.meetings.minutes-pdf', $meeting) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="file-pdf" class="w-5 h-5" /> {{ __('churchcouncil::messages.minutes') }} (PDF)
                    </a>
                @endif
                @if (Route::has('pastor.conselho.meetings.convocation-pdf'))
                    <a href="{{ route('pastor.conselho.meetings.convocation-pdf', $meeting) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        <x-icon name="file-pdf" class="w-5 h-5" /> Convocação (PDF)
                    </a>
                @endif
                <a href="{{ route('pastor.conselho.meetings.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('churchcouncil::messages.back') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-amber-500" />
                        {{ __('churchcouncil::messages.meeting_details') }}
                    </h2>
                    @if ($meeting->description)
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">
                                {{ __('churchcouncil::messages.initial_agenda_description') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $meeting->description }}
                            </p>
                        </div>
                    @endif
                    @if ($meeting->objectives ?? null)
                        <div class="mb-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
                            <h3 class="text-sm font-bold text-amber-800 dark:text-amber-200 mb-2">
                                {{ __('churchcouncil::messages.main_objectives') }}</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $meeting->objectives }}
                            </p>
                        </div>
                    @endif
                    @if ($meeting->president)
                        <div class="flex items-center gap-3 pt-2">
                            @if ($meeting->president->user->photo ?? null)
                                <img src="{{ asset('storage/' . $meeting->president->user->photo) }}" alt=""
                                    class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                                    <span
                                        class="text-sm font-bold text-gray-500">{{ substr($meeting->president->user->name ?? 'P', 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $meeting->president->user->name ?? '' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Presidente</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="list-check" class="w-5 h-5 text-amber-500" />
                        {{ __('churchcouncil::messages.agendas') }}
                    </h2>
                    @if ($meeting->agendas->count() > 0)
                        <div class="space-y-3">
                            @foreach ($meeting->agendas as $agenda)
                                <div
                                    class="p-4 rounded-xl border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700/30">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $agenda->title }}</p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if ($agenda->status === 'pending') bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-300
                                @elseif($agenda->status === 'discussed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                @elseif($agenda->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($agenda->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @endif">
                                            {{ __('churchcouncil::messages.status_' . $agenda->status) ?? $agenda->status }}
                                        </span>
                                    </div>
                                    @if ($agenda->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ Str::limit($agenda->description, 120) }}</p>
                                    @endif
                                    @if ($agenda->decision)
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-2">
                                            {{ __('churchcouncil::messages.decision') }}:
                                            {{ Str::limit($agenda->decision, 80) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            {{ __('churchcouncil::messages.no_agendas_yet') }}</p>
                    @endif
                </div>
            </div>
            <div class="space-y-6">
                @if ($meeting->minutesVersions->count() > 0)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">
                            {{ __('churchcouncil::messages.minutes') }} (versões)</h3>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            @foreach ($meeting->minutesVersions->take(5) as $v)
                                <li>v{{ $v->version }} · {{ $v->created_at->format('d/m/Y') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
