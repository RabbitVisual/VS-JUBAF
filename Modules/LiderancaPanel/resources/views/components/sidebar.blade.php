<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-800 dark:bg-slate-950 border-r border-amber-900/30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none">
    <div class="flex flex-col h-full">
        <div class="flex items-center h-16 px-6 border-b border-amber-900/30">
            <a href="{{ route('lideranca.dashboard') }}" class="text-base font-bold text-white">Liderança Local</a>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            {{-- Dashboard --}}
            <a href="{{ route('lideranca.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.dashboard*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="grid-2" class="w-4 h-4" />
                Dashboard
            </a>

            {{-- ── Gestão Local ────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Gestão Local</div>

            @can('gerenciar caravana')
                <a href="{{ route('lideranca.caravanas.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.caravanas*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <x-icon name="bus" class="w-4 h-4" />
                    Minha Caravana
                </a>
            @endcan

            <a href="{{ route('lideranca.rebanho.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.rebanho*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="users" class="w-4 h-4" />
                Jovens da Igreja
            </a>

            <a href="{{ route('lideranca.eventos.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.eventos*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="calendar-star" class="w-4 h-4" />
                Eventos
            </a>

            {{-- ── Tesouraria & Transparência ─────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Financeiro</div>

            <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.tesouraria*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="coins" class="w-4 h-4" />
                Tesouraria Local
            </a>

            <a href="{{ route('lideranca.transparencia.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.transparencia*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="eye" class="w-4 h-4" />
                Transparência
            </a>

            {{-- ── Conselho (Diretoria Local) ─────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Conselho</div>

            <a href="{{ route('lideranca.conselho.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.conselho*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="gavel" class="w-4 h-4" />
                Aprovações
            </a>

            {{-- ── Hub de Recursos ─────────────────────── --}}
            @can('visualizar recursos')
                <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Hub de Recursos</div>

                <a href="{{ route('lideranca.sermoes.sermons.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.sermoes*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <x-icon name="book-open" class="w-4 h-4" />
                    Sermões e Estudos
                </a>

                <a href="{{ route('memberpanel.bible.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('memberpanel.bible*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <x-icon name="book-bible" class="w-4 h-4" />
                    Bíblia
                </a>
            @endcan

            {{-- ── Comunicação ─────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Comunicação</div>

            <a href="{{ route('mural.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('mural.index') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="bullhorn" class="w-4 h-4" />
                Mural Oficial
            </a>

            {{-- ── Perfil ──────────────────────────────── --}}
            <div class="pt-4 pb-1 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Conta</div>

            <a href="{{ route('lideranca.profile.show') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl {{ request()->routeIs('lideranca.profile*') ? 'bg-amber-900/30 text-amber-100' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="user" class="w-4 h-4" />
                Meu Perfil
            </a>
        </nav>
    </div>
</aside>

<div id="sidebar-overlay"
    class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    style="z-index: 30;"></div>
