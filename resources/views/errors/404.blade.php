@extends('errors::layout')

@section('title', '404 - Onde fomos parar?')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-blue-500/30 blur-3xl rounded-full"></div>
        <x-icon name="map-location-dot" style="duotone" class="relative text-9xl text-blue-500 drop-shadow-[0_0_20px_rgba(59,130,246,0.5)]" />
        <x-icon name="magnifying-glass" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-amber-500 animate-pulse" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            404
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Perdido no deserto? 🌵
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            Parece que você pegou o caminho errado. Essa página não mora mais aqui, ou talvez ela nunca tenha existido... tipo um unicórnio de terno. 🦄💼
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.history.back()"
                class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="arrow-left-long" style="duotone" class="text-xl group-hover:-translate-x-1 transition-transform" />
            Voltar para a Civilização
        </button>

        <a href="{{ url('/') }}"
           class="group flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(37,99,235,0.4)] transition-all active:scale-95">
            <x-icon name="house-chimney" style="duotone" class="text-xl group-hover:scale-110 transition-transform" />
            Ir para a Home
        </a>
    </div>

    <!-- Subtle Footer Hint -->
    <div class="mt-16 pt-8 border-t border-white/5 space-y-4">
        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">
            Quer tentar a sorte?
        </p>
        <div class="flex flex-wrap justify-center gap-8">
            <a href="{{ route('events.public.index') }}" class="text-slate-400 hover:text-blue-400 transition-colors flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                <x-icon name="calendar-star" style="duotone" />
                Eventos
            </a>
            <a href="{{ route('memberpanel.sermons.index') }}" class="text-slate-400 hover:text-purple-400 transition-colors flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                <x-icon name="microphone-stand" style="duotone" />
                Mensagens
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" class="text-slate-400 hover:text-amber-400 transition-colors flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                <x-icon name="headset" style="duotone" />
                Suporte
            </a>
        </div>
    </div>
</div>
@endsection
