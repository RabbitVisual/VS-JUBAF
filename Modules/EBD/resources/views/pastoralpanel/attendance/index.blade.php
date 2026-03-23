@extends('pastoralpanel::components.layouts.master')

@section('title', 'Presença EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Presença EBD</h1>
                    <p class="text-slate-300 text-sm md:text-base">Registros de presença por lição.</p>
                </div>
                <a href="{{ route('pastor.ebd.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="gauge-high" class="w-5 h-5" /> Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total registros</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Presentes</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['present'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
            <form method="GET" action="{{ route('pastor.ebd.attendance.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lição</label>
                    <select name="lesson_id" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 min-w-[220px]">
                        <option value="">Todas</option>
                        @foreach($lessons as $l)
                            <option value="{{ $l->id }}" {{ request('lesson_id') == $l->id ? 'selected' : '' }}>{{ $l->title }} ({{ $l->lesson_date ? $l->lesson_date->format('d/m/Y') : '—' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">Todos</option>
                        <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Presente</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Ausente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">De</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Até</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium">Filtrar</button>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Lição</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aluno</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($attendance as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4">
                                    <a href="{{ route('pastor.ebd.attendance.show', $a->lesson) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium text-sm">{{ $a->lesson->title ?? '—' }}</a>
                                    <p class="text-xs text-gray-500">{{ $a->lesson && $a->lesson->lesson_date ? $a->lesson->lesson_date->format('d/m/Y') : '—' }} · {{ $a->lesson->ebdClass->name ?? '—' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $a->student->user->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $a->status === 'present' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">{{ $a->status === 'present' ? 'Presente' : 'Ausente' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $a->registeredBy->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum registro de presença.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendance->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">{{ $attendance->appends(request()->query())->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
@endsection
