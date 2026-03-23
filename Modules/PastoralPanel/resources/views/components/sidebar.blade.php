@php
    $rebanho = request()->routeIs('pastor.rebanho*');
    $oracao = request()->routeIs('pastor.oracao*');
    $sermoes = request()->routeIs('pastor.sermoes*');
    $educacao = request()->routeIs('pastor.educacao*') || request()->routeIs('pastor.ebd*');
    $ministerios = request()->routeIs('pastor.ministerios*');
    $transparencia = request()->routeIs('pastor.transparencia*') || request()->routeIs('pastor.tesouraria*');
    $conselho = request()->routeIs('pastor.conselho*');
    $eventos = request()->routeIs('pastor.eventos*');
@endphp

<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-800 dark:bg-slate-950 border-r border-amber-900/30 dark:border-amber-800/20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
    x-data="{
        rebanhoOpen: {{ $rebanho ? 'true' : 'false' }},
        sermoesOpen: {{ $sermoes ? 'true' : 'false' }},
        ministeriosOpen: {{ $ministerios ? 'true' : 'false' }},
        educacaoOpen: {{ $educacao ? 'true' : 'false' }},
        transparenciaOpen: {{ $transparencia ? 'true' : 'false' }},
        conselhoOpen: {{ $conselho ? 'true' : 'false' }},
        eventosOpen: {{ $eventos ? 'true' : 'false' }},
    }">

    <div class="flex flex-col h-full">
        <div class="flex items-center h-16 px-6 border-b border-amber-900/30 dark:border-amber-800/20 bg-slate-800 dark:bg-slate-950">
            <a href="{{ route('pastor.dashboard') }}" class="flex items-center space-x-3 group w-full">
                <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/20 group-hover:shadow-amber-500/40 transition-all duration-300 group-hover:scale-105">
                    <img src="{{ asset('storage/image/logo_icon.png') }}" alt="Logo" class="w-7 h-7 object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold text-white tracking-tight leading-tight group-hover:text-amber-300 transition-colors">Vertex CBAV</span>
                    <span class="text-[10px] font-medium text-amber-200/80 uppercase tracking-widest">Gabinete Pastoral</span>
                </div>
            </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="{{ route('pastor.dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('pastor.dashboard*')
                    ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                    : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="gauge-high" class="w-5 h-5 mr-3 {{ request()->routeIs('pastor.dashboard*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                Dashboard
            </a>

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-amber-200/60 uppercase tracking-wider">Ministério</p>
            </div>

            <a href="{{ route('pastor.rebanho.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $rebanho ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="users-rays" class="w-5 h-5 mr-3 {{ $rebanho ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                Rebanho
            </a>

            @if(Route::has('pastor.oracao.index'))
            <a href="{{ route('pastor.oracao.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $oracao ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                <x-icon name="hands-praying" class="w-5 h-5 mr-3 {{ $oracao ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                Pedidos de Oração
            </a>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-amber-200/60 uppercase tracking-wider">Palavra & Ensino</p>
            </div>

            @if(Route::has('pastor.sermoes.sermons.index'))
            <div class="space-y-1">
                <button @click="sermoesOpen = !sermoesOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $sermoes ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="book-bible" class="w-5 h-5 mr-3 {{ $sermoes ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Estúdio da Palavra
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': sermoesOpen }" />
                </button>
                <div x-show="sermoesOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.sermoes.sermons.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.sermoes.sermons*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Sermões
                    </a>
                    <a href="{{ route('pastor.sermoes.categories.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.sermoes.categories*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Categorias
                    </a>
                    <a href="{{ route('pastor.sermoes.series.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.sermoes.series*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Séries
                    </a>
                    <a href="{{ route('pastor.sermoes.studies.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.sermoes.studies*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Estudos
                    </a>
                    <a href="{{ route('pastor.sermoes.commentaries.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.sermoes.commentaries*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Comentários
                    </a>
                </div>
            </div>
            @endif

            @if(Route::has('pastor.educacao.index'))
            <div class="space-y-1">
                <button @click="educacaoOpen = !educacaoOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $educacao ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="graduation-cap" class="w-5 h-5 mr-3 {{ $educacao ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Educação
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': educacaoOpen }" />
                </button>
                <div x-show="educacaoOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.educacao.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.educacao.index') && !request()->routeIs('pastor.ebd*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Visão geral
                    </a>
                    @if(Route::has('pastor.ebd.dashboard'))
                    <a href="{{ route('pastor.ebd.dashboard') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.dashboard*') && !request()->routeIs('pastor.ebd.classes*') && !request()->routeIs('pastor.ebd.courses*') && !request()->routeIs('pastor.ebd.lessons*') && !request()->routeIs('pastor.ebd.teachers*') && !request()->routeIs('pastor.ebd.students*') && !request()->routeIs('pastor.ebd.attendance*') && !request()->routeIs('pastor.ebd.evaluations*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Dashboard
                    </a>
                    <a href="{{ route('pastor.ebd.classes.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.classes*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Turmas
                    </a>
                    <a href="{{ route('pastor.ebd.courses.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.courses*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Cursos
                    </a>
                    <a href="{{ route('pastor.ebd.lessons.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.lessons*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Lições
                    </a>
                    <a href="{{ route('pastor.ebd.students.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.students*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Alunos
                    </a>
                    <a href="{{ route('pastor.ebd.teachers.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.teachers*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Professores
                    </a>
                    <a href="{{ route('pastor.ebd.attendance.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.attendance*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Presença
                    </a>
                    <a href="{{ route('pastor.ebd.evaluations.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ebd.evaluations*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        EBD · Avaliações
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-amber-200/60 uppercase tracking-wider">Igreja</p>
            </div>

            @if(Route::has('pastor.ministerios.index'))
            <div class="space-y-1">
                <button @click="ministeriosOpen = !ministeriosOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $ministerios ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="church" class="w-5 h-5 mr-3 {{ $ministerios ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Ministérios
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': ministeriosOpen }" />
                </button>
                <div x-show="ministeriosOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.ministerios.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ministerios.index') || request()->routeIs('pastor.ministerios.show') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Ministérios
                    </a>
                    <a href="{{ route('pastor.ministerios.plans.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.ministerios.plans*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Planos
                    </a>
                </div>
            </div>
            @endif

            @if(Route::has('pastor.transparencia.index'))
            <div class="space-y-1">
                <button @click="transparenciaOpen = !transparenciaOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $transparencia ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="sack-dollar" class="w-5 h-5 mr-3 {{ $transparencia ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Tesouraria
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': transparenciaOpen }" />
                </button>
                <div x-show="transparenciaOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.transparencia.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.transparencia.index') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Visão geral
                    </a>
                    @if(Route::has('pastor.tesouraria.dashboard'))
                    <a href="{{ route('pastor.tesouraria.dashboard') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.tesouraria.dashboard') || request()->routeIs('pastor.tesouraria.dashboard.index') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('pastor.tesouraria.entries.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.tesouraria.entries.index') || request()->routeIs('pastor.tesouraria.entries.edit') || request()->routeIs('pastor.tesouraria.entries.create') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Lançamentos
                    </a>
                    <a href="{{ route('pastor.tesouraria.campaigns.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.tesouraria.campaigns*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Campanhas
                    </a>
                    <a href="{{ route('pastor.tesouraria.goals.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.tesouraria.goals*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Metas
                    </a>
                    <a href="{{ route('pastor.tesouraria.reports.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.tesouraria.reports*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Relatórios
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if(Route::has('pastor.conselho.index'))
            <div class="space-y-1">
                <button @click="conselhoOpen = !conselhoOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $conselho ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="scale-balanced" class="w-5 h-5 mr-3 {{ $conselho ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Conselho
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': conselhoOpen }" />
                </button>
                <div x-show="conselhoOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.conselho.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.index') && !request()->routeIs('pastor.conselho.approvals*') && !request()->routeIs('pastor.conselho.meetings*') && !request()->routeIs('pastor.conselho.agendas*') && !request()->routeIs('pastor.conselho.documents*') && !request()->routeIs('pastor.conselho.projects*') && !request()->routeIs('pastor.conselho.members*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Visão geral
                    </a>
                    @if(Route::has('pastor.conselho.meetings.index'))
                    <a href="{{ route('pastor.conselho.meetings.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.meetings*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Reuniões
                    </a>
                    @endif
                    <a href="{{ route('pastor.conselho.approvals') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.approvals*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Aprovações
                    </a>
                    @if(Route::has('pastor.conselho.agendas.index'))
                    <a href="{{ route('pastor.conselho.agendas.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.agendas*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Pautas
                    </a>
                    @endif
                    @if(Route::has('pastor.conselho.documents.index'))
                    <a href="{{ route('pastor.conselho.documents.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.documents*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Documentos
                    </a>
                    @endif
                    @if(Route::has('pastor.conselho.projects.index'))
                    <a href="{{ route('pastor.conselho.projects.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.projects*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Projetos
                    </a>
                    @endif
                    @if(Route::has('pastor.conselho.members.index'))
                    <a href="{{ route('pastor.conselho.members.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.conselho.members*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Membros
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if(Route::has('pastor.eventos.index'))
            <div class="space-y-1">
                <button @click="eventosOpen = !eventosOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $eventos ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="flex items-center">
                        <x-icon name="calendar-days" class="w-5 h-5 mr-3 {{ $eventos ? 'text-amber-400' : 'text-slate-400 group-hover:text-amber-400' }}" />
                        Eventos
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': eventosOpen }" />
                </button>
                <div x-show="eventosOpen" style="display: none;" class="mt-1">
                    <a href="{{ route('pastor.eventos.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.eventos.index') && !request()->routeIs('pastor.eventos.show') && !request()->routeIs('pastor.eventos.registrations*') && !request()->routeIs('pastor.eventos.checkin*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Visão geral
                    </a>
                    @if(Route::has('pastor.eventos.checkin.index'))
                    <a href="{{ route('pastor.eventos.checkin.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg {{ request()->routeIs('pastor.eventos.checkin*') ? 'text-amber-300 bg-amber-500/10' : 'text-slate-400 hover:text-white' }}">
                        Check-in
                    </a>
                    @endif
                    @if(Route::has('admin.events.events.create'))
                    <a href="{{ route('admin.events.events.create') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg text-slate-400 hover:text-white">
                        Novo evento
                    </a>
                    @endif
                    @if(Route::has('admin.events.events.index'))
                    <a href="{{ route('admin.events.events.index') }}" class="flex items-center gap-2 px-4 py-2 pl-12 text-xs font-medium rounded-lg text-slate-400 hover:text-white">
                        Gerenciar no Admin
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </nav>
    </div>
</aside>

<div id="sidebar-overlay" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity" style="z-index: 30;"></div>
