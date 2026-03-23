@extends('liderancapanel::components.layouts.master')

@section('title', __('churchcouncil::messages.projects'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('churchcouncil::messages.council') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ __('churchcouncil::messages.projects') }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="diagram-project" class="w-7 h-7 text-amber-500" />
                    {{ __('churchcouncil::messages.projects') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('churchcouncil::messages.project') }}</p>
            </div>
            <a href="{{ route('lideranca.conselho.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('churchcouncil::messages.back') }}
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($projects as $project)
                    <a href="{{ route('lideranca.conselho.projects.show', $project) }}"
                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $project->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $project->proposer->name ?? __('churchcouncil::messages.no_info') }}
                                    @if ($project->ministry)
                                        · {{ $project->ministry->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
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
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @if ($project->status === 'approved' || $project->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($project->status === 'rejected' || $project->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @elseif($project->status === 'under_review' || $project->status === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $statusLabel }}
                                </span>
                                <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="diagram-project" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>Nenhum projeto encontrado.</p>
                    </div>
                @endforelse
            </div>
            @if ($projects->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
