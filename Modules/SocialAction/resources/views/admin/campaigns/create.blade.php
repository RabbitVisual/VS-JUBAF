@extends('admin::components.layouts.master')

@section('title', 'Nova Campanha | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('socialaction.admin.campaigns.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nova Campanha</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Crie uma nova mobilização de doações.</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('socialaction.admin.campaigns.store') }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <!-- Header Icon -->
                <div class="flex items-center gap-4 mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
                    <div class="w-12 h-12 rounded-3xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <x-icon name="bullhorn" class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detalhes da Mobilização</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Defina o objetivo, meta e prazos.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título da Campanha <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors"
                            placeholder="Ex: Campanha do Agasalho 2026">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4 transition-colors resize-none"
                            placeholder="Descreva o objetivo da campanha e como as pessoas podem ajudar...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Amount -->
                    <div>
                        <label for="target_amount" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Meta (Quantidade) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="target_amount" id="target_amount" value="{{ old('target_amount') }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors"
                            placeholder="0">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Total numérico (ex: 50, 1000).</p>
                        @error('target_amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Goal Description (Unit) -->
                    <div>
                        <label for="goal_description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição da Meta (Unidade)</label>
                        <input type="text" name="goal_description" id="goal_description" value="{{ old('goal_description') }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors"
                            placeholder="Ex: Cestas Básicas, Cobertores, Kg">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Texto exibido junto à meta (ex: 50 Cestas Básicas).</p>
                        @error('goal_description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data de Início</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data de Término (Opcional)</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vincular à Tesouraria -->
                    <div class="col-span-2">
                        <label for="treasury_campaign_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Vincular à campanha da Tesouraria (opcional)</label>
                        <select name="treasury_campaign_id" id="treasury_campaign_id"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                            <option value="">Nenhuma – controle manual do arrecadado</option>
                            @foreach($treasuryCampaigns ?? [] as $tc)
                                <option value="{{ $tc->id }}" {{ old('treasury_campaign_id') == $tc->id ? 'selected' : '' }}>
                                    {{ $tc->name }} (R$ {{ number_format($tc->current_amount ?? 0, 2, ',', '.') }} / R$ {{ number_format($tc->target_amount ?? 0, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Se vincular, o valor arrecadado será sincronizado com a Tesouraria.</p>
                        @error('treasury_campaign_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="status" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" id="status" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Pausada</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <x-icon name="circle-plus" class="h-5 w-5 mr-2" />
                        Publicar Campanha
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

