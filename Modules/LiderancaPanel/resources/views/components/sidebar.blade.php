<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-800 dark:bg-slate-950 border-r border-amber-900/30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none">
    <div class="flex flex-col h-full">
        <div class="flex items-center h-16 px-6 border-b border-amber-900/30">
            <a href="{{ route('lideranca.dashboard') }}" class="text-base font-bold text-white">Gabinete de Liderança</a>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('lideranca.dashboard') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Dashboard</a>
            <a href="{{ route('lideranca.rebanho.index') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Rebanho</a>
            <a href="{{ route('lideranca.profile.show') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Meu Perfil</a>
            @can('gerenciar igrejas')
                <a href="{{ route('lideranca.igrejas.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">
                    <x-icon name="church" class="w-4 h-4" />
                    Gestão de Igrejas
                </a>
            @endcan
            <a href="{{ route('lideranca.sermoes.sermons.index') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Sermões</a>
            <a href="{{ route('lideranca.transparencia.index') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Transparência</a>
            <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Tesouraria</a>
            <a href="{{ route('lideranca.conselho.index') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Conselho</a>
            <a href="{{ route('lideranca.eventos.index') }}"
                class="flex items-center px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">Eventos</a>
            @can('acesso painel lideranca')
                <a href="{{ route('lideranca.caravanas.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl text-slate-300 hover:bg-slate-700/50 hover:text-white">
                    <x-icon name="users" class="w-4 h-4" />
                    Minha Caravana
                </a>
            @endcan
        </nav>
    </div>
</aside>

<div id="sidebar-overlay"
    class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    style="z-index: 30;"></div>
