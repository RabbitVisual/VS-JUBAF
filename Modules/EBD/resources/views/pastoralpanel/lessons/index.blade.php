@extends('pastoralpanel::components.layouts.master')

@section('title', 'Lições EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Lições EBD</h1>
                    <p class="text-slate-300 text-sm md:text-base">Agenda e conteúdo das lições.</p>
                </div>
                <a href="{{ route('pastor.ebd.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="gauge-high" class="w-5 h-5" /> Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
            <form method="GET" action="{{ route('pastor.ebd.lessons.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Curso</label>
                    <select name="course_id" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 min-w-[180px]">
                        <option value="">Todos</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Turma</label>
                    <select name="class_id" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 min-w-[160px]">
                        <option value="">Todas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">Todos</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Agendada</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluída</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Turma / Curso</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @php
                            $statusClass = [
                                'scheduled' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400',
                            ];
                            $statusLabel = ['scheduled' => 'Agendada', 'in_progress' => 'Em andamento', 'completed' => 'Concluída', 'cancelled' => 'Cancelada'];
                        @endphp
                        @forelse($lessons as $lesson)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $lesson->ebdClass->name ?? '—' }} / {{ $lesson->course->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $lesson->lesson_date ? $lesson->lesson_date->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $statusClass[$lesson->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $statusLabel[$lesson->status] ?? $lesson->status }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('pastor.ebd.lessons.show', $lesson) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium text-sm">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhuma lição encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lessons->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">{{ $lessons->appends(request()->query())->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
@endsection
