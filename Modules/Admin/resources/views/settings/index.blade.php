@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Configurações do Sistema';
    $activeTab = request()->query('tab', 'general');
@endphp

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão dashboard) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Sistema</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Configurações Globais</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Configurações do Sistema</h1>
                    <p class="text-gray-300 max-w-xl">Gerencie identidade, segurança, e-mail, regional e integrações em um só lugar. As alterações são aplicadas com cache atualizado.</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="submit" form="settingsForm"
                        class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 flex items-center gap-2">
                        <x-icon name="check" class="w-5 h-5 text-blue-600" />
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation (card estilo dashboard) -->
            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider mb-3 px-2">Navegação</p>
                        <nav class="space-y-1">
                            <button type="button" onclick="showTab('general')" id="tab-general"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 group-btn-hover:scale-105"><x-icon name="cog" class="w-5 h-5" /></span>
                                Geral
                            </button>
                            <button type="button" onclick="showTab('appearance')" id="tab-appearance"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400"><x-icon name="palette" class="w-5 h-5" /></span>
                                Aparência
                            </button>
                            <button type="button" onclick="showTab('security')" id="tab-security"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400"><x-icon name="shield-check" class="w-5 h-5" /></span>
                                Segurança
                            </button>
                            <button type="button" onclick="showTab('payments')" id="tab-payments"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400"><x-icon name="credit-card" class="w-5 h-5" /></span>
                                Pagamentos
                            </button>
                            <button type="button" onclick="showTab('email')" id="tab-email"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400"><x-icon name="envelope" class="w-5 h-5" /></span>
                                E-mail
                            </button>
                            <button type="button" onclick="showTab('broadcasting')" id="tab-broadcasting"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400"><x-icon name="bell" class="w-5 h-5" /></span>
                                Notificações
                            </button>
                            <button type="button" onclick="showTab('system')" id="tab-system"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400"><x-icon name="server" class="w-5 h-5" /></span>
                                Sistema
                            </button>
                            <button type="button" onclick="showTab('bible')" id="tab-bible"
                                class="tab-button w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50 group-btn">
                                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400"><x-icon name="book-bible" class="w-5 h-5" /></span>
                                Bíblia
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 min-w-0">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando configurações...' } }))">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

                    <div id="settings-tab-viewport" class="relative w-full">
                    <!-- Tab: General -->
                    <div id="tab-content-general" class="tab-content settings-tab-panel {{ $activeTab === 'general' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <x-icon name="cog" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Geral</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Informações básicas do site, contato e regional.</p>
                                    </div>
                                </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nome do Site <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', \App\Models\Settings::get('site_name', config('app.name'))) }}" required
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    @error('site_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Descrição do Site
                                    </label>
                                    <textarea name="site_description" id="site_description" rows="3"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('site_description', \App\Models\Settings::get('site_description', '')) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="site_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            E-mail de Contato <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-icon name="envelope" class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input type="email" name="site_email" id="site_email" value="{{ old('site_email', \App\Models\Settings::get('site_email', config('mail.from.address'))) }}" required
                                                class="w-full pl-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="site_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Telefone
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-icon name="phone" class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input type="text" name="site_phone" id="site_phone" value="{{ old('site_phone', \App\Models\Settings::get('site_phone', '')) }}" data-mask="phone"
                                                class="w-full pl-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="site_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Endereço
                                    </label>
                                    <input type="text" name="site_address" id="site_address" value="{{ old('site_address', \App\Models\Settings::get('site_address', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                {{-- Regional: fuso, idioma, formatos --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                        <x-icon name="globe" class="w-5 h-5 text-gray-500" />
                                        Regional
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Fuso horário, idioma e formatos de data/hora usados em todo o sistema (relatórios, e-mails, eventos).</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="app_timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fuso horário</label>
                                            <select name="app_timezone" id="app_timezone" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                @foreach(timezone_identifiers_list() as $tz)
                                                    <option value="{{ $tz }}" {{ old('app_timezone', \App\Models\Settings::get('app_timezone', config('app.timezone'))) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ex.: America/Sao_Paulo</p>
                                        </div>
                                        <div>
                                            <label for="app_locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Idioma padrão</label>
                                            <select name="app_locale" id="app_locale" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="pt_BR" {{ old('app_locale', \App\Models\Settings::get('app_locale', config('app.locale'))) === 'pt_BR' ? 'selected' : '' }}>Português (Brasil)</option>
                                                <option value="en" {{ old('app_locale', \App\Models\Settings::get('app_locale', config('app.locale'))) === 'en' ? 'selected' : '' }}>English</option>
                                                <option value="es" {{ old('app_locale', \App\Models\Settings::get('app_locale', config('app.locale'))) === 'es' ? 'selected' : '' }}>Español</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="app_first_day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primeiro dia da semana</label>
                                            <select name="app_first_day_of_week" id="app_first_day_of_week" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="0" {{ old('app_first_day_of_week', \App\Models\Settings::get('app_first_day_of_week', '0')) === '0' ? 'selected' : '' }}>Domingo</option>
                                                <option value="1" {{ old('app_first_day_of_week', \App\Models\Settings::get('app_first_day_of_week', '0')) === '1' ? 'selected' : '' }}>Segunda-feira</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Formato de data</label>
                                            <input type="text" name="date_format" id="date_format" value="{{ old('date_format', \App\Models\Settings::get('date_format', 'd/m/Y')) }}" placeholder="d/m/Y"
                                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ex.: d/m/Y, Y-m-d</p>
                                        </div>
                                        <div>
                                            <label for="time_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Formato de hora</label>
                                            <input type="text" name="time_format" id="time_format" value="{{ old('time_format', \App\Models\Settings::get('time_format', 'H:i')) }}" placeholder="H:i"
                                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ex.: H:i (24h), h:i A (12h)</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status do Site (manutenção real: artisan down/up) --}}
                                @php
                                    $maintenanceService = app(\App\Services\MaintenanceModeService::class);
                                    $maintenanceActive = $maintenanceService->isActive();
                                @endphp
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                        <x-icon name="tower-broadcast" class="w-5 h-5 text-gray-500" />
                                        Status do Site
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ative a manutenção para exibir a página 503 aos visitantes. A Bíblia online e o acesso admin (por link de bypass) continuam disponíveis.</p>

                                    <div class="flex flex-wrap items-center gap-4 p-4 rounded-xl {{ $maintenanceActive ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' : 'bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full {{ $maintenanceActive ? 'bg-amber-500 animate-pulse' : 'bg-green-500' }}"></span>
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ $maintenanceActive ? 'Site em manutenção' : 'Site no ar' }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($maintenanceActive)
                                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Desativando manutenção...' } })); document.getElementById('formDeactivateMaintenance').submit();" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                                    <x-icon name="circle-play" class="w-4 h-4" />
                                                    Desativar Manutenção
                                                </button>
                                            @else
                                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Ativando manutenção...' } })); document.getElementById('formActivateMaintenance').submit();" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                                    <x-icon name="circle-pause" class="w-4 h-4" />
                                                    Ativar Manutenção
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if(session('maintenance_bypass_url'))
                                        <div class="mt-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Link de bypass (guarde para outra aba ou dispositivo):</p>
                                            <div class="flex items-center gap-2">
                                                <input type="text" readonly id="bypassUrlInput" value="{{ session('maintenance_bypass_url') }}" class="flex-1 px-3 py-2 text-sm rounded-lg bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 text-gray-900 dark:text-white font-mono">
                                                <button type="button" id="copyBypassBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Copiar</button>
                                            </div>
                                        </div>
                                    @endif

                                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Em manutenção, use <a href="{{ route('admin.acesso-mestre') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ route('admin.acesso-mestre') }}</a> para entrar como admin.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Appearance -->
                    <div id="tab-content-appearance" class="tab-content settings-tab-panel {{ $activeTab === 'appearance' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                        <x-icon name="palette" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Aparência</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Logo e ícones do sistema.</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        Logo Principal
                                    </label>
                                    @php
                                        $logoPath = \App\Models\Settings::get('logo_path', 'storage/image/logo_oficial.png');
                                        $logoExists = file_exists(public_path($logoPath)) || (str_starts_with($logoPath, 'storage/') && file_exists(storage_path('app/public/' . str_replace('storage/', '', $logoPath))));
                                    @endphp
                                    <div class="mb-4 h-32 w-full flex items-center justify-center bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-2">
                                        @if ($logoExists)
                                            <img src="{{ asset($logoPath) }}" alt="Logo atual" class="h-full object-contain">
                                        @else
                                            <span class="text-gray-400 text-sm">Sem logo</span>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" id="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    <p class="mt-2 text-xs text-gray-500">Recomendado: 200px de altura (PNG transparente)</p>
                                </div>

                                <div class="flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        Favicon / Ícone
                                    </label>
                                    @php
                                        $iconPath = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png');
                                        $iconExists = file_exists(public_path($iconPath)) || (str_starts_with($iconPath, 'storage/') && file_exists(storage_path('app/public/' . str_replace('storage/', '', $iconPath))));
                                    @endphp
                                    <div class="mb-4 h-32 w-32 flex items-center justify-center bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-2">
                                        @if ($iconExists)
                                            <img src="{{ asset($iconPath) }}" alt="Ícone atual" class="h-full object-contain">
                                        @else
                                            <span class="text-gray-400 text-sm">Sem ícone</span>
                                        @endif
                                    </div>
                                    <input type="file" name="logo_icon" id="logo_icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    <p class="mt-2 text-xs text-gray-500">Recomendado: 512x512px (PNG, SVG)</p>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Security (reCAPTCHA + 2FA) -->
                    <div id="tab-content-security" class="tab-content settings-tab-panel {{ $activeTab === 'security' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                        <x-icon name="shield-check" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Segurança</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">reCAPTCHA e autenticação em dois fatores (2FA) para administradores.</p>
                                    </div>
                                </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-icon name="robot" class="h-5 w-5 text-blue-400" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700 dark:text-blue-200 font-medium mb-1">reCAPTCHA (Google)</p>
                                        <p class="text-sm text-blue-700 dark:text-blue-200">
                                            <strong>v2:</strong> checkbox "Não sou um robô" no login. <strong>v3:</strong> verificação invisível com score (0–1); ajuste o score mínimo abaixo. Obtenha chaves em <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener" class="font-bold underline hover:text-blue-600">Console reCAPTCHA</a> (crie um site v2 ou v3 conforme a versão escolhida).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="relative flex items-start p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="recaptcha_enabled" id="recaptcha_enabled" value="1"
                                            {{ old('recaptcha_enabled', \App\Models\Settings::get('recaptcha_enabled', false)) ? 'checked' : '' }}
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="recaptcha_enabled" class="font-medium text-gray-700 dark:text-gray-300">Habilitar reCAPTCHA no login</label>
                                        <p class="text-gray-500 dark:text-gray-400">Exige verificação no formulário de login do site.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="recaptcha_version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Versão reCAPTCHA</label>
                                        <select name="recaptcha_version" id="recaptcha_version" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="v2" {{ old('recaptcha_version', \App\Models\Settings::get('recaptcha_version', 'v2')) === 'v2' ? 'selected' : '' }}>v2 (checkbox)</option>
                                            <option value="v3" {{ old('recaptcha_version', \App\Models\Settings::get('recaptcha_version', 'v2')) === 'v3' ? 'selected' : '' }}>v3 (invisível + score)</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">v3 não exibe checkbox; usa score para decidir se é humano.</p>
                                    </div>
                                    <div id="recaptcha_v3_score_wrapper">
                                        <label for="recaptcha_v3_score_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Score mínimo (v3)</label>
                                        <input type="number" name="recaptcha_v3_score_threshold" id="recaptcha_v3_score_threshold" step="0.1" min="0" max="1" value="{{ old('recaptcha_v3_score_threshold', \App\Models\Settings::get('recaptcha_v3_score_threshold', 0.5)) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0.0–1.0; recomendado 0.5. Abaixo disso o login é bloqueado.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Key</label>
                                        <input type="text" name="recaptcha_site_key" id="recaptcha_site_key" value="{{ old('recaptcha_site_key', \App\Models\Settings::get('recaptcha_site_key', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secret Key</label>
                                        <input type="password" name="recaptcha_secret_key" id="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', \App\Models\Settings::get('recaptcha_secret_key', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mantenha em sigilo; não é exibido após salvar.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- 2FA (preparação) --}}
                            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <x-icon name="key" class="w-5 h-5 text-gray-500" />
                                    Autenticação em dois fatores (2FA)
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Quando ativado, o login administrativo exige um código de 6 dígitos após a senha. Cada administrador configura o 2FA em <strong>Meu Perfil → Autenticação em duas etapas (2FA)</strong>.</p>
                                <div class="space-y-4">
                                    <div class="relative flex items-start p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" value="1"
                                                {{ old('two_factor_enabled', \App\Models\Settings::get('two_factor_enabled', false)) ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="two_factor_enabled" class="font-medium text-gray-700 dark:text-gray-300">Habilitar 2FA (TOTP) para administradores</label>
                                            <p class="text-gray-500 dark:text-gray-400">Quando ativado, o login no painel admin poderá exigir um código do app.</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="two_factor_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provedor 2FA</label>
                                        <select name="two_factor_provider" id="two_factor_provider" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="none" {{ old('two_factor_provider', \App\Models\Settings::get('two_factor_provider', 'none')) === 'none' ? 'selected' : '' }}>Nenhum</option>
                                            <option value="google" {{ old('two_factor_provider', \App\Models\Settings::get('two_factor_provider', 'none')) === 'google' ? 'selected' : '' }}>Google Authenticator</option>
                                            <option value="microsoft" {{ old('two_factor_provider', \App\Models\Settings::get('two_factor_provider', 'none')) === 'microsoft' ? 'selected' : '' }}>Microsoft Authenticator</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">App que os admins usarão para gerar o código.</p>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Payments -->
                    <div id="tab-content-payments" class="tab-content settings-tab-panel {{ $activeTab === 'payments' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                        <x-icon name="credit-card" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pagamentos</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Gateways de pagamento e transações.</p>
                                    </div>
                                </div>
                            <div class="space-y-4">
                                @php
                                    $gateways = \Modules\PaymentGateway\App\Models\PaymentGateway::ordered()->get();
                                @endphp

                                @forelse($gateways as $gateway)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            @if($gateway->icon)
                                                <div class="p-2 bg-white dark:bg-gray-600 rounded-lg shadow-sm">
                                                    <x-icon name="{{ $gateway->icon }}" class="w-8 h-8 text-gray-700 dark:text-gray-200" />
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-white text-lg">{{ $gateway->display_name }}</h4>
                                                @if($gateway->description)
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $gateway->description }}</p>
                                                @endif

                                                <div class="flex items-center gap-3 mt-2">
                                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gateway->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $gateway->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                                        {{ $gateway->is_active ? 'Ativo' : 'Inativo' }}
                                                    </span>
                                                    @if($gateway->isConfigured())
                                                        <span class="inline-flex items-center text-xs text-green-600 dark:text-green-400">
                                                            <x-icon name="check-circle" class="w-3 h-3 mr-1" />
                                                            Configurado
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center text-xs text-amber-600 dark:text-amber-400">
                                                            <x-icon name="triangle-exclamation" class="w-3 h-3 mr-1" />
                                                            Requer atenção
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.payment-gateways.edit', $gateway) }}"
                                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Configurar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/20 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <x-icon name="search" class="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum gateway encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Execute os seeders para instalar os gateways padrão.</p>
                                </div>
                                @endforelse

                                <div class="mt-4 text-right">
                                    <a href="{{ route('admin.payment-gateways.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 hover:underline">
                                        Gerenciar todos os gateways &rarr;
                                    </a>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Email -->
                    <div id="tab-content-email" class="tab-content settings-tab-panel {{ $activeTab === 'email' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                        <x-icon name="envelope" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configurações de E-mail</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Servidor SMTP, SES, Mailgun e remetente.</p>
                                    </div>
                                </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mailer Driver</label>
                                    <select name="mail_mailer" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach(['log' => 'Log (Dev)', 'smtp' => 'SMTP', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'mailgun' => 'Mailgun'] as $key => $label)
                                            <option value="{{ $key }}" {{ old('mail_mailer', \App\Models\Settings::get('mail_mailer', 'log')) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Host SMTP</label>
                                    <input type="text" name="mail_host" value="{{ old('mail_host', \App\Models\Settings::get('mail_host', '')) }}" placeholder="smtp.gmail.com"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Porta</label>
                                    <input type="number" name="mail_port" value="{{ old('mail_port', \App\Models\Settings::get('mail_port', 587)) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuário</label>
                                    <input type="text" name="mail_username" value="{{ old('mail_username', \App\Models\Settings::get('mail_username', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                                    <input type="password" name="mail_password" value="{{ old('mail_password', \App\Models\Settings::get('mail_password', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Criptografia</label>
                                    <select name="mail_encryption" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="tls" {{ old('mail_encryption', \App\Models\Settings::get('mail_encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', \App\Models\Settings::get('mail_encryption', 'tls')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="null" {{ old('mail_encryption', \App\Models\Settings::get('mail_encryption', 'tls')) == 'null' ? 'selected' : '' }}>Nenhuma</option>
                                    </select>
                                </div>

                                <!-- SES Settings -->
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 mailer-fields" id="mailer-ses">
                                    <div class="md:col-span-3 pb-2 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">Configurações Amazon SES</h3>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Access Key ID</label>
                                        <input type="text" name="ses_key" value="{{ old('ses_key', \App\Models\Settings::get('ses_key', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secret Access Key</label>
                                        <input type="password" name="ses_secret" value="{{ old('ses_secret', \App\Models\Settings::get('ses_secret', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Região (Region)</label>
                                        <input type="text" name="ses_region" value="{{ old('ses_region', \App\Models\Settings::get('ses_region', 'us-east-1')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                </div>

                                <!-- Mailgun Settings -->
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 mailer-fields" id="mailer-mailgun">
                                    <div class="md:col-span-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">Configurações Mailgun</h3>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Domínio (Domain)</label>
                                        <input type="text" name="mailgun_domain" value="{{ old('mailgun_domain', \App\Models\Settings::get('mailgun_domain', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secret API Key</label>
                                        <input type="password" name="mailgun_secret" value="{{ old('mailgun_secret', \App\Models\Settings::get('mailgun_secret', '')) }}"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endpoint</label>
                                        <select name="mailgun_endpoint" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="api.mailgun.net" {{ old('mailgun_endpoint', \App\Models\Settings::get('mailgun_endpoint', 'api.mailgun.net')) == 'api.mailgun.net' ? 'selected' : '' }}>US (api.mailgun.net)</option>
                                            <option value="api.eu.mailgun.net" {{ old('mailgun_endpoint', \App\Models\Settings::get('mailgun_endpoint', 'api.mailgun.net')) == 'api.eu.mailgun.net' ? 'selected' : '' }}>EU (api.eu.mailgun.net)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Remetente</label>
                                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', \App\Models\Settings::get('mail_from_name', config('app.name'))) }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail do Remetente</label>
                                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', \App\Models\Settings::get('mail_from_address', config('mail.from.address'))) }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Broadcasting -->
                    <div id="tab-content-broadcasting" class="tab-content settings-tab-panel {{ $activeTab === 'broadcasting' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-cyan-50 dark:bg-cyan-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                        <x-icon name="bell" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Notificações Realtime (Pusher)</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Integração com serviços de WebSocket.</p>
                                    </div>
                                </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Driver</label>
                                     <select name="broadcast_driver" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="log" {{ old('broadcast_driver', \App\Models\Settings::get('broadcast_driver', 'log')) == 'log' ? 'selected' : '' }}>Log (Desativado)</option>
                                        <option value="pusher" {{ old('broadcast_driver', \App\Models\Settings::get('broadcast_driver', 'log')) == 'pusher' ? 'selected' : '' }}>Pusher</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">App ID</label>
                                    <input type="text" name="pusher_app_id" value="{{ old('pusher_app_id', \App\Models\Settings::get('pusher_app_id', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">App Key</label>
                                    <input type="text" name="pusher_app_key" value="{{ old('pusher_app_key', \App\Models\Settings::get('pusher_app_key', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">App Secret</label>
                                    <input type="password" name="pusher_app_secret" value="{{ old('pusher_app_secret', \App\Models\Settings::get('pusher_app_secret', '')) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cluster</label>
                                    <input type="text" name="pusher_app_cluster" value="{{ old('pusher_app_cluster', \App\Models\Settings::get('pusher_app_cluster', 'mt1')) }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                             </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: System -->
                    <div id="tab-content-system" class="tab-content settings-tab-panel {{ $activeTab === 'system' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-gray-100 dark:bg-gray-700/50 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                        <x-icon name="server" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sistema Interno</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Cache, sessão e fila.</p>
                                    </div>
                                </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cache Driver</label>
                                    <select name="cache_store" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach(['file', 'database', 'redis', 'array'] as $driver)
                                            <option value="{{ $driver }}" {{ old('cache_store', \App\Models\Settings::get('cache_store', 'file')) == $driver ? 'selected' : '' }}>{{ ucfirst($driver) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Driver</label>
                                    <select name="session_driver" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach(['file', 'database', 'redis', 'cookie'] as $driver)
                                            <option value="{{ $driver }}" {{ old('session_driver', \App\Models\Settings::get('session_driver', 'database')) == $driver ? 'selected' : '' }}>{{ ucfirst($driver) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Lifetime (minutos)</label>
                                     <input type="number" name="session_lifetime" value="{{ old('session_lifetime', \App\Models\Settings::get('session_lifetime', 120)) }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Queue Connection</label>
                                    <select name="queue_connection" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach(['sync', 'database', 'redis'] as $driver)
                                            <option value="{{ $driver }}" {{ old('queue_connection', \App\Models\Settings::get('queue_connection', 'sync')) == $driver ? 'selected' : '' }}>{{ ucfirst($driver) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Bible -->
                    <div id="tab-content-bible" class="tab-content settings-tab-panel {{ $activeTab === 'bible' ? 'settings-tab-visible' : '' }}" data-settings-tab-panel>
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                        <x-icon name="book-bible" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Versão padrão da Bíblia</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Fallback em planos de leitura, verso do dia e módulo Bíblia.</p>
                                    </div>
                                </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-icon name="book-bible" class="h-5 w-5 text-amber-500" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-amber-800 dark:text-amber-200">
                                            Esta versão será usada como fallback em <strong>planos de leitura</strong>, <strong>verso do dia</strong> e onde o sistema precisar de uma versão padrão. Se estiver vazio, o sistema usa a versão marcada como padrão no módulo Bíblia.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="default_bible_version_abbreviation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Versão padrão da Bíblia em todo o sistema</label>
                                <select name="default_bible_version_abbreviation" id="default_bible_version_abbreviation" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 max-w-md">
                                    <option value="">— Usar versão padrão do módulo Bíblia —</option>
                                    @isset($bibleVersions)
                                        @foreach($bibleVersions as $bv)
                                            <option value="{{ $bv->abbreviation }}" {{ old('default_bible_version_abbreviation', \App\Models\Settings::get('default_bible_version_abbreviation', '')) === $bv->abbreviation ? 'selected' : '' }}>{{ $bv->name }} ({{ $bv->abbreviation }})</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opcional. Deixe em branco para manter o comportamento atual (versão marcada como padrão no cadastro de versões).</p>
                            </div>
                            </div>
                        </div>
                    </div>

                    </div><!-- /#settings-tab-viewport -->
                </form>

                {{-- Formulários de manutenção fora do settingsForm (evitar form aninhado: HTML inválido faz o submit ir para o form externo) --}}
                <form id="formActivateMaintenance" method="POST" action="{{ route('admin.settings.activate-maintenance') }}" class="hidden">
                    @csrf
                </form>
                <form id="formDeactivateMaintenance" method="POST" action="{{ route('admin.settings.deactivate-maintenance') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            const viewport = document.getElementById('settings-tab-viewport');
            const content = document.getElementById('tab-content-' + tabName);
            if (!viewport || !content) return;

            // Move active panel to first child so it is the only one that can be "shown" by any first-child rule
            viewport.insertBefore(content, viewport.firstChild);

            // Hide all panels with inline style, then show only the active one
            document.querySelectorAll('[data-settings-tab-panel]').forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });
            content.style.setProperty('display', 'block', 'important');

            // Reset buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400', 'shadow-sm');
                btn.classList.add('text-gray-600', 'dark:text-gray-400');
            });

            // Activate button
            const activeBtn = document.getElementById('tab-' + tabName);
            if(activeBtn) {
                activeBtn.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400', 'shadow-sm');
                activeBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            }

            // Update hidden input
            const input = document.getElementById('active_tab');
            if(input) input.value = tabName;

            // Update URL without reload (optional but professional)
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Copy bypass URL
        const copyBypassBtn = document.getElementById('copyBypassBtn');
        const bypassUrlInput = document.getElementById('bypassUrlInput');
        if (copyBypassBtn && bypassUrlInput) {
            copyBypassBtn.addEventListener('click', function() {
                bypassUrlInput.select();
                navigator.clipboard.writeText(bypassUrlInput.value).then(() => {
                    const t = copyBypassBtn.textContent;
                    copyBypassBtn.textContent = 'Copiado!';
                    setTimeout(() => { copyBypassBtn.textContent = t; }, 2000);
                });
            });
        }

        // On Load
        document.addEventListener('DOMContentLoaded', () => {
             // Get tab from URL first (persistence), then PHP fallback
             const urlParams = new URLSearchParams(window.location.search);
             const tabFromUrl = urlParams.get('tab');
             const activeTab = tabFromUrl && /^(general|appearance|security|payments|email|broadcasting|system|bible)$/.test(tabFromUrl) ? tabFromUrl : "{{ $activeTab }}";
             showTab(activeTab);

             // reCAPTCHA v3 score field visibility
             toggleRecaptchaV3Score();
             const recaptchaVersionSelect = document.getElementById('recaptcha_version');
             if (recaptchaVersionSelect) recaptchaVersionSelect.addEventListener('change', toggleRecaptchaV3Score);

             // Initial Mailer Toggle
             toggleMailerFields();

             // Mailer Toggle Listener
             const mailerSelect = document.querySelector('select[name="mail_mailer"]');
             if(mailerSelect) {
                 mailerSelect.addEventListener('change', toggleMailerFields);
             }
        });

        function toggleRecaptchaV3Score() {
            const sel = document.getElementById('recaptcha_version');
            const wrap = document.getElementById('recaptcha_v3_score_wrapper');
            if (!sel || !wrap) return;
            wrap.style.display = sel.value === 'v3' ? 'block' : 'none';
        }

        // Toast ao salvar configurações (feedback "Configurações Globais Aplicadas e Cache Atualizado")
        @if (session('success'))
        (function() {
            const msg = @json(session('success'));
            const container = document.getElementById('notification-toast-container');
            if (container && msg) {
                const toast = document.createElement('div');
                toast.setAttribute('role', 'alert');
                toast.className = 'pointer-events-auto mb-2 px-4 py-3 rounded-xl shadow-lg bg-green-600 text-white text-sm font-medium flex items-center gap-2 animate-in fade-in slide-in-from-bottom-2';
                toast.innerHTML = '<span class="flex-1">' + msg + '</span>';
                container.appendChild(toast);
                setTimeout(function() {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';
                    setTimeout(function() { toast.remove(); }, 300);
                }, 5000);
            }
        })();
        @endif

        function toggleMailerFields() {
            const mailer = document.querySelector('select[name="mail_mailer"]').value;
            // Hide all
            document.querySelectorAll('.mailer-fields').forEach(el => el.classList.add('hidden'));

            // Show specific
            if (mailer === 'smtp') {
                // SMTP uses the default fields that are always visible or you can wrap them too
                // For now, host/port/user/pass are always there, following the current UI
            } else if (mailer === 'ses') {
                const sesFields = document.getElementById('mailer-ses');
                if(sesFields) sesFields.classList.remove('hidden');
            } else if (mailer === 'mailgun') {
                const mailgunFields = document.getElementById('mailer-mailgun');
                if(mailgunFields) mailgunFields.classList.remove('hidden');
            }
        }

        async function sendTestEmail() {
            const email = document.getElementById('test_email_address').value;
            const btn = document.getElementById('btnSendTest');
            const spinner = document.getElementById('test-spinner');
            const btnText = document.getElementById('test-btn-text');
            const result = document.getElementById('test_result');

            if (!email) {
                alert('Por favor, insira um e-mail para o teste.');
                return;
            }

            // UI Feedback
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.innerText = 'Enviando...';
            result.classList.add('hidden');

            try {
                const response = await fetch("{{ route('admin.settings.test-email') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();

                result.classList.remove('hidden');
                if (data.success) {
                    result.className = 'mt-3 text-xs font-semibold p-3 rounded-lg bg-green-100 text-green-700 border border-green-200';
                    result.innerText = data.message;
                } else {
                    result.className = 'mt-3 text-xs font-semibold p-3 rounded-lg bg-red-100 text-red-700 border border-red-200';
                    result.innerText = data.message + (data.error ? ': ' + data.error : '');
                }
            } catch (error) {
                result.classList.remove('hidden');
                result.className = 'mt-3 text-xs font-semibold p-3 rounded-lg bg-red-100 text-red-700 border border-red-200';
                result.innerText = 'Erro ao processar a requisição.';
                console.error(error);
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
                btnText.innerText = 'Enviar Teste';
            }
        }
    </script>
@endsection

