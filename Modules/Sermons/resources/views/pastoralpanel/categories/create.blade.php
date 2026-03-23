@extends('pastoralpanel::components.layouts.master')

@section('title', 'Criar Categoria')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30">
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200 text-xs font-bold uppercase tracking-wider">Estúdio da Palavra</span>
                <h1 class="text-2xl md:text-3xl font-bold mt-2">Criar Nova Categoria</h1>
                <p class="text-slate-300 mt-1">Adicione uma nova categoria para organizar os sermões</p>
            </div>
            <a href="{{ route('pastor.sermoes.categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600">
                <x-icon name="arrow-left" class="w-5 h-5" />
                Voltar
            </a>
        </div>
    </div>

    <form action="{{ route('pastor.sermoes.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nome da Categoria <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 sm:text-sm @error('name') border-red-500 @enderror"
                            placeholder="Ex: Evangelismo, Doutrina, Vida Cristã">
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
                            class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 sm:text-sm @error('description') border-red-500 @enderror"
                            placeholder="Descrição da categoria">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cor -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cor
                        </label>
                        <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}"
                            class="mt-1 block w-full h-10 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 focus:ring-2 focus:ring-amber-500 sm:text-sm @error('color') border-red-500 @enderror">
                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ícone -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Ícone
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                            class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 sm:text-sm @error('icon') border-red-500 @enderror"
                            placeholder="Ex: book, heart, star">
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ordem -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Ordem
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                            class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 sm:text-sm @error('order') border-red-500 @enderror">
                        @error('order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="flex items-center mt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-slate-600 dark:bg-slate-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativa</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('pastor.sermoes.categories.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium">Criar Categoria</button>
            </div>
    </form>
</div>
@endsection

