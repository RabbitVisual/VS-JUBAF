@extends('errors::layout')

@section('title', '403 - Área Proibida')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-red-500/30 blur-3xl rounded-full"></div>
        <x-icon name="hand-promt" style="duotone" class="relative text-9xl text-red-500 drop-shadow-[0_0_20px_rgba(239,68,68,0.5)]" />
        <x-icon name="shield-halved" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-blue-500 animate-pulse" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            403
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Oops! Área Restrita 🛑
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            @if(isset($exception) && $exception->getMessage())
                "{{ $exception->getMessage() }}"
            @else
                Você não tem o "cartão de acesso VIP" para esta área. É como tentar entrar na Batcaverna sem ser o Batman. 🦇🚫
            @endif
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.history.back()"
                class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="reply" style="duotone" class="text-xl group-hover:-translate-x-1 transition-transform" />
            Voltar
        </button>

        <a href="{{ url('/') }}"
           class="group flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(220,38,38,0.3)] transition-all active:scale-95">
            <x-icon name="house-lock" style="duotone" class="text-xl group-hover:scale-110 transition-transform" />
            Vou para Home
        </a>
    </div>

    <!-- Custom Support Hint -->
    <div class="mt-16 text-slate-500 text-sm font-medium italic">
        Acha que deveria estar aqui?
        <a href="mailto:{{ config('mail.from.address') }}" class="text-red-400 hover:text-red-300 font-bold not-italic transition-colors">
            Fale com os guardas (suporte)!
        </a>
    </div>
</div>
@endsection
