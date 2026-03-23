@extends('memberpanel::components.layouts.master')

@section('title', 'Nova Proposta de Projeto - Diretoria')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Nova Proposta de Projeto</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1">Preencha o formulário abaixo para submeter um novo projeto à diretoria.</p>
            </div>
            <a href="{{ route('memberpanel.churchcouncil.projects.index') }}"
               class="px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-purple-600 dark:hover:text-white transition-colors font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700 flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 md:p-8 transition-colors duration-200">
            <form id="createProjectForm" class="space-y-8">
                @csrf

                <!-- Basic Info -->
                <div>
                     <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 font-bold">
                            1
                        </span>
                        Informações Básicas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Título do Projeto <span class="text-purple-600 dark:text-purple-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600"
                                placeholder="Ex: Reforma da Sala das Crianças">
                        </div>

                        <div>
                            <label for="ministry_id" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Ministério / Comissão
                            </label>
                            <select name="ministry_id" id="ministry_id"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                                <option value="">— Nenhum / Geral —</option>
                                @foreach($ministries ?? [] as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="department" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Departamento (texto livre)
                            </label>
                            <input type="text" name="department" id="department"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600"
                                placeholder="Ex: Outra área">
                        </div>

                        <div>
                            <label for="estimated_cost" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Custo Estimado (R$)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-gray-500 dark:text-slate-500 font-bold">R$</span>
                                <input type="number" step="0.01" min="0" name="estimated_cost" id="estimated_cost"
                                    class="w-full pl-10 px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-bold"
                                    placeholder="0,00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div>
                     <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 font-bold">
                            2
                        </span>
                        Detalhes do Projeto
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label for="description" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Descrição Completa <span class="text-purple-600 dark:text-purple-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 resize-y"
                                placeholder="Descreva o que será feito..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="justification" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                    Justificativa
                                </label>
                                <textarea name="justification" id="justification" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 resize-y"
                                    placeholder="Por que este projeto é necessário?"></textarea>
                            </div>

                            <div>
                                <label for="goals" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                    Objetivos Esperados
                                </label>
                                <textarea name="goals" id="goals" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 resize-y"
                                    placeholder="O que esperamos alcançar?"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                 <div>
                     <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 font-bold">
                            3
                        </span>
                        Cronograma (Estimado)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Data de Início Prevista
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all [color-scheme:light] dark:[color-scheme:dark]">
                        </div>

                        <div>
                            <label for="end_date" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                Data de Conclusão Prevista
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('memberpanel.churchcouncil.projects.index') }}"
                       class="px-6 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white font-bold transition-all text-center">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 dark:hover:bg-purple-500 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-purple-500/30 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        Submeter Projeto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('createProjectForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('{{ route("memberpanel.churchcouncil.projects.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            alert(result.message || 'Erro ao criar projeto');
            // Here you could add more sophisticated error handling, showing validation errors near fields
        }
    } catch (error) {
        console.error(error);
        alert('Erro ao processar solicitação');
    }
});
</script>
@endsection

