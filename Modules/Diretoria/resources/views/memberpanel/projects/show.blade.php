@extends('memberpanel::components.layouts.master')

@section('title', 'Detalhes do Projeto - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Detalhes do Projeto</h1>
                        @if ($project->status == 'submitted')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 uppercase tracking-wide">Submetido</span>
                        @elseif($project->status == 'under_review')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-500 dark:border-yellow-500/20 uppercase tracking-wide">Em
                                Revisão</span>
                        @elseif($project->status == 'approved')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 uppercase tracking-wide">Aprovado</span>
                        @elseif($project->status == 'rejected')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 uppercase tracking-wide">Rejeitado</span>
                        @endif
                    </div>
                    <p class="text-gray-500 dark:text-slate-400">Visualizando informações do projeto.</p>
                </div>
                <a href="{{ route('memberpanel.Diretoria.projects.index') }}"
                    class="px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-purple-600 dark:hover:text-white transition-colors font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700 flex items-center justify-center sm:w-auto w-full">
                    <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                    Voltar
                </a>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Project Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-8 relative overflow-hidden transition-colors duration-200">
                        <div class="absolute top-0 right-0 p-6 opacity-5">
                            <x-icon name="lightbulb" class="w-64 h-64 text-purple-600 dark:text-purple-500" />
                        </div>

                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6 relative z-10">
                            {{ $project->title }}</h2>

                        <div class="prose prose-invert max-w-none text-gray-600 dark:text-slate-300 relative z-10">
                            <h3
                                class="text-sm font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mt-0 mb-3">
                                Descrição</h3>
                            <p
                                class="leading-relaxed text-sm text-gray-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-950/30 p-4 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                {{ $project->description }}</p>

                            @if ($project->justification)
                                <h3
                                    class="text-sm font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mt-6 mb-3">
                                    Justificativa</h3>
                                <p
                                    class="leading-relaxed text-sm text-gray-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-950/30 p-4 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                    {{ $project->justification }}</p>
                            @endif

                            @if ($project->goals)
                                <h3
                                    class="text-sm font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mt-6 mb-3">
                                    Objetivos</h3>
                                <p
                                    class="leading-relaxed text-sm text-gray-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-950/30 p-4 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                    {{ $project->goals }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Review Feedback (if available) -->
                    @if ($project->diretoria_comments)
                        <div
                            class="bg-blue-50 dark:bg-blue-900/10 rounded-2xl p-6 border border-blue-100 dark:border-blue-500/20">
                            <h3
                                class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-3 flex items-center uppercase tracking-widest">
                                <x-icon name="comment" class="w-4 h-4 mr-2" />
                                Parecer da Diretoria
                            </h3>
                            <p class="text-blue-800 dark:text-blue-200/80 italic leading-relaxed">
                                "{{ $project->diretoria_comments }}"
                            </p>
                            @if ($project->reviewer)
                                <p
                                    class="text-xs text-blue-600/70 dark:text-blue-500/60 mt-3 font-bold uppercase tracking-wide">
                                    Revisado por: {{ $project->reviewer->user->name }} em
                                    {{ $project->reviewed_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <!-- Meta Data -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                        <h3 class="text-xs font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-6">
                            Informações Gerais</h3>

                        <div class="space-y-5">
                            <div>
                                <span
                                    class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wide">Proponente</span>
                                <div class="flex items-center mt-2">
                                    @if ($project->proposer->photo)
                                        <img src="{{ asset('storage/' . $project->proposer->photo) }}" alt=""
                                            class="h-8 w-8 rounded-full mr-3 object-cover border border-gray-200 dark:border-slate-700">
                                    @else
                                        <div
                                            class="h-8 w-8 rounded-full bg-purple-100 dark:bg-slate-800 text-purple-600 dark:text-purple-500 flex items-center justify-center mr-3 text-xs font-black border border-purple-200 dark:border-slate-700">
                                            {{ substr($project->proposer->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->proposer->name }}</span>
                                </div>
                            </div>

                            @if ($project->ministry)
                                <div>
                                    <span
                                        class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wide">Ministério
                                        / Comissão</span>
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white mt-1 block">{{ $project->ministry->name }}</span>
                                </div>
                            @endif
                            @if ($project->department)
                                <div>
                                    <span
                                        class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wide">Departamento</span>
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white mt-1 block">{{ $project->department }}</span>
                                </div>
                            @endif

                            @if ($project->estimated_cost)
                                <div>
                                    <span
                                        class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wide">Custo
                                        Estimado</span>
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 block mt-1">
                                        R$ {{ number_format($project->estimated_cost, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div>
                                <span
                                    class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wide">Data
                                    de Submissão</span>
                                <span class="text-sm font-medium text-gray-600 dark:text-slate-300 mt-1 block">
                                    {{ $project->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    @if ($project->start_date || $project->end_date)
                        <div
                            class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                            <h3
                                class="text-xs font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-4">
                                Cronograma</h3>

                            <div class="space-y-4">
                                @if ($project->start_date)
                                    <div
                                        class="flex justify-between items-center pb-2 border-b border-gray-100 dark:border-slate-800">
                                        <span
                                            class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase">Início</span>
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->start_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                                @if ($project->end_date)
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase">Término</span>
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->end_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
