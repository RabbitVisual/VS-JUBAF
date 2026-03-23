@extends('memberpanel::components.layouts.master')

@section('title', $lesson->title . ' - Lição')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="min-w-0">
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2 flex-wrap">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Lições</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium truncate">{{ Str::limit($lesson->title, 30) }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight truncate">{{ $lesson->title }}</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ $lesson->ebdClass->name }} • {{ $lesson->lesson_date->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                    Voltar
                </a>
                <a href="{{ route('memberpanel.ebd.teacher.attendance.manage', $lesson) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98]">
                    <x-icon name="clipboard-user" class="w-4 h-4" />
                    <span class="hidden sm:inline">Presença</span>
                </a>
            </div>
        </div>

        <!-- Hero -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-4 text-center border border-gray-100 dark:border-slate-700">
                        <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Data / Hora</span>
                        <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ $lesson->lesson_date->format('d/m/Y') }}</span>
                        <span class="block text-xs text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-4 text-center border border-gray-100 dark:border-slate-700">
                        <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Status</span>
                        <span class="block text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $lesson->status_display }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-4 text-center border border-gray-100 dark:border-slate-700">
                        <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Frequência</span>
                        <span class="block text-lg font-bold text-gray-900 dark:text-white">{{ $lesson->attendance->where('status', 'present')->count() }}/{{ $lesson->ebdClass->activeStudents->count() }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl sm:rounded-2xl p-4 text-center border border-gray-100 dark:border-slate-700">
                        <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Recursos</span>
                        <span class="block text-lg font-bold text-gray-900 dark:text-white">{{ $lesson->materials->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($lesson->bible_reference && $bibleContent && $bibleContent->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-8 overflow-hidden">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="book-bible" class="w-5 h-5 text-indigo-500" />
                    {{ $lesson->bible_reference }}
                </h3>
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 sm:p-6 max-h-[360px] overflow-y-auto border border-gray-100 dark:border-slate-700">
                    <div class="space-y-4">
                        @foreach($bibleContent as $verse)
                            <p class="text-gray-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed flex gap-3">
                                <span class="shrink-0 w-7 h-7 rounded-lg bg-indigo-500 text-white text-xs font-bold flex items-center justify-center">{{ $verse->verse }}</span>
                                {{ $verse->text }}
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            <!-- Main content -->
            <div class="lg:col-span-8 space-y-6">
                @foreach(['objective' => 'Objetivo', 'introduction' => 'Introdução', 'development' => 'Resumo', 'conclusion' => 'Conclusão', 'application' => 'Aplicação Prática'] as $field => $label)
                    @if($lesson->$field)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-8 {{ $field === 'application' ? 'border-l-4 border-l-amber-500' : '' }}">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                @if($field === 'objective') <x-icon name="bullseye-arrow" class="w-5 h-5 text-rose-500" />
                                @elseif($field === 'introduction') <x-icon name="door-open" class="w-5 h-5 text-indigo-500" />
                                @elseif($field === 'development') <x-icon name="list-check" class="w-5 h-5 text-blue-500" />
                                @elseif($field === 'conclusion') <x-icon name="flag-checkered" class="w-5 h-5 text-emerald-500" />
                                @else <x-icon name="user-check" class="w-5 h-5 text-amber-500" />
                                @endif
                                {{ $label }}
                            </h3>
                            <div class="text-gray-600 dark:text-slate-300 text-sm leading-relaxed">
                                {!! nl2br(e($lesson->$field)) !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Materials -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <x-icon name="folders" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Biblioteca</h3>
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        @forelse($lesson->materials->sortBy('order') as $material)
                            <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800 transition-all">
                                <span class="block text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase mb-1">{{ $material->type_display }}</span>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-2">{{ $material->title }}</h4>
                                @if($material->url)
                                    <a href="{{ $material->url }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Abrir link <x-icon name="arrow-up-right" class="w-3 h-3" />
                                    </a>
                                @elseif($material->file_path)
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Download <x-icon name="download" class="w-3 h-3" />
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-slate-400 text-center py-4">Nenhum material anexo.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Consolidado -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4">Consolidado</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase">Presentes</span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $lesson->attendance->where('status', 'present')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl border border-rose-100 dark:border-rose-900/30">
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase">Ausentes</span>
                            <span class="text-lg font-bold text-rose-600 dark:text-rose-400">{{ $lesson->attendance->where('status', 'absent')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-900/30">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase">Avaliações</span>
                            <span class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $lesson->evaluations->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
