@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Criar Nova Notificação</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Crie e envie uma notificação para os membros.</p>
            </div>
            <a href="{{ route('admin.notifications.index') }}"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                ← Voltar
            </a>
        </div>

        <form action="{{ route('admin.notifications.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            @csrf

            <div class="space-y-6">
                <!-- Informações Básicas -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações Básicas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensagem *</label>
                            <textarea name="message" id="message" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo *</label>
                            <select name="type" id="type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Informação</option>
                                <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Sucesso</option>
                                <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Aviso</option>
                                <option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>Erro</option>
                                <option value="achievement" {{ old('type') == 'achievement' ? 'selected' : '' }}>Conquista</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioridade *</label>
                            <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Destinatários -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Destinatários</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Deixe todos em branco para enviar para todos os membros</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="target_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuários Específicos</label>
                            <select name="target_users[]" id="target_users" multiple
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                style="min-height: 120px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('target_users', [])) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Segure Ctrl/Cmd para selecionar múltiplos</p>
                        </div>

                        <div>
                            <label for="target_roles" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Funções (Roles)</label>
                            <select name="target_roles[]" id="target_roles" multiple
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                style="min-height: 120px;">
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}" {{ in_array($role->slug, old('target_roles', [])) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Segure Ctrl/Cmd para selecionar múltiplos</p>
                        </div>

                        <div>
                            <label for="target_ministries" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ministérios</label>
                            <select name="target_ministries[]" id="target_ministries" multiple
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                style="min-height: 120px;">
                                @foreach($ministries as $ministry)
                                    <option value="{{ $ministry->id }}" {{ in_array($ministry->id, old('target_ministries', [])) ? 'selected' : '' }}>{{ $ministry->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Segure Ctrl/Cmd para selecionar múltiplos</p>
                        </div>
                    </div>
                </div>

                <!-- Ação Opcional -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ação Opcional</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="action_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL de Ação</label>
                            <input type="text" name="action_url" id="action_url" value="{{ old('action_url') }}" placeholder="/painel/ministerios"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="action_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto do Botão</label>
                            <input type="text" name="action_text" id="action_text" value="{{ old('action_text') }}" placeholder="Ver mais"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Agendamento -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Agendamento (Opcional)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agendar para</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expira em</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.notifications.index') }}"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Criar e Enviar Notificação
                </button>
            </div>
        </form>
    </div>
@endsection


