@extends('admin::components.layouts.master')

@section('title', 'Configurações de Intercessão')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Configurações do Módulo</h1>
            <p class="text-gray-600 dark:text-gray-400">Personalize o comportamento e as regras de intercessão.</p>
        </div>
        <a href="{{ route('admin.intercessor.dashboard') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 transition-all duration-200">
            <span>Voltar ao Dashboard</span>
        </a>
    </div>

    <form action="{{ route('admin.intercessor.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Global Rules Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6 transition-all hover:shadow-md">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                        <x-icon name="shield-check" class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Moderação e Regras</h3>
                </div>

                <div class="space-y-4">
                    <!-- Module Enabled -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Módulo Ativo</p>
                            <p class="text-xs text-gray-500">Controla se o módulo Intercessor aparece para os membros.</p>
                        </div>
                        <input type="hidden" name="module_enabled" value="0">
                        <input type="checkbox" name="module_enabled" value="1" {{ $settings['module_enabled'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>

                    <!-- Require Moderation -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Exigir Moderação</p>
                            <p class="text-xs text-gray-500">Novos pedidos só aparecem no muro após aprovação.</p>
                        </div>
                        <input type="hidden" name="require_moderation" value="0">
                        <input type="checkbox" name="require_moderation" value="1" {{ $settings['require_moderation'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>

                    <!-- Allow Comments -->
                     <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Permitir Comentários</p>
                            <p class="text-xs text-gray-500">Habilita o muro de interações nos pedidos.</p>
                        </div>
                        <input type="hidden" name="allow_comments" value="0">
                        <input type="checkbox" name="allow_comments" value="1" {{ $settings['allow_comments'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Appearance and Limits Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6 transition-all hover:shadow-md">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <x-icon name="view-grid" class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Exibição e Limites</h3>
                </div>

                <div class="space-y-4">
                     <!-- Notification Days -->
                     <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Validade da Urgência (Dias)</label>
                        <input type="number" name="notification_days" value="{{ $settings['notification_days'] }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-[10px] text-gray-500 italic">Tempo que um pedido crítico permanece em destaque.</p>
                    </div>

                    <!-- Max Open Requests -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Máximo de Pedidos por Usuário (Ativos)</label>
                        <input type="number" name="max_open_requests" value="{{ $settings['max_open_requests'] }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-[10px] text-gray-500 italic">0 = ilimitado por mês.</p>
                    </div>
                </div>
            </div>

            <!-- Advanced Configuration Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md md:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg">
                        <x-icon name="adjustments" class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Opções Gerais</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <!-- Privacy Default -->
                     <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Permitir Pedidos Privados</p>
                            <p class="text-xs text-gray-500">Permite que o usuário marque o pedido como confidencial.</p>
                        </div>
                         <input type="hidden" name="allow_private" value="0">
                         <input type="checkbox" name="allow_private" value="1" {{ $settings['allow_private'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>

                    <!-- Anonymous Posting -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Permitir Postagem Anônima</p>
                            <p class="text-xs text-gray-500">Oculta o nome do autor do pedido no mural público.</p>
                        </div>
                        <input type="hidden" name="allow_anonymous" value="0">
                        <input type="checkbox" name="allow_anonymous" value="1" {{ $settings['allow_anonymous'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>

                    <!-- Allow Requests -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Aceitar Novos Pedidos</p>
                            <p class="text-xs text-gray-500">Controle rápido para pausar a criação de novos pedidos.</p>
                        </div>
                        <input type="hidden" name="allow_requests" value="0">
                        <input type="checkbox" name="allow_requests" value="1" {{ $settings['allow_requests'] ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Show Intercessor Names -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-200 block">Visibilidade dos Intercessores</label>
                        <p class="text-[10px] text-gray-500 mb-2">Define quem enxerga os nomes de quem está orando pelos pedidos.</p>
                        <select name="show_intercessor_names"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="author_only" {{ $settings['show_intercessor_names'] === 'author_only' ? 'selected' : '' }}>Apenas o autor vê nomes</option>
                            <option value="intercessors_only" {{ $settings['show_intercessor_names'] === 'intercessors_only' ? 'selected' : '' }}>Intercessores & pastoral veem nomes</option>
                            <option value="all" {{ $settings['show_intercessor_names'] === 'all' ? 'selected' : '' }}>Todos veem nomes</option>
                        </select>
                    </div>

                    <!-- Room Label -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-200 block">Título da Sala de Oração</label>
                        <input type="text" name="room_label" value="{{ $settings['room_label'] ?? 'Sala de Oração' }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-[10px] text-gray-500 italic">Ex.: Sala de Oração, Espaço de Intercessão, Foco de Oração.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                class="inline-flex items-center justify-center px-10 py-4 text-base font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300 min-w-[200px]">
                <x-icon name="check" class="w-6 h-6 mr-2" />
                Salvar Configurações
            </button>
        </div>
    </form>
</div>
@endsection

