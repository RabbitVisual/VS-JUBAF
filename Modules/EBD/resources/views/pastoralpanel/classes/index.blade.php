@extends('pastoralpanel::components.layouts.master')

@section('title', 'Turmas EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Turmas EBD</h1>
                    <p class="text-slate-300 text-sm md:text-base">Turmas, faixas etárias e lotação.</p>
                </div>
                <a href="{{ route('pastor.ebd.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="gauge-high" class="w-5 h-5" /> Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
            <form method="GET" action="{{ route('pastor.ebd.classes.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Faixa etária</label>
                    <select name="age_group" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">Todas</option>
                        <option value="adult" {{ request('age_group') === 'adult' ? 'selected' : '' }}>Adultos</option>
                        <option value="youth" {{ request('age_group') === 'youth' ? 'selected' : '' }}>Jovens</option>
                        <option value="teen" {{ request('age_group') === 'teen' ? 'selected' : '' }}>Adolescentes</option>
                        <option value="children" {{ request('age_group') === 'children' ? 'selected' : '' }}>Crianças</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="is_active" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">Todos</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativas</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativas</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium">Filtrar</button>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Turma</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Faixa etária</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sala / Horário</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Alunos</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @php
                            $ageGroupStyles = [
                                'adult' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'youth' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'teen' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                'children' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            ];
                        @endphp
                        @forelse($classes as $class)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $class->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">{{ $class->description ?? '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $ageGroupStyles[$class->age_group] ?? 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400' }}">{{ $class->age_group_display }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $class->room ?? '—' }} · {{ $class->schedule_time ?? '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $class->activeStudents->count() }}</span>
                                    @if($class->max_students) <span class="text-xs text-gray-500">/ {{ $class->max_students }}</span> @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $class->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400' }}">{{ $class->is_active ? 'Ativa' : 'Inativa' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('pastor.ebd.classes.show', $class) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium text-sm">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhuma turma encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($classes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">{{ $classes->appends(request()->query())->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
@endsection
