@extends('admin::components.layouts.master')

@section('title', 'Editar Categoria - Administração')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Categoria</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Atualize: {{ $category->name }}</p>
            </div>
            <a href="{{ route('admin.sermons.categories.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                Voltar
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.sermons.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nome da Categoria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Descrição
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <!-- Cor -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cor
                        </label>
                        <input type="color" name="color" id="color"
                            value="{{ old('color', $category->color ?? '#3B82F6') }}"
                            class="mt-1 block w-full h-10 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 sm:text-sm">
                    </div>

                    <!-- Ícone -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Ícone
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                    </div>

                    <!-- Ordem -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Ordem
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', $category->order) }}"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="flex items-center mt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativa</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.sermons.categories.index') }}"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Atualizar Categoria
                </button>
            </div>
        </form>
    </div>
@endsection

