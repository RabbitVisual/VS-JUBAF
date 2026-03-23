@extends('admin::components.layouts.master')

@section('title', 'Adicionar Membro - Equipe de Intercessão')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Adicionar à Equipe</h1>
            <p class="text-gray-600 dark:text-gray-400">Selecione um usuário para integrar a equipe de intercessão.</p>
        </div>
        <a href="{{ route('admin.intercessor.team.index') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all duration-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            <span>Voltar</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-4xl">
        <form action="{{ route('admin.intercessor.team.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="space-y-4">
                <label for="user_id" class="text-sm font-bold text-gray-700 dark:text-gray-300 block">Selecionar Usuário</label>
                <div class="relative">
                    <select name="user_id" id="user_id" required
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none appearance-none">
                        <option value="">Selecione um usuário da lista...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <x-icon name="chevron-down" class="w-5 h-5 text-gray-400" />
                    </div>
                </div>
                <p class="text-xs text-gray-500 flex items-center italic">
                    <x-icon name="information-circle" class="w-4 h-4 mr-1 text-blue-500" />
                    Apenas usuários registrados podem ser adicionados à equipe.
                </p>
                @error('user_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="p-6 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/30">
                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2 flex items-center">
                    <x-icon name="shield-check" class="w-5 h-5 mr-2" />
                    Permissões Adicionais
                </h4>
                <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                    Membros da equipe de intercessão terão acesso para visualizar pedidos privados (quando permitido pelas configurações) e realizar interações no muro de oração.
                </p>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center px-10 py-4 text-base font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                    <x-icon name="user-add" class="w-6 h-6 mr-2" />
                    Confirmar Adição
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

