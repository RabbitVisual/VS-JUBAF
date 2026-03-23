@extends('liderancapanel::components.layouts.master')

@section('title', 'Editar: ' . $goal->name)

@section('content')
    <div class="space-y-6">
        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <a href="{{ route('lideranca.tesouraria.goals.index') }}"
                            class="hover:text-white transition-colors">Metas</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Editar</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Editar meta</h1>
                    <p class="text-slate-300 text-sm max-w-xl">{{ $goal->name }}</p>
                </div>
                <a href="{{ route('lideranca.tesouraria.goals.show', $goal) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('lideranca.tesouraria.goals.update', $goal) }}" method="POST" class="p-6 space-y-6"
                x-data
                x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Atualizando...' } }))">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label for="name"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome da meta
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $goal->name) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    @error('name')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label for="description"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrição /
                        Propósito</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('description', $goal->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="type"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo
                            <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="monthly" {{ old('type', $goal->type) === 'monthly' ? 'selected' : '' }}>Mensal
                            </option>
                            <option value="yearly" {{ old('type', $goal->type) === 'yearly' ? 'selected' : '' }}>Anual
                            </option>
                            <option value="campaign" {{ old('type', $goal->type) === 'campaign' ? 'selected' : '' }}>
                                Campanha</option>
                            <option value="custom" {{ old('type', $goal->type) === 'custom' ? 'selected' : '' }}>
                                Personalizada</option>
                        </select>
                        @error('type')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="category"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria
                            base</label>
                        <input type="text" name="category" id="category" value="{{ old('category', $goal->category) }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all"
                            placeholder="tithe, offering, etc.">
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="campaign_id"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campanha
                        vinculada (opcional)</label>
                    <select name="campaign_id" id="campaign_id"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <option value="">Nenhuma</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}"
                                {{ old('campaign_id', $goal->campaign_id) == $campaign->id ? 'selected' : '' }}>
                                {{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="target_amount"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor
                            objetivo (R$) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">R$</span>
                            <input type="number" name="target_amount" id="target_amount" step="0.01" min="0.01"
                                required value="{{ old('target_amount', $goal->target_amount) }}"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        @error('target_amount')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="start_date"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início
                            <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="start_date" required
                            value="{{ old('start_date', $goal->start_date?->toDateString()) }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('start_date')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="end_date"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Término
                            <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date" required
                            value="{{ old('end_date', $goal->end_date?->toDateString()) }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('end_date')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/30 rounded-xl">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $goal->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                    <label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">Meta ativa</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ route('lideranca.tesouraria.goals.show', $goal) }}"
                        class="px-6 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">Cancelar</a>
                    <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-all inline-flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4" /> Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
