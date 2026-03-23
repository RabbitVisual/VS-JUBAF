@extends('memberpanel::components.layouts.master')

@section('page-title', 'Notificações')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
        <!-- Decorative Mesh Gradient Background -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-pink-600 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-10 w-80 h-80 bg-red-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-pink-200/80 font-bold uppercase tracking-widest text-xs">Atualizações</p>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Notificações
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    Fique por dentro de todas as novidades, avisos e atividades recentes.
                </p>
            </div>

            <div class="flex-shrink-0 flex flex-wrap items-center gap-3 justify-center">
                @if($unreadCount > 0)
                <div data-tour="notifications-read-all">
                    <form action="{{ route('memberpanel.notifications.read-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl font-bold transition-all backdrop-blur-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            <x-icon name="check-circle" class="w-5 h-5 mr-2" />
                            Marcar todas como lidas
                        </button>
                    </form>
                </div>
                @endif
                @if($notifications->total() > 0)
                <form action="{{ route('memberpanel.notifications.clear-all') }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir todas as notificações? Esta ação não pode ser desfeita.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-white border border-red-400/30 rounded-xl font-bold transition-all backdrop-blur-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <x-icon name="trash" class="w-5 h-5 mr-2" />
                        Excluir todas
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4 max-w-4xl mx-auto" data-tour="notifications-list">
        @forelse($notifications as $notification)
            @php
                $notif = $notification->notification;
                $isUnread = !$notification->is_read;

                $typeStyles = [
                    'info' => [
                        'bg' => 'bg-white dark:bg-gray-800',
                        'border_l' => 'border-l-blue-500',
                        'icon_bg' => 'bg-blue-100 dark:bg-blue-900/30',
                        'icon_text' => 'text-blue-600 dark:text-blue-400',
                        'icon' => 'info'
                    ],
                    'success' => [
                        'bg' => 'bg-white dark:bg-gray-800',
                        'border_l' => 'border-l-green-500',
                        'icon_bg' => 'bg-green-100 dark:bg-green-900/30',
                        'icon_text' => 'text-green-600 dark:text-green-400',
                        'icon' => 'check-circle'
                    ],
                    'warning' => [
                        'bg' => 'bg-white dark:bg-gray-800',
                        'border_l' => 'border-l-amber-500',
                        'icon_bg' => 'bg-amber-100 dark:bg-amber-900/30',
                        'icon_text' => 'text-amber-600 dark:text-amber-400',
                        'icon' => 'exclamation'
                    ],
                    'error' => [
                        'bg' => 'bg-white dark:bg-gray-800',
                        'border_l' => 'border-l-red-500',
                        'icon_bg' => 'bg-red-100 dark:bg-red-900/30',
                        'icon_text' => 'text-red-600 dark:text-red-400',
                        'icon' => 'x-circle'
                    ],
                    'achievement' => [
                        'bg' => 'bg-white dark:bg-gray-800',
                        'border_l' => 'border-l-amber-500',
                        'icon_bg' => 'bg-amber-100 dark:bg-amber-900/30',
                        'icon_text' => 'text-amber-600 dark:text-amber-400',
                        'icon' => 'trophy'
                    ],
                ];

                $style = $typeStyles[$notif->type] ?? $typeStyles['info'];
            @endphp

            <div class="{{ $style['bg'] }} rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 relative overflow-hidden group transition-all duration-300 hover:shadow-md
                {{ $isUnread ? 'ring-2 ring-blue-500/20 dark:ring-blue-500/40 ' . $style['border_l'] . ' border-l-4' : 'border-l-4 border-l-transparent' }}">

                @if($isUnread)
                    <div class="absolute top-4 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse shadow-lg shadow-blue-500/50"></div>
                @endif

                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl {{ $style['icon_bg'] }} flex items-center justify-center {{ $style['icon_text'] }}">
                            <x-icon name="{{ $style['icon'] }}" class="w-6 h-6" />
                        </div>
                    </div>

                    <div class="flex-1 min-w-0 pt-1">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1 pr-6">
                                {{ $notif->title }}
                            </h3>
                        </div>

                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-3">
                            {{ $notif->message }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 mt-4">
                             <div class="flex items-center text-xs font-bold text-gray-400 uppercase tracking-wide">
                                <x-icon name="clock" class="w-3.5 h-3.5 mr-1" />
                                {{ $notif->created_at->diffForHumans() }}
                            </div>

                             @if($notif->action_url)
                                <a href="{{ $notif->action_url }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-xs font-bold text-gray-700 dark:text-white transition-colors">
                                    {{ $notif->action_text ?? 'Ver detalhes' }}
                                    <x-icon name="arrow-right" class="w-3 h-3 ml-2" />
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        @if($isUnread)
                            <form action="{{ route('memberpanel.notifications.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Marcar como lida">
                                    <x-icon name="check" class="w-5 h-5" />
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('memberpanel.notifications.destroy', $notification) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja remover esta notificação?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Remover">
                                <x-icon name="trash" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="bell-off" class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Tudo limpo por aqui!</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Você não possui novas notificações no momento.
                </p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="max-w-4xl mx-auto mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

