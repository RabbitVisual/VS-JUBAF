@extends('liderancapanel::components.layouts.master')

@section('title', $project->title)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('Diretoria::messages.diretoria') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('lideranca.conselho.projects.index') }}"
                        class="hover:text-white transition-colors">{{ __('Diretoria::messages.projects') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ $project->title }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->title }}</h1>
                @php
                    $statusLabels = [
                        'draft' => 'Rascunho',
                        'submitted' => 'Enviado',
                        'under_review' => 'Em análise',
                        'approved' => 'Aprovado',
                        'rejected' => 'Rejeitado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ];
                    $statusLabel = $statusLabels[$project->status] ?? $project->status;
                @endphp
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-2
                @if ($project->status === 'approved' || $project->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                @elseif($project->status === 'rejected' || $project->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                    {{ $statusLabel }}
                </span>
            </div>
            <a href="{{ route('lideranca.conselho.projects.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('Diretoria::messages.back') }}
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 space-y-6">
            @if ($project->description)
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">
                        {{ __('Diretoria::messages.description') }}</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
                </div>
            @endif
            @if ($project->justification)
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Justificativa
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $project->justification }}</p>
                </div>
            @endif
            @if ($project->goals)
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Objetivos</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $project->goals }}</p>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Proponente</p>
                    <p class="text-gray-900 dark:text-white font-medium">
                        {{ $project->proposer->name ?? __('Diretoria::messages.no_info') }}</p>
                </div>
                @if ($project->ministry)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ministério
                        </p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $project->ministry->name }}</p>
                    </div>
                @endif
                @if ($project->reviewer)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revisor</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $project->reviewer->user->name ?? '' }}</p>
                    </div>
                @endif
                @if ($project->start_date)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início</p>
                        <p class="text-gray-900 dark:text-white">{{ $project->start_date->format('d/m/Y') }}</p>
                    </div>
                @endif
                @if ($project->end_date)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fim</p>
                        <p class="text-gray-900 dark:text-white">{{ $project->end_date->format('d/m/Y') }}</p>
                    </div>
                @endif
            </div>
            @if ($project->diretoria_comments)
                <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Parecer da
                        diretoria</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $project->diretoria_comments }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
