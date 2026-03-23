@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <!-- Hero Header (padrão configuração) -->
        <div
            class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Diretoria</span>
                        <span
                            class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Dashboard</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">
                        {{ $diretoria_display_name ?? __('Diretoria::messages.diretoria_title') }}</h1>
                    <p class="text-gray-300 max-w-xl">{{ __('Diretoria::messages.diretoria_subtitle') }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('admin.Diretoria.members.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                        <x-icon name="users" class="w-5 h-5" /> {{ __('Diretoria::messages.members') }}
                    </a>
                    <a href="{{ route('admin.Diretoria.ministries.dashboard') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                        <x-icon name="traffic-light" class="w-5 h-5" /> Ministérios
                    </a>
                    <a href="{{ route('admin.Diretoria.documents.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                        <x-icon name="file-lines" class="w-5 h-5" /> {{ __('Diretoria::messages.documents') }}
                    </a>
                    <a href="{{ route('admin.Diretoria.projects.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                        <x-icon name="diagram-project" class="w-5 h-5" /> {{ __('Diretoria::messages.projects') }}
                    </a>
                    <a href="{{ route('admin.Diretoria.meetings.create') }}"
                        class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 inline-flex items-center gap-2">
                        <x-icon name="plus" class="w-5 h-5 text-blue-600" /> {{ __('Diretoria::messages.new_meeting') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Members -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        {{ __('Diretoria::messages.active_members') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['total_members'] ?? 0) }}
                    </h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        {{ __('Diretoria::messages.counselors_registered') }}
                    </div>
                </div>
            </div>

            <!-- Upcoming Meetings -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        {{ __('Diretoria::messages.upcoming_meetings') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['upcoming_meetings'] ?? 0) }}
                    </h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                        {{ __('Diretoria::messages.scheduled') }}
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        {{ __('Diretoria::messages.pending_approvals') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['pending_approvals'] ?? 0) }}
                    </h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                        {{ __('Diretoria::messages.pending') }}
                    </div>
                </div>
            </div>

            <!-- Completed Meetings -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110">
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        {{ __('Diretoria::messages.completed_meetings') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['completed_meetings'] ?? 0) }}
                    </h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                        {{ __('Diretoria::messages.total_history') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Members by Role -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <x-icon name="badge-check" class="w-5 h-5 text-blue-500" />
                    Distribuição de Cargos
                </h3>
                @if (isset($membersByRole) && $membersByRole->count() > 0)
                    <div class="space-y-4">
                        @foreach ($membersByRole as $item)
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-sm font-bold text-gray-700 dark:text-gray-300 capitalize flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                        {{ str_replace('_', ' ', $item->diretoria_role_display ?? $item->diretoria_role) }}
                                    </span>
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">
                                        {{ $item->count }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500 group-hover:bg-indigo-400"
                                        style="width: {{ $stats['total_members'] > 0 ? ($item->count / $stats['total_members']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma informação disponível.</p>
                    </div>
                @endif
            </div>

            <!-- Meetings by Status -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <x-icon name="chart-pie" class="w-5 h-5 text-purple-500" />
                    Status das Reuniões
                </h3>
                @if (isset($meetingsByStatus) && $meetingsByStatus->count() > 0)
                    <div class="space-y-4">
                        @foreach ($meetingsByStatus as $item)
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-sm font-bold text-gray-700 dark:text-gray-300 capitalize flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                        {{ str_replace('_', ' ', $item->status_display ?? $item->status) }}
                                    </span>
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">
                                        {{ $item->count }}
                                    </span>
                                </div>
                                @php $totalMeetings = isset($meetingsByStatus) ? $meetingsByStatus->sum('count') : 0; @endphp
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500 group-hover:bg-purple-400"
                                        style="width: {{ $totalMeetings > 0 ? ($item->count / $totalMeetings) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum dado disponível.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div
                class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Atividades Recentes</h3>
                <a href="{{ route('admin.Diretoria.meetings.index') }}"
                    class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    Ver todas
                </a>
            </div>
            @if (isset($recentMeetings) && $recentMeetings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Título</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Data</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Status</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Participantes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($recentMeetings->take(5) as $meeting)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white block">{{ Str::limit($meeting->title, 40) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <x-icon name="calendar" class="w-4 h-4 mr-2 text-gray-400" />
                                            {{ $meeting->scheduled_date->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = match ($meeting->status) {
                                                'scheduled'
                                                    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'in_progress'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'completed'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                default
                                                    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            };
                                            $statusIcon = match ($meeting->status) {
                                                'scheduled' => 'clock',
                                                'in_progress' => 'play',
                                                'completed' => 'check',
                                                default => 'minus',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClasses }}">
                                            <x-icon name="{{ $statusIcon }}" class="w-3 h-3 mr-1" />
                                            {{ $meeting->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex -space-x-2 overflow-hidden">
                                            @foreach ($meeting->participant_members->take(3) as $participant)
                                                @php
                                                    $pName =
                                                        $participant->user->name ?? ($participant->name ?? 'Membro');
                                                    $pPhoto = $participant->user->photo ?? null;
                                                @endphp
                                                @if ($pPhoto)
                                                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover"
                                                        src="{{ asset('storage/' . $pPhoto) }}"
                                                        alt="{{ $pName }}" title="{{ $pName }}">
                                                @else
                                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300"
                                                        title="{{ $pName }}">
                                                        {{ substr($pName, 0, 1) }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if ($meeting->participant_members->count() > 3)
                                                <div
                                                    class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                                    +{{ $meeting->participant_members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <x-icon name="clipboard-list" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                    <p>Nenhuma atividade recente registrada.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
