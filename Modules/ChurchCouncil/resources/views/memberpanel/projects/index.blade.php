@extends('memberpanel::components.layouts.master')

@section('title', 'Projetos - Diretoria')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
    <!-- Hero Section -->
    <div class="relative bg-linear-to-r from-purple-900 to-purple-800 dark:from-slate-900 dark:to-slate-950 border-b border-purple-200 dark:border-purple-900/30 p-6 md:p-10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-purple-200 dark:text-purple-500 font-bold uppercase tracking-widest text-xs">Ideias e Realizações</p>
                <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                    Projetos da Diretoria
                </h1>
                <p class="text-purple-100 dark:text-slate-400 font-medium max-w-xl">
                    Acompanhe, analise e submeta propostas de projetos para a igreja.
                </p>
            </div>

            <a href="{{ route('memberpanel.churchcouncil.projects.create') }}"
               class="group relative inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-purple-600 hover:bg-purple-50 dark:hover:bg-purple-500 text-purple-700 dark:text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-xl dark:hover:shadow-purple-500/20 hover:-translate-y-0.5 border border-purple-200 dark:border-purple-500/50">
                <x-icon name="plus" class="w-5 h-5 mr-2 text-purple-600 dark:text-white group-hover:scale-110 transition-transform" />
                Nova Proposta
            </a>
        </div>
    </div>

    <!-- Projects List -->
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                    <thead class="bg-gray-50 dark:bg-slate-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">Projeto</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">Proponente</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">Custo Est.</th>
                            <th scope="col" class="relative px-6 py-4"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @forelse($projects as $project)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $project->title }}</span>
                                        <span class="text-xs text-gray-500 dark:text-slate-500 mt-0.5 line-clamp-1 max-w-xs">{{ $project->description }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                        @if($project->status == 'submitted') bg-blue-50 text-blue-700 border-blue-200 dark:text-blue-400 dark:border-blue-500/30 dark:bg-blue-500/10
                                        @elseif($project->status == 'under_review') bg-yellow-50 text-yellow-700 border-yellow-200 dark:text-yellow-400 dark:border-yellow-500/30 dark:bg-yellow-500/10
                                        @elseif($project->status == 'approved') bg-emerald-50 text-emerald-700 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/30 dark:bg-emerald-500/10
                                        @elseif($project->status == 'rejected') bg-red-50 text-red-700 border-red-200 dark:text-red-400 dark:border-red-500/30 dark:bg-red-500/10
                                        @else bg-gray-100 text-gray-600 border-gray-200 dark:text-slate-400 dark:border-slate-600 dark:bg-slate-800 @endif">
                                        {{ $project->status_display ?? ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                                    <div class="flex items-center gap-2">
                                         <div class="w-6 h-6 rounded-full bg-purple-100 dark:bg-slate-800 border border-purple-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-purple-600 dark:text-purple-400">
                                            {{ substr($project->proposer->name, 0, 1) }}
                                         </div>
                                         {{ $project->proposer->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-600 dark:text-slate-300">
                                    R$ {{ number_format($project->estimated_cost, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('memberpanel.churchcouncil.projects.show', $project) }}"
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-bold text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-purple-600 dark:hover:text-white transition-all shadow-sm">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-500 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border border-gray-200 dark:border-slate-700">
                                            <x-icon name="lightbulb" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                                        </div>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum projeto encontrado</p>
                                        <p class="text-gray-500 dark:text-slate-400 mb-6">Seja o primeiro a submeter uma proposta de projeto!</p>
                                        <a href="{{ route('memberpanel.churchcouncil.projects.create') }}"
                                           class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 hover:bg-purple-700 dark:hover:bg-purple-500 text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-purple-500/30 border border-transparent">
                                            Criar Primeira Proposta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

