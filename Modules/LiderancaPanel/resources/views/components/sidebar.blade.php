<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-800 dark:bg-slate-950 border-r border-amber-900/30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none">
    <div class="flex flex-col h-full">
        <div class="flex items-center h-16 px-6 border-b border-amber-900/30">
            <a href="{{ route('lideranca.dashboard') }}" class="text-base font-bold text-white">Liderança Local</a>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('lideranca.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.dashboard*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="grid-2" class="w-4 h-4" />
                Dashboard
            </a>

            @can('gerenciar caravana')
                <a href="{{ route('lideranca.caravanas.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.caravanas*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <x-icon name="bus" class="w-4 h-4" />
                    Minha Caravana
                </a>
            @endcan

            @can('visualizar recursos')
                <div x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
                        <div class="flex items-center gap-2">
                            <x-icon name="layer-group" class="w-4 h-4" />
                            Hub de Recursos
                        </div>
                        <x-icon name="chevron-down" class="w-3 h-3 transition-transform duration-200" x-bind:class="{ 'rotate-180': expanded }" />
                    </button>
                    <div x-show="expanded" x-collapse class="mt-1 space-y-1 px-4 border-l border-amber-900/30 ml-6">
                        <a href="{{ route('lideranca.sermoes.sermons.index') }}"
                            class="block px-4 py-2 text-sm rounded-xl {{ request()->routeIs('lideranca.sermoes*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/30' }}">
                            Sermões e Estudos
                        </a>
                        <a href="{{ route('memberpanel.bible.index') }}"
                            class="block px-4 py-2 text-sm rounded-xl {{ request()->routeIs('memberpanel.bible*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/30' }}">
                            Bíblia e Desafios
                        </a>
                    </div>
                </div>
            @endcan

            <a href="{{ route('mural.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('mural.index') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="bullhorn" class="w-4 h-4" />
                Mural Oficial
            </a>
        </nav>
    </div>
</aside>

<div id="sidebar-overlay"
    class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    style="z-index: 30;"></div>
