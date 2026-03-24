@php
    $user = auth()->user();
    $userPhoto =
        $user && $user->photo
            ? \Illuminate\Support\Facades\Storage::url($user->photo) . '?v=' . $user->updated_at->timestamp
            : null;
    $userName = $user->first_name ?? ($user->name ?? 'Liderança');
@endphp

<nav
    class="sticky top-0 z-50 w-full bg-slate-800 dark:bg-slate-950 border-b border-amber-900/30 dark:border-amber-800/20 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" type="button"
                    class="lg:hidden p-2 text-slate-400 hover:text-amber-300 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                    <x-icon name="bars" class="w-6 h-6" />
                </button>
                <div class="hidden md:flex items-center space-x-2 text-sm">
                    <a href="{{ route('lideranca.dashboard') }}"
                        class="flex items-center text-amber-200/80 hover:text-amber-300 transition-colors font-medium">
                        <x-icon name="house" class="w-4 h-4 mr-1.5" />
                        Gabinete
                    </a>
                    @if (isset($breadcrumb))
                        <x-icon name="chevron-right" class="w-3.5 h-3.5 text-amber-600/60" />
                        <span class="text-white font-semibold tracking-wide">{{ $breadcrumb }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if ($user && $user->isAdmin() && Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 hover:text-white border border-slate-600 transition-colors">
                        <x-icon name="gear" class="w-4 h-4" />
                        Ir para Admin
                    </a>
                @endif

                <!-- Notifications Dropdown -->
                @php
                    $unreadCount = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                    $recentNotifications = \Modules\Notifications\App\Models\UserNotification::where('user_id', Auth::id())
                        ->with('notification')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                <div class="relative" x-data="{ open: false }" id="notifications-dropdown">
                    <button @click="open = !open" type="button" id="notifications-toggle"
                        class="relative p-2.5 rounded-xl text-slate-400 hover:text-amber-300 hover:bg-slate-700 focus:outline-none transition-all active:scale-95">
                        <x-icon name="bell" class="w-5 h-5" />
                        @if ($unreadCount > 0)
                            <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-slate-800 dark:ring-slate-950 animate-pulse"></span>
                        @endif
                        <span id="notification-badge"
                            class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 flex items-center justify-center text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-slate-800 dark:bg-slate-900 border border-amber-900/30 rounded-2xl shadow-xl z-50 max-h-[30rem] overflow-y-auto">
                        <div class="p-4 border-b border-amber-900/20 bg-slate-700/30 rounded-t-2xl">
                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-widest">Notificações</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5 {{ $unreadCount > 0 ? '' : 'hidden' }}">
                                {{ $unreadCount > 0 ? $unreadCount . ' não lida' . ($unreadCount !== 1 ? 's' : '') : '' }}
                            </p>
                        </div>
                        <div class="max-h-64 overflow-y-auto custom-scrollbar">
                            @forelse($recentNotifications as $notification)
                                @php
                                    $notif = $notification->notification;
                                    $iconConfig = [
                                        'icon' => 'bell',
                                        'bg' => 'bg-slate-700/50',
                                        'iconColor' => 'text-slate-300',
                                    ];
                                    if ($notif->notification_type === 'mural_post') {
                                         $iconConfig = ['icon' => 'bullhorn', 'bg' => 'bg-purple-900/30', 'iconColor' => 'text-purple-400'];
                                    } elseif ($notif->type === 'success') {
                                         $iconConfig = ['icon' => 'circle-check', 'bg' => 'bg-green-900/30', 'iconColor' => 'text-green-400'];
                                    } elseif ($notif->type === 'warning') {
                                         $iconConfig = ['icon' => 'triangle-exclamation', 'bg' => 'bg-yellow-900/30', 'iconColor' => 'text-yellow-400'];
                                    } elseif ($notif->type === 'error' || in_array($notif->priority ?? '', ['urgent', 'high'])) {
                                         $iconConfig = ['icon' => 'circle-exclamation', 'bg' => 'bg-red-900/30', 'iconColor' => 'text-red-400'];
                                    }
                                @endphp
                                <a href="{{ $notif->action_url ?: route('memberpanel.notifications.index') }}"
                                    class="flex gap-3 px-4 py-3 hover:bg-slate-700/50 transition-colors {{ !$notification->is_read ? 'bg-amber-900/10' : '' }} border-b border-slate-700/50 last:border-b-0">
                                    <div class="flex-shrink-0 w-9 h-9 rounded-xl {{ $iconConfig['bg'] }} flex items-center justify-center {{ $iconConfig['iconColor'] }}">
                                        <x-icon name="{{ $iconConfig['icon'] }}" class="w-4 h-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-200 truncate flex items-center gap-2">
                                            {{ $notif->title }}
                                            @if (!$notification->is_read)
                                                <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-2 leading-snug">
                                            {{ \Illuminate\Support\Str::limit($notif->message, 80) }}
                                        </p>
                                        <div class="mt-2 text-[10px] font-medium text-slate-500 uppercase tracking-wide">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-6 py-8 text-center text-slate-400">
                                    <x-icon name="bell-slash" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                    <p class="text-sm font-medium">Tudo limpo por aqui</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-amber-900/20 bg-slate-700/30 rounded-b-2xl text-center">
                            <a href="{{ route('memberpanel.notifications.index') }}"
                                class="block text-xs font-bold uppercase tracking-widest text-amber-400 hover:text-amber-300 transition-colors">
                                Ver todas no Painel
                            </a>
                        </div>
                    </div>
                </div>

                <button id="theme-toggle" type="button"
                    class="p-2.5 rounded-xl text-slate-400 hover:text-amber-300 hover:bg-slate-700 focus:outline-none transition-all">
                    <span class="hidden dark:inline"><x-icon name="sun-bright" class="w-5 h-5 text-amber-400" /></span>
                    <span class="inline dark:hidden"><x-icon name="moon-stars" class="w-5 h-5" /></span>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 p-1.5 rounded-xl text-slate-300 hover:bg-slate-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                        @if ($userPhoto)
                            <img src="{{ $userPhoto }}" alt="" class="w-8 h-8 rounded-lg object-cover">
                        @else
                            <span
                                class="w-8 h-8 rounded-lg bg-amber-500/30 flex items-center justify-center text-amber-300 font-bold text-sm">{{ strtoupper(mb_substr($userName, 0, 1)) }}</span>
                        @endif
                        <span
                            class="hidden sm:inline text-sm font-medium max-w-32 truncate">{{ $userName }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 py-1 bg-slate-800 dark:bg-slate-900 border border-amber-900/30 rounded-xl shadow-xl z-50">
                        <a href="{{ route('lideranca.dashboard') }}"
                            class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">Dashboard</a>
                        @if (Route::has('lideranca.profile.show'))
                            <a href="{{ route('lideranca.profile.show') }}"
                                class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">Meu
                                perfil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-red-300">Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
