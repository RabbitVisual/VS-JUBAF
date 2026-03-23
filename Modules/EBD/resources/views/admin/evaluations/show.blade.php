@extends('admin::components.layouts.master')

@section('title', 'Avaliação | ' . $evaluation->student->user->name)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Avaliação Acadêmica</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Registro de <span class="text-amber-600">Performance</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">
                Análise detalhada da lição: <span class="text-gray-900 dark:text-white font-bold text-sm uppercase tracking-tight">{{ $evaluation->lesson->title }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.evaluations.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <x-icon name="user-graduate" style="duotone" class="w-24 h-24 text-gray-900 dark:text-white" />
            </div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Estudante</span>
            <div class="text-lg font-black text-gray-900 dark:text-white truncate uppercase">{{ $evaluation->student->user->name }}</div>
            <div class="text-[10px] font-bold text-blue-600 uppercase">{{ $evaluation->student->ebdClass->name }}</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Status da Atividade</span>
            <div class="inline-flex">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border
                    @if ($evaluation->status === 'completed') bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800
                    @elseif($evaluation->status === 'graded') bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800
                    @else bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800 @endif">
                    {{ $evaluation->status_display }}
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Score Final</span>
            <div class="text-3xl font-black {{ $evaluation->score >= 70 ? 'text-green-600' : ($evaluation->score > 0 ? 'text-amber-600' : 'text-gray-400') }}">
                {{ $evaluation->score ?? 'PENDENTE' }}
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Conclusão</span>
            <div class="text-sm font-black text-gray-900 dark:text-white uppercase">
                {{ $evaluation->completed_at ? $evaluation->completed_at->format('d/m/Y') : '--/--/----' }}
            </div>
            <div class="text-[9px] font-bold text-gray-400 uppercase">{{ $evaluation->completed_at ? $evaluation->completed_at->format('H:i') : '' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Answers) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden pb-8">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Respostas Enviadas</h3>
                    <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-[9px] font-black text-gray-500 rounded tracking-widest uppercase">
                        {{ count($evaluation->answers ?? []) }} Questões
                    </span>
                </div>

                @if ($evaluation->answers && count($evaluation->answers) > 0)
                    <div class="p-8 space-y-8">
                        @foreach ($evaluation->lesson->questions as $index => $question)
                            @php $answer = $evaluation->answers[$question->id] ?? null; @endphp
                            <div class="relative pl-8 border-l-2 {{ $answer ? 'border-amber-500/30' : 'border-red-500/30' }}">
                                <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-white dark:bg-slate-900 border-2 {{ $answer ? 'border-amber-500' : 'border-red-500' }} flex items-center justify-center text-[9px] font-black text-gray-900 dark:text-white">
                                    {{ $index + 1 }}
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $question->question }}</h4>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $question->points }} pts</span>
                                        </div>

                                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-transparent group-hover:border-amber-500/20 transition-all">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium italic">"{{ $answer ?? 'Não respondida' }}"</p>
                                        </div>

                                        @if ($question->answer)
                                            <div class="mt-3 flex items-start gap-2 text-green-600 dark:text-green-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 mt-0.5" />
                                                <div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest block">Gabarito Esperado:</span>
                                                    <p class="text-[11px] font-bold leading-tight">{{ $question->answer }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <x-icon name="file-circle-exclamation" style="duotone" class="w-16 h-16 text-gray-200 mx-auto mb-4" />
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nenhuma resposta disponível para esta avaliação.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar (Grading & Info) -->
        <div class="space-y-8">
            <!-- Lesson Info Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Detalhes da Lição</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Título</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $evaluation->lesson->title }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Data Realizada</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $evaluation->lesson->lesson_date->format('d/m/Y') }}</span>
                    </div>
                    @if ($evaluation->lesson->bible_reference)
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Referência Bíblica</span>
                            <span class="text-sm font-black text-blue-600 uppercase">{{ $evaluation->lesson->bible_reference }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Grade Form -->
            @if ($evaluation->status === 'completed')
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-amber-50/50 dark:bg-amber-900/10">
                        <h3 class="text-[10px] font-black text-amber-900 dark:text-amber-400 uppercase tracking-widest flex items-center gap-2">
                            <x-icon name="pen-nib" class="w-3 h-3" />
                            Correção do Professor
                        </h3>
                    </div>
                    <form action="{{ route('admin.ebd.evaluations.grade', $evaluation) }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Score de Performance (0-100)</label>
                            <input type="number" name="score" min="0" max="100" step="1" value="{{ old('score', $evaluation->score) }}" required
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-2xl font-black text-center focus:ring-2 focus:ring-amber-500/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Feedback Pedagógico</label>
                            <textarea name="feedback" rows="4" placeholder="Algum comentário sobre o desempenho..."
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('feedback', $evaluation->feedback) }}</textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl hover:scale-[1.02] active:scale-95">
                            Salvar Correção
                        </button>
                    </form>
                </div>
            @endif

            <!-- Feedback Display (If Already Graded) -->
            @if ($evaluation->feedback && $evaluation->status === 'graded')
                <div class="bg-indigo-600 rounded-3xl p-8 shadow-xl shadow-indigo-600/20 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <x-icon name="quote-right" class="w-32 h-32 text-white" />
                    </div>
                    <h3 class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-4">Feedback Registrado</h3>
                    <p class="text-sm font-medium text-white leading-relaxed italic mb-6">"{{ $evaluation->feedback }}"</p>
                    @if ($evaluation->gradedBy)
                        <div class="flex items-center gap-3 border-t border-indigo-500/30 pt-4">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/50 flex items-center justify-center text-[10px] font-black text-white">
                                {{ substr($evaluation->gradedBy->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-[9px] font-black text-indigo-200 uppercase tracking-widest block">Corrigido por</span>
                                <span class="text-[10px] font-bold text-white uppercase">{{ $evaluation->gradedBy->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

