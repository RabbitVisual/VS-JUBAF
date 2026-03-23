@extends('pastoralpanel::components.layouts.master')

@section('title', 'Avaliação - EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ebd.evaluations.index') }}" class="hover:text-white">Avaliações</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $evaluation->lesson->title ?? 'Avaliação' }}</span>
                    </nav>
                    <p class="text-slate-300 text-sm">Aluno: {{ $evaluation->student->user->name ?? '—' }} · Turma: {{ $evaluation->lesson->ebdClass->name ?? '—' }}</p>
                </div>
                <a href="{{ route('pastor.ebd.evaluations.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Resumo</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Aluno</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $evaluation->student->user->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Lição</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $evaluation->lesson->title ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Turma</dt><dd class="font-medium text-gray-900 dark:text-white">{{ $evaluation->lesson->ebdClass->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Nota</dt><dd class="font-bold text-amber-600 dark:text-amber-400">{{ $evaluation->score !== null ? $evaluation->score : '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Status</dt><dd><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $evaluation->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' }}">{{ $evaluation->status === 'graded' ? 'Corrigida' : 'Pendente' }}</span></dd></div>
                    @if($evaluation->graded_at)
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Corrigida em</dt><dd class="text-gray-600 dark:text-gray-400">{{ $evaluation->graded_at->format('d/m/Y H:i') }}</dd></div>
                    @endif
                </dl>
            </div>
            @if($evaluation->feedback)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Feedback</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $evaluation->feedback }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
