<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Administrativo - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">
</head>
<body class="h-full antialiased bg-slate-950 text-slate-200 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-white/10 bg-slate-900/80 backdrop-blur-xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-xl font-bold text-white mb-1">Acesso Administrativo</h1>
                <p class="text-sm text-slate-400">Apenas administradores. Use em caso de manutenção.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-300">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.acesso-mestre.login') }}" class="space-y-5">
                @csrf
                @if(!empty($bypassSecret))
                    <input type="hidden" name="bypass_secret" value="{{ $bypassSecret }}">
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-600 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Senha</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-600 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-slate-400">Manter conectado</label>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-colors">
                    Entrar
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-slate-400">Voltar ao site</a>
            </p>
        </div>
    </div>
</body>
</html>
