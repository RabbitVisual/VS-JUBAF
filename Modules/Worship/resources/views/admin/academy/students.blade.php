@extends('admin::components.layouts.master')

@section('title', 'Alunos | Worship Academy')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest mb-1.5">
                <a href="{{ route('worship.admin.dashboard') }}" class="hover:underline">Louvor</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <a href="{{ route('worship.admin.academy.courses.index') }}" class="hover:underline">Academy</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <span class="text-gray-400 dark:text-gray-500">Alunos</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Alunos da Academy</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Inscrições, progresso e nível técnico por curso.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <form method="get" action="{{ route('worship.admin.academy.students') }}" class="flex flex-wrap items-end gap-4">
                <div class="min-w-[200px]">
                    <label for="course_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Filtrar por curso</label>
                    <select name="course_id" id="course_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4">
                        <option value="">Todos os cursos</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm">
                    <x-icon name="magnifying-glass" class="w-4 h-4 mr-2" />
                    Filtrar
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Aluno</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Curso</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Progresso</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Inscrito em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                        {{ $enrollment->user ? strtoupper(mb_substr($enrollment->user->name ?? '?', 0, 2)) : '?' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $enrollment->user->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $enrollment->course->title ?? '—' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->course ? ucfirst($enrollment->course->level) : '' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $pct = $enrollment->progress_percent ?? 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-indigo-500 dark:bg-indigo-500 transition-all" style="width: {{ min(100, $pct) }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ round($pct) }}%</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-gray-500 dark:text-gray-400">
                                {{ $enrollment->created_at ? $enrollment->created_at->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                    <x-icon name="users" class="w-8 h-8 text-gray-400" />
                                </div>
                                <p>Nenhuma inscrição encontrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $enrollments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
