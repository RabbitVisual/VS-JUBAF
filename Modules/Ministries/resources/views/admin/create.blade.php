@extends('admin::components.layouts.master')

@section('title', 'Criar Novo Ministério')

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Ministérios</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Novo</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Criar Novo Ministério</h1>
                    <p class="text-gray-300 max-w-xl">Preencha os dados abaixo para fundar um novo ministério na congregação.</p>
                </div>
                <a href="{{ route('admin.ministries.index') }}"
                    class="flex-shrink-0 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="arrow-left" class="w-5 h-5 text-blue-600" />
                    Voltar à Lista
                </a>
            </div>
        </div>

        <form action="{{ route('admin.ministries.store') }}" method="POST" x-data="ministryCreateForm()" x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }))">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- 1. Informações Básicas -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="circle-info" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informações Básicas</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nome e propósito do ministério</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Ministério <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="Ex: Ministério de Louvor, EBD, Ação Social...">
                                @error('name')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição / Propósito</label>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="Descreva em poucas linhas o objetivo e o público deste ministério...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Liderança -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="user-tie" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Liderança Inicial</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Quem vai coordenar este ministério</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Líder Responsável</label>
                                <select name="leader_id" id="leader_id"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">Selecione um líder...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                                <div>
                                    <label for="co_leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Co-Líder (opcional)</label>
                                <select name="co_leader_id" id="co_leader_id"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">Nenhum</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('co_leader_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna lateral: Aparência e Config -->
                <div class="space-y-6">
                    <!-- 3. Ícone: escolha visual -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <x-icon name="palette" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Identidade visual</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ícone e cor do ministério</p>
                                </div>
                            </div>

                        <input type="hidden" name="icon" id="icon" value="{{ old('icon', 'fa:church') }}">

                            <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Escolha um ícone</p>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mb-4">
                            @php
                                $faIcons = ['church', 'music', 'book-bible', 'hands-praying', 'heart', 'users', 'graduation-cap', 'children', 'hand-holding-heart', 'microphone-lines', 'video', 'bullhorn', 'mug-hot', 'utensils', 'shirt', 'star'];
                            @endphp
                            @foreach($faIcons as $faIcon)
                                <button type="button"
                                    class="aspect-square rounded-xl border-2 flex items-center justify-center transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 icon-picker-btn {{ (old('icon') === 'fa:' . $faIcon) || (!old('icon') && $faIcon === 'church') ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 text-gray-500 dark:text-gray-400' }}"
                                    data-value="fa:{{ $faIcon }}"
                                    title="{{ $faIcon }}">
                                    <x-icon name="{{ $faIcon }}" class="w-6 h-6" />
                                </button>
                            @endforeach
                        </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ou use um emoji (opcional):</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['⛪', '🎵', '📖', '🙏', '❤️', '👥', '🎓', '👶', '🎤', '📺', '☕', '👕', '⭐'] as $emoji)
                                <button type="button"
                                    class="w-10 h-10 rounded-xl border-2 flex items-center justify-center text-xl transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 emoji-picker-btn {{ old('icon') === $emoji ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}"
                                    data-value="{{ $emoji }}">
                                    {{ $emoji }}
                                </button>
                            @endforeach
                        </div>
                        </div>
                    </div>

                    <!-- 4. Cor temática: swatches -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Cor temática</p>
                        <div class="grid grid-cols-4 gap-2">
                            @php
                                $colorOptions = [
                                    'blue' => ['bg' => 'bg-blue-500', 'ring' => 'ring-blue-500'],
                                    'green' => ['bg' => 'bg-green-500', 'ring' => 'ring-green-500'],
                                    'red' => ['bg' => 'bg-red-500', 'ring' => 'ring-red-500'],
                                    'yellow' => ['bg' => 'bg-amber-500', 'ring' => 'ring-amber-500'],
                                    'purple' => ['bg' => 'bg-purple-500', 'ring' => 'ring-purple-500'],
                                    'pink' => ['bg' => 'bg-pink-500', 'ring' => 'ring-pink-500'],
                                    'indigo' => ['bg' => 'bg-indigo-500', 'ring' => 'ring-indigo-500'],
                                ];
                            @endphp
                            @foreach($colorOptions as $value => $classes)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="color" value="{{ $value }}" {{ old('color', 'blue') === $value ? 'checked' : '' }} class="sr-only peer color-radio">
                                    <span class="flex h-10 rounded-xl {{ $classes['bg'] }} border-2 border-transparent peer-checked:border-white peer-checked:ring-2 peer-checked:ring-gray-400 dark:peer-checked:ring-gray-500 peer-checked:ring-offset-2 dark:peer-checked:ring-offset-gray-800 transition-all shadow-inner"></span>
                                </label>
                            @endforeach
                        </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Usada em cards e listas do ministério.</p>
                        </div>
                    </div>

                    <!-- 5. Configurações -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-gray-100 dark:bg-gray-700/50 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                    <x-icon name="gear" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configurações</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Limites e aprovação</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Limite de membros</label>
                                <input type="number" name="max_members" id="max_members" value="{{ old('max_members') }}" min="1"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Vazio = ilimitado">
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 space-y-3">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Ministério ativo (visível e aceitando voluntários)</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Requer aprovação para entrar (conselho analisa)</span>
                                </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-4 px-6 text-white bg-blue-600 hover:bg-blue-700 rounded-2xl font-black uppercase tracking-widest shadow-lg hover:shadow-blue-500/30 transition-all duration-300 focus:ring-4 focus:ring-blue-300 flex items-center justify-center gap-2">
                            <x-icon name="plus" class="w-5 h-5" />
                            Fundar Ministério
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.data('ministryCreateForm', () => ({
                init() {
                    this.$nextTick(() => {
                        const hiddenIcon = document.getElementById('icon');
                        if (!hiddenIcon) return;
                        document.querySelectorAll('.icon-picker-btn, .emoji-picker-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const value = btn.getAttribute('data-value');
                                hiddenIcon.value = value;
                                document.querySelectorAll('.icon-picker-btn, .emoji-picker-btn').forEach(b => {
                                    b.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400');
                                    b.classList.add('border-gray-200', 'dark:border-gray-600');
                                });
                                btn.classList.remove('border-gray-200', 'dark:border-gray-600');
                                btn.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                                if (btn.classList.contains('icon-picker-btn')) btn.classList.add('text-blue-600', 'dark:text-blue-400');
                            });
                        });
                        const oldIcon = '{{ old('icon', 'fa:church') }}';
                        if (oldIcon && oldIcon.startsWith('fa:')) {
                            const match = document.querySelector('.icon-picker-btn[data-value="' + oldIcon + '"]');
                            if (match) { match.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400'); match.classList.remove('border-gray-200', 'dark:border-gray-600'); }
                        } else if (oldIcon) {
                            const match = document.querySelector('.emoji-picker-btn[data-value="' + oldIcon + '"]');
                            if (match) { match.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20'); match.classList.remove('border-gray-200', 'dark:border-gray-600'); }
                        }
                    });
                }
            }));
        });
    </script>
@endsection
