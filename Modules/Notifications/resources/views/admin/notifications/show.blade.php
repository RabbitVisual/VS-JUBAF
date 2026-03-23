@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notification->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Detalhes da notificação</p>
            </div>
            <a href="{{ route('admin.notifications.index') }}"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                ← Voltar
            </a>
        </div>

        <!-- Notification Header -->
        @php
            $typeConfig = [
                'info' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-800', 'text' => 'text-blue-800 dark:text-blue-200'],
                'success' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-800 dark:text-green-200'],
                'warning' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-200 dark:border-yellow-800', 'text' => 'text-yellow-800 dark:text-yellow-200'],
                'error' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-800 dark:text-red-200'],
                'achievement' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800', 'text' => 'text-amber-800 dark:text-amber-200'],
            ];
            $config = $typeConfig[$notification->type] ?? $typeConfig['info'];
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ $config['bg'] }} {{ $config['border'] }}">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($notification->type === 'success')
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($notification->type === 'error')
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($notification->type === 'warning')
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($notification->type === 'achievement')
                        <x-icon name="trophy" class="w-8 h-8 text-amber-600 dark:text-amber-400" />
                    @else
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold {{ $config['text'] }} mb-2">{{ $notification->title }}</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $notification->message }}</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $config['text'] }} {{ $config['bg'] }}">
                            {{ ucfirst($notification->type) }}
                        </span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ ucfirst($notification->priority) }}
                        </span>
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800">
                                {{ $notification->action_text ?? 'Ver mais' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Criada por</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $notification->creator ? $notification->creator->name : 'Sistema' }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Data de Criação</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $notification->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if($notification->isActive())
                        <span class="text-green-600 dark:text-green-400">Ativa</span>
                    @else
                        <span class="text-gray-600 dark:text-gray-400">Inativa</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estatísticas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Destinatários</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalCount }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Lidas</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $readCount }}</p>
                </div>
            </div>
            @if($totalCount > 0)
                <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-green-600 dark:bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ ($readCount / $totalCount) * 100 }}%"></div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        {{ round(($readCount / $totalCount) * 100, 1) }}% de leitura
                    </p>
                </div>
            @endif
        </div>

        <!-- Target Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Destinatários</h2>
            <div class="space-y-4">
                @if(empty($notification->target_users) && empty($notification->target_roles) && empty($notification->target_ministries))
                    <p class="text-gray-600 dark:text-gray-400">Enviada para todos os membros</p>
                @else
                    @if(!empty($notification->target_users))
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuários Específicos:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($notification->target_users) }} usuário(s)</p>
                        </div>
                    @endif
                    @if(!empty($notification->target_roles))
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Funções:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ implode(', ', $notification->target_roles) }}</p>
                        </div>
                    @endif
                    @if(!empty($notification->target_ministries))
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ministérios:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($notification->target_ministries) }} ministério(s)</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection


