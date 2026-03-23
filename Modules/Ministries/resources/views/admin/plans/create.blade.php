@extends('admin::components.layouts.master')

@section('title', 'Novo Plano - ' . $ministry->name)

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Plano</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">{{ $ministry->name }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Novo Plano de Ação</h1>
                    <p class="text-gray-300 max-w-xl">Ministério: {{ $ministry->name }}</p>
                </div>
                <a href="{{ route('admin.ministries.show', $ministry) }}" class="flex-shrink-0 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="arrow-left" class="w-5 h-5 text-blue-600" /> Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('admin.ministries.plans.store', $ministry) }}" method="POST" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando plano...' } }))">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Título do plano <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="period_year" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ano <span class="text-red-500">*</span></label>
                        <input type="number" name="period_year" id="period_year" value="{{ old('period_year', date('Y')) }}" min="2020" max="2030" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                        @error('period_year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="period_type" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipo de período <span class="text-red-500">*</span></label>
                        <select name="period_type" id="period_type" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                            <option value="annual" {{ old('period_type') === 'annual' ? 'selected' : '' }}>Anual</option>
                            <option value="semiannual" {{ old('period_type') === 'semiannual' ? 'selected' : '' }}>Semestral</option>
                            <option value="quarterly" {{ old('period_type') === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                            <option value="monthly" {{ old('period_type') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        </select>
                        @error('period_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="period_start" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Início <span class="text-red-500">*</span></label>
                        <input type="date" name="period_start" id="period_start" value="{{ old('period_start') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                        @error('period_start')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="period_end" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Fim <span class="text-red-500">*</span></label>
                        <input type="date" name="period_end" id="period_end" value="{{ old('period_end') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                        @error('period_end')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="objectives" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Objetivos</label>
                        <textarea name="objectives" id="objectives" rows="4" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">{{ old('objectives') }}</textarea>
                    </div>
                    <div>
                        <label for="budget_requested" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Orçamento solicitado (R$)</label>
                        <input type="number" name="budget_requested" id="budget_requested" value="{{ old('budget_requested') }}" step="0.01" min="0" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">
                        @error('budget_requested')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="budget_notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Observações sobre orçamento</label>
                        <textarea name="budget_notes" id="budget_notes" rows="2" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white">{{ old('budget_notes') }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.ministries.show', $ministry) }}" class="px-5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-bold">Cancelar</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 flex items-center">
                        <x-icon name="floppy-disk" class="w-5 h-5 mr-2" />
                        Criar rascunho
                    </button>
                </div>
                </div>
            </div>
        </form>
    </div>
@endsection
