@extends('memberpanel::components.layouts.master')

@section('title', 'Corrigir - ' . $evaluation->student->user->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-4xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('memberpanel.ebd.teacher.evaluations') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Avaliações</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium truncate max-w-[140px] sm:max-w-none">Corrigir</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Corrigir Atividade</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ $evaluation->lesson->title }} • {{ $evaluation->lesson->ebdClass->name }}</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.evaluations') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Voltar
            </a>
        </div>

        <!-- Student card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border-2 border-purple-200 dark:border-purple-800 shrink-0">
                    <img src="{{ $evaluation->student->user->avatar_url }}" alt="{{ $evaluation->student->user->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $evaluation->student->user->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-gray-500 dark:text-slate-400">
                        <span>{{ $evaluation->lesson->ebdClass->name }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-slate-600"></span>
                        <span class="text-purple-600 dark:text-purple-400 font-bold">Aluno</span>
                    </div>
                    @if($evaluation->completed_at)
                        <p class="text-[10px] font-medium text-gray-400 dark:text-slate-500 mt-2 flex items-center gap-1">
                            <x-icon name="clock" class="w-3 h-3" />
                            Entregue em {{ $evaluation->completed_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
                @if($evaluation->status === 'graded')
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl px-6 py-4 text-center shrink-0">
                        <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Nota atual</span>
                        <span class="text-2xl sm:text-3xl font-black {{ $evaluation->score >= 70 ? 'text-emerald-500' : ($evaluation->score >= 50 ? 'text-amber-500' : 'text-rose-500') }}">{{ $evaluation->score }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Respostas enviadas</h3>

            @foreach($evaluation->lesson->questions->sortBy('order') as $index => $question)
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-8 space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase">Questão {{ $index + 1 }}</span>
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg text-[10px] font-bold">{{ $question->points }} pts</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 rounded-lg text-[10px] font-bold">{{ $question->type_display }}</span>
                    </div>
                    <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ $question->question }}</h4>

                    @if($question->type === 'multiple_choice' && $question->options)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($question->options as $option)
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 text-xs font-medium text-gray-600 dark:text-slate-300">
                                    • {{ $option }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <div>
                            <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase flex items-center gap-1 mb-2">
                                <x-icon name="user" class="w-3 h-3" /> Resposta do aluno
                            </span>
                            <div class="p-4 sm:p-6 bg-blue-50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/30 min-h-[80px]">
                                <p class="text-sm font-medium text-gray-900 dark:text-white leading-relaxed">
                                    {{ $evaluation->answers[$question->id] ?? 'O aluno não enviou resposta.' }}
                                </p>
                            </div>
                        </div>
                        @if($question->correct_answer)
                            <div>
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase flex items-center gap-1 mb-2">
                                    <x-icon name="bullseye-arrow" class="w-3 h-3" /> Resposta esperada
                                </span>
                                <div class="p-4 sm:p-6 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 min-h-[80px]">
                                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300 leading-relaxed">{{ $question->correct_answer }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Grade form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-8">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-6">Veredito</h3>

            <form action="{{ route('memberpanel.ebd.teacher.evaluations.grade.store', $evaluation) }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-4">
                        <label for="score" class="block text-xs font-bold text-gray-600 dark:text-slate-400 uppercase mb-2">Pontuação (0–100)</label>
                        <input type="number" name="score" id="score" min="0" max="100" step="0.1" required
                            value="{{ old('score', $evaluation->score) }}"
                            class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-2xl font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 transition-all">
                        @error('score')
                            <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="lg:col-span-8">
                        <label for="feedback" class="block text-xs font-bold text-gray-600 dark:text-slate-400 uppercase mb-2">Feedback</label>
                        <textarea name="feedback" id="feedback" rows="4"
                            class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500/20 transition-all"
                            placeholder="Orientação construtiva para o aluno...">{{ old('feedback', $evaluation->feedback) }}</textarea>
                        @error('feedback')
                            <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('memberpanel.ebd.teacher.evaluations') }}" class="text-sm font-bold text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-purple-600/20 active:scale-[0.98]">
                        <x-icon name="check-double" class="w-4 h-4" />
                        {{ $evaluation->status === 'graded' ? 'Atualizar Nota' : 'Confirmar Avaliação' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
