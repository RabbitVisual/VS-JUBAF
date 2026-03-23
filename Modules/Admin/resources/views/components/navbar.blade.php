@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $user = auth()->user();
    $user = $user ? $user->fresh() : null;
    $userPhoto = ($user && $user->photo) ? Storage::url($user->photo) . '?v=' . $user->updated_at->timestamp : null;
    $userInitial = $user ? strtoupper(substr($user->first_name ?? $user->name, 0, 1)) : 'A';
    $userName = $user->name ?? 'Admin';
    $userEmail = $user->email ?? '';

    // Notifications Logic
    $unreadCount = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())->where('is_read', false)->count();
    $recentNotifications = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())
        ->with('notification')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

@endphp

<nav class="sticky top-0 z-50 w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left Side: Sidebar Toggle & Breadcrumb -->
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden p-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all">
                    <x-icon name="bars" class="w-6 h-6" />
                </button>

                <!-- Elegant Breadcrumb -->
                <div class="hidden md:flex items-center space-x-2 text-sm">
                     <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium">
                        <x-icon name="house" class="w-4 h-4 mr-1.5" />
                        Dashboard
                    </a>
                    @if(isset($breadcrumb))
                        <x-icon name="chevron-right" class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" />
                        <span class="text-gray-900 dark:text-white font-semibold tracking-wide">{{ $breadcrumb }}</span>
                    @endif
                </div>
            </div>

            <!-- Right Side: Integrations -->
            <div class="flex items-center gap-3 sm:gap-5">

                @if($marketplace_store_available ?? false)
                <div class="relative" x-data="{ cartOpen: false }">
                    <button type="button" @click="cartOpen = true" class="relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none" aria-label="Carrinho">
                        <x-icon name="cart-shopping" style="duotone" class="w-5 h-5" />
                        @if(($marketplace_cart_count ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] px-1 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900">{{ ($marketplace_cart_count ?? 0) > 99 ? '99+' : $marketplace_cart_count }}</span>
                        @endif
                    </button>
                    <div x-show="cartOpen" x-cloak x-transition class="fixed inset-0 z-[99] bg-black/30" style="display: none;" @click="cartOpen = false"></div>
                    <div x-show="cartOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full" class="fixed inset-y-0 right-0 z-[100] w-full max-w-sm bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700 flex flex-col" style="display: none;">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Carrinho</h2>
                            <button type="button" @click="cartOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"><x-icon name="xmark" class="w-5 h-5" /></button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4">
                            @forelse($marketplace_cart_summary ?? [] as $item)
                                <div class="py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['quantity'] }} &times; R$ {{ number_format($item['price'], 2, ',', '.') }} = R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum item no carrinho.</p>
                            @endforelse
                        </div>
                        @if(($marketplace_cart_count ?? 0) > 0)
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('marketplace.storefront.checkout') }}" class="block w-full text-center py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                                Finalizar compra
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Custom Theme Toggle -->
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

                <!-- Notifications Dropdown (padrão premium: ícone único, dropdown harmonioso) -->
                <div class="relative" x-data="{ open: false }" id="admin-notifications-dropdown">
                    <button @click="open = !open" type="button" id="admin-notifications-toggle"
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all duration-200 active:scale-95">
                        <x-icon name="bell" class="w-5 h-5" />
                        @if($unreadCount > 0)
                            <span id="notification-badge" class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] px-1 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @else
                            <span id="notification-badge" class="hidden"></span>
                        @endif
                    </button>

                    <!-- Dropdown: design limpo, sem bordas irregulares -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" id="admin-notifications-menu"
                        class="absolute right-0 mt-2 w-[22rem] max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                        style="display: none;" data-notifications-all-url="{{ route('admin.notifications.index') }}">
                        <!-- Cabeçalho único, sem badge duplicado -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
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
                                <a href="{{ $notif->action_url ?: route('admin.notifications.index') }}"
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
                                <div class="px-4 py-10 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <x-icon name="bell-slash" class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Nada por aqui</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Você está em dia.</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-col gap-2">
                            @if($unreadCount > 0)
                                <button type="button" onclick="window.NotificationSystem && window.NotificationSystem.markAllAsRead()" class="w-full px-3 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors text-center">
                                    Marcar todas como lidas
                                </button>
                            @endif
                            <a href="{{ route('admin.notifications.index') }}" class="flex items-center justify-center gap-2 w-full px-3 py-2.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors">
                                Ver todas as notificações
                                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="flex items-center gap-3 focus:outline-none group pl-2" id="user-menu-button">
                        <div class="hidden md:flex flex-col items-end mr-1">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-tight">{{ $userName }}</span>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Administrador</span>
                        </div>

                        <div class="relative">
                            @if($userPhoto)
                                <img src="{{ $userPhoto }}"
                                     alt="{{ $userName }}"
                                     class="w-10 h-10 rounded-xl object-cover border-2 border-white dark:border-gray-800 shadow-md ring-2 ring-gray-100 dark:ring-gray-700 group-hover:ring-blue-500 dark:group-hover:ring-blue-500 transition-all"
                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                                <div class="w-10 h-10 bg-linear-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center hidden border-2 border-white dark:border-gray-800 shadow-md ring-2 ring-gray-100 dark:ring-gray-700">
                                    <span class="text-white text-sm font-bold">{{ $userInitial }}</span>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-linear-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-md ring-2 ring-gray-100 dark:ring-gray-700 group-hover:ring-blue-500 dark:group-hover:ring-blue-500 transition-all">
                                    <span class="text-white text-sm font-bold">{{ $userInitial }}</span>
                                </div>
                            @endif
                        </div>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" id="user-menu"
                        class="absolute right-0 mt-3 w-64 bg-white dark:bg-gray-900 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 border border-gray-100 dark:border-gray-800 py-2 z-50 overflow-hidden"
                        style="display: none;">

                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $userName }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $userEmail }}</p>
                        </div>


                        <div class="p-2 space-y-1">
                            @if(Route::has('pastor.dashboard') && auth()->user()->hasAdminAccess())
                            <a href="{{ route('pastor.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-600 dark:hover:text-amber-400 transition-colors rounded-xl group">
                                <x-icon name="church" class="w-4 h-4 mr-3 text-amber-500 dark:text-amber-400" />
                                Ir para Gabinete Pastoral
                            </a>
                            @endif
                            <a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-xl group">
                                <x-icon name="user" class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" />
                                Meu Perfil
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-xl group">
                                <x-icon name="gear" class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" />
                                Configurações
                            </a>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-800 p-2 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors rounded-xl group">
                                    <x-icon name="logout" class="w-4 h-4 mr-3 group-hover:text-red-500" />
                                    Sair do Sistema
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

