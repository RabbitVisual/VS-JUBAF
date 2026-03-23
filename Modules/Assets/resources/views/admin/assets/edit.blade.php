@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Item: {{ $asset->code }}</h1>
                    <p class="text-gray-300 max-w-xl">{{ $asset->name }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('assets.admin.assets.show', $asset->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="eye" class="w-5 h-5" />
                        Ver
                    </a>
                    <a href="{{ route('assets.admin.assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('assets.admin.assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Atualizando item...' } }))">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="cube" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dados do Item</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Altere código, categoria, localização e demais campos.</p>
                    </div>
                </div>

                <div class="relative grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código Patrimonial *</label>
                        <input type="text" name="code" id="code" required value="{{ old('code', $asset->code') }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Item *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $asset->name) }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria *</label>
                        <select name="category_id" id="category_id" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Localização Atual *</label>
                        <select name="location_id" id="location_id" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                        <select name="status" id="status" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Disponível</option>
                            <option value="borrowed" {{ old('status', $asset->status) == 'borrowed' ? 'selected' : '' }}>Emprestado</option>
                            <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Em Manutenção</option>
                            <option value="lost" {{ old('status', $asset->status) == 'lost' ? 'selected' : '' }}>Perdido</option>
                            <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Descartado</option>
                        </select>
                    </div>
                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condição</label>
                        <select name="condition" id="condition" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            <option value="new" {{ old('condition', $asset->condition) == 'new' ? 'selected' : '' }}>Novo</option>
                            <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Bom</option>
                            <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Regular</option>
                            <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Ruim</option>
                            <option value="unusable" {{ old('condition', $asset->condition) == 'unusable' ? 'selected' : '' }}>Inutilizável</option>
                        </select>
                    </div>
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Compra</label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="purchase_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor de Compra (R$)</label>
                        <input type="number" step="0.01" name="purchase_value" id="purchase_value" value="{{ old('purchase_value', $asset->purchase_value) }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nota Fiscal</label>
                        <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $asset->invoice_number) }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="photo_path" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto</label>
                        @if($asset->photo_path)
                            <div class="mb-2">
                                <img src="{{ asset($asset->photo_path) }}" alt="Foto do item" class="h-20 w-20 object-cover rounded-xl border border-gray-200 dark:border-gray-600">
                            </div>
                        @endif
                        <input type="file" name="photo_path" id="photo_path" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300">
                    </div>
                </div>

                <div class="relative mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição Detalhada</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('description', $asset->description) }}</textarea>
                </div>

                <div class="relative flex justify-end gap-3 pt-6">
                    <button type="button" onclick="if(confirm('Tem certeza que deseja excluir este item?')) document.getElementById('delete-form').submit();" class="px-6 py-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 font-bold rounded-xl transition-colors">
                        Excluir
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all">
                        Atualizar Item
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('assets.admin.assets.destroy', $asset->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
