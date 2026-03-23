@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Registrar Manutenção</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Registrar Manutenção</h1>
                    <p class="text-gray-300 max-w-xl">Agende ou registre reparos e serviços em itens do patrimônio.</p>
                </div>
                <a href="{{ route('assets.admin.maintenances.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('assets.admin.maintenances.store') }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando manutenção...' } }))">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <x-icon name="screwdriver-wrench" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dados da Manutenção</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Item, descrição, fornecedor e datas.</p>
                    </div>
                </div>
                <div class="relative space-y-6">
            <div>
                <label for="asset_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item de Patrimônio</label>
                <select name="asset_id" id="asset_id" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione o item...</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->code }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
                @error('asset_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição do Problema/Serviço *</label>
                <textarea name="description" id="description" rows="3" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fornecedor/Prestador</label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custo Estimado (R$)</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Início *</label>
                    <input type="date" name="start_date" id="start_date" required value="{{ old('start_date', now()->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="expected_return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Previsão de Retorno</label>
                    <input type="date" name="expected_return_date" id="expected_return_date" value="{{ old('expected_return_date') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                <select name="status" id="status" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="scheduled">Agendado</option>
                    <option value="in_progress">Em Progresso</option>
                    <option value="completed">Concluído</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all">
                    Agendar Manutenção
                </button>
            </div>
                </div>
            </div>
        </form>
    </div>
@endsection

