@extends('liderancapanel::components.layouts.master')

@section('title', __('churchcouncil::messages.council'))

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
                <x-icon name="scale-balanced" class="w-7 h-7 text-amber-400" />
                {{ __('churchcouncil::messages.council') }}
            </h1>
            <p class="text-slate-300 mt-1">{{ __('churchcouncil::messages.council_subtitle') }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                @if (Route::has('lideranca.conselho.meetings.index'))
                    <a href="{{ route('lideranca.conselho.meetings.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="calendar-days" class="w-5 h-5" />
                        {{ __('churchcouncil::messages.meetings') }}
                    </a>
                @endif
                <a href="{{ route('lideranca.conselho.approvals') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                    <x-icon name="clipboard-check" class="w-5 h-5" />
                    {{ __('churchcouncil::messages.approvals') }}
                </a>
                @if (Route::has('lideranca.conselho.documents.index'))
                    <a href="{{ route('lideranca.conselho.documents.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="file-lines" class="w-5 h-5" />
                        {{ __('churchcouncil::messages.documents') }}
                    </a>
                @endif
                @if (Route::has('lideranca.conselho.projects.index'))
                    <a href="{{ route('lideranca.conselho.projects.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="diagram-project" class="w-5 h-5" />
                        {{ __('churchcouncil::messages.projects') }}
                    </a>
                @endif
                @if (Route::has('lideranca.conselho.members.index'))
                    <a href="{{ route('lideranca.conselho.members.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="users" class="w-5 h-5" />
                        {{ __('churchcouncil::messages.members') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('churchcouncil::messages.active_members') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_members'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('churchcouncil::messages.upcoming_meetings') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['upcoming_meetings'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('churchcouncil::messages.pending_approvals') }}</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                    {{ $stats['pending_approvals'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('churchcouncil::messages.completed_meetings') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_meetings'] ?? 0 }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        {{ __('churchcouncil::messages.meetings') }} ({{ __('churchcouncil::messages.view') }})</h2>
                    @if (Route::has('lideranca.conselho.meetings.index'))
                        <a href="{{ route('lideranca.conselho.meetings.index') }}"
                            class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ __('churchcouncil::messages.view_meetings') }}</a>
                    @endif
                </div>
                <div class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($recentMeetings ?? [] as $meeting)
                        <a href="{{ route('lideranca.conselho.meetings.show', $meeting) }}"
                            class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $meeting->title ?? __('churchcouncil::messages.meeting') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $meeting->scheduled_date ? $meeting->scheduled_date->format('d/m/Y H:i') : '' }} ·
                                {{ $meeting->status_display ?? '' }}</p>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('churchcouncil::messages.no_meetings') }}</div>
                    @endforelse
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        {{ __('churchcouncil::messages.pending_approvals') }}</h2>
                    <a href="{{ route('lideranca.conselho.approvals') }}"
                        class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ __('churchcouncil::messages.view') }}</a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($pendingApprovals ?? [] as $approval)
                        <a href="{{ route('lideranca.conselho.approvals.show', $approval) }}"
                            class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $approval->approval_type_display ?? 'Solicitação' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $approval->requester->name ?? '' }} ·
                                {{ $approval->submitted_at ? $approval->submitted_at->format('d/m/Y') : '' }}</p>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('churchcouncil::messages.pending_items') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
