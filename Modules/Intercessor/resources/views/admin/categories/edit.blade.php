@extends('admin::components.layouts.master')

@section('title', 'Editar Categoria - Intercessão')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Categoria</h1>
            <p class="text-gray-600 dark:text-gray-400">Atualize os detalhes da categoria "{{ $category->name }}".</p>
        </div>
        <a href="{{ route('admin.intercessor.categories.index') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all duration-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            <span>Voltar</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-4xl">
        <form action="{{ route('admin.intercessor.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Nome da Categoria</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        placeholder="Ex: Família, Saúde, Gratidão">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Color -->
                <div class="space-y-2">
                    <label for="color" class="text-sm font-bold text-gray-700 dark:text-gray-300">Cor de Identificação</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" id="color" value="{{ old('color', $category->color) }}"
                            class="h-11 w-20 p-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer">
                        <span class="text-xs text-gray-500 font-medium italic">Cor atual: {{ $category->color }}</span>
                    </div>
                    @error('color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="text-sm font-bold text-gray-700 dark:text-gray-300">Breve Descrição (Opcional)</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                    placeholder="Descreva o objetivo desta categoria...">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600">
                <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">Categoria Ativa</label>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                    <x-icon name="check" class="w-6 h-6 mr-2" />
                    Atualizar Categoria
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

