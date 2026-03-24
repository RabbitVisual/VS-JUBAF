<div data-tour="sidebar" class="flex h-screen overflow-hidden fixed left-0 top-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex h-screen w-16 flex-col justify-between border-e border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="px-2 py-4 space-y-2">
            <a href="{{ route('memberpanel.dashboard') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="gauge-high" class="size-5" /></a>
            <a href="{{ route('mural.index') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 {{ request()->routeIs('mural.index') ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}" title="Mural Oficial"><x-icon name="bullhorn" class="size-5" /></a>
            <a href="{{ route('memberpanel.bible.index') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="book-bible" class="size-5" /></a>
            <a href="{{ route('memberpanel.events.index') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="calendar-days" class="size-5" /></a>
            <a href="{{ route('memberpanel.events.my-registrations') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="ticket" class="size-5" /></a>
            <a href="{{ route('memberpanel.sermons.index') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="microphone-lines" class="size-5" /></a>
            <a href="{{ route('memberpanel.notifications.index') }}" class="group relative flex justify-center rounded-sm px-2 py-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"><x-icon name="bell" class="size-5" /></a>
        </div>
    </div>

    <div class="flex h-screen w-64 flex-col justify-between border-e border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex-1 overflow-y-auto px-4 py-6">
            <ul class="space-y-2">
                <li><a href="{{ route('memberpanel.dashboard') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a></li>
                <li><a href="{{ route('mural.index') }}" class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-700 {{ request()->routeIs('mural.index') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300' : '' }}"><x-icon name="bullhorn" class="w-4 h-4" /> Mural Oficial</a></li>
                <li><a href="{{ route('memberpanel.profile.show') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Meu Perfil</a></li>
                <li>
                    <div x-data="{ expanded: false }">
                        <button @click="expanded = !expanded" class="w-full flex justify-between items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center gap-2">
                                <x-icon name="layer-group" class="w-4 h-4" /> Hub de Recursos
                            </div>
                            <x-icon name="chevron-down" class="w-3 h-3 transition-transform duration-200" x-bind:class="{ 'rotate-180': expanded }" />
                        </button>
                        <ul x-show="expanded" x-collapse class="mt-1 space-y-1 px-4 border-l border-gray-200 dark:border-gray-700 ml-6">
                            <li><a href="{{ route('memberpanel.bible.index') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700">Bíblia & Desafios</a></li>
                            <li><a href="{{ route('memberpanel.sermons.index') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700">Sermões & Estudos</a></li>
                        </ul>
                    </div>
                </li>
                <li><a href="{{ route('memberpanel.events.index') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Próximos Eventos</a></li>
                <li><a href="{{ route('memberpanel.events.my-registrations') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Minhas Inscrições</a></li>
                <li><a href="{{ route('memberpanel.notifications.index') }}" class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Notificações</a></li>
            </ul>
        </div>
    </div>
</div>

<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
