@extends('admin::components.layouts.master')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between gap-4 md:items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('admin.churchcouncil.projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <x-icon name="arrow-left" class="w-6 h-6" />
                    </a>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                        @if($project->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                        @elseif($project->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                        @elseif($project->status == 'under_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ match($project->status) {
                            'submitted' => 'Submetido',
                            'under_review' => 'Em Análise',
                            'approved' => 'Aprovado',
                            'rejected' => 'Rejeitado',
                            'draft' => 'Rascunho',
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado',
                            default => $project->status
                        } }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">#{{ $project->id }}</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $project->title }}</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">{{ $project->ministry?->name ?? $project->department ?? 'Geral' }}</p>
            </div>

            <div class="flex gap-2">
                @if(in_array($project->status, ['draft', 'submitted', 'under_review']))
                    <button type="button" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-2">
                        <x-icon name="pencil" class="w-4 h-4" /> Editar
                    </button>
                @endif
                <form action="{{ route('admin.churchcouncil.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-lg font-bold transition-colors flex items-center gap-2">
                        <x-icon name="trash" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-blue-500" />
                        Descrição do Projeto
                    </h3>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {{ $project->description }}
                    </div>
                </div>

                <!-- Justification & Goals -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Justificativa</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $project->justification ?? 'Não informada.' }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Objetivos</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $project->goals ?? 'Não informados.' }}</p>
                    </div>
                </div>

                <!-- Review Section -->
                 @if($project->council_comments)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl p-6 border border-yellow-100 dark:border-yellow-900/30">
                        <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-400 mb-2 flex items-center gap-2">
                            <x-icon name="chat-alt" class="w-5 h-5" />
                            Parecer da Diretoria
                        </h3>
                         <div class="text-yellow-800 dark:text-yellow-300 italic mb-3">
                            "{{ $project->council_comments }}"
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-yellow-700 dark:text-yellow-500">
                            <span>Revisado por {{ $project->reviewer->user->name ?? 'Membro da Diretoria' }}</span>
                            <span>•</span>
                            <span>{{ $project->reviewed_at ? $project->reviewed_at->format('d/m/Y H:i') : '' }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Meta & Actions -->
            <div class="space-y-6">
                <!-- Financials Card -->
                 <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Investimento</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-medium text-gray-500">R$</span>
                        <span class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($project->estimated_cost, 2, ',', '.') }}</span>
                    </div>
                    @if($project->estimated_cost > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Status Financeiro</span>
                                <span class="font-bold {{ $project->status == 'approved' ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $project->status == 'approved' ? 'Aprovado' : 'Pendente' }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Info Card -->
                 <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <ul class="space-y-4">
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Proponente</span>
                            <div class="flex items-center gap-2">
                                @if($project->proposer->photo)
                                     <img src="{{ asset('storage/' . $project->proposer->photo) }}" class="w-6 h-6 rounded-full object-cover">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gray-200 text-xs flex items-center justify-center font-bold">
                                        {{ substr($project->proposer->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->proposer->name ?? 'Desconhecido' }}</span>
                            </div>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Data de Envio</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->created_at->format('d/m/Y') }}</span>
                        </li>
                        @if($project->start_date)
                            <li class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Início Previsto</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->start_date->format('d/m/Y') }}</span>
                            </li>
                        @endif
                         @if($project->end_date)
                            <li class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Término Previsto</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->end_date->format('d/m/Y') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Action Card (For Council Members) -->
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-3xl p-6 border border-blue-100 dark:border-blue-900/30">
                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-4">Ações da Diretoria</h3>

                    <form action="{{ route('admin.churchcouncil.projects.review', $project) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-blue-800 dark:text-blue-400 uppercase mb-2">Comentários / Parecer</label>
                            <textarea name="comments" rows="3" class="w-full bg-white dark:bg-gray-900 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm focus:ring-blue-500" placeholder="Insira observações sobre a decisão...">{{ old('comments', $project->council_comments) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                             <button type="submit" name="status" value="approved" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-sm transition-colors flex items-center justify-center gap-1">
                                <x-icon name="check" class="w-4 h-4" /> Aprovar
                            </button>
                            <button type="submit" name="status" value="rejected" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-sm transition-colors flex items-center justify-center gap-1">
                                <x-icon name="x" class="w-4 h-4" /> Rejeitar
                            </button>
                        </div>
                        <button type="submit" name="status" value="under_review" class="w-full py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-bold shadow-sm transition-colors flex items-center justify-center gap-1">
                            <x-icon name="search" class="w-4 h-4" /> Marcar como Em Análise
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

