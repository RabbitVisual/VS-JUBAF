@extends('errors::layout')

@section('title', '429 - Vá com calma!')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-amber-500/30 blur-3xl rounded-full"></div>
        <x-icon name="rocket-launch" style="duotone" class="relative text-9xl text-amber-500 drop-shadow-[0_0_20px_rgba(245,158,11,0.5)]" />
        <x-icon name="hand-prohibition" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-red-500 animate-pulse" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            429
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Muita sede ao pote? 🚰
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            Nossa, que velocidade! Você está mais rápido que o Flash pedindo pizza. 🍕⚡ Vamos dar um tempinho para o servidor recuperar o fôlego?
        </p>
    </div>

    <!-- Countdown / Action -->
    <div class="mb-12">
        <div class="inline-flex flex-col items-center glass-card rounded-3xl p-8 border-amber-500/20">
            <x-icon name="hourglass-start" style="duotone" class="text-4xl text-amber-400 mb-4 animate-spin-slow" />
            <span class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Aguarde um momento</span>
            <span class="text-2xl font-black text-white">Pronto em alguns segundos</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.location.reload()"
                class="group flex items-center gap-3 px-8 py-4 bg-amber-600 hover:bg-amber-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(217,119,6,0.4)] transition-all active:scale-95">
            <x-icon name="rotate-right" style="duotone" class="text-xl group-hover:rotate-180 transition-transform duration-500" />
            Tentar Novamente
        </button>

        <a href="{{ url('/') }}"
           class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="house-chimney" style="duotone" class="text-xl group-hover:scale-110 transition-transform" />
            Vou esperar na Home
        </a>
    </div>

    <!-- Why Section -->
    <div class="mt-16 text-slate-500 text-sm font-medium">
        <div class="flex items-center justify-center gap-2 mb-2">
            <x-icon name="circle-info" style="duotone" class="text-amber-500" />
            <span class="font-bold uppercase tracking-wider">Por que fui parado?</span>
        </div>
        <p class="max-w-md mx-auto opacity-70">
            Para garantir que todos tenham uma experiência rápida e estável, limitamos o número de cliques seguidos. É só esperar um tiquinho e tudo volta ao normal! 😊
        </p>
    </div>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow { animation: spin-slow 8s linear infinite; }
</style>
@endsection
