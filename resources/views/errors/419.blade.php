@extends('errors::layout')

@section('title', '419 - O tempo voa!')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-purple-500/30 blur-3xl rounded-full"></div>
        <x-icon name="hourglass-clock" style="duotone" class="relative text-9xl text-purple-500 drop-shadow-[0_0_20px_rgba(168,85,247,0.5)]" />
        <x-icon name="bolt-lightning" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-yellow-400 animate-pulse" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            419
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Sessão expirada? ⌛
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            Parece que você tirou um cochilo ou o tempo voou mais rápido que o normal. Por segurança, sua sessão deu adeus. É hora de um "refresh" mágico! ✨
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.location.reload()"
                class="group flex items-center gap-3 px-8 py-4 bg-purple-600 hover:bg-purple-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all active:scale-95">
            <x-icon name="arrows-rotate" style="duotone" class="text-xl group-hover:rotate-180 transition-transform duration-500" />
            Recarregar Página
        </button>

        <button onclick="window.history.back()"
                class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="arrow-left-to-line" style="duotone" class="text-xl group-hover:-translate-x-1 transition-transform" />
            Voltar
        </button>
    </div>

    <!-- Didactic Hint -->
    <div class="mt-16 bg-white/5 border border-white/10 rounded-3xl p-8 max-w-2xl mx-auto backdrop-blur-sm">
        <div class="flex gap-6 items-center text-left">
            <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-purple-500/20 items-center justify-center flex-shrink-0">
                <x-icon name="shield-check" style="duotone" class="text-3xl text-purple-400" />
            </div>
            <div>
                <h3 class="text-white font-bold text-lg mb-2">Por que isso acontece?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Para proteger sua conta, o sistema encerra automaticamente sua conexão após um período de inatividade. Isso garante que, se você esquecer o site aberto, seus dados continuem protegidos! 🛡️
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
