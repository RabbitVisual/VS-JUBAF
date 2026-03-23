@php
    $routes = [
        'treasury' => request()->routeIs('treasury.*'),
        'bible' => request()->routeIs('admin.bible*'),
        'churchcouncil' => request()->routeIs('admin.churchcouncil*'),
        'homepage' => request()->routeIs('admin.homepage*'),
        'events' => request()->routeIs('admin.events*') || request()->routeIs('admin.events.checkin*'),
        'marketplace' => request()->routeIs('admin.marketplace*'),
        'sermons' => request()->routeIs('admin.sermons*'),
        'ebd' => request()->routeIs('admin.ebd*'),
        'assets' => request()->routeIs('assets.admin.*'),
        'socialaction' => request()->routeIs('socialaction.admin.*'),
        'worship' => request()->routeIs('worship.admin.*'),
        'projection' => request()->routeIs('projection.*') || request()->routeIs('admin.projection.*'),
        'ministries' => request()->routeIs('admin.ministries*'),
    ];

    $hasTreasuryPermission = \Modules\Treasury\App\Models\TreasuryPermission::where('user_id', auth()->id())->first();
    $showTreasury = \Nwidart\Modules\Facades\Module::isEnabled('Treasury') && (auth()->user()?->isAdmin() || $hasTreasuryPermission);
@endphp

<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-slate-950 border-r border-gray-200 dark:border-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
    x-data="{
        treasuryOpen: {{ $routes['treasury'] ? 'true' : 'false' }},
        bibleOpen: {{ $routes['bible'] ? 'true' : 'false' }},
        churchcouncilOpen: {{ $routes['churchcouncil'] ? 'true' : 'false' }},
        homepageOpen: {{ $routes['homepage'] ? 'true' : 'false' }},
        eventsOpen: {{ $routes['events'] ? 'true' : 'false' }},
        marketplaceOpen: {{ $routes['marketplace'] ? 'true' : 'false' }},
        sermonsOpen: {{ $routes['sermons'] ? 'true' : 'false' }},
        intercessorOpen: {{ request()->routeIs('admin.intercessor*') ? 'true' : 'false' }},
        ebdOpen: {{ $routes['ebd'] ? 'true' : 'false' }},
        assetsOpen: {{ $routes['assets'] ? 'true' : 'false' }},
        socialActionOpen: {{ $routes['socialaction'] ? 'true' : 'false' }},
        worshipOpen: {{ $routes['worship'] ? 'true' : 'false' }},
        projectionOpen: {{ $routes['projection'] ? 'true' : 'false' }},
        ministriesOpen: {{ $routes['ministries'] ? 'true' : 'false' }},
    }">

    <div class="flex flex-col h-full">
        <!-- Brand Logo -->
        <div class="flex items-center h-16 px-6 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-slate-950">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group w-full">
                <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-linear-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-all duration-300 group-hover:scale-105">
                     <img src="{{ asset('storage/image/logo_icon.png') }}" alt="Logo" class="w-7 h-7 object-contain" >
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold text-gray-900 dark:text-gray-100 tracking-tight leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Vertex CBAV</span>
                    <span class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">Admin Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">



            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard*')
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="gauge-high" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500 dark:group-hover:text-blue-400' }} transition-colors" />
                Dashboard
            </a>


            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Sistema</p>
            </div>

            @if(auth()->user()->isAdmin())
                <!-- Modules -->
                <a href="{{ route('admin.modules.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.modules*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="cubes" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.modules*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Módulos
                </a>

                <!-- CEP Ranges -->
                <a href="{{ route('admin.cep-ranges.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.cep-ranges*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="map-location-dot" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.cep-ranges*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Gerenciar CEPs
                </a>

                <!-- Settings -->
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.settings*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="gear" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.settings*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Configurações
                </a>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Gestão</p>
            </div>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="users" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Membros
            </a>

            @if(Route::has('admin.reports.family-demographics.index'))
            <a href="{{ route('admin.reports.family-demographics.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.reports.family-demographics*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="chart-pie" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.family-demographics*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Inteligência Familiar
            </a>
            @endif

            <!-- Ministries (Collapsible) -->
            @if(\Nwidart\Modules\Facades\Module::isEnabled('Ministries'))
            <div class="space-y-1">
                <button @click="ministriesOpen = !ministriesOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['ministries'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="church" class="w-5 h-5 mr-3 {{ $routes['ministries'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Ministérios</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': ministriesOpen }" />
                </button>
                <div x-show="ministriesOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.ministries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Lista de Ministérios
                        </a>
                        <a href="{{ route('admin.ministries.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Ministério
                        </a>
                        <a href="{{ route('admin.ministries.plans.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.plans*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="diagram-project" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Planos Estratégicos
                        </a>
                        <a href="{{ route('admin.churchcouncil.ministries.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.ministries.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="traffic-light" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Visão do Conselho (Semáforo)
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Assets (Collapsible) -->
            <div class="space-y-1">
                <button @click="assetsOpen = !assetsOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['assets'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="boxes-stacked" class="w-5 h-5 mr-3 {{ $routes['assets'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Patrimônio</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': assetsOpen }" />
                </button>
                <div x-show="assetsOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('assets.admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('assets.admin.assets.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.assets.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="box" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Inventário
                        </a>
                        <a href="{{ route('assets.admin.movements.history') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.movements*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="right-left" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Movimentações
                        </a>
                        <a href="{{ route('assets.admin.maintenances.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.maintenances*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="wrench" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Manutenções
                        </a>
                        <a href="{{ route('assets.admin.terms.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.terms*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="file-contract" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Termos
                        </a>
                        <a href="{{ route('assets.admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.categories*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('assets.admin.locations.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.locations*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="location-dot" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Locais
                        </a>
                    </div>
                </div>
            </div>

            <!-- HomePage (Collapsible) -->
            <div class="space-y-1">
                <button @click="homepageOpen = !homepageOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['homepage'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="house" class="w-5 h-5 mr-3 {{ $routes['homepage'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>HomePage</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': homepageOpen }" />
                </button>
                <div x-show="homepageOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                         <a href="{{ route('admin.homepage.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="pen-to-square" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Conteúdo
                        </a>
                        <a href="{{ route('admin.homepage.carousel.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.carousel*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="images" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Carousel
                        </a>
                        <a href="{{ route('admin.homepage.contacts.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.contacts*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="envelope" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Central de Contatos
                        </a>
                        <a href="{{ route('admin.homepage.newsletter.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.newsletter*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="newspaper" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Newsletter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bible (Collapsible) -->
            <div class="space-y-1">
                <button @click="bibleOpen = !bibleOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['bible'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="book-bible" class="w-5 h-5 mr-3 {{ $routes['bible'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Bíblia Digital</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': bibleOpen }" />
                </button>
                <div x-show="bibleOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.bible.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="book-open" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Versões e Livros
                        </a>
                        <a href="{{ route('admin.bible.import') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.import*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="file-import" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Importação
                        </a>
                        <a href="{{ route('admin.bible.plans.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.plans.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="calendar-check" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Planos de Leitura
                        </a>
                        <a href="{{ route('admin.bible.reports.church-plan') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.reports.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-line" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Relatório Plano da Igreja
                        </a>
                    </div>
                </div>
            </div>

            <!-- Church Council (Collapsible) -->
            <div class="space-y-1">
                <button @click="churchcouncilOpen = !churchcouncilOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['churchcouncil'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="users-rectangle" class="w-5 h-5 mr-3 {{ $routes['churchcouncil'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>{{ $council_display_name ?? 'Conselho da Igreja' }}</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': churchcouncilOpen }" />
                </button>
                <div x-show="churchcouncilOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.churchcouncil.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Visão Geral
                        </a>
                        <a href="{{ route('admin.churchcouncil.ministries.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.ministries.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="traffic-light" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Ministérios (Semáforo)
                        </a>
                        <a href="{{ route('admin.churchcouncil.members.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.members*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="users" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Membros
                        </a>
                        <a href="{{ route('admin.churchcouncil.meetings.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.meetings*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="calendar-days" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Reuniões
                        </a>
                        <a href="{{ route('admin.churchcouncil.approvals.index', ['type' => 'ministry_plan']) }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.approvals*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="check-double" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Aprovações de Planos
                        </a>
                        <a href="{{ route('admin.churchcouncil.planning.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.planning*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Planejamento (Homologação)
                        </a>
                        <a href="{{ route('admin.churchcouncil.assembly.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.assembly*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="scale-balanced" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Recomendações Assembleia
                        </a>
                        <a href="{{ route('admin.churchcouncil.discipline.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.discipline*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="gavel" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Disciplina
                        </a>
                        <a href="{{ route('admin.churchcouncil.transfers.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.transfers*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="right-left" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Transferências
                        </a>
                        <a href="{{ route('admin.churchcouncil.documents.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.documents*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="file-lines" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Documentos
                        </a>
                        <a href="{{ route('admin.churchcouncil.projects.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.projects*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="diagram-project" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Projetos
                        </a>
                        <a href="{{ route('assets.admin.reservations.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('assets.admin.reservations*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="box" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Reservas de Equipamentos
                        </a>
                        <a href="{{ route('admin.churchcouncil.settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.churchcouncil.settings*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="gears" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <a href="{{ route('admin.notifications.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.notifications*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="bell" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.notifications*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Notificações
            </a>

            @if(auth()->user()->isAdmin())
                <!-- Password Resets Monitoring -->
                <a href="{{ route('admin.password-resets.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.password-resets*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="key" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.password-resets*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Monitoramento de Senhas
                </a>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Educação e Eventos</p>
            </div>

            <!-- EBD (Collapsible) -->
            <div class="space-y-1">
                <button @click="ebdOpen = !ebdOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['ebd'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="school" style="duotone" class="w-5 h-5 mr-3 {{ $routes['ebd'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Escola Bíblica</span>
                    </div>
                    <x-icon name="chevron-down" style="duotone" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': ebdOpen }" />
                </button>
                <div x-show="ebdOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.ebd.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('admin.ebd.courses.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.courses*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="graduation-cap" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Cursos
                        </a>
                        <a href="{{ route('admin.ebd.classes.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.classes*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="users-viewfinder" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Turmas
                        </a>
                        <a href="{{ route('admin.ebd.teachers.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.teachers*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chalkboard-teacher" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Professores
                        </a>
                        <a href="{{ route('admin.ebd.students.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.students*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="user-graduate" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Alunos
                        </a>
                        <a href="{{ route('admin.ebd.lessons.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.lessons*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="book-open" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Lições
                        </a>
                        <a href="{{ route('admin.ebd.evaluations.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.evaluations*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-check" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Avaliações
                        </a>
                        <a href="{{ route('admin.ebd.gamification.levels.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.gamification.levels*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="bars-progress" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Níveis (EBD)
                        </a>
                        <a href="{{ route('admin.ebd.gamification.badges.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.gamification.badges*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="medal" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Medalhas (EBD)
                        </a>
                        <a href="{{ route('admin.ebd.attendance.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.attendance*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-user" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Presenças
                        </a>
                        <a href="{{ route('admin.ebd.settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ebd.settings*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="gears" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Configurações
                        </a>
                    </div>
                </div>
            </div>





            <!-- Sermons (Collapsible) -->
             <div class="space-y-1">
                <button @click="sermonsOpen = !sermonsOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['sermons'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="microphone-lines" class="w-5 h-5 mr-3 {{ $routes['sermons'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Sermões</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': sermonsOpen }" />
                </button>
                <div x-show="sermonsOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.sermons.sermons.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.sermons.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Listar Sermões
                        </a>
                        <a href="{{ route('admin.sermons.sermons.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.sermons.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Sermão
                        </a>
                        <a href="{{ route('admin.sermons.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.categories*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('admin.sermons.series.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.series*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="layer-group" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Séries
                        </a>
                        <a href="{{ route('admin.sermons.studies.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.studies*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="book-open-reader" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Estudos
                        </a>
                        <a href="{{ route('admin.sermons.commentaries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.commentaries*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="comments" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Comentários
                        </a>
                    </div>
                </div>
            </div>



            <!-- Intercession (Collapsible) -->
            <div class="space-y-1">
                <button @click="intercessorOpen = !intercessorOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.intercessor*') ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="hands-praying" style="duotone" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.intercessor*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Intercessão</span>
                    </div>
                    <x-icon name="chevron-down" style="duotone" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': intercessorOpen }" />
                </button>
                <div x-show="intercessorOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.intercessor.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('admin.intercessor.moderation.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.moderation.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-check" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Moderação de Pedidos
                        </a>
                        <a href="{{ route('admin.intercessor.moderation.testimonies.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.moderation.testimonies.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="quote-right" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Moderação de Testemunhos
                        </a>
                        <a href="{{ route('admin.intercessor.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.categories*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('admin.intercessor.team.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.team*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="users" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Intercessores
                        </a>
                        <a href="{{ route('admin.intercessor.reports.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.reports*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-line" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Relatórios de Intercessão
                        </a>
                        <a href="{{ route('admin.intercessor.settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.settings*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="gears" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Worship / Louvor (Collapsible) -->
            <div class="space-y-1">
                <button @click="worshipOpen = !worshipOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['worship'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="music" class="w-5 h-5 mr-3 {{ $routes['worship'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Louvor</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': worshipOpen }" />
                </button>
                <div x-show="worshipOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('worship.admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('worship.admin.setlists.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.setlists.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Cultos e Repertórios
                        </a>
                        <a href="{{ route('worship.admin.songs.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.songs.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="music" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Músicas
                        </a>
                        <a href="{{ route('worship.admin.rosters.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.rosters.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="calendar-days" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Escalas
                        </a>
                        <a href="{{ route('worship.admin.instruments.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.instruments.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="guitar" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Instrumentos
                        </a>
                        <a href="{{ route('worship.admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.categories.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('worship.admin.academy.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.academy.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="graduation-cap" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Worship Academy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Projection (Collapsible) -->
            <div class="space-y-1">
                <button @click="projectionOpen = !projectionOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['projection'] ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="projector" class="w-5 h-5 mr-3 {{ $routes['projection'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Projeção</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': projectionOpen }" />
                </button>
                <div x-show="projectionOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('projection.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('projection.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="display" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Abrir Console
                        </a>
                        <a href="{{ route('admin.projection.team.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.projection.team.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="users" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Equipe de Mídia
                        </a>
                    </div>
                </div>
            </div>

            <!-- Events (Collapsible) -->
            @can('viewAny', \Modules\Events\App\Models\Event::class)
             <div class="space-y-1">
                <button @click="eventsOpen = !eventsOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['events'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="calendar-days" class="w-5 h-5 mr-3 {{ $routes['events'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Eventos</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': eventsOpen }" />
                </button>
                <div x-show="eventsOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.events.events.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.events.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Listar Eventos
                        </a>
                        <a href="{{ route('admin.events.events.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.events.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Evento
                        </a>
                        @can('checkin', \Modules\Events\App\Models\Event::class)
                        <a href="{{ route('admin.events.checkin.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.checkin.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="qrcode" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Check-in (Scanner)
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcan

            <!-- Marketplace / Loja Missionária (Collapsible) -->
            @if(\Nwidart\Modules\Facades\Module::isEnabled('Marketplace'))
            <div class="space-y-1">
                <button @click="marketplaceOpen = !marketplaceOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['marketplace'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="store" class="w-5 h-5 mr-3 {{ $routes['marketplace'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Loja Missionária</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': marketplaceOpen }" />
                </button>
                <div x-show="marketplaceOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.marketplace.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.marketplace.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('admin.marketplace.products.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.marketplace.products.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="box" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Produtos
                        </a>
                        <a href="{{ route('admin.marketplace.orders.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.marketplace.orders.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="cart-shopping" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Pedidos
                        </a>
                        <a href="{{ route('admin.marketplace.pickup-locations.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.marketplace.pickup-locations.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="location-dot" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Pontos de retirada
                        </a>
                        <a href="{{ route('admin.marketplace.coupons.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.marketplace.coupons.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tag" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Cupons
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gamification Section -->
             <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Gamificação</p>
            </div>

            <a href="{{ route('admin.badges.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.badges*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="badge-check" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.badges*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Badges
            </a>

            <a href="{{ route('admin.gamification-levels.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.gamification-levels*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="layer-group" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.gamification-levels*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Níveis
            </a>

            <a href="{{ route('admin.cbav-bot.settings.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.cbav-bot*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="robot" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.cbav-bot*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Bot Elias
            </a>

            <!-- Financial Section -->
             <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Financeiro</p>
            </div>

            <a href="{{ route('admin.transactions.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.transactions*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="money-bill-transfer" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.transactions*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Transações
            </a>

            <a href="{{ route('admin.payment-gateways.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.payment-gateways*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="credit-card" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.payment-gateways*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Gateways
            </a>

            <!-- Ação Social -->
            @if(Module::isEnabled('SocialAction'))
            <div class="space-y-1">
                <button @click="socialActionOpen = !socialActionOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('socialaction.admin.*') ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="hand-holding-heart" class="w-5 h-5 mr-3 {{ request()->routeIs('socialaction.admin.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Ação Social</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': socialActionOpen }" />
                </button>
                <div x-show="socialActionOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('socialaction.admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('socialaction.admin.beneficiaries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.beneficiaries.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="user-group" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Beneficiários
                        </a>
                        <a href="{{ route('socialaction.admin.volunteers.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.volunteers.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="hands-holding-heart" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Voluntários
                        </a>
                        <a href="{{ route('socialaction.admin.prayer.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.prayer.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="hands-praying" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Pedidos de Oração (Social)
                        </a>
                        <a href="{{ route('socialaction.admin.stock.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.stock.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="boxes-stacked" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Estoque & Kits
                        </a>
                        <a href="{{ route('socialaction.admin.campaigns.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.campaigns.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="bullhorn" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Campanhas
                        </a>
                        <a href="{{ route('socialaction.admin.assistance.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('socialaction.admin.assistance.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="hand-holding-heart" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Assistências
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @if ($showTreasury)
                <div class="space-y-1">
                    <button @click="treasuryOpen = !treasuryOpen" data-treasury-toggle
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['treasury'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                        <div class="flex items-center">
                            <x-icon name="building-columns" class="w-5 h-5 mr-3 {{ $routes['treasury'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                            <span>Tesouraria</span>
                        </div>
                        <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': treasuryOpen }" />
                    </button>
                     <div x-show="treasuryOpen" style="display: none;">
                        <div class="pl-12 pr-4 space-y-1 mt-1" data-treasury-menu>
                             <a href="{{ route('treasury.dashboard.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.dashboard*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                Dashboard
                            </a>
                            <a href="{{ route('treasury.entries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.entries*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                <x-icon name="money-bill-transfer" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                Entradas
                            </a>
                            <a href="{{ route('treasury.campaigns.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.campaigns*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                <x-icon name="bullhorn" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                Campanhas
                            </a>
                            <a href="{{ route('treasury.goals.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.goals*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                <x-icon name="bullseye" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                Metas
                            </a>
                            <a href="{{ route('treasury.reports.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.reports*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                <x-icon name="chart-line" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                Relatórios
                            </a>
                            @if (auth()->user()->isAdmin() || ($hasTreasuryPermission && $hasTreasuryPermission->isAdmin()))
                                <a href="{{ route('treasury.permissions.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('treasury.permissions*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                                    <x-icon name="key" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                                    Permissões
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bottom Section -->
            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('memberpanel.dashboard') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200 transition-all duration-200 group">
                    <x-icon name="arrow-left" class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" />
                    Painel de Membros
                </a>
            </div>


            <!-- Copyright -->
            <div class="px-6 py-4 mt-auto">
                <p class="text-[9px] text-center text-gray-400 dark:text-gray-600 font-bold uppercase tracking-widest">
                    Vertex Solutions © {{ date('Y') }}
                </p>
            </div>

        </nav>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity" style="z-index: 30;"></div>
