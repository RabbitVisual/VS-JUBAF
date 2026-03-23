@extends('admin::components.layouts.master')

@section('title', 'Editar Ministério: ' . $ministry->name)

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
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Editar</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Ministério</h1>
                    <p class="text-gray-300 max-w-xl">Atualize os detalhes do ministério <span class="font-bold text-white">{{ $ministry->name }}</span>.</p>
                </div>
                <a href="{{ route('admin.ministries.show', $ministry) }}"
                    class="flex-shrink-0 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="arrow-left" class="w-5 h-5 text-blue-600" />
                    Voltar ao Ministério
                </a>
            </div>
        </div>

        <form action="{{ route('admin.ministries.update', $ministry) }}" method="POST" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }))">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="circle-info" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informações Gerais</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nome e propósito do ministério</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Ministério <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $ministry->name) }}" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                @error('name')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição / Propósito</label>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('description', $ministry->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leadership -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="user-tie" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gestão de Liderança</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Alterar líder e co-líder</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alterar Líder</label>
                                <select name="leader_id" id="leader_id"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">Selecione um líder...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('leader_id', $ministry->leader_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                                <div>
                                    <label for="co_leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alterar Co-Líder</label>
                                <select name="co_leader_id" id="co_leader_id"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">Selecione um co-líder...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('co_leader_id', $ministry->co_leader_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Config -->
                <div class="space-y-6">
                    <!-- Aesthetics -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <x-icon name="palette" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Visual e Identidade</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ícone e cor temática</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ícone (Emoji)</label>
                                <input type="text" name="icon" id="icon" value="{{ old('icon', $ministry->icon) }}"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-2xl text-center focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor Temática</label>
                                <select name="color" id="color"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="blue" {{ old('color', $ministry->color) == 'blue' ? 'selected' : '' }}>Azul</option>
                                    <option value="green" {{ old('color', $ministry->color) == 'green' ? 'selected' : '' }}>Verde</option>
                                    <option value="red" {{ old('color', $ministry->color) == 'red' ? 'selected' : '' }}>Vermelho</option>
                                    <option value="yellow" {{ old('color', $ministry->color) == 'yellow' ? 'selected' : '' }}>Amarelo</option>
                                    <option value="purple" {{ old('color', $ministry->color) == 'purple' ? 'selected' : '' }}>Roxo</option>
                                    <option value="pink" {{ old('color', $ministry->color) == 'pink' ? 'selected' : '' }}>Rosa</option>
                                    <option value="indigo" {{ old('color', $ministry->color) == 'indigo' ? 'selected' : '' }}>Índigo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-gray-100 dark:bg-gray-700/50 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                    <x-icon name="gear" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configurações de Acesso</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Limites e aprovação</p>
                                </div>
                            </div>

                        <div class="space-y-4">
                            <div>
                                <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Limite de Membros</label>
                                <input type="number" name="max_members" id="max_members" value="{{ old('max_members', $ministry->max_members) }}" min="1"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Deixe vazio para ilimitado">
                            </div>

                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 space-y-3">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ministry->is_active) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Ministério Ativo</span>
                                </label>

                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $ministry->requires_approval) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Requer Aprovação</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 px-6 text-white bg-blue-600 hover:bg-blue-700 rounded-2xl font-black uppercase tracking-widest shadow-lg hover:shadow-blue-500/30 transition-all duration-300 focus:ring-4 focus:ring-blue-300 flex items-center justify-center">
                            <x-icon name="check" class="w-5 h-5 mr-2" />
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

