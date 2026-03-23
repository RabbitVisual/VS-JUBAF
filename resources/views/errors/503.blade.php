@extends('errors.layout')

@section('title', '503 - Santuário em Obra')

@section('content')
<div class="text-center">
    {{-- Hero: construção sagrada --}}
    <div class="relative inline-block mb-8 animate-float">
        <div class="absolute inset-0 bg-amber-500/20 blur-3xl rounded-full"></div>
        <x-icon name="book-bible" style="duotone" class="relative text-8xl text-amber-400/90 drop-shadow-[0_0_30px_rgba(251,191,36,0.4)]" />
        <x-icon name="helmet-safety" style="duotone" class="absolute -bottom-2 -right-2 text-4xl text-slate-500/80" />
    </div>

    {{-- Mensagem inspiradora --}}
    <div class="space-y-4 mb-10">
        <h1 class="text-8xl md:text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-600 opacity-20 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 select-none" style="font-family: 'Merriweather', Georgia, serif;">
            503
        </h1>
        <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight" style="font-family: 'Merriweather', Georgia, serif;">
            Santuário em Obra
        </h2>
        <p class="text-base md:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed" style="font-family: Georgia, serif;">
            Estamos organizando a casa para melhor serví-lo. A Palavra de Deus não para — use o botão abaixo para abrir a Bíblia.
        </p>
    </div>

    {{-- Card de status --}}
    <div class="mb-10">
        <div class="inline-flex flex-col items-center glass-card rounded-2xl px-8 py-6 border border-amber-500/20">
            <div class="flex gap-1.5 mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse" style="animation-delay: 0.15s"></span>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse" style="animation-delay: 0.3s"></span>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Em manutenção</span>
            <span class="text-xl font-black text-white mt-1">Voltamos em breve</span>
        </div>
    </div>

    {{-- CTA: apenas o botão para abrir a Bíblia (sem iframe) --}}
    <div class="mb-12">
        <a href="{{ url('/biblia-online') }}"
           target="_blank"
           rel="noopener"
           class="inline-flex items-center gap-3 px-8 py-4 bg-amber-600 hover:bg-amber-500 rounded-2xl text-lg font-bold text-white shadow-[0_0_24px_rgba(217,119,6,0.35)] transition-all active:scale-95">
            <x-icon name="book-bible" style="duotone" class="text-2xl" />
            Abrir Bíblia Online
        </a>
        <p class="mt-3 text-sm text-slate-500">Abre em nova aba. Leia livros e capítulos mesmo durante a manutenção.</p>
    </div>

    {{-- Botão atualizar --}}
    <div class="mt-10">
        <button type="button" onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
            <x-icon name="arrows-rotate" style="duotone" class="w-4 h-4" />
            Verificar se já voltamos
        </button>
    </div>

    {{-- Rodapé inspirador --}}
    <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto text-left opacity-80">
        <div class="flex gap-3 p-4 rounded-xl bg-white/5 border border-white/5">
            <x-icon name="sparkles" style="duotone" class="text-xl text-amber-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-white font-bold text-sm">Novidades</h4>
                <p class="text-xs text-slate-500">Implementando melhorias para você.</p>
            </div>
        </div>
        <div class="flex gap-3 p-4 rounded-xl bg-white/5 border border-white/5">
            <x-icon name="shield-heart" style="duotone" class="text-xl text-blue-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-white font-bold text-sm">Segurança</h4>
                <p class="text-xs text-slate-500">Cuidando da sua experiência.</p>
            </div>
        </div>
        <div class="flex gap-3 p-4 rounded-xl bg-white/5 border border-white/5">
            <x-icon name="book-bible" style="duotone" class="text-xl text-amber-400 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-white font-bold text-sm">Palavra viva</h4>
                <p class="text-xs text-slate-500">A Bíblia segue disponível.</p>
            </div>
        </div>
    </div>
</div>
@endsection
