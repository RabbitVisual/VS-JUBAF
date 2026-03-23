@extends('liderancapanel::components.layouts.master')

@section('title', __('diretoria::messages.agendas'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('diretoria::messages.diretoria') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ __('diretoria::messages.agendas') }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="list-check" class="w-7 h-7 text-amber-500" />
                    {{ __('diretoria::messages.view_agendas') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('diretoria::messages.agendas_subtitle') }}</p>
            </div>
            <a href="{{ route('lideranca.conselho.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('diretoria::messages.back') }}
            </a>
        </div>

        @if (isset($meetings) && $meetings->count() > 0)
            <form method="GET" action="{{ route('lideranca.conselho.agendas.index') }}"
                class="flex flex-wrap items-center gap-3">
                <label
                    class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('diretoria::messages.meeting') }}:</label>
                <select name="meeting_id" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm">
                    <option value="">{{ __('diretoria::messages.all_meetings') }}</option>
                    @foreach ($meetings as $m)
                        <option value="{{ $m->id }}" {{ request('meeting_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->title }} ({{ $m->scheduled_date ? $m->scheduled_date->format('d/m/Y') : '' }})
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('diretoria::messages.meeting') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('diretoria::messages.agenda_title') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('diretoria::messages.current_status') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('diretoria::messages.decision') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($agendas as $agenda)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $agenda->meeting->title ?? '—' }}
                                    @if ($agenda->meeting && $agenda->meeting->scheduled_date)
                                        <span
                                            class="text-gray-500 dark:text-gray-400 block">{{ $agenda->meeting->scheduled_date->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $agenda->title }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if ($agenda->status === 'pending') bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-300
                                @elseif($agenda->status === 'discussed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                @elseif($agenda->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($agenda->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @endif">
                                        {{ __('diretoria::messages.status_' . $agenda->status) ?? $agenda->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($agenda->decision, 50) ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('diretoria::messages.no_agenda_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($agendas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $agendas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
