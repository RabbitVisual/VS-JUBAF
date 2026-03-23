@extends('admin::components.layouts.master')

@php
    $totalModules = count($modulesData);
    $enabledCount = collect($modulesData)->where('enabled', true)->count();
    $coreCount = collect($modulesData)->where('is_core', true)->count();
    $moduleIcons = [
        'admin' => 'cubes',
        'assets' => 'box-archive',
        'bible' => 'book-bible',
        'churchcouncil' => 'people-roof',
        'ebd' => 'school',
        'events' => 'calendar-days',
        'gamification' => 'trophy',
        'homepage' => 'house',
        'intercessor' => 'hands-praying',
        'memberpanel' => 'users',
        'ministries' => 'church',
        'notifications' => 'bell',
        'paymentgateway' => 'credit-card',
        'projection' => 'tv',
        'sermons' => 'book-open-reader',
        'socialaction' => 'hand-holding-heart',
        'treasury' => 'landmark',
        'worship' => 'music',
    ];
@endphp

@section('content')
    <div class="space-y-8">
        <!-- Hero (padrão Configurações) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Sistema</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Módulos</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Gerenciamento de Módulos</h1>
                    <p class="text-gray-300 max-w-xl">Ative, desative e consulte informações dos módulos do sistema. Módulos core são protegidos e não podem ser desativados.</p>
                </div>
                <div class="flex flex-shrink-0 gap-3">
                    <button type="button" onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="refresh" class="w-5 h-5 text-blue-300" />
                        Atualizar lista
                    </button>
                </div>
            </div>
        </div>

        <!-- Contador -->
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">{{ $totalModules }}</strong> módulos</span>
            <span class="text-gray-400 dark:text-gray-500">|</span>
            <span class="text-gray-600 dark:text-gray-400"><strong class="text-green-600 dark:text-green-400">{{ $enabledCount }}</strong> ativos</span>
            <span class="text-gray-400 dark:text-gray-500">|</span>
            <span class="text-gray-600 dark:text-gray-400"><strong class="text-blue-600 dark:text-blue-400">{{ $coreCount }}</strong> core</span>
        </div>

        <!-- Filtros e busca -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-stretch sm:items-center">
                <div class="flex items-center gap-2 flex-1 max-w-md">
                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex-1">
                        <x-icon name="search" class="w-4 h-4 text-gray-400 ml-3 shrink-0" />
                        <input type="text" id="moduleSearch" placeholder="Buscar por nome, descrição ou palavras-chave..."
                            class="flex-1 min-w-0 py-2.5 pr-3 pl-0 bg-transparent border-0 text-gray-900 dark:text-white placeholder:text-gray-400 text-sm focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="filterModules('all')" class="filter-btn px-4 py-2 text-sm font-medium rounded-xl transition-colors bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800" data-filter="all">Todos</button>
                    <button type="button" onclick="filterModules('enabled')" class="filter-btn px-4 py-2 text-sm font-medium rounded-xl transition-colors text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50 border border-transparent" data-filter="enabled">Ativos</button>
                    <button type="button" onclick="filterModules('disabled')" class="filter-btn px-4 py-2 text-sm font-medium rounded-xl transition-colors text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50 border border-transparent" data-filter="disabled">Inativos</button>
                    <button type="button" onclick="filterModules('core')" class="filter-btn px-4 py-2 text-sm font-medium rounded-xl transition-colors text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50 border border-transparent" data-filter="core">Core</button>
                </div>
                <div class="flex items-center gap-2">
                    <label for="sortSelect" class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ordenar</label>
                    <select id="sortSelect" onchange="applySort()" class="py-2 pl-3 pr-8 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer">
                        <option value="priority">Prioridade</option>
                        <option value="name">Nome</option>
                        <option value="status">Status (ativos primeiro)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid de módulos -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="modulesGrid">
            @foreach($modulesData as $module)
                @php
                    $currentIcon = $moduleIcons[strtolower($module['alias'])] ?? 'cube';
                    $keywordsList = $module['keywords'] ?? [];
                    $author = $module['author'] ?? [];
                    $authorName = is_array($author) ? ($author['name'] ?? 'Reinan Rodrigues') : 'Reinan Rodrigues';
                    $authorEmail = is_array($author) ? ($author['email'] ?? '') : '';
                    $authorCompany = is_array($author) ? ($author['company'] ?? '© 2026 Vertex Solution LTDA') : '© 2026 Vertex Solution LTDA';
                @endphp
                <div class="module-card group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 flex flex-col h-full cursor-pointer"
                     data-name="{{ strtolower($module['name']) }}"
                     data-description="{{ strtolower(e($module['description'] ?? '')) }}"
                     data-keywords="{{ e(json_encode($keywordsList)) }}"
                     data-status="{{ $module['enabled'] ? 'enabled' : 'disabled' }}"
                     data-is-core="{{ isset($module['is_core']) && $module['is_core'] ? '1' : '0' }}"
                     data-module-name="{{ e($module['name']) }}"
                     data-module-alias="{{ e($module['alias']) }}"
                     data-module-version="{{ e($module['version'] ?? '1.0.0') }}"
                     data-module-description="{{ e($module['description'] ?? '') }}"
                     data-module-priority="{{ (int)($module['priority'] ?? 0) }}"
                     data-module-enabled="{{ $module['enabled'] ? '1' : '0' }}"
                     data-author-name="{{ e($authorName) }}"
                     data-author-email="{{ e($authorEmail) }}"
                     data-author-company="{{ e($authorCompany) }}"
                     data-settings-route="{{ $module['settings_route'] ?? '' }}"
                     data-settings-url="{{ !empty($module['settings_route']) ? route($module['settings_route']) : '' }}"
                     onclick="openModal(this)">

                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl group-hover:scale-105 transition-transform">
                                <x-icon :name="$currentIcon" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if (isset($module['is_core']) && $module['is_core'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Core</span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $module['enabled'] ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800' : 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700/30 dark:text-gray-400 dark:border-gray-600' }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $module['enabled'] ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $module['enabled'] ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $module['name'] }}</h3>
                            <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-lg">v{{ $module['version'] ?? '1.0.0' }}</span>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 min-h-[2.5rem]">
                            {{ $module['description'] ?: 'Sem descrição disponível.' }}
                        </p>

                        @if (!empty($keywordsList))
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach(array_slice($keywordsList, 0, 5) as $kw)
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">{{ $kw }}</span>
                                @endforeach
                                @if (count($keywordsList) > 5)
                                    <span class="text-[10px] text-gray-400">+{{ count($keywordsList) - 5 }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="space-y-2">
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 font-mono bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded-xl">
                                <span title="Alias"><x-icon name="code" class="w-3 h-3 inline mr-1" />{{ $module['alias'] }}</span>
                                <span title="Prioridade"><x-icon name="sort-ascending" class="w-3 h-3 inline mr-1" />{{ $module['priority'] }}</span>
                            </div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 flex justify-between items-center pt-1">
                                <span>{{ $authorCompany }}</span>
                                <span title="Desenvolvedor">{{ $authorName }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between rounded-b-3xl" onclick="event.stopPropagation()">
                        @if (isset($module['is_core']) && $module['is_core'])
                            <span class="text-xs text-gray-400 italic flex items-center">
                                <x-icon name="lock-closed" class="w-3 h-3 mr-1" /> Protegido
                            </span>
                            @if(!empty($module['settings_route']))
                                <a href="{{ route($module['settings_route']) }}" class="p-2 rounded-xl text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Configurações">
                                    <x-icon name="cog" class="w-5 h-5" />
                                </a>
                            @else
                                <span class="p-2 text-gray-300 dark:text-gray-600"><x-icon name="cog" class="w-5 h-5" /></span>
                            @endif
                        @else
                            <button type="button" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline" onclick="event.stopPropagation(); openModal(this.closest('.module-card'))">Ver detalhes</button>
                            <div class="flex items-center gap-2">
                                @if ($module['enabled'])
                                    <button type="button" onclick="event.stopPropagation(); toggleModule('{{ $module['name'] }}', false)"
                                        class="flex items-center justify-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all shadow-sm">
                                        <x-icon name="ban" class="w-4 h-4 mr-2" /> Desativar
                                    </button>
                                @else
                                    <button type="button" onclick="event.stopPropagation(); toggleModule('{{ $module['name'] }}', true)"
                                        class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow-md transition-all">
                                        <x-icon name="check" class="w-4 h-4 mr-2" /> Ativar
                                    </button>
                                @endif
                                @if(!empty($module['settings_route']))
                                    <a href="{{ route($module['settings_route']) }}" class="p-2 rounded-xl text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:shadow-sm transition-colors" title="Configurações">
                                        <x-icon name="cog" class="w-5 h-5" />
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        <div id="noResults" class="hidden p-12 text-center bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <x-icon name="search" class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhum módulo encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Ajuste os filtros ou o termo de busca.</p>
        </div>

        <!-- Rodapé de créditos -->
        <div class="py-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
            <p><strong class="text-gray-700 dark:text-gray-300">DEV:</strong> Reinan Rodrigues &lt;<a href="mailto:r.rodriguesjs@gmail.com" class="text-blue-600 dark:text-blue-400 hover:underline">r.rodriguesjs@gmail.com</a>&gt; &nbsp;|&nbsp; <strong class="text-gray-700 dark:text-gray-300">Empresa:</strong> © 2026 Vertex Solution LTDA</p>
        </div>
    </div>

    <!-- Modal detalhe do módulo -->
    <div id="moduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="moduleModalContent" class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div id="modalIconWrap" class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-900/20">
                                <x-icon name="cube" id="modalIcon" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white"></h2>
                                <p id="modalSubtitle" class="text-sm text-gray-500 dark:text-gray-400"></p>
                            </div>
                        </div>
                        <button type="button" onclick="closeModal()" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 transition-colors">
                            <x-icon name="xmark" class="w-5 h-5" />
                        </button>
                    </div>
                    <p id="modalDescription" class="text-sm text-gray-600 dark:text-gray-400 mb-4"></p>
                    <div id="modalKeywords" class="flex flex-wrap gap-2 mb-4"></div>
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Versão</span>
                            <span id="modalVersion" class="font-mono font-bold text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Prioridade</span>
                            <span id="modalPriority" class="font-bold text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Status</span>
                            <span id="modalStatus" class="font-bold"></span>
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider">Core</span>
                            <span id="modalCore" class="font-bold text-gray-900 dark:text-white"></span>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Créditos</p>
                        <p id="modalCredits" class="text-sm text-gray-700 dark:text-gray-300"></p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal()" class="flex-1 py-2.5 px-4 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Fechar</button>
                        <a id="modalSettingsLink" href="#" class="hidden flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-center transition-colors">Abrir configurações</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('moduleSearch');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const modulesGrid = document.getElementById('modulesGrid');
        const modules = document.querySelectorAll('.module-card');
        const noResults = document.getElementById('noResults');
        const moduleModal = document.getElementById('moduleModal');
        const sortSelect = document.getElementById('sortSelect');
        const iconMap = @json($moduleIcons);

        let currentFilter = 'all';

        function updateFilter(filter) {
            currentFilter = filter;
            filterBtns.forEach(btn => {
                if (btn.dataset.filter === filter) {
                    btn.classList.add('bg-blue-50', 'text-blue-700', 'dark:bg-blue-900/30', 'dark:text-blue-300', 'border', 'border-blue-200', 'dark:border-blue-800');
                    btn.classList.remove('text-gray-600', 'hover:bg-gray-50', 'dark:text-gray-400', 'dark:hover:bg-gray-700/50', 'border-transparent');
                } else {
                    btn.classList.remove('bg-blue-50', 'text-blue-700', 'dark:bg-blue-900/30', 'dark:text-blue-300', 'border', 'border-blue-200', 'dark:border-blue-800');
                    btn.classList.add('text-gray-600', 'hover:bg-gray-50', 'dark:text-gray-400', 'dark:hover:bg-gray-700/50', 'border-transparent');
                }
            });
            applyFilters();
        }

        function filterModules(filter) {
            updateFilter(filter);
        }

        function applyFilters() {
            const query = (searchInput.value || '').toLowerCase();
            let visibleCount = 0;
            modules.forEach(module => {
                const name = module.dataset.name || '';
                const desc = module.dataset.description || '';
                const status = module.dataset.status;
                const isCore = module.dataset.isCore === '1';
                let keywordsStr = '';
                try {
                    const kw = JSON.parse(module.dataset.keywords || '[]');
                    keywordsStr = Array.isArray(kw) ? kw.join(' ').toLowerCase() : '';
                } catch (e) {}
                const matchesSearch = !query || name.includes(query) || desc.includes(query) || keywordsStr.includes(query);
                const matchesFilter = currentFilter === 'all' ||
                    (currentFilter === 'enabled' && status === 'enabled') ||
                    (currentFilter === 'disabled' && status === 'disabled') ||
                    (currentFilter === 'core' && isCore);
                if (matchesSearch && matchesFilter) {
                    module.style.display = 'flex';
                    visibleCount++;
                } else {
                    module.style.display = 'none';
                }
            });
            noResults.classList.toggle('hidden', visibleCount > 0);
        }

        searchInput.addEventListener('input', applyFilters);

        function applySort() {
            const order = sortSelect.value;
            const cards = Array.from(modules);
            cards.sort((a, b) => {
                if (order === 'name') {
                    return (a.dataset.moduleName || '').localeCompare(b.dataset.moduleName || '');
                }
                if (order === 'status') {
                    const ae = a.dataset.moduleEnabled === '1' ? 1 : 0;
                    const be = b.dataset.moduleEnabled === '1' ? 1 : 0;
                    return be - ae;
                }
                return (parseInt(b.dataset.modulePriority, 10) || 0) - (parseInt(a.dataset.modulePriority, 10) || 0);
            });
            cards.forEach(c => modulesGrid.appendChild(c));
        }

        function openModal(cardEl) {
            if (!cardEl || !cardEl.dataset) return;
            const name = cardEl.dataset.moduleName || '';
            const alias = (cardEl.dataset.moduleAlias || '').toLowerCase();
            const description = cardEl.dataset.moduleDescription || 'Sem descrição.';
            const version = cardEl.dataset.moduleVersion || '1.0.0';
            const priority = cardEl.dataset.modulePriority || '0';
            const enabled = cardEl.dataset.moduleEnabled === '1';
            const isCore = cardEl.dataset.isCore === '1';
            const authorName = cardEl.dataset.authorName || 'Reinan Rodrigues';
            const authorEmail = cardEl.dataset.authorEmail || '';
            const authorCompany = cardEl.dataset.authorCompany || '© 2026 Vertex Solution LTDA';
            const settingsUrl = cardEl.dataset.settingsUrl || '';
            let keywords = [];
            try {
                keywords = JSON.parse(cardEl.dataset.keywords || '[]');
            } catch (e) {}
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalSubtitle').textContent = alias;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('modalVersion').textContent = 'v' + version;
            document.getElementById('modalPriority').textContent = priority;
            document.getElementById('modalStatus').innerHTML = enabled ? '<span class="text-green-600 dark:text-green-400">Ativo</span>' : '<span class="text-gray-500">Inativo</span>';
            document.getElementById('modalCore').textContent = isCore ? 'Sim' : 'Não';
            document.getElementById('modalCredits').textContent = authorCompany + ' · ' + authorName + (authorEmail ? ' (' + authorEmail + ')' : '');
            const kwEl = document.getElementById('modalKeywords');
            kwEl.innerHTML = '';
            keywords.forEach(k => {
                const span = document.createElement('span');
                span.className = 'text-xs px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                span.textContent = k;
                kwEl.appendChild(span);
            });
            const link = document.getElementById('modalSettingsLink');
            if (settingsUrl) {
                link.href = settingsUrl;
                link.classList.remove('hidden');
            } else {
                link.classList.add('hidden');
            }
            moduleModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            moduleModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !moduleModal.classList.contains('hidden')) closeModal();
        });

        async function toggleModule(moduleName, enable) {
            if (!confirm('Tem certeza que deseja ' + (enable ? 'ativar' : 'desativar') + ' o módulo "' + moduleName + '"?')) return;
            window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: enable ? 'Ativando módulo...' : 'Desativando módulo...' } }));
            const btn = event.currentTarget;
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            try {
                const url = enable ? '{{ url("admin/modules") }}/' + moduleName + '/enable' : '{{ url("admin/modules") }}/' + moduleName + '/disable';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                window.dispatchEvent(new CustomEvent('stop-loading'));
                if (data.success) {
                    setTimeout(function() { window.location.reload(); }, 400);
                } else {
                    alert(data.message || 'Erro ao processar');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                console.error(error);
                alert('Erro de conexão');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }
    </script>
@endsection
