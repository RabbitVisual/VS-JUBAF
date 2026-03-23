@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Badges e Conquistas')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Badges e Conquistas</h1>
                <p class="text-gray-600 dark:text-gray-400">Gerencie os badges e conquistas disponíveis no sistema de
                    gamificação</p>
            </div>
            <a href="{{ route('admin.badges.create') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                <x-icon name="circle-check" class="w-5 h-5 mr-2" />
                <span>Criar Badge</span>
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:text-green-300 dark:border-green-800 flex items-center animate-fade-in"
                role="alert">
                <x-icon name="circle-check" class="w-5 h-5 mr-3 flex-shrink-0 text-green-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-6 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:text-red-300 dark:border-red-800 flex items-center animate-fade-in"
                role="alert">
                <x-icon name="circle-exclamation" class="w-5 h-5 mr-3 flex-shrink-0 text-red-600" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Badges Grid -->
        @if ($badges->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($badges as $badge)
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden">
                        <!-- Card Header -->
                        <div
                            class="p-6 bg-linear-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <x-badge-card :badge="$badge" size="md" class="p-0 border-0 hover:bg-transparent hover:border-transparent" />
                                </div>
                                @if ($badge->is_active)
                                    <span
                                        class="shrink-0 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        Ativo
                                    </span>
                                @else
                                    <span
                                        class="shrink-0 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Critério</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('_', ' ', $badge->criteria_type)) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Pontos</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $badge->points_required ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Usuários com este badge
                                </p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $badge->users()->count() }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.badges.show', $badge) }}"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    Ver Detalhes
                                </a>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.badges.edit', $badge) }}"
                                    class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    title="Editar">
                                    <x-icon name="pen-to-square" class="w-5 h-5" />
                                </a>
                                <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja remover este badge?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        title="Remover">
                                        <x-icon name="trash-can" class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <x-icon name="trophy" class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" />
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nenhum badge criado</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Comece criando seu primeiro badge para o sistema de
                    gamificação.</p>
                <a href="{{ route('admin.badges.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <x-icon name="circle-check" class="w-5 h-5 mr-2" />
                    Criar Primeiro Badge
                </a>
            </div>
        @endif
    </div>
@endsection

