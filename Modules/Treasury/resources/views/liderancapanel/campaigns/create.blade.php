@extends('liderancapanel::components.layouts.master')

@section('title', 'Nova Campanha')

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
                        <a href="{{ route('lideranca.tesouraria.campaigns.index') }}"
                            class="hover:text-white transition-colors">Campanhas</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Nova campanha</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Nova campanha</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Crie uma frente de arrecadação.</p>
                </div>
                <a href="{{ route('lideranca.tesouraria.campaigns.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('lideranca.tesouraria.campaigns.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6" x-data
                x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando campanha...' } }))">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            placeholder="Ex: Reforma do Templo"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('name')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="slug"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug
                            (opcional)</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            placeholder="reforma-do-templo"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('slug')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="description"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrição</label>
                    <textarea name="description" id="description" rows="4" placeholder="Objetivo da campanha..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="target_amount"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta
                            (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">R$</span>
                            <input type="number" name="target_amount" id="target_amount" step="0.01" min="0"
                                value="{{ old('target_amount') }}"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        @error('target_amount')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="start_date"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('start_date')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="end_date"
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Término</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        @error('end_date')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="image"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capa
                        (imagem)</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    @error('image')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/30 rounded-xl">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                    <label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">Campanha ativa
                        (visível para doações)</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ route('lideranca.tesouraria.campaigns.index') }}"
                        class="px-6 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">Cancelar</a>
                    <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-all inline-flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4" /> Criar campanha
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
