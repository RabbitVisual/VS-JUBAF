@extends('admin::components.layouts.master')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Nova Proposta de Projeto</h1>
            <a href="{{ route('admin.churchcouncil.projects.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold text-sm flex items-center">
                <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Voltar
            </a>
        </div>

        <form action="{{ route('admin.churchcouncil.projects.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-8">
            @csrf

            <!-- Basic Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Informações Básicas</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do Projeto *</label>
                        <input type="text" name="title" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Reforma da Sala das Crianças">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ministério / Comissão</label>
                        <select name="ministry_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Nenhum / Geral —</option>
                            @foreach($ministries ?? [] as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Departamento (texto livre)</label>
                        <input type="text" name="department" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Outra área">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data Início (Prevista)</label>
                            <input type="date" name="start_date" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data Fim (Prevista)</label>
                            <input type="date" name="end_date" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Detalhes e Justificativa</h3>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição Detalhada *</label>
                    <textarea name="description" required rows="4" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Descreva o que será feito..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Justificativa</label>
                        <textarea name="justification" rows="3" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Por que este projeto é necessário?"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Objetivos / Metas</label>
                        <textarea name="goals" rows="3" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="O que se espera alcançar?"></textarea>
                    </div>
                </div>
            </div>

            <!-- Financials -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Investimento Estimado</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Custo Total Estimado (R$)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-500 font-bold">R$</span>
                            <input type="number" name="estimated_cost" step="0.01" min="0" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 pl-10 pr-4 focus:ring-blue-500 focus:border-blue-500 font-mono font-bold" placeholder="0,00">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Insira o valor total previsto para execução.</p>
                    </div>
                     <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">
                            <x-icon name="information-circle" class="w-4 h-4 inline mr-1" />
                            Este valor passará por aprovação da diretoria e, se necessário, da assembleia.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black shadow-lg hover:shadow-blue-500/30 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <x-icon name="paper-airplane" class="w-5 h-5" />
                    Submeter Proposta
                </button>
            </div>
        </form>
    </div>
@endsection

