@extends('admin::components.layouts.master')

@section('page-title', 'Configurações da Projeção')

@section('content')
    <div class="p-6 space-y-8 max-w-3xl">
        <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest">
            <a href="{{ route('admin.projection.index') }}" class="hover:underline">Projeção</a>
            <span class="w-1 h-1 rounded-full bg-gray-400"></span>
            <span class="text-gray-500">Configurações</span>
        </nav>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Configurações da Projeção</h1>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.projection.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-white/5 p-6 shadow-sm">
                <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="projector" class="w-5 h-5 text-indigo-500" />
                    Tela sem login (viewer token)
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Quando ativado, a tela de projeção pode ser exibida em outro dispositivo (ex.: projetor) sem precisar fazer login.
                </p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2 font-bold">URL da tela (abrir no navegador do projetor — esta é a página que exibe a projeção):</p>
                <p class="mb-2">
                    <code class="block bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg text-xs break-all">{{ $viewerScreenUrl }}</code>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Substitua <strong>SEU_TOKEN</strong> pelo token secreto definido abaixo. Não use a URL da API no navegador (ela devolve só JSON).</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                    A página acima carrega a tela e consome a API internamente: <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded text-[10px]">{{ $viewerStateUrl }}?viewer_token=…</code>
                </p>

                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="projection_viewer_enabled" value="0">
                        <input type="checkbox" name="projection_viewer_enabled" value="1"
                               {{ $viewerEnabled ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Ativar tela sem login (viewer token)</span>
                    </label>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Token do viewer</label>
                        <input type="password" name="projection_viewer_token"
                               value=""
                               placeholder="{{ $viewerTokenMasked ? 'Deixe em branco para manter o atual' : 'Digite um token secreto' }}"
                               class="w-full max-w-md rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900"
                               autocomplete="new-password">
                        @if($viewerTokenMasked)
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Token atual: {{ $viewerTokenMasked }}</p>
                            <label class="mt-2 flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="projection_viewer_token_clear" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Limpar token (desativa o acesso por token até definir um novo)</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-white/5 p-6 shadow-sm">
                <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="desktop" class="w-5 h-5 text-indigo-500" />
                    App Desktop de Projeção
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Permite que operadores façam login no Vertex Projector (app Windows) com e-mail e senha do site. Quem pode usar pode ser restrito por perfil (role).
                </p>
                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="projection_desktop_login_enabled" value="0">
                        <input type="checkbox" name="projection_desktop_login_enabled" value="1"
                               {{ $desktopLoginEnabled ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Permitir login no app desktop</span>
                    </label>
                    <div class="pt-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Quem pode usar</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Deixe nenhum marcado para permitir qualquer usuário autenticado. Marque um ou mais perfis para restringir.</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="projection_desktop_allowed_roles[]" value="{{ $role->slug }}"
                                           {{ in_array($role->slug, $desktopAllowedRoles, true) ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-black uppercase transition">
                    Salvar configurações
                </button>
                <a href="{{ route('admin.projection.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-bold text-sm">
                    Voltar ao centro de projeção
                </a>
            </div>
        </form>
    </div>
@endsection
