<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-slate-950 border-r border-gray-200 dark:border-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none">
    <div class="flex flex-col h-full">
        <div class="flex items-center h-16 px-6 border-b border-gray-100 dark:border-gray-800">
            <a href="{{ route('admin.dashboard') }}" class="text-base font-bold text-gray-900 dark:text-gray-100">Vertex
                JUBAF Admin</a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">Dashboard</a>

            @can('gerenciar igrejas')
                <a href="{{ route('admin.igrejas.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.igrejas*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="church" class="w-4 h-4" />
                    Gestão de Igrejas
                </a>
            @endcan

            @can('gerenciar usuarios')
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="users" class="w-4 h-4" />
                    Gestão de Usuários
                </a>
            @endcan

            @can('gerenciar financeiro_macro')
                <a href="{{ route('admin.transactions.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.transactions*') || request()->routeIs('admin.payment-gateways*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="coins" class="w-4 h-4" />
                    Financeiro JUBAF
                </a>
            @endcan

            @can('gerenciar eventos')
                <a href="{{ route('admin.events.events.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.events*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="calendar-star" class="w-4 h-4" />
                    Gestão de Eventos
                </a>
            @endcan

            @can('gerenciar diretoria')
                <a href="{{ route('admin.Diretoria.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.Diretoria*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="briefcase" class="w-4 h-4" />
                    Gabinete (Diretoria)
                </a>
            @endcan

            @can('gerenciar mural')
                <a href="{{ route('admin.comunicacao.postagens.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('admin.comunicacao.postagens*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <x-icon name="pen-to-square" class="w-4 h-4" />
                    Mural Oficial
                </a>
            @endcan
        </nav>
    </div>
</aside>

<div id="sidebar-overlay" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    style="z-index: 30;"></div>
