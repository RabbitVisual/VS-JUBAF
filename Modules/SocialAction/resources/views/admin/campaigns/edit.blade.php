@extends('admin::components.layouts.master')

@section('title', 'Editar Campanha | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('socialaction.admin.campaigns.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Campanha</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar campanha: {{ $campaign->title }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('socialaction.admin.campaigns.update', $campaign->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <!-- Header Icon -->
                <div class="flex items-center gap-4 mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
                    <div class="w-12 h-12 rounded-3xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <x-icon name="bullhorn" class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detalhes da Mobilização</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Atualize informações, status e progresso.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status (Toggle) -->
                    <div class="col-span-2 bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 flex items-center justify-between border border-gray-100 dark:border-gray-700">
                        <div>
                            <label for="status" class="block text-sm font-bold text-gray-900 dark:text-white">Status da Campanha</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Campanhas inativas não recebem novas doações.</p>
                        </div>
                        <select name="status" id="status" class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-4 transition-colors">
                            <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="completed" {{ $campaign->status == 'completed' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelled" {{ $campaign->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            <option value="paused" {{ $campaign->status == 'paused' ? 'selected' : '' }}>Pausada</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título da Campanha <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $campaign->title) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4 transition-colors resize-none">{{ old('description', $campaign->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Progress Manual Update -->
                    <div class="col-span-2 border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <x-icon name="chart-line" class="h-4 w-4 mr-2 text-blue-500" />
                            Progresso e Metas
                        </h4>
                    </div>

                    <!-- Vincular à Tesouraria -->
                    <div class="col-span-2">
                        <label for="treasury_campaign_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Campanha da Tesouraria vinculada</label>
                        <select name="treasury_campaign_id" id="treasury_campaign_id"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                            <option value="">Nenhuma – controle manual</option>
                            @foreach($treasuryCampaigns ?? [] as $tc)
                                <option value="{{ $tc->id }}" {{ old('treasury_campaign_id', $campaign->treasury_campaign_id) == $tc->id ? 'selected' : '' }}>
                                    {{ $tc->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($campaign->treasury_campaign_id)
                            <p class="mt-1.5 text-xs text-blue-600 dark:text-blue-400">Arrecadado sincronizado com a Tesouraria. Use "Sincronizar" ao salvar para atualizar.</p>
                        @endif
                        @error('treasury_campaign_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Amount (só quando não vinculado à tesouraria) -->
                    <div class="col-span-2" id="current_amount_wrap">
                        <label for="current_amount" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Arrecadado Atual</label>
                        <input type="number" step="0.01" name="current_amount" id="current_amount" value="{{ old('current_amount', $campaign->current_amount) }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Quando vinculado à Tesouraria, o valor é atualizado ao salvar.</p>
                        @error('current_amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Amount -->
                    <div>
                        <label for="target_amount" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Meta Total</label>
                        <input type="number" step="0.01" name="target_amount" id="target_amount" value="{{ old('target_amount', $campaign->target_amount) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('target_amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Goal Description (Unit) -->
                    <div class="col-span-2">
                        <label for="goal_description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição da Meta (Unidade)</label>
                        <input type="text" name="goal_description" id="goal_description" value="{{ old('goal_description', $campaign->goal_description) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('goal_description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data de Início</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data de Término</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                    <form action="{{ route('socialaction.admin.campaigns.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta campanha?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-bold hover:underline">
                            Excluir Campanha
                        </button>
                    </form>

                    <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <x-icon name="check" class="h-5 w-5 mr-2" />
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

