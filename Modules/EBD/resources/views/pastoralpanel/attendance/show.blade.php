@extends('pastoralpanel::components.layouts.master')

@section('title', 'Presença - ' . $lesson->title)

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ebd.attendance.index') }}" class="hover:text-white">Presença</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $lesson->title }}</span>
                    </nav>
                    <p class="text-slate-300 text-sm">{{ $lesson->ebdClass->name ?? '—' }} · {{ $lesson->lesson_date ? $lesson->lesson_date->format('d/m/Y') : '—' }}</p>
                </div>
                <a href="{{ route('pastor.ebd.lessons.show', $lesson) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Ver lição
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Registros de presença ({{ $attendance->count() }})</h3>
                <span class="text-sm text-green-600 dark:text-green-400 font-medium">{{ $attendance->where('status', 'present')->count() }} presentes</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aluno</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($attendance as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $a->student->user->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $a->status === 'present' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">{{ $a->status === 'present' ? 'Presente' : 'Ausente' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum registro para esta lição.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
