@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<!-- Top Navigation -->
<nav class="sticky top-0 z-40 backdrop-blur-xl bg-white/80 dark:bg-gray-900/90 border-b border-gray-200/50 dark:border-gray-700/50 px-6 py-4 shadow-sm transition-all duration-300">
    <div class="flex items-center justify-between">
        <!-- Mobile menu button -->
        <button id="sidebar-toggle" type="button"
            class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <x-icon name="bars" class="h-6 w-6" />
        </button>

        <!-- Page Title -->
        <div class="flex-1 lg:ml-0">
            <h1 class="text-xl font-black tracking-tight text-gray-900 dark:text-white">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
            @if(($marketplace_store_available ?? false) && Route::has('marketplace.storefront.cart'))
                <a href="{{ route('marketplace.storefront.cart') }}" class="relative p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" aria-label="Ver carrinho">
                    <x-icon name="cart-shopping" style="duotone" class="w-6 h-6" />
                    @if(($marketplace_cart_count ?? 0) > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full">{{ ($marketplace_cart_count ?? 0) > 99 ? '99+' : $marketplace_cart_count }}</span>
                    @endif
                </a>
            @endif
            <!-- Notifications Dropdown -->
            @php
                $unreadCount = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())->where('is_read', false)->count();
                $recentNotifications = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())
                    ->with('notification')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            <div class="relative" x-data="{ open: false }" id="notifications-dropdown">
                <button @click="open = !open" type="button" id="notifications-toggle" data-tour="notifications"
                    class="relative text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-xl text-sm p-2.5 transition-all active:scale-95">
                    <x-icon name="bell" class="w-6 h-6" />
                    @if($unreadCount > 0)
                        <span class="absolute top-2 right-2 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900 animate-pulse"></span>
                    @endif
                    <span id="notification-badge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    id="notifications-menu"
                    class="absolute right-0 mt-4 w-80 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-xl shadow-blue-500/10 border border-gray-100 dark:border-gray-700 z-50 max-h-[30rem] overflow-y-auto">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 rounded-t-2xl" data-notifications-all-url="{{ route('memberpanel.notifications.index') }}">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Notificações</h3>
                        <p id="notification-count-label" class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount > 0 ? $unreadCount . ' não lida' . ($unreadCount !== 1 ? 's' : '') : '' }}</p>
                    </div>
                    <div id="notification-list-container" class="max-h-80 overflow-y-auto">
                        @forelse($recentNotifications as $notification)
                            @php
                                $notif = $notification->notification;
                                $byCategory = [
                                    'treasury_approval' => ['icon' => 'coins', 'bg' => 'bg-amber-100 dark:bg-amber-900/30', 'iconColor' => 'text-amber-600 dark:text-amber-400'],
                                    'payment_completed' => ['icon' => 'coins', 'bg' => 'bg-amber-100 dark:bg-amber-900/30', 'iconColor' => 'text-amber-600 dark:text-amber-400'],
                                    'churchcouncil_minutes' => ['icon' => 'scale-balanced', 'bg' => 'bg-violet-100 dark:bg-violet-900/30', 'iconColor' => 'text-violet-600 dark:text-violet-400'],
                                    'ebd_lesson' => ['icon' => 'graduation-cap', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'iconColor' => 'text-emerald-600 dark:text-emerald-400'],
                                    'student_leveled_up' => ['icon' => 'trophy', 'bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'iconColor' => 'text-yellow-600 dark:text-yellow-400'],
                                    'worship_roster' => ['icon' => 'music', 'bg' => 'bg-sky-100 dark:bg-sky-900/30', 'iconColor' => 'text-sky-600 dark:text-sky-400'],
                                    'event_registration' => ['icon' => 'calendar-check', 'bg' => 'bg-blue-100 dark:bg-blue-900/30', 'iconColor' => 'text-blue-600 dark:text-blue-400'],
                                    'sermon_collaboration' => ['icon' => 'book-bible', 'bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'iconColor' => 'text-indigo-600 dark:text-indigo-400'],
                                    'family_relationship_invite' => ['icon' => 'people-group', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'iconColor' => 'text-emerald-600 dark:text-emerald-400'],
                                ];
                                $iconConfig = ($notif->notification_type && isset($byCategory[$notif->notification_type]))
                                    ? $byCategory[$notif->notification_type]
                                    : match($notif->type ?? '') {
                                        'info' => ['icon' => 'circle-info', 'bg' => 'bg-blue-100 dark:bg-blue-900/30', 'iconColor' => 'text-blue-600 dark:text-blue-400'],
                                        'success' => ['icon' => 'circle-check', 'bg' => 'bg-green-100 dark:bg-green-900/30', 'iconColor' => 'text-green-600 dark:text-green-400'],
                                        'warning' => ['icon' => 'triangle-exclamation', 'bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'iconColor' => 'text-yellow-600 dark:text-yellow-400'],
                                        'error' => ['icon' => 'circle-xmark', 'bg' => 'bg-red-100 dark:bg-red-900/30', 'iconColor' => 'text-red-600 dark:text-red-400'],
                                        default => ['icon' => 'bell', 'bg' => 'bg-gray-100 dark:bg-gray-700/50', 'iconColor' => 'text-gray-600 dark:text-gray-400'],
                                    };
                                if (in_array($notif->priority ?? '', ['urgent', 'high'])) {
                                    $iconConfig = ['icon' => 'circle-exclamation', 'bg' => 'bg-red-100 dark:bg-red-900/30', 'iconColor' => 'text-red-600 dark:text-red-400'];
                                }
                            @endphp
                            <a href="{{ $notif->action_url ?: route('memberpanel.notifications.index') }}"
                                class="flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }} border-b border-gray-100 dark:border-gray-800 last:border-b-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-xl {{ $iconConfig['bg'] }} flex items-center justify-center {{ $iconConfig['iconColor'] }}">
                                    <x-icon name="{{ $iconConfig['icon'] }}" class="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate flex items-center gap-2">
                                        {{ $notif->title }}
                                        @if(!$notification->is_read)
                                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500" aria-hidden="true"></span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2 leading-snug">
                                        {{ Str::limit($notif->message, 90) }}
                                    </p>
                                    <div class="mt-2 flex items-center justify-between gap-2 flex-wrap">
                                        @if($notif->action_url && $notif->action_text)
                                            <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                                {{ $notif->action_text }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif
                                        <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3 text-gray-400">
                                    <x-icon name="bell-slash" class="w-6 h-6" />
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Nada por aqui</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Você está em dia com as notificações!</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 rounded-b-2xl space-y-2">
                        @if($unreadCount > 0)
                            <button type="button" onclick="window.NotificationSystem && window.NotificationSystem.markAllAsRead()" class="w-full px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                Marcar todas como lidas
                            </button>
                        @endif
                        <a href="{{ route('memberpanel.notifications.index') }}"
                            class="block text-center text-xs font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                            Ver todas as notificações
                        </a>
                        <a href="{{ route('memberpanel.preferences.notifications.index') }}"
                            class="flex items-center justify-center gap-1.5 w-full px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                            <x-icon name="sliders" class="w-3.5 h-3.5" />
                            O que receber / Silenciar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <button id="theme-toggle" type="button"
                class="focus:outline-none rounded-full text-sm p-2.5 transition-all duration-200 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800 active:scale-95">
                <!-- Toggle Icons -->
                <span class="hidden dark:block">
                    <x-icon name="sun-bright" class="w-6 h-6 text-yellow-400" />
                </span>
                <span class="block dark:hidden">
                    <x-icon name="moon-stars" class="w-6 h-6 text-gray-500" />
                </span>
            </button>

            <!-- User Menu -->
@php
                $user = Auth::user()->fresh();
                $userAvatarUrl = $user->avatar_url . (str_contains($user->avatar_url, '?') ? '&' : '?') . 'v=' . $user->updated_at->timestamp;
                $userInitial = strtoupper(substr($user->first_name ?? $user->name, 0, 1));
            @endphp
            <div class="relative" x-data="{ open: false }" id="user-menu-dropdown">
                <button @click="open = !open" type="button" id="user-menu-toggle"
                    class="flex items-center space-x-2 text-sm focus:outline-none group transition-transform hover:scale-105 active:scale-95">
                    <div class="relative">
                        <img src="{{ $userAvatarUrl }}"
                             alt="{{ $user->name }}"
                             class="w-9 h-9 rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-md ring-2 ring-blue-500/20 dark:ring-blue-500/40"
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                        <div class="w-9 h-9 rounded-full bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-md border-2 border-white dark:border-gray-800 hidden">
                            {{ $userInitial }}
                        </div>
                    </div>
                    <div class="hidden md:flex flex-col items-start px-1">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ Str::limit($user->first_name ?? $user->name, 15) }}</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-blue-500 transition-colors" />
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    id="user-menu"
                    class="absolute right-0 mt-4 w-56 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-xl shadow-blue-500/10 py-2 z-50 border border-gray-100 dark:border-gray-700">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 mb-1 lg:hidden">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                    </div>


                    <a href="{{ route('memberpanel.profile.show') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 transition-colors mx-2 rounded-xl">
                        <x-icon name="user" class="w-4 h-4" />
                        Meu Perfil
                    </a>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-1 mx-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors mx-2 rounded-xl text-left">
                            <x-icon name="logout" class="w-4 h-4" />
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

