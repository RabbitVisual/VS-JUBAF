@extends('errors::layout')

@section('title', '500 - O servidor desmaiou!')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-red-500/30 blur-3xl rounded-full"></div>
        <x-icon name="server" style="duotone" class="relative text-9xl text-red-500 drop-shadow-[0_0_20px_rgba(239,68,68,0.5)]" />
        <x-icon name="plug-circle-xmark" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-amber-500 animate-pulse" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            500
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Eita! Algo explodiu 💥
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            Nossos servidores decidiram tirar uma folga não planejada. Provavelmente alguém tropeçou no cabo ou o estagiário derrubou café. ☕🔌 Já estamos correndo para consertar!
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.location.reload()"
                class="group flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(220,38,38,0.3)] transition-all active:scale-95">
            <x-icon name="wrench" style="duotone" class="text-xl group-hover:rotate-45 transition-transform" />
            Tentar a Sorte (Recarregar)
        </button>

        <a href="{{ url('/') }}"
           class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="house-chimney" style="duotone" class="text-xl group-hover:scale-110 transition-transform" />
            Vou fugir para a Home
        </a>
    </div>

    <!-- Debug Section (only in development) -->
    @if(config('app.debug') && isset($exception))
        <div class="mt-16 text-left max-w-2xl mx-auto">
            <button onclick="document.getElementById('tech-details').classList.toggle('hidden')"
                    class="flex items-center gap-2 text-slate-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest mx-auto mb-4">
                <x-icon name="code" style="duotone" />
                Dossiê Secreto (Detalhes Técnicos)
            </button>

            <div id="tech-details" class="hidden glass-card rounded-3xl p-8 border-red-500/20 font-mono text-xs overflow-x-auto space-y-4">
                <p class="text-red-400"><span class="text-slate-500">Erro:</span> {{ get_class($exception) }}</p>
                <p class="text-amber-400"><span class="text-slate-500">Mensagem:</span> {{ $exception->getMessage() }}</p>
                <p class="text-blue-400"><span class="text-slate-500">Local:</span> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>

                <button onclick="copyErrorDetails()"
                        class="mt-4 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-slate-300 transition-all flex items-center gap-2">
                    <x-icon name="copy" style="duotone" />
                    Copiar Detalhes
                </button>
            </div>
        </div>

        <script>
            function copyErrorDetails() {
                const details = `Erro: {{ get_class($exception) }}\nMensagem: {{ $exception->getMessage() }}\nLocal: {{ $exception->getFile() }}:{{ $exception->getLine() }}\nURL: {{ request()->fullUrl() }}`;
                navigator.clipboard.writeText(details).then(() => alert('Detalhes copiados!'));
            }
        </script>
    @endif
</div>
@endsection
