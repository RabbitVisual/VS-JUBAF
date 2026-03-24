<!-- Navigation -->
<!-- Navigation -->
<nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-200 dark:border-gray-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('homepage.index') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset(\App\Models\Settings::get('logo_path', 'logo_oficial.svg')) }}" alt="JUBAF - Juventude Batista Feirense"
                        class="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-110"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="flex flex-col" style="display: none;">
                        <span class="text-lg font-bold text-gray-900 dark:text-white leading-tight">JUBAF</span>
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wider">Juventude Batista Feirense</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex md:items-center md:space-x-1">
                <a href="{{ route('homepage.index') }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Início</a>
                <a href="{{ request()->routeIs('homepage.index') ? '#sobre' : route('homepage.index').'#sobre' }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Sobre</a>
                @if(\App\Models\Settings::get('homepage_show_ministries', 1))
                <a href="{{ request()->routeIs('homepage.index') ? '#ministerios' : route('homepage.index').'#ministerios' }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Ministérios</a>
                @endif
                <a href="{{ route('events.public.index') }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Eventos</a>
                @if (Route::has('marketplace.storefront.index') && \App\Models\Settings::get('homepage_show_marketplace', 1))
                    <a href="{{ route('marketplace.storefront.index') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Loja</a>
                @endif
                <a href="{{ route('bible.public.index') }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Bíblia Online</a>
                @if(\App\Models\Settings::get('homepage_show_radio', 1) && Route::has('homepage.radio'))
                <a href="{{ route('homepage.radio') }}"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Rádio</a>
                @endif
                <a href="#contato"
                    class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Contato</a>

                @php
                    $hasActiveGateways = \Modules\PaymentGateway\App\Models\PaymentGateway::active()
                        ->get()
                        ->filter(function ($gateway) {
                            return $gateway->isConfigured();
                        })
                        ->isNotEmpty();
                @endphp
                @if($hasActiveGateways)
                    <a href="{{ route('donation.create') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">Doação</a>
                @endif

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-2"></div>

                @if(($marketplace_store_available ?? false) && Route::has('marketplace.storefront.cart'))
                    <a href="{{ route('marketplace.storefront.cart') }}" class="relative p-2.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors mr-2" aria-label="Ver carrinho">
                        <x-icon name="cart-shopping" style="duotone" class="w-6 h-6" />
                        @if(($marketplace_cart_count ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 flex items-center justify-center text-[10px] font-bold text-white bg-blue-600 rounded-full">{{ ($marketplace_cart_count ?? 0) > 99 ? '99+' : $marketplace_cart_count }}</span>
                        @endif
                    </a>
                @endif

                <!-- Dark Mode Toggle -->
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" type="button"
                    class="focus:outline-none rounded-full text-sm p-2.5 transition-all duration-200 mr-2 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                    <!-- Toggle Icons -->
                    <span class="hidden dark:block">
                        <x-icon name="sun-bright" class="w-6 h-6 text-yellow-400" />
                    </span>
                    <span class="block dark:hidden">
                        <x-icon name="moon-stars" class="w-6 h-6 text-gray-500" />
                    </span>
                </button>

                @auth
                    @php
                        $user = auth()->user();
                        $dashboardRoute = $user->hasAdminAccess() ? route('admin.dashboard') : route('memberpanel.dashboard');
                    @endphp
                    <div class="flex items-center space-x-3 bg-gray-50 dark:bg-gray-800/50 pl-2 pr-4 py-1.5 rounded-full border border-gray-100 dark:border-gray-700">
                        <div class="flex-shrink-0">
                            <img class="h-9 w-9 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-sm"
                                 src="{{ $user->avatar_url }}"
                                 alt="{{ $user->name }}">
                        </div>
                        <div class="hidden lg:flex flex-col items-start leading-tight mr-1">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $user->first_name }}</span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">{{ $user->role->name ?? 'Membro' }}</span>
                        </div>
                        <a href="{{ $dashboardRoute }}"
                            class="bg-blue-600 dark:bg-blue-600 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-md hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-0.5">
                            Painel
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-blue-600 dark:bg-blue-600 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:bg-blue-700 dark:hover:bg-blue-500 transition-all duration-300 transform hover:-translate-y-0.5">
                        Login
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button and theme toggle -->
            <div class="md:hidden flex items-center space-x-2">
                <!-- Dark Mode Toggle Mobile -->
                <button id="theme-toggle-mobile" type="button"
                    class="hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none rounded-lg text-sm p-2.5">
                    <span class="hidden dark:block">
                        <x-icon name="sun-bright" class="w-5 h-5 text-yellow-400" />
                    </span>
                    <span class="block dark:hidden">
                        <x-icon name="moon-stars" class="w-5 h-5 text-gray-500" />
                    </span>
                </button>

                <button type="button" id="mobile-menu-button"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none p-2 transition-colors">
                    <x-icon name="bars" class="h-7 w-7" />
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu"
        class="hidden md:hidden border-t border-gray-100 dark:border-gray-800 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl absolute w-full left-0 shadow-lg">
        <div class="px-4 pt-4 pb-6 space-y-2">
            @auth
                @php
                    $user = auth()->user();
                    $dashboardRoute = $user->hasAdminAccess() ? route('admin.dashboard') : route('memberpanel.dashboard');
                @endphp
                <!-- User Profile Section Mobile -->
                <div class="flex items-center space-x-4 p-4 mb-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <img class="h-12 w-12 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-sm"
                         src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->role->name ?? 'Membro' }}</span>
                    </div>
                </div>
            @endauth

            <a href="{{ route('homepage.index') }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Início</a>
            <a href="{{ request()->routeIs('homepage.index') ? '#sobre' : route('homepage.index').'#sobre' }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Sobre</a>
            @if(\App\Models\Settings::get('homepage_show_ministries', 1))
            <a href="{{ request()->routeIs('homepage.index') ? '#ministerios' : route('homepage.index').'#ministerios' }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Ministérios</a>
            @endif
            <a href="{{ route('events.public.index') }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Eventos</a>
            @if (Route::has('marketplace.storefront.index') && \App\Models\Settings::get('homepage_show_marketplace', 1))
                <a href="{{ route('marketplace.storefront.index') }}"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Loja</a>
            @endif
            <a href="{{ route('bible.public.index') }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Bíblia Online</a>
            @if(\App\Models\Settings::get('homepage_show_radio', 1) && Route::has('homepage.radio'))
            <a href="{{ route('homepage.radio') }}"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Rádio</a>
            @endif
            <a href="#contato"
                class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Contato</a>

            @if($hasActiveGateways)
                <a href="{{ route('donation.create') }}"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl text-base font-medium transition-colors">Doação</a>
            @endif

            <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                @auth
                    <a href="{{ $dashboardRoute }}"
                        class="block w-full text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-base font-bold shadow-md transition-colors">
                        Ir para o Painel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-base font-bold shadow-md transition-colors">
                        Área do Membro
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

