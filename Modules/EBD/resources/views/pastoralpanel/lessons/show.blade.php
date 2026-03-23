@extends('pastoralpanel::components.layouts.master')

@section('title', $lesson->title . ' - EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ebd.lessons.index') }}" class="hover:text-white">Lições</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $lesson->title }}</span>
                    </nav>
                    <span class="inline-flex px-2 py-0.5 rounded bg-white/10 text-xs font-medium mb-2">{{ $lesson->lesson_date ? $lesson->lesson_date->format('d/m/Y') : '—' }} · {{ $lesson->ebdClass->name ?? '—' }}</span>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $lesson->title }}</h1>
                    <p class="text-slate-300 text-sm">{{ $lesson->objective ? \Str::limit($lesson->objective, 120) : 'Sem objetivo descrito.' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pastor.ebd.attendance.show', $lesson) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="clipboard-user" class="w-5 h-5" /> Presença
                    </a>
                    <a href="{{ route('pastor.ebd.lessons.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                @if($lesson->objective)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Objetivo</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $lesson->objective }}</p>
                    </div>
                @endif
                @if($lesson->bible_book && $lesson->bible_chapter)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Referência bíblica</h3>
                        <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $lesson->bible_book }} {{ $lesson->bible_chapter }}{{ $lesson->bible_verses ? ':' . $lesson->bible_verses : '' }}</p>
                        @if($bibleContent && $bibleContent->isNotEmpty())
                            <div class="mt-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl text-sm text-gray-600 dark:text-gray-400 italic">{{ $bibleContent->pluck('text')->join(' ') }}</div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Informações</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Turma</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $lesson->ebdClass->name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Curso</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $lesson->course->name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Data</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $lesson->lesson_date ? $lesson->lesson_date->format('d/m/Y') : '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Horário</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $lesson->lesson_time ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                @php $s = $lesson->status; @endphp
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $s === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : ($s === 'scheduled' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400') }}">{{ $s === 'scheduled' ? 'Agendada' : ($s === 'completed' ? 'Concluída' : ($s === 'in_progress' ? 'Em andamento' : $s)) }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Presença</h3>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $lesson->attendance->where('status', 'present')->count() }} <span class="text-sm font-normal text-gray-500">presentes</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total registros: {{ $lesson->attendance->count() }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
