@extends('admin::components.layouts.master')

@section('title', 'Novo Item | Estoque')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('socialaction.admin.stock.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Adicionar Item</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Cadastre um novo item na despensa.</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('socialaction.admin.stock.store') }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome do Item <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Arroz, Leite, Cesta Básica"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Unidade <span class="text-red-500">*</span></label>
                        <select name="unit" id="unit" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                            <option value="unit" {{ old('unit') == 'unit' ? 'selected' : '' }}>Unidade (un)</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Quilograma (kg)</option>
                            <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Grama (g)</option>
                            <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Litro (L)</option>
                            <option value="packet" {{ old('unit') == 'packet' ? 'selected' : '' }}>Pacote</option>
                            <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Caixa</option>
                        </select>
                        @error('unit')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Min Quantity -->
                    <div>
                        <label for="min_quantity" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Estoque Mínimo (Alerta)</label>
                        <input type="number" step="0.01" name="min_quantity" id="min_quantity" value="{{ old('min_quantity', 0) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('min_quantity')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Quantity -->
                    <div class="col-span-2">
                        <label for="current_quantity" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Quantidade Inicial</label>
                        <input type="number" step="0.01" name="current_quantity" id="current_quantity" value="{{ old('current_quantity', 0) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                        @error('current_quantity')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <x-icon name="check" class="h-5 w-5 mr-2" />
                        Salvar Item
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

