@extends('admin::components.layouts.master')

@section('title', $lesson->title . ' | Detalhes da Lição')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">{{ $lesson->ebdClass->name }}</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Detalhes da Lição</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $lesson->title }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">
                {{ $lesson->lesson_date->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }} •
                {{ $lesson->bible_reference }} ({{ strtoupper($lesson->bible_version) }})
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.lessons.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
            <a href="{{ route('admin.ebd.lessons.edit', $lesson) }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="pen-to-square" style="duotone" class="mr-2 h-4 w-4" />
                Editar Lição
            </a>
        </div>
    </div>

    <!-- Quick Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="signal" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</span>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border
                @if ($lesson->status === 'scheduled') bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800
                @elseif($lesson->status === 'in_progress') bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800
                @elseif($lesson->status === 'completed') bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800
                @else bg-gray-50 text-gray-500 border-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 @endif">
                {{ $lesson->status_display }}
            </span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-icon name="user-check" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Presenças</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $lesson->attendance->where('status', 'present')->count() }} / {{ $lesson->attendance->count() }}</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <x-icon name="clipboard-list" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Avaliações</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $lesson->evaluations->where('status', 'completed')->count() }} / {{ $lesson->evaluations->count() }}</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <x-icon name="folder-open" style="duotone" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Arquivos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $lesson->materials->count() }}</div>
        </div>
    </div>

    <!-- Bible Content -->
    @if ($lesson->bible_reference)
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-blue-100 dark:border-blue-900/30 shadow-xl overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <x-icon name="book-bible" style="duotone" class="w-32 h-32 text-blue-600" />
        </div>
        <div class="p-8 border-b border-blue-50 dark:border-blue-900/20 bg-blue-50/50 dark:bg-blue-900/10">
            <h3 class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-tighter flex items-center gap-2">
                <x-icon name="book-open-reader" style="duotone" class="w-5 h-5" />
                Texto Áureo & Leitura: {{ $lesson->bible_reference }}
            </h3>
        </div>
        <div class="p-8">
            @if ($bibleContent && $bibleContent->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6 max-h-80 overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-blue-200 dark:scrollbar-thumb-blue-900">
                    @foreach ($bibleContent as $verse)
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[10px] font-black mr-2">{{ $verse->verse }}</span>
                            {{ $verse->text }}
                        </p>
                    @endforeach
                </div>
            @else
                <div class="p-6 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-800 flex items-center gap-4">
                    <x-icon name="triangle-exclamation" style="duotone" class="w-6 h-6 text-amber-600" />
                    <div>
                        <h4 class="text-sm font-black text-amber-900 dark:text-amber-400 uppercase tracking-widest">Conteúdo Bíblico Indisponível</h4>
                        <p class="text-[10px] font-bold text-amber-700 dark:text-amber-500 uppercase tracking-tighter">Sincronize o módulo Bible para habilitar a leitura automática.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pedagogical Content -->
        <div class="space-y-6">
            @if ($lesson->objective)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <x-icon name="bullseye-arrow" style="duotone" class="w-4 h-4 text-red-500" />
                    Objetivo da Lição
                </h3>
                <div class="text-lg font-black text-gray-900 dark:text-white leading-tight tracking-tight uppercase">{{ $lesson->objective }}</div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8 space-y-8">
                @if ($lesson->introduction)
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-icon name="door-open" style="duotone" class="w-4 h-4 text-blue-500" />
                        Introdução
                    </h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium whitespace-pre-line">{{ $lesson->introduction }}</div>
                </div>
                @endif

                @if ($lesson->development)
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-icon name="chess-piece" style="duotone" class="w-4 h-4 text-purple-500" />
                        Desenvolvimento
                    </h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium whitespace-pre-line">{{ $lesson->development }}</div>
                </div>
                @endif

                @if ($lesson->conclusion)
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-icon name="check-double" style="duotone" class="w-4 h-4 text-green-500" />
                        Conclusão
                    </h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium whitespace-pre-line">{{ $lesson->conclusion }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Management & Media -->
        <div class="space-y-8">
            <!-- Materials Section -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Materiais Auxiliares</h3>
                    <a href="{{ route('admin.ebd.lessons.materials.create', $lesson) }}" class="p-2 bg-blue-600 text-white rounded-lg hover:scale-105 transition-all">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                    </a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse ($lesson->materials->sortBy('order') as $material)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl border border-transparent hover:border-blue-500/20 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    @php
                                        $icon = match($material->type) {
                                            'video' => 'video',
                                            'pdf' => 'file-pdf',
                                            'image' => 'image',
                                            'link' => 'link',
                                            default => 'file-lines'
                                        };
                                        $color = match($material->type) {
                                            'video' => 'text-red-500',
                                            'pdf' => 'text-orange-500',
                                            'image' => 'text-purple-500',
                                            'link' => 'text-blue-500',
                                            default => 'text-gray-500'
                                        };
                                    @endphp
                                    <x-icon name="{{ $icon }}" style="duotone" class="w-5 h-5 {{ $color }}" />
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight truncate max-w-[150px]">{{ $material->title }}</h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[9px] font-bold text-gray-400 underline uppercase tracking-tighter">{{ $material->type_display }}</span>
                                        @if($material->is_required)
                                            <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                            <span class="text-[8px] font-black text-red-500 uppercase">Obrigatório</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if ($material->url || $material->file_path)
                                    <a href="{{ $material->url ?? asset('storage/' . $material->file_path) }}" target="_blank"
                                       class="p-2 text-gray-400 hover:text-blue-600 transition-colors tooltip" title="Abrir">
                                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                                <a href="{{ route('admin.ebd.lessons.materials.edit', [$lesson, $material]) }}"
                                   class="p-2 text-gray-400 hover:text-indigo-600 transition-colors tooltip" title="Editar">
                                    <x-icon name="pen" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.ebd.lessons.materials.destroy', [$lesson, $material]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors tooltip" title="Excluir"
                                            onclick="return confirm('Excluir este material?')">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nenhum material anexado</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Questions Section -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Questions & Quiz</h3>
                    <a href="{{ route('admin.ebd.lessons.questions.create', $lesson) }}" class="p-2 bg-indigo-600 text-white rounded-lg hover:scale-105 transition-all">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                    </a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse ($lesson->questions->sortBy('order') as $index => $question)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl border-l-4 border-l-indigo-600 group transition-all hover:translate-x-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/20 px-2 py-0.5 rounded">Questão {{ $index + 1 }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $question->points }} XP</span>
                                    <a href="{{ route('admin.ebd.lessons.questions.edit', [$lesson, $question]) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                        <x-icon name="pen" class="w-3 h-3" />
                                    </a>
                                </div>
                            </div>
                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight">{{ $question->question }}</h4>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nenhuma questão cadastrada</p>
                        </div>
                    @endforelse

                    @if ($lesson->questions->count() > 0 && $lesson->ebdClass->activeStudents->count() > 0)
                        <form action="{{ route('admin.ebd.evaluations.create-from-lesson') }}" method="POST" class="pt-4 mt-4 border-t border-gray-50 dark:border-gray-800">
                            @csrf
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                            <button type="submit" class="w-full py-3 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100 dark:border-indigo-800">
                                Gerar Avaliações para {{ $lesson->ebdClass->activeStudents->count() }} Alunos
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

