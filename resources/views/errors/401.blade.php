@extends('errors::layout')

@section('title', '401 - Quem é você?')

@section('content')
<div class="text-center">
    <!-- Comical Animated Icon -->
    <div class="relative inline-block mb-12 animate-float">
        <div class="absolute inset-0 bg-orange-500/30 blur-3xl rounded-full"></div>
        <x-icon name="user-secret" style="duotone" class="relative text-9xl text-orange-500 drop-shadow-[0_0_20px_rgba(249,115,22,0.5)]" />
        <x-icon name="key" style="duotone" class="absolute -bottom-4 -right-4 text-5xl text-amber-500 animate-bounce" />
    </div>

    <!-- Main Message -->
    <div class="space-y-6 mb-12 text-center">
        <h1 class="text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-b from-white to-slate-500 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none">
            401
        </h1>
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight">
            Identidade secreta? 🕵️‍♂️
        </h2>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
            Para entrar aqui você precisa mostrar sua carteirinha (ou só fazer login mesmo). Não adianta tentar o "truque da mente Jedi", nosso sistema é imune! 🖐️✨
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('login') }}"
           class="group flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_20px_rgba(37,99,235,0.4)] transition-all active:scale-95">
            <x-icon name="right-to-bracket" style="duotone" class="text-xl group-hover:translate-x-1 transition-transform" />
            Fazer Login
        </a>

        <a href="{{ url('/') }}"
           class="group flex items-center gap-3 px-8 py-4 glass-card rounded-2xl text-lg font-bold text-white hover:bg-white/10 transition-all active:scale-95">
            <x-icon name="house-chimney" style="duotone" class="text-xl group-hover:scale-110 transition-transform" />
            Voltar para Home
        </a>
    </div>

    <!-- Help links -->
    <div class="mt-16 text-slate-500 text-sm font-medium">
        Ainda não tem uma conta?
        <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">
            Crie uma agora e junte-se a nós!
        </a>
    </div>
</div>
@endsection
