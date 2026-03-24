<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none">
    <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="flex items-center h-16 px-6 border-b border-gray-100 dark:border-gray-800">
            <a href="{{ route('memberpanel.dashboard') }}" class="text-base font-bold text-gray-900 dark:text-gray-100">
                Meu Painel
            </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            {{-- Dashboard --}}
            <a href="{{ route('memberpanel.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.dashboard*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="gauge-high" class="w-4 h-4" />
                Dashboard
            </a>

            {{-- ── Eventos ────────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Eventos</div>

            <a href="{{ route('memberpanel.events.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.events.index') || request()->routeIs('memberpanel.events.show') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="calendar-days" class="w-4 h-4" />
                Próximos Eventos
            </a>

            <a href="{{ route('memberpanel.events.my-registrations') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.events.my-registrations*') || request()->routeIs('memberpanel.events.show-registration*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="ticket" class="w-4 h-4" />
                Minhas Inscrições
            </a>

            {{-- ── Crescimento ────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Crescimento</div>

            <a href="{{ route('memberpanel.bible.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.bible*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="book-bible" class="w-4 h-4" />
                Bíblia
            </a>

            <a href="{{ route('memberpanel.sermons.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.sermons*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="microphone-lines" class="w-4 h-4" />
                Sermões
            </a>

            {{-- ── Financeiro ─────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</div>

            <a href="{{ route('memberpanel.treasury.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.treasury*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="chart-pie" class="w-4 h-4" />
                Tesouraria
            </a>

            <a href="{{ route('memberpanel.donations.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.donations*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="hand-holding-heart" class="w-4 h-4" />
                Doações
            </a>

            {{-- ── Comunicação ────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Comunicação</div>

            <a href="{{ route('mural.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('mural.index') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="bullhorn" class="w-4 h-4" />
                Mural Oficial
            </a>

            <a href="{{ route('memberpanel.notifications.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.notifications*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="bell" class="w-4 h-4" />
                Notificações
            </a>

            {{-- ── Conta ──────────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Conta</div>

            <a href="{{ route('memberpanel.profile.show') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.profile*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="user" class="w-4 h-4" />
                Meu Perfil
            </a>

            <a href="{{ route('memberpanel.relationships.pending') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.relationships*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="link" class="w-4 h-4" />
                Vínculos Familiares
            </a>

            <a href="{{ route('memberpanel.preferences.notifications.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.preferences*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                <x-icon name="sliders" class="w-4 h-4" />
                Preferências
            </a>
        </nav>
    </div>
</aside>

<div id="sidebar-overlay" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    style="z-index: 30;"></div>
