@extends('admin::components.layouts.master')

@section('title', 'Novo Culto | Worship')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('worship.admin.setlists.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('worship.admin.dashboard') }}" class="hover:underline">Louvor</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <a href="{{ route('worship.admin.setlists.index') }}" class="hover:underline">Cultos</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Novo</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Novo culto</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Defina data, dirigente e depois monte o repertório no gerenciador.</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('worship.admin.setlists.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <x-icon name="calendar" class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dados do evento</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Título, data e dirigente para a equipe.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do evento</label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: Culto de Jovens - Edição Especial"
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data e hora</label>
                        <input type="datetime-local" name="scheduled_at" required value="{{ old('scheduled_at') }}"
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dirigente</label>
                        <select name="leader_id" required class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white appearance-none">
                            <option value="">Selecione o líder...</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>{{ $leader->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Orientações / notas</label>
                        <textarea name="description" rows="4" placeholder="Ex: Foco em músicas de júbilo, ministração após o 3º louvor..."
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 text-gray-700 dark:text-gray-300">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-4">
                <a href="{{ route('worship.admin.setlists.index') }}" class="text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-lg shadow-purple-500/20 transition-all active:scale-[0.98]">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Criar e montar setlist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
