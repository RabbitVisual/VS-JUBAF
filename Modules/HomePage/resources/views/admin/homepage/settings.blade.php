@extends('admin::components.layouts.master')

@section('title', 'Configurar HomePage')

@php
    $activeTab = request()->query('tab', 'geral');
@endphp

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configurar HomePage</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Gerencie o conteúdo e a aparência da página inicial.</p>
            </div>

            <button type="submit" form="settingsForm"
                    class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors shadow-sm flex items-center">
                    <x-icon name="floppy-disk" class="w-5 h-5 mr-2" />
                    Salvar Alterações
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="w-full lg:w-64 flex-shrink-0">
                <nav class="space-y-1">
                    <button type="button" onclick="showTab('geral')" id="tab-geral"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="house" class="mr-3 h-5 w-5" />
                        Geral & Hero
                    </button>

                    <button type="button" onclick="showTab('seo')" id="tab-seo"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="magnifying-glass" class="mr-3 h-5 w-5" />
                        SEO
                    </button>

                    <button type="button" onclick="showTab('secoes')" id="tab-secoes"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="layer-group" class="mr-3 h-5 w-5" />
                        Seções
                    </button>

                    <button type="button" onclick="showTab('carousel')" id="tab-carousel"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="images" class="mr-3 h-5 w-5" />
                        Carousel (Config)
                    </button>

                    <a href="{{ route('admin.homepage.carousel.index') }}"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <x-icon name="list-check" class="mr-3 h-5 w-5" />
                        Gerenciar Slides
                    </a>

                    <button type="button" onclick="showTab('contato')" id="tab-contato"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="envelope" class="mr-3 h-5 w-5" />
                        Contato & Social
                    </button>

                    <button type="button" onclick="showTab('estatisticas')" id="tab-estatisticas"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="chart-simple" class="mr-3 h-5 w-5" />
                        Estatísticas
                    </button>

                    <button type="button" onclick="showTab('navegacao')" id="tab-navegacao"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                         <x-icon name="arrows-up-down" class="mr-3 h-5 w-5" />
                        Navegação
                    </button>

                    <button type="button" onclick="showTab('loja')" id="tab-loja"
                        class="tab-button w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group">
                        <x-icon name="store" class="mr-3 h-5 w-5" />
                        Loja Missionária
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="flex-1 min-w-0">
                <form action="{{ route('admin.homepage.settings.update') }}" method="POST" id="settingsForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

                    <!-- Tab: Geral -->
                    <div id="tab-content-geral" class="tab-content">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hero Section & Sobre</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Texto e imagem exibidos no topo da página inicial.</p>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título Principal (Hero)</label>
                                    <input type="text" name="hero_title" value="{{ old('hero_title', $homepageSettings['hero_title']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtítulo (Hero)</label>
                                    <textarea name="hero_subtitle" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('hero_subtitle', $homepageSettings['hero_subtitle']) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagem de Fundo (Hero Estático)</label>
                                    @if($homepageSettings['hero_bg_image'])
                                        <div class="mb-2 relative w-32 h-20 group">
                                            <img src="{{ asset($homepageSettings['hero_bg_image']) }}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                                <span class="text-[10px] text-white font-bold">Atual</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="remove_hero_bg_image" id="remove_hero_bg_image" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                            <label for="remove_hero_bg_image" class="ml-2 text-xs text-red-600 font-medium cursor-pointer">Excluir imagem atual</label>
                                        </div>
                                    @endif
                                    <input type="file" name="hero_bg_image" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Recomendado: 1920×1080px. Máximo 5MB. Deixe em branco para manter a atual.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Botão 1 - Texto</label>
                                        <input type="text" name="hero_button_1_text" value="{{ old('hero_button_1_text', $homepageSettings['hero_button_1_text']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Botão 1 - Link</label>
                                        <input type="text" name="hero_button_1_link" value="{{ old('hero_button_1_link', $homepageSettings['hero_button_1_link']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Botão 2 - Texto</label>
                                        <input type="text" name="hero_button_2_text" value="{{ old('hero_button_2_text', $homepageSettings['hero_button_2_text']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Botão 2 - Link</label>
                                        <input type="text" name="hero_button_2_link" value="{{ old('hero_button_2_link', $homepageSettings['hero_button_2_link']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título da Seção "Sobre"</label>
                                    <input type="text" name="about_title" value="{{ old('about_title', $homepageSettings['about_title']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-4">

                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição "Sobre"</label>
                                    <textarea name="about_description" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('about_description', $homepageSettings['about_description']) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: SEO -->
                    <div id="tab-content-seo" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">SEO da HomePage</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Metadados para motores de busca e redes sociais.</p>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                                    <input type="text" name="meta_title" value="{{ old('meta_title', $homepageSettings['meta_title']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Título que aparece na aba do navegador e no Google.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                                    <textarea name="meta_description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('meta_description', $homepageSettings['meta_description']) }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Breve descrição para motores de busca.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keywords</label>
                                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $homepageSettings['meta_keywords']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Palavras-chave separadas por vírgula.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Seções -->
                    <div id="tab-content-secoes" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Controle de Seções</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Visibilidade</h3>

                                    @php
                                        $sections = [
                                            'show_ministries' => 'Ministérios',
                                            'show_events' => 'Eventos',
                                            'show_campaigns' => 'Campanhas',
                                            'show_testimonials' => 'Testemunhos',
                                            'show_gallery' => 'Galeria',
                                            'show_daily_verse' => 'Versículo do Dia',
                                            'show_statistics' => 'Estatísticas',
                                            'show_newsletter' => 'Newsletter',
                                            'show_radio' => 'Rádio 3:16',
                                            'show_marketplace' => 'Loja Missionária (disponível + CTA na Home)',
                                        ];
                                    @endphp

                                    @foreach($sections as $key => $label)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ old($key, $homepageSettings[$key]) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="space-y-4">
                                    <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Títulos das Seções</h3>

                                    @php
                                        $titles = [
                                            'ministries_title' => 'Título de Ministérios',
                                            'events_title' => 'Título de Eventos',
                                            'campaigns_title' => 'Título de Campanhas',
                                            'testimonials_title' => 'Título de Testemunhos',
                                            'gallery_title' => 'Título da Galeria',
                                            'daily_verse_title' => 'Título do Versículo',
                                            'statistics_title' => 'Título de Estatísticas',
                                            'newsletter_title' => 'Título da Newsletter',
                                            'marketplace_title' => 'Título da Loja Missionária (na Home)',
                                        ];
                                    @endphp

                                    @foreach($titles as $key => $label)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</label>
                                        <input type="text" name="{{ $key }}" value="{{ old($key, $homepageSettings[$key]) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Carousel -->
                    <div id="tab-content-carousel" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Configurações do Carousel</h2>

                            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Para gerenciar os slides (criar, editar, excluir), acesse a página de <a href="{{ route('admin.homepage.carousel.index') }}" class="font-bold underline">Gerenciar Carousel</a>.
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900 dark:text-white">Habilitar Carousel</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="carousel_enabled" value="1" class="sr-only peer" {{ old('carousel_enabled', $homepageSettings['carousel_enabled']) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Altura</label>
                                        <select name="carousel_height" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            @foreach(['h-56', 'h-64', 'h-72', 'h-80', 'h-96', 'h-screen'] as $h)
                                                <option value="{{ $h }}" {{ old('carousel_height', $homepageSettings['carousel_height']) == $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transição</label>
                                        <select name="carousel_transition" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            @foreach(['fade', 'slide', 'zoom'] as $t)
                                                <option value="{{ $t }}" {{ old('carousel_transition', $homepageSettings['carousel_transition']) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Intervalo (ms)</label>
                                        <input type="number" name="carousel_interval" min="1000" step="500" value="{{ old('carousel_interval', $homepageSettings['carousel_interval']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                </div>

                                <div class="flex items-center gap-6">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="carousel_autoplay" value="1" {{ old('carousel_autoplay', $homepageSettings['carousel_autoplay']) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Autoplay</span>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="carousel_indicators" value="1" {{ old('carousel_indicators', $homepageSettings['carousel_indicators']) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Indicadores</span>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="carousel_controls" value="1" {{ old('carousel_controls', $homepageSettings['carousel_controls']) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Controles</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Contato -->
                    <div id="tab-content-contato" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Contato & Redes Sociais</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Links das redes e contato. Endereço, e-mail e telefone em <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Configurações do Sistema</a>.</p>

                            <div class="space-y-6">
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800 mb-6">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        As informações de <strong>Endereço, E-mail e Telefone</strong> são agora gerenciadas centralmente em
                                        <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" class="font-bold underline">Configurações do Sistema</a>.
                                    </p>
                                </div>

                                <div class="">
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-4">Redes Sociais</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Facebook</label>
                                            <input type="url" name="social_facebook" value="{{ old('social_facebook', $homepageSettings['social_facebook']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instagram</label>
                                            <input type="url" name="social_instagram" value="{{ old('social_instagram', $homepageSettings['social_instagram']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">YouTube</label>
                                            <input type="url" name="social_youtube" value="{{ old('social_youtube', $homepageSettings['social_youtube']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <x-icon name="tower-broadcast" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        Rádio 3:16
                                    </h3>

                                    <div class="flex items-center justify-between mb-4">
                                        <div class="max-w-md">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Ative o player da Rádio 3:16 no rodapé e em uma seção dedicada na HomePage.
                                            </p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_radio" value="1" class="sr-only peer" {{ old('show_radio', $homepageSettings['show_radio']) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            URL do embed do player
                                        </label>
                                        <input
                                            type="url"
                                            name="radio_embed_url"
                                            value="{{ old('radio_embed_url', $homepageSettings['radio_embed_url']) }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                            placeholder="Cole aqui a URL do widget da rádio (WebRadioSite)"
                                        >
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Por padrão usamos a URL atual da Rádio 3:16. Você pode substituí-la pela URL de outro widget compatível, se necessário.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Estatísticas -->
                    <div id="tab-content-estatisticas" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Números & Estatísticas</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Membros</label>
                                    <input type="number" name="stats_members" min="0" value="{{ old('stats_members', $homepageSettings['stats_members']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anos de História</label>
                                    <input type="number" name="stats_years" min="0" value="{{ old('stats_years', $homepageSettings['stats_years']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contagem de Ministérios (Manual)</label>
                                    <input type="number" name="ministries_count" min="0" value="{{ old('ministries_count', $homepageSettings['ministries_count']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contagem de Campanhas (Manual)</label>
                                    <input type="number" name="campaigns_count" min="0" value="{{ old('campaigns_count', $homepageSettings['campaigns_count']) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Navegação -->
                    <div id="tab-content-navegacao" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Configurações de Navegação</h2>

                            <div class="space-y-6">
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="show_scroll_to_top" value="1" {{ old('show_scroll_to_top', $homepageSettings['show_scroll_to_top']) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Mostrar "Voltar ao Topo"</span>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="show_scroll_to_bottom" value="1" {{ old('show_scroll_to_bottom', $homepageSettings['show_scroll_to_bottom']) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Mostrar "Rolar para Baixo" (Hero)</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Botão "Voltar ao Topo"</h3>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posição</label>
                                            <select name="scroll_button_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="bottom-right" {{ old('scroll_button_position', $homepageSettings['scroll_button_position']) == 'bottom-right' ? 'selected' : '' }}>Canto Inferior Direito</option>
                                                <option value="bottom-left" {{ old('scroll_button_position', $homepageSettings['scroll_button_position']) == 'bottom-left' ? 'selected' : '' }}>Canto Inferior Esquerdo</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tamanho</label>
                                            <select name="scroll_button_size" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="small" {{ old('scroll_button_size', $homepageSettings['scroll_button_size']) == 'small' ? 'selected' : '' }}>Pequeno</option>
                                                <option value="medium" {{ old('scroll_button_size', $homepageSettings['scroll_button_size']) == 'medium' ? 'selected' : '' }}>Médio</option>
                                                <option value="large" {{ old('scroll_button_size', $homepageSettings['scroll_button_size']) == 'large' ? 'selected' : '' }}>Grande</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Botão "Rolar para Baixo"</h3>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posição</label>
                                            <select name="scroll_down_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="center" {{ old('scroll_down_position', $homepageSettings['scroll_down_position']) == 'center' ? 'selected' : '' }}>Centro</option>
                                                <option value="left" {{ old('scroll_down_position', $homepageSettings['scroll_down_position']) == 'left' ? 'selected' : '' }}>Esquerda</option>
                                                <option value="right" {{ old('scroll_down_position', $homepageSettings['scroll_down_position']) == 'right' ? 'selected' : '' }}>Direita</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tamanho</label>
                                            <select name="scroll_down_size" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="small" {{ old('scroll_down_size', $homepageSettings['scroll_down_size']) == 'small' ? 'selected' : '' }}>Pequeno</option>
                                                <option value="medium" {{ old('scroll_down_size', $homepageSettings['scroll_down_size']) == 'medium' ? 'selected' : '' }}>Médio</option>
                                                <option value="large" {{ old('scroll_down_size', $homepageSettings['scroll_down_size']) == 'large' ? 'selected' : '' }}>Grande</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Loja Missionária -->
                    <div id="tab-content-loja" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Modos da Loja</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Controle de acesso e vendas.</p>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-center justify-between p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Modo Manutenção</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Loja fechada: todas as páginas da loja exibem aviso elegante (503).</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="marketplace_maintenance" value="1" class="sr-only peer" {{ old('marketplace_maintenance', $homepageSettings['marketplace_maintenance'] ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Modo Vitrine</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Só visualização: listagem e detalhe visíveis, mas sem &quot;Adicionar ao carrinho&quot; nem checkout.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="marketplace_showcase_only" value="1" class="sr-only peer" {{ old('marketplace_showcase_only', $homepageSettings['marketplace_showcase_only'] ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>

                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Políticas e Conteúdo da Loja</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Textos exibidos nas páginas de política (rodapé da loja).</p>

                            <div class="space-y-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Política de Entrega e Prazos</label>
                                    <x-rich-editor name="marketplace_policy_delivery" :value="old('marketplace_policy_delivery', $homepageSettings['marketplace_policy_delivery'] ?? '')" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Política de Trocas e Devoluções</label>
                                    <x-rich-editor name="marketplace_policy_returns" :value="old('marketplace_policy_returns', $homepageSettings['marketplace_policy_returns'] ?? '')" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Termos de Uso e Privacidade da Loja</label>
                                    <x-rich-editor name="marketplace_policy_terms" :value="old('marketplace_policy_terms', $homepageSettings['marketplace_policy_terms'] ?? '')" />
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            // Show selected tab content
            document.getElementById('tab-content-' + tabName).classList.remove('hidden');

            // Reset all tab buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('bg-white', 'dark:bg-gray-800', 'shadow-sm', 'text-blue-600', 'dark:text-blue-400', 'border-l-4', 'border-blue-600');
                btn.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/50');
            });

            // Activate selected tab button
            const activeBtn = document.getElementById('tab-' + tabName);
            if(activeBtn) {
                activeBtn.classList.add('bg-white', 'dark:bg-gray-800', 'shadow-sm', 'text-blue-600', 'dark:text-blue-400', 'border-l-4', 'border-blue-600');
                activeBtn.classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/50');
            }

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);

            // Update hidden input
            const activeTabInput = document.getElementById('active_tab');
            if(activeTabInput) {
                activeTabInput.value = tabName;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
             const activeTab = "{{ $activeTab }}";
             showTab(activeTab);
        });
    </script>
@endsection

