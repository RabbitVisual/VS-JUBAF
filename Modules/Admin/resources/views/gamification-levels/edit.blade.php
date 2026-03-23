@extends('admin::components.layouts.master')

@section('title', 'Editar Nível de Gamificação')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Gamificação</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Editar</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Nível</h1>
                <p class="text-gray-300 max-w-xl">Altere as informações do nível <strong>{{ $gamificationLevel->name }}</strong>. Pontos e ordem afetam a progressão dos membros.</p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.gamification-levels.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
        <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12"></div>
        <div class="relative p-8">
            <form action="{{ route('admin.gamification-levels.update', $gamificationLevel) }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando nível...' } }))">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Nível <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $gamificationLevel->name) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none transition-all">{{ old('description', $gamificationLevel->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ícone <span class="text-red-500">*</span></label>
                            <select id="icon" name="icon" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                                @foreach($icons as $iconKey => $iconName)
                                    <option value="{{ $iconKey }}" {{ old('icon', $gamificationLevel->icon) == $iconKey ? 'selected' : '' }}>{{ $iconName }}</option>
                                @endforeach
                            </select>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor <span class="text-red-500">*</span></label>
                            <select id="color" name="color" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                                <option value="blue" {{ old('color', $gamificationLevel->color) == 'blue' ? 'selected' : '' }}>Azul</option>
                                <option value="green" {{ old('color', $gamificationLevel->color) == 'green' ? 'selected' : '' }}>Verde</option>
                                <option value="yellow" {{ old('color', $gamificationLevel->color) == 'yellow' ? 'selected' : '' }}>Amarelo</option>
                                <option value="purple" {{ old('color', $gamificationLevel->color) == 'purple' ? 'selected' : '' }}>Roxo</option>
                                <option value="red" {{ old('color', $gamificationLevel->color) == 'red' ? 'selected' : '' }}>Vermelho</option>
                                <option value="gray" {{ old('color', $gamificationLevel->color) == 'gray' ? 'selected' : '' }}>Cinza</option>
                            </select>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="points_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pontos Mínimos <span class="text-red-500">*</span></label>
                            <input type="number" id="points_min" name="points_min" required min="0" value="{{ old('points_min', $gamificationLevel->points_min) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            @error('points_min')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe máximo vazio para "ou mais".</p>
                        </div>
                        <div>
                            <label for="points_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pontos Máximos</label>
                            <input type="number" id="points_max" name="points_max" min="0" value="{{ old('points_max', $gamificationLevel->points_max) }}" placeholder="Opcional"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            @error('points_max')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordem</label>
                            <input type="number" id="order" name="order" min="0" value="{{ old('order', $gamificationLevel->order) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Define a sequência na listagem.</p>
                        </div>
                        <div class="flex items-center pt-8">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gamificationLevel->is_active) ? 'checked' : '' }}
                                    class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nível ativo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('admin.gamification-levels.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        <x-icon name="check" class="w-5 h-5" />
                        Atualizar Nível
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
