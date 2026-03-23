@extends('pastoralpanel::components.layouts.master')

@section('title', $class->name . ' - EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ebd.classes.index') }}" class="hover:text-white">Turmas</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $class->name }}</span>
                    </nav>
                    <span class="inline-flex px-2 py-0.5 rounded bg-white/10 text-xs font-medium mb-2">{{ $class->age_group_display }}</span>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $class->name }}</h1>
                    <p class="text-slate-300 text-sm">{{ $class->description ?? 'Turma da EBD.' }}</p>
                </div>
                <a href="{{ route('pastor.ebd.classes.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sala</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $class->room ?? '—' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Horário</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $class->schedule_time ?? '—' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Alunos</p>
                <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $class->activeStudents->count() }} @if($class->max_students)/ {{ $class->max_students }} @endif</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Professores</h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($class->teachers as $teacher)
                        <li class="px-6 py-3 flex items-center gap-3">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $teacher->user->name ?? '—' }}</span>
                            @if($teacher->role)
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400">{{ $teacher->role }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-gray-500 dark:text-gray-400">Nenhum professor vinculado.</li>
                    @endforelse
                </ul>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Alunos</h3>
                    <span class="text-xs text-gray-500">{{ $class->activeStudents->count() }} ativos</span>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-slate-700 max-h-80 overflow-y-auto">
                    @forelse($class->activeStudents as $student)
                        <li class="px-6 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $student->user->name ?? '—' }}</li>
                    @empty
                        <li class="px-6 py-6 text-sm text-gray-500 dark:text-gray-400">Nenhum aluno matriculado.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        @if($class->lessons->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Últimas lições</h3>
                    <a href="{{ route('pastor.ebd.lessons.index') }}?class_id={{ $class->id }}" class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline">Ver todas</a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($class->lessons as $lesson)
                        <li class="px-6 py-3 flex items-center justify-between">
                            <a href="{{ route('pastor.ebd.lessons.show', $lesson) }}" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ $lesson->title }}</a>
                            <span class="text-xs text-gray-500">{{ $lesson->lesson_date->format('d/m/Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection
