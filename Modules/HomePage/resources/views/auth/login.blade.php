@extends('homepage::components.layouts.master')

@php
    $logoPath = \App\Models\Settings::get('logo_path', 'storage/image/logo_oficial.png');
    $iconPath = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png');
    $siteName = \App\Models\Settings::get('site_name', 'Igreja Batista Avenida');
    // Em ambiente local/dev, desabilitamos o reCAPTCHA mesmo que esteja ativo nas configurações (banco copiado da produção).
    $recaptchaEnabled = ! app()->environment('local', 'development', 'dev')
        && \App\Models\Settings::get('recaptcha_enabled', false);
    $recaptchaSiteKey = \App\Models\Settings::get('recaptcha_site_key', '');
    $recaptchaVersion = config('services.recaptcha.version', 'v2');
    $hideNavFooter = true;
@endphp

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50 dark:bg-gray-950 px-4 py-12">
        <!-- Floating Back to Home Button -->
        <a href="{{ route('homepage.index') }}"
           class="absolute top-6 left-6 z-50 flex items-center gap-2 py-2 px-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full shadow-lg border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all group active:scale-95">
            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
            <span class="text-xs font-bold uppercase tracking-wider">Voltar ao Início</span>
        </a>

        <!-- Background Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-5xl grid lg:grid-cols-2 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden z-10">
            <!-- Left Side - Login Form -->
            <div class="p-8 sm:p-12 lg:p-16 flex flex-col justify-center order-2 lg:order-1">
                <div class="mb-10 text-center lg:text-left">
                    <a href="{{ route('homepage.index') }}" class="inline-flex items-center group mb-6">
                        <img src="{{ asset($logoPath) }}"
                             alt="{{ $siteName }}"
                             class="h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                             onerror="this.src='/storage/image/logo_oficial.png';">
                    </a>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Bem-vindo de volta</h2>
                    <p class="text-gray-600 dark:text-gray-400">Acesse sua conta para continuar</p>
                </div>

                @if(isset($maintenanceMode) && $maintenanceMode)
                    <div class="mb-6 rounded-2xl bg-amber-50 dark:bg-amber-900/20 p-4 border border-amber-200 dark:border-amber-700">
                        <div class="flex">
                            <svg class="h-5 w-5 text-amber-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Sistema em Manutenção</h3>
                                <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">O sistema está temporariamente offline para melhorias.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-700">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-xs text-red-800 dark:text-red-400 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabs -->
                <div class="bg-gray-100 dark:bg-gray-800 p-1 rounded-xl mb-8 flex">
                    <button type="button" id="tab-email" class="tab-button active flex-1 py-2 px-4 rounded-lg text-sm font-semibold transition-all duration-200 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-sm border border-gray-200 dark:border-gray-600">
                        E-mail
                    </button>
                    <button type="button" id="tab-cpf" class="tab-button flex-1 py-2 px-4 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        CPF
                    </button>
                </div>

                <form action="{{ route('login.submit') }}" method="POST" id="login-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="identifier" id="identifier-field">
                    <input type="hidden" name="login_type" id="login-type-field" value="email">

                    <div id="content-email" class="tab-content transition-all duration-300">
                        <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 ml-1">E-mail</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="block w-full pl-11 pr-4 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                                placeholder="seu@email.com">
                        </div>
                    </div>

                    <div id="content-cpf" class="tab-content hidden transition-all duration-300">
                        <label for="cpf" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 ml-1">CPF</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input id="cpf" name="cpf" type="text" autocomplete="off" data-mask="cpf"
                                class="block w-full pl-11 pr-4 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                                placeholder="000.000.000-00">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5 ml-1">
                            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Senha</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                Esqueceu a senha?
                            </a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="block w-full pl-11 pr-12 px-3 py-3 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-gray-600 sm:text-sm"
                                placeholder="Digite sua senha">
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-500 transition-colors">
                                <x-icon id="eye-icon" name="eye" style="duotone" class="h-5 w-5" />
                                <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0A9.973 9.973 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500/20 border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 transition-all">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-400">Lembrar-me</label>
                    </div>

                    <!-- Dynamic reCAPTCHA Integration: v2 (checkbox) ou v3 (invisível) -->
                    @if($recaptchaEnabled && !empty($recaptchaSiteKey))
                        @if($recaptchaVersion === 'v3')
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-v3" value="">
                            <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}" async defer></script>
                        @else
                            <div class="flex flex-col items-center justify-center p-2 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                <div class="g-recaptcha scale-[0.85] sm:scale-100 origin-center" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                            </div>
                        @endif
                        @error('g-recaptcha-response')
                            <p class="text-xs text-red-600 mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    @endif

                    <button type="submit"
                        class="w-full flex justify-center py-4 px-4 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl shadow-lg shadow-blue-500/20 font-bold transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                        Entrar no Portal
                    </button>
                </form>

                @if(app()->environment('local', 'development', 'dev'))
                    <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-800">
                        <div class="text-center mb-4">
                            <span class="px-3 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase tracking-wider rounded-full">Ferramentas de Desenvolvedor</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <form action="{{ route('quick-login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="admin">
                                <button type="submit" class="w-full py-2.5 px-4 bg-white dark:bg-gray-800 border border-purple-100 dark:border-purple-900/30 rounded-xl text-xs font-bold text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                    Admin Demo
                                </button>
                            </form>
                            <form action="{{ route('quick-login') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="member">
                                <button type="submit" class="w-full py-2.5 px-4 bg-white dark:bg-gray-800 border border-green-100 dark:border-green-900/30 rounded-xl text-xs font-bold text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all flex items-center justify-center gap-2">
                                    <x-icon name="user" style="duotone" class="w-4 h-4" />
                                    Membro Demo
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Side - Hero Image Section -->
            <div class="hidden lg:flex relative bg-linear-to-br from-blue-600 via-indigo-700 to-purple-800 p-16 flex-col justify-between order-1 lg:order-2">
                <!-- Abstract Pattern -->
                <div class="absolute inset-0 opacity-20 pointer-events-none"
                    style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                </div>

                <div class="relative z-10">
                    <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center mb-8 border border-white/30 shadow-2xl">
                        <img src="{{ asset($iconPath) }}"
                             alt="Icon"
                             class="h-16 w-auto filter drop-shadow-xl"
                             onerror="this.src='/storage/image/logo_icon.png';">
                    </div>
                    <h3 class="text-4xl font-extrabold text-white leading-tight mb-4">
                        Conectando Corações,<br/>Vivendo a Fé.
                    </h3>
                    <p class="text-blue-100 text-lg">Acesse o portal do membro para gerenciar seu ministério, acessar estudos e muito mais.</p>
                </div>

                <div class="relative z-10 bg-white/10 backdrop-blur-sm border border-white/20 p-6 rounded-2xl">
                    <div class="flex items-center gap-4 text-white">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Gerenciamento Seguro</p>
                            <p class="text-xs text-blue-200">Seus dados estão protegidos com criptografia de ponta a ponta.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabEmail = document.getElementById('tab-email');
            const tabCpf = document.getElementById('tab-cpf');
            const contentEmail = document.getElementById('content-email');
            const contentCpf = document.getElementById('content-cpf');
            const emailInput = document.getElementById('email');
            const cpfInput = document.getElementById('cpf');
            const loginForm = document.getElementById('login-form');

            const activeClass = ['bg-white', 'dark:bg-gray-700', 'text-blue-600', 'dark:text-blue-400', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-600'];
            const inactiveClass = ['text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-200'];

            function switchTab(activeTab, inactiveTab, activeContent, inactiveContent) {
                activeTab.classList.add(...activeClass);
                activeTab.classList.remove(...inactiveClass);
                inactiveTab.classList.remove(...activeClass);
                inactiveTab.classList.add(...inactiveClass);

                activeContent.classList.remove('hidden');
                activeContent.classList.add('block');
                inactiveContent.classList.add('hidden');
                inactiveContent.classList.remove('block');

                if (activeTab === tabEmail) {
                    emailInput.setAttribute('required', 'required');
                    cpfInput.removeAttribute('required');
                } else {
                    cpfInput.setAttribute('required', 'required');
                    emailInput.removeAttribute('required');
                }
            }

            tabEmail.addEventListener('click', () => switchTab(tabEmail, tabCpf, contentEmail, contentCpf));
            tabCpf.addEventListener('click', () => switchTab(tabCpf, tabEmail, contentCpf, contentEmail));

            // Real-time Verification Logic
            const checkUserUrl = "{{ route('auth.check-user') }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            function updateIndicator(input, status, message) {
                const container = input.closest('.relative');
                let indicator = container.querySelector('.verification-indicator');

                if (!indicator) {
                    indicator = document.createElement('div');
                    indicator.className = 'verification-indicator absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none transition-all duration-300';
                    container.appendChild(indicator);
                }

                if (status === 'loading') {
                    indicator.innerHTML = '<svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                } else if (status === 'exists') {
                    indicator.innerHTML = `<x-icon name="check" style="duotone" class="h-5 w-5 text-green-500" />`;
                    input.classList.remove('border-red-500', 'dark:border-red-500');
                    input.classList.add('border-green-500', 'dark:border-green-500');
                } else if (status === 'not_found') {
                    indicator.innerHTML = `<x-icon name="xmark" style="duotone" class="h-5 w-5 text-red-500" />`;
                    input.classList.remove('border-green-500', 'dark:border-green-500');
                    input.classList.add('border-red-500', 'dark:border-red-500');
                } else {
                    indicator.innerHTML = '';
                    input.classList.remove('border-green-500', 'dark:border-green-500', 'border-red-500', 'dark:border-red-500');
                }
            }

            async function verifyUser(input, type) {
                const value = input.value.trim();
                if (value.length < 5) {
                    updateIndicator(input, 'none');
                    return;
                }

                updateIndicator(input, 'loading');

                try {
                    const response = await fetch(checkUserUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ value, type })
                    });

                    const data = await response.json();
                    if (data.exists) {
                        updateIndicator(input, 'exists', data.message);
                    } else {
                        updateIndicator(input, 'not_found', data.message);
                    }
                } catch (error) {
                    console.error('Verificação falhou:', error);
                    updateIndicator(input, 'none');
                }
            }

            let typingTimer;
            const doneTypingInterval = 800;

            [emailInput, cpfInput].forEach(input => {
                const type = input.id === 'email' ? 'email' : 'cpf';

                input.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => verifyUser(input, type), doneTypingInterval);
                });

                input.addEventListener('keydown', () => clearTimeout(typingTimer));
                input.addEventListener('blur', () => verifyUser(input, type));
            });

            // CPF Mask
            cpfInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);
                if (v.length <= 11) {
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                }
                e.target.value = v;
            });

            // Toggle Password
            const tp = document.getElementById('toggle-password');
            const pi = document.getElementById('password');
            const ei = document.getElementById('eye-icon');
            const eoi = document.getElementById('eye-off-icon');

            tp.addEventListener('click', function() {
                const isPass = pi.type === 'password';
                pi.type = isPass ? 'text' : 'password';
                ei.classList.toggle('hidden', !isPass);
                eoi.classList.toggle('hidden', isPass);
            });

            // Submission logic
            loginForm.addEventListener('submit', function(e) {
                const isEmail = tabEmail.classList.contains('bg-white');
                const val = isEmail ? emailInput.value : cpfInput.value;
                document.getElementById('identifier-field').value = val;
                document.getElementById('login-type-field').value = isEmail ? 'email' : 'cpf';

                // reCAPTCHA v3: get token before submit
                @if($recaptchaEnabled && !empty($recaptchaSiteKey) && $recaptchaVersion === 'v3')
                const recaptchaInput = document.getElementById('g-recaptcha-response-v3');
                if (recaptchaInput && typeof grecaptcha !== 'undefined') {
                    e.preventDefault();
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $recaptchaSiteKey }}', { action: 'login' }).then(function(token) {
                            recaptchaInput.value = token;
                            loginForm.submit();
                        });
                    });
                }
                @endif
            });
        });
    </script>
@endsection
