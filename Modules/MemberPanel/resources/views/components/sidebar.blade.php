@php
    use Illuminate\Support\Facades\Storage;
    use Modules\Treasury\App\Models\TreasuryPermission;

    $hasTreasuryPermission = \Modules\Treasury\App\Models\TreasuryPermission::where('user_id', auth()->id())->first();
    $isTreasuryActive = request()->routeIs('memberpanel.treasury.*');
@endphp

<style>[x-cloak] { display: none !important; }</style>

<!-- Sidebar Multi-Column Layout -->
<div
    data-tour="sidebar"
    class="flex h-screen overflow-hidden fixed left-0 top-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Icon Sidebar (Left) -->
    <div
        class="flex h-screen w-16 flex-col justify-between border-e border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div>
            <!-- Logo -->
            <div class="inline-flex size-16 items-center justify-center border-b border-gray-100 dark:border-gray-700">
                <a href="{{ route('memberpanel.dashboard') }}" class="group">
                    <img src="{{ asset('storage/image/logo_icon.png') }}" alt="Logo"
                        class="size-10 object-contain transition-transform group-hover:scale-110 rounded-lg bg-gray-100 dark:bg-gray-700 p-1"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                    <span
                        class="hidden size-10 place-content-center rounded-lg bg-purple-100 dark:bg-purple-900 text-xs font-bold text-purple-600 dark:text-purple-400">
                        {{ strtoupper(substr(config('app.name', 'V'), 0, 1)) }}
                    </span>
                </a>
            </div>

            <!-- Navigation Icons -->
            <div class="border-t border-gray-100 dark:border-gray-700">
                <div class="px-2">
                    <div class="py-4">
                        <a href="{{ route('memberpanel.dashboard') }}"
                            class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.dashboard*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <x-icon name="gauge-high" class="size-5" />
                            <span
                                class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                Dashboard
                            </span>
                        </a>
                    </div>

                    <ul class="space-y-1 border-t border-gray-100 dark:border-gray-700 pt-4">


                        <!-- Ministérios -->
                        <li>
                            <a href="{{ route('memberpanel.ministries.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.ministries*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="church" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Ministérios & Equipes
                                </span>
                            </a>
                        </li>

                        @if(Module::isEnabled('Intercessor'))
                            <!-- Novo Pedido -->
                            <li>
                                <a href="{{ route('member.intercessor.requests.create') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('member.intercessor.requests.create') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="plus-circle" style="duotone" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Novo Pedido de Oração
                                    </span>
                                </a>
                            </li>

                            <!-- Meus Pedidos -->
                            <li>
                                <a href="{{ route('member.intercessor.requests.index') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('member.intercessor.requests.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="list-check" style="duotone" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Meus Pedidos
                                    </span>
                                </a>
                            </li>

                            <!-- Intercessão (Mural) -->
                            <li>
                                <a href="{{ route('member.intercessor.room.index') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('member.intercessor.room.*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="clipboard-list" style="duotone" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Mural de Intercessão
                                    </span>
                                </a>
                            </li>
                        @endif

                        <!-- Eventos -->
                        <li>
                            <a href="{{ route('memberpanel.events.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.events*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="calendar-days" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Eventos
                                </span>
                            </a>
                        </li>



                        @if(Module::isEnabled('Worship'))
                            <li>
                                <a href="{{ route('worship.member.rosters.index') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('worship.member.*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="clipboard-user" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Louvor / Escalas
                                    </span>
                                </a>
                            </li>
                        @endif


                        <!-- Bíblia Digital -->
                        <li>
                            <a href="{{ route('memberpanel.bible.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.bible*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="book-bible" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Bíblia Digital
                                </span>
                            </a>
                        </li>

                        <!-- Planos de Leitura -->
                        <li>
                            <a href="{{ route('member.bible.plans.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('member.bible.plans*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="book-open-reader" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Planos
                                </span>
                            </a>
                        </li>
        @if(Module::isEnabled('SocialAction'))
                        <li>
                            <a href="{{ route('socialaction.member.impact.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('socialaction.member.*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="hand-holding-heart" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Ação Social
                                </span>
                            </a>
                        </li>
        @endif
                        <li>
                            <a href="{{ route('memberpanel.sermons.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.sermons*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="microphone-lines" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Sermões
                                </span>
                            </a>
                        </li>

                        <!-- Notificações -->
                        <li>
                            <a href="{{ route('memberpanel.notifications.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.notifications*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="bell" class="size-5" />
                                @php
                                    $unreadCount = \Modules\Notifications\App\Models\UserNotification::where(
                                        'user_id',
                                        Auth::id(),
                                    )
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if ($unreadCount > 0)
                                    <span
                                        class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-800"></span>
                                @endif
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Notificações
                                    @if ($unreadCount > 0)
                                        <span class="ml-1">({{ $unreadCount }})</span>
                                    @endif
                                </span>
                            </a>
                        </li>



                        {{-- ChurchCouncil - Only for Council Members --}}
                        @if(auth()->user()->councilMember)
                        <li>
                            <a href="{{ route('memberpanel.churchcouncil.index') }}"
                                class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.churchcouncil*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <x-icon name="users-rectangle" class="size-5" />
                                <span
                                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                    Conselho
                                </span>
                            </a>
                        </li>
                        @endif

                        {{-- Doações --}}
                        @php
                            $hasActiveGateways = \Modules\PaymentGateway\App\Models\PaymentGateway::active()
                                ->get()
                                ->filter(function ($gateway) {
                                    return $gateway->isConfigured();
                                })
                                ->isNotEmpty();
                        @endphp
                        @if ($hasActiveGateways)
                            <!-- Nova Doação -->
                            <li>
                                <a href="{{ route('memberpanel.donations.create') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.donations.create') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="hand-holding-dollar" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Fazer Doação
                                    </span>
                                </a>
                            </li>
                            <!-- Minhas Doações -->
                            <li>
                                <a href="{{ route('memberpanel.donations.index') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.donations.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="file-invoice-dollar" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Minhas Doações
                                    </span>
                                </a>
                            </li>
                            <!-- Minhas Compras (Loja) -->
                            <li>
                                <a href="{{ route('memberpanel.marketplace.orders.index') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ request()->routeIs('memberpanel.marketplace.orders*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="bag-shopping" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        {{ __('marketplace::messages.my_orders') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        <!-- Tesouraria (se tiver permissão) -->
                        @if ($hasTreasuryPermission)
                            <li>
                                <a href="{{ route('memberpanel.treasury.dashboard') }}"
                                    class="group relative flex justify-center rounded-sm px-2 py-1.5 {{ $isTreasuryActive ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <x-icon name="building-columns" class="size-5" />
                                    <span
                                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                                        Tesouraria
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('pastor') || auth()->user()->isAdmin())
        <div class="px-2 pb-2">
             <a href="{{ route('admin.dashboard') }}"
                class="group relative flex w-full justify-center rounded-lg px-2 py-1.5 text-sm text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40">
                <x-icon name="user-shield" class="size-5" />
                <span
                    class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                    Painel Admin
                </span>
            </a>
        </div>
        @endif

        <!-- Logout -->
        <div
            class="sticky inset-x-0 bottom-0 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="group relative flex w-full justify-center rounded-lg px-2 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                    <x-icon name="right-from-bracket" class="size-5" />
                    <span
                        class="invisible absolute start-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-900 dark:bg-gray-700 px-2 py-1.5 text-xs font-medium text-white whitespace-nowrap group-hover:visible z-50">
                        Sair
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Text Sidebar (Right) -->
    <div
        class="flex h-screen w-64 flex-col justify-between border-e border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex-1 overflow-y-auto px-4 py-6">
            <!-- User Info -->
            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <img class="h-12 w-12 rounded-full object-cover ring-2 ring-purple-500 dark:ring-purple-400"
                        src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="h-12 w-12 rounded-full bg-linear-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold text-lg ring-2 ring-purple-500 dark:ring-purple-400"
                        style="display: none;">
                        {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <ul class="mt-4 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('memberpanel.dashboard') }}"
                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.dashboard*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        <span class="flex items-center gap-2">
                             <span class="shrink-0">
                                <x-icon name="gauge-high" class="size-5" />
                             </span>
                             Dashboard
                        </span>
                    </a>
                </li>

                @php
                    $isAdmin = auth()->user()->hasRole('admin') || auth()->user()->hasRole('pastor') || auth()->user()->isAdmin();
                    $isTeacher = \Modules\EBD\App\Models\EBDTeacher::isTeacher(auth()->id());
                    $isStudent = \Modules\EBD\App\Models\EBDStudent::isStudent(auth()->id());
                @endphp

                <!-- ========================================================================= -->
                <!-- EBD: PORTAL DO ALUNO (Conteúdo Acadêmico) -->
                <!-- ========================================================================= -->
                @if($isStudent || $isAdmin || $isTeacher)
                <li x-data="{ open: {{ request()->routeIs('memberpanel.ebd.student.*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ebd.student.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="user-graduate" style="duotone" class="w-5 h-5 mr-3 {{ request()->routeIs('memberpanel.ebd.student.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" />
                                </span>
                                EBD: Aluno
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" style="duotone" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4 border-l-2 border-gray-100 dark:border-gray-700 ml-6" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.ebd.student.index') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.student.index') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.student.my-classes') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.student.my-classes') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Minhas Turmas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.student.lessons') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.student.lessons*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Lições / Aulas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.student.my-progress') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.student.my-progress') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Meu Progresso
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- ========================================================================= -->
                <!-- EBD MASTER: PORTAL DO PROFESSOR -->
                <!-- ========================================================================= -->
                @if($isTeacher || $isAdmin)
                <li x-data="{ open: {{ request()->routeIs('memberpanel.ebd.teacher.*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ebd.teacher.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="chalkboard-teacher" style="duotone" class="w-5 h-5 mr-3 {{ request()->routeIs('memberpanel.ebd.teacher.*') ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500' }}" />
                                </span>
                                EBD: Professor
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" style="duotone" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4 border-l-2 border-gray-100 dark:border-gray-700 ml-6" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.ebd.teacher.index') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.teacher.index') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Painel Geral
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.teacher.my-classes') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.teacher.my-classes') || request()->routeIs('memberpanel.ebd.teacher.classes.*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Minhas Turmas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.teacher.lessons') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.teacher.lessons*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Todas Lições
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.teacher.evaluations') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.teacher.evaluations*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Avaliações
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- ========================================================================= -->
                <!-- EBD ARCADE: JOGOS & GAMIFICAÇÃO -->
                <!-- ========================================================================= -->
                @if($isStudent || $isTeacher || $isAdmin)
                <li x-data="{ open: {{ request()->routeIs('memberpanel.ebd.arcade*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ebd.arcade*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="gamepad-modern" style="duotone" class="w-5 h-5 mr-3 {{ request()->routeIs('memberpanel.ebd.arcade*') ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}" />
                                </span>
                                EBD: Arcade
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" style="duotone" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4 border-l-2 border-gray-100 dark:border-gray-700 ml-6" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.ebd.arcade.leaderboard') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.arcade.leaderboard') ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Meu XP & Conquistas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.ebd.arcade.index') }}"
                                    class="block rounded-lg px-4 py-2 text-xs font-medium {{ request()->routeIs('memberpanel.ebd.arcade.index') ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Central de Jogos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Perfil -->
                <li x-data="{ open: {{ request()->routeIs('memberpanel.profile*') || request()->routeIs('memberpanel.relationships*') ? 'true' : 'false' }} }">
                    <div
                        class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.profile*') || request()->routeIs('memberpanel.relationships*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="user-circle" class="size-5" />
                                </span>
                                Conta
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.profile.show') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.profile*') && !request()->routeIs('memberpanel.relationships*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Meu Perfil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.relationships.pending') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.relationships*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Convites de parentesco
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.transfers.index') }}"
                                   class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.transfers*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Cartas de Transferência
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Meus Ministérios -->
                <li x-data="{ open: {{ request()->routeIs('memberpanel.ministries*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ministries*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="church" class="size-5" />
                                </span>
                                Meus Ministérios
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            @php
                                $myMinistries = auth()->user()
                                    ? auth()->user()->activeMinistries()->orderBy('name')->get()
                                    : collect();
                            @endphp
                            @forelse($myMinistries as $m)
                                <li>
                                    <a href="{{ route('memberpanel.ministries.show', $m) }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ministries.show') && request()->route('ministry')?->id === $m->id ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        {{ $m->name }}
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <span class="block rounded-lg px-4 py-2 text-xs font-medium text-gray-400 dark:text-gray-500">
                                        Você ainda não participa de nenhum ministério.
                                    </span>
                                </li>
                            @endforelse
                            <li>
                                <a href="{{ route('memberpanel.ministries.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.ministries.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Ver todos os ministérios
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Intercessão -->
                @if(Module::isEnabled('Intercessor'))
                    <li x-data="{ open: {{ request()->routeIs('member.intercessor*') ? 'true' : 'false' }} }">
                        <div class="flex flex-col">
                            @php
                                $memberIntercessorRoomLabel = \Modules\Intercessor\App\Services\IntercessorSettings::get('room_label') ?? 'Sala de Oração';
                            @endphp
                            <button @click="open = !open"
                                class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <span class="flex items-center gap-2">
                                    <span class="shrink-0">
                                        <x-icon name="hands-praying" style="duotone" class="size-5" />
                                    </span>
                                    Intercessão
                                </span>
                                <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                    <x-icon name="chevron-down" class="size-5" />
                                </span>
                            </button>
                            @if(\Modules\Intercessor\App\Services\IntercessorSettings::get('module_enabled'))
                            <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                                @if(auth()->user()->role?->slug === 'intercessor' || auth()->user()->role?->slug === 'admin' || auth()->user()->role?->slug === 'pastor')
                                <li>
                                    <a href="{{ route('member.intercessor.dashboard') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor.dashboard') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        <span class="flex items-center gap-2">
                                            <x-icon name="gauge-high" style="duotone" class="size-4" />
                                            Painel de Intercessão
                                        </span>
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('member.intercessor.requests.create') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor.requests.create') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        <span class="flex items-center gap-2">
                                            <x-icon name="user-shield" style="duotone" class="size-5" />
                                            Novo Pedido
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('member.intercessor.requests.index') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor.requests.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        <span class="flex items-center gap-2">
                                            <x-icon name="file-invoice-dollar" style="duotone" class="size-5" />
                                            Meus Pedidos
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('member.intercessor.room.index') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor.room.index', 'member.intercessor.room.show') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        <span class="flex items-center gap-2">
                                            <x-icon name="users-rectangle" style="duotone" class="size-5" />
                                            {{ $memberIntercessorRoomLabel }}
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('member.intercessor.room.testimonies') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.intercessor.room.testimonies') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        <span class="flex items-center gap-2">
                                            <x-icon name="comment-lines" style="duotone" class="size-4" />
                                            Mural de Testemunhos
                                        </span>
                                    </a>
                                </li>
                            </ul>
                            @else
                            <div class="px-4 py-2 text-xs font-bold text-amber-500/80 italic" x-show="open" x-cloak x-transition>
                                Módulo desativado
                            </div>
                            @endif
                        </div>
                    </li>
                @endif



                @if(Module::isEnabled('Worship'))
                <li x-data="{ open: {{ request()->routeIs('worship.member.*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('worship.member.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="music" class="size-5" />
                                </span>
                                Louvor
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('worship.member.rosters.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('worship.member.rosters.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Minhas Escalas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('worship.member.rehearsal.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('worship.member.rehearsal.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Ensaio / Músicas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('worship.member.academy.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('worship.member.academy.*') ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-500 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <span class="flex items-center gap-2">
                                        Worship Academy
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Projeção -->
                @if(Module::isEnabled('Projection') && auth()->user()->canProject())
                <li>
                    <a href="{{ route('memberpanel.projection.index') }}"
                        class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.projection.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        <span class="flex items-center gap-2">
                            <span class="shrink-0">
                            <x-icon name="projector" class="size-5 {{ request()->routeIs('memberpanel.projection.*') ? 'text-blue-600 dark:text-blue-400' : '' }}" />
                            </span>
                            Projeção
                        </span>
                    </a>
                </li>
                @endif

                <!-- Eventos -->
                <li x-data="{ open: {{ request()->routeIs('memberpanel.events*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.events*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="calendar-days" class="size-5" />
                                </span>
                                Eventos
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.events.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.events.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Lista de Eventos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.events.my-registrations') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.events.my-registrations') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Minhas Inscrições
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Ação Social -->
                @if(Module::isEnabled('SocialAction'))
                <li x-data="{ open: {{ request()->routeIs('socialaction.member.*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('socialaction.member.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="hand-holding-heart" class="size-5" />
                                </span>
                                Ação Social
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('socialaction.member.impact.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('socialaction.member.impact.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Meu Impacto
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('socialaction.member.campaigns.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('socialaction.member.campaigns.*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Campanhas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Minha Leitura (Bíblia) -->
                <li x-data="{ open: {{ request()->routeIs('member.bible.plans*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.bible.plans*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="book-open-reader" class="size-5" />
                                </span>
                                Planos de Leitura
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('member.bible.plans.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.bible.plans.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Meus Planos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('member.bible.plans.catalog') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.bible.plans.catalog') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Explorar Planos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Recursos -->
                <li x-data="{ open: {{ request()->routeIs('memberpanel.bible*') || request()->routeIs('memberpanel.notifications*') || request()->routeIs('memberpanel.preferences.notifications*') || request()->routeIs('memberpanel.sermons*') || request()->routeIs('memberpanel.series*') || request()->routeIs('memberpanel.studies*') || request()->routeIs('memberpanel.commentaries*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.bible*') || request()->routeIs('memberpanel.notifications*') || request()->routeIs('memberpanel.preferences.notifications*') || request()->routeIs('memberpanel.sermons*') || request()->routeIs('memberpanel.series*') || request()->routeIs('memberpanel.studies*') || request()->routeIs('memberpanel.commentaries*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="book-open" class="size-5" />
                                </span>
                                Recursos
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.bible.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.bible.index') || request()->routeIs('memberpanel.bible.read') || request()->routeIs('memberpanel.bible.book') || request()->routeIs('memberpanel.bible.chapter') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Bíblia Digital
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.bible.interlinear') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.bible.interlinear') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Bíblia Interlinear
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('member.bible.plans.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('member.bible.plans*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Planos de Leitura
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.sermons.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.sermons*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Sermões
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.series.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.series*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Séries
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.studies.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.studies*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Estudos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.commentaries.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.commentaries*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Comentários
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.notifications.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.notifications.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Notificações
                                    @if ($unreadCount > 0)
                                        <span
                                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white min-w-6">
                                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.preferences.notifications.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.preferences.notifications*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    O que receber / Silenciar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Conselho (se for membro do conselho) --}}
                @if(auth()->user()->councilMember)
                <li x-data="{ open: {{ request()->routeIs('memberpanel.churchcouncil*') ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <button @click="open = !open"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="users-rectangle" class="size-5" />
                                </span>
                                Conselho
                            </span>
                            <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                <x-icon name="chevron-down" class="size-5" />
                            </span>
                        </button>
                        <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.meetings.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.meetings*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Reuniões
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.agendas.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.agendas*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Pautas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.approvals.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.approvals*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Aprovações
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.documents.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.documents*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Documentos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('memberpanel.churchcouncil.projects.index') }}"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.churchcouncil.projects*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    Projetos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- Financeiro --}}
                @if ($hasActiveGateways)
                    <li x-data="{ open: {{ request()->routeIs('memberpanel.donations*') ? 'true' : 'false' }} }">
                        <div class="flex flex-col">
                            <button @click="open = !open"
                                class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.donations*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <span class="flex items-center gap-2">
                                     <span class="shrink-0">
                                        <x-icon name="hand-holding-dollar" class="size-5" />
                                    </span>
                                    Financeiro
                                </span>
                                <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                    <x-icon name="chevron-down" class="size-5" />
                                </span>
                            </button>
                            <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                                <li>
                                    <a href="{{ route('memberpanel.donations.create') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.donations.create') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        Fazer Doação
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('memberpanel.donations.index') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.donations.index') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        Minhas Doações
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('memberpanel.marketplace.orders.index') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.marketplace.orders*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        {{ __('marketplace::messages.my_orders') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Tesouraria (se tiver permissão) -->
                @if ($hasTreasuryPermission)
                    <li x-data="{ open: {{ $isTreasuryActive ? 'true' : 'false' }} }">
                        <div class="flex flex-col">
                            <button @click="open = !open"
                                class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-sm font-medium {{ $isTreasuryActive ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                <span class="flex items-center gap-2">
                                <span class="shrink-0">
                                    <x-icon name="building-columns" class="size-5" />
                                </span>
                                Tesouraria
                            </span>
                                <span class="shrink-0 transition duration-300" :class="{ '-rotate-180': open }">
                                    <x-icon name="chevron-down" class="size-5" />
                                </span>
                            </button>
                            <ul class="mt-2 space-y-1 px-4" x-show="open" x-cloak x-transition>
                                <li>
                                    <a href="{{ route('memberpanel.treasury.dashboard') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.dashboard*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('memberpanel.treasury.transparency') }}"
                                        class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.transparency*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                        Transparência
                                    </a>
                                </li>

                                @if ($hasTreasuryPermission->canCreateEntries() || $hasTreasuryPermission->canEditEntries() || $hasTreasuryPermission->canDeleteEntries())
                                    <li>
                                        <a href="{{ route('memberpanel.treasury.entries.index') }}"
                                            class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.entries*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            Entradas Financeiras
                                        </a>
                                    </li>
                                @endif

                                @if ($hasTreasuryPermission->canManageCampaigns())
                                    <li>
                                        <a href="{{ route('memberpanel.treasury.campaigns.index') }}"
                                            class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.campaigns*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            Campanhas
                                        </a>
                                    </li>
                                @endif

                                @if ($hasTreasuryPermission->canManageGoals())
                                    <li>
                                        <a href="{{ route('memberpanel.treasury.goals.index') }}"
                                            class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.goals*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            Metas Financeiras
                                        </a>
                                    </li>
                                @endif

                                @if ($hasTreasuryPermission->canViewReports())
                                    <li>
                                        <a href="{{ route('memberpanel.treasury.reports.index') }}"
                                            class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.reports*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            Relatórios
                                        </a>
                                    </li>
                                @endif
                                @if ($hasTreasuryPermission->isAdmin())
                                    <li>
                                        <a href="{{ route('memberpanel.treasury.permissions.index') }}"
                                            class="block rounded-lg px-4 py-2 text-sm font-medium {{ request()->routeIs('memberpanel.treasury.permissions*') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 active' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            Permissões
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Voltar ao Site -->
                <li>
                    <a href="{{ route('homepage.index') }}"
                        class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300 border-t border-gray-100 dark:border-gray-700 mt-4 pt-4">
                        Voltar ao Site
                    </a>
                </li>
            </ul>
        </div>

        <!-- Footer -->
        <div
            class="sticky inset-x-0 bottom-0 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                <p class="font-semibold">Vertex Solutions LTDA</p>
                <p>© 2025</p>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
