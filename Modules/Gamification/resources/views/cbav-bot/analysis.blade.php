@extends('memberpanel::components.layouts.master')

@section('title', 'CBAV Bot - Análise')

@section('page-title', 'CBAV Bot')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 animate-in fade-in duration-500">
    {{-- Hero do CBAV Bot — Elias --}}
    <div class="rounded-2xl border border-amber-200/50 dark:border-amber-800/50 bg-gradient-to-br from-amber-50/80 to-white dark:from-slate-900 dark:to-slate-800 p-6 shadow-lg shadow-amber-500/5 dark:shadow-none">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-b-xl flex items-end justify-center overflow-hidden bg-amber-100/50 dark:bg-slate-800/50 border border-amber-200/50 dark:border-amber-800/30 shadow-inner">
                <img src="{{ asset('images/CBAVBOT.png') }}" alt="Elias — CBAV Bot" class="w-full h-full object-contain object-bottom" style="max-height: 5.5rem;" onerror="this.style.display='none'">
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight" style="font-family: 'Poppins', sans-serif;">Elias · Sua jornada no painel</h2>
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400 mt-0.5">Seu guia evangélico</p>
            </div>
        </div>
        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            Aqui você acompanha dicas e conquistas sugeridas pelo Elias (CBAV Bot). Complete seu perfil, leia a Bíblia, participe de eventos e ministérios para desbloquear medalhas e insights.
        </p>
    </div>

    {{-- Tópico notificações: uma única dica integrada ao estilo Elias --}}
    @if(request()->query('topic') === 'notifications' || ($topic ?? '') === 'notifications')
    <div class="rounded-2xl border border-amber-200/60 dark:border-amber-700/50 bg-white dark:bg-slate-800/80 p-6 shadow-md">
        <div class="flex items-center gap-2 mb-3">
            <x-icon name="bell" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            <h3 class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400">Notificações no painel</h3>
        </div>
        <p class="text-gray-800 dark:text-gray-200 leading-relaxed mb-4">
            Para escolher <strong>o que receber e silenciar</strong>, use o menu lateral em <strong>Recursos → O que receber / Silenciar</strong> ou o sino de notificações (no topo da página), no rodapé do menu. Lá você define canais (painel, e-mail, push) e o horário de não perturbe por tipo de aviso.
        </p>
        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed mb-4">
            Avisos do mesmo tipo em pouco tempo são <strong>agrupados</strong> em uma única notificação (ex.: « Você tem 5 novos pedidos de oração »). Avisos marcados pela administração como <strong>importantes</strong> sempre são entregues.
        </p>
        <a href="{{ route('memberpanel.preferences.notifications.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500 text-white text-sm font-semibold shadow-lg shadow-amber-500/25 transition-all">
            <x-icon name="sliders" class="w-4 h-4" />
            Ir para preferências
        </a>
    </div>
    @endif

    @if(!empty($verseOfTheDay))
    <div class="rounded-2xl border border-amber-200/60 dark:border-amber-700/50 bg-white dark:bg-slate-800/80 p-6 shadow-md">
        <div class="flex items-center gap-2 mb-3">
            <x-icon name="book-bible" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            <h3 class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400">Versículo</h3>
        </div>
        <p class="text-gray-800 dark:text-gray-200 italic text-lg leading-relaxed">"{{ $verseOfTheDay['text'] }}"</p>
        <p class="text-sm font-semibold text-amber-700 dark:text-amber-400 mt-3">{{ $verseOfTheDay['reference'] }}</p>
    </div>
    @endif

    @if(isset($insight) && $insight)
    <div class="rounded-2xl border border-emerald-200/60 dark:border-emerald-800/50 bg-white dark:bg-slate-800/80 p-6 shadow-md">
        <div class="flex items-center gap-2 mb-3">
            <x-icon name="lightbulb" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Dica do momento</h3>
        </div>
        <p class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ $insight['content'] ?? '' }}</p>
    </div>
    @endif

    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800/80 p-6 shadow-md">
        <div class="flex items-center gap-2 mb-4">
            <x-icon name="medal" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" style="font-family: 'Poppins', sans-serif;">Suas conquistas</h3>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-4">
            Conquistas e medalhas são exibidas no seu perfil e no dashboard. Continue engajado para desbloquear mais!
        </p>
        <a href="{{ route('memberpanel.profile.show') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500 text-white text-sm font-semibold shadow-lg shadow-amber-500/25 dark:shadow-amber-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
            Ver meu perfil
            <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </div>
</div>
@endsection
