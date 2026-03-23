@extends('memberpanel::components.layouts.master')

@section('page-title', 'Preferências de Notificações')

@section('content')
<div class="space-y-8 pb-12">
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-600 rounded-full blur-[100px]"></div>
        </div>
        <div class="relative px-8 py-10 z-10">
            <p class="text-indigo-200/80 font-bold uppercase tracking-widest text-xs">Central de Preferências</p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mt-1">Notificações</h1>
            <p class="text-slate-300 font-medium max-w-xl mt-2">
                Escolha por onde e quando deseja receber cada tipo de aviso. O horário "Não perturbe" evita envio de e-mail e push no período definido.
            </p>
        </div>
    </div>

    {{-- Infobox educativa: privacidade e controle --}}
    <div class="rounded-2xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/30 p-6" role="region" aria-label="Sobre suas preferências">
        <div class="flex gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                <x-icon name="shield-halved" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white" style="font-family: 'Poppins', sans-serif;">Sua privacidade é nossa prioridade</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1" style="font-family: 'Inter', sans-serif;">
                    Escolha aqui quais avisos deseja receber por E-mail ou Push para manter seu foco no que importa. Nada é enviado sem sua preferência por canal.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2" style="font-family: 'Inter', sans-serif;">
                    Notificações marcadas pela administração como <strong>importantes</strong> (prioridade alta/urgente) são sempre entregues, mesmo no horário de silêncio ou com canal desativado.
                </p>
            </div>
        </div>
    </div>

    @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pastor') || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())) && Route::has('admin.notifications.control.dashboard'))
    <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-950/30 px-4 py-3 flex items-center gap-3">
        <x-icon name="circle-info" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
        <p class="text-sm text-amber-800 dark:text-amber-200">
            Você está no <strong>painel do membro</strong>, configurando as preferências do seu perfil. Para gerenciar notificações do sistema (enviar, templates, Control Room), use o
            <a href="{{ route('admin.notifications.control.dashboard') }}" class="font-semibold underline hover:no-underline">painel administrativo → Notificações</a>.
        </p>
    </div>
    @endif

    @if (session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 flex items-center gap-3">
            <x-icon name="circle-check" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 shrink-0" />
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('memberpanel.preferences.notifications.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Por tipo de notificação</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Marque os canais em que deseja receber cada aviso.</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach ($notificationTypes as $typeKey => $typeLabel)
                    @php
                        $pref = $preferences->get($typeKey);
                        $selectedChannels = $pref ? $pref->channels : ['in_app'];
                        $dndFrom = $pref && $pref->dnd_from ? substr($pref->dnd_from, 0, 5) : '';
                        $dndTo = $pref && $pref->dnd_to ? substr($pref->dnd_to, 0, 5) : '';
                    @endphp
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="sm:w-64 shrink-0">
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300">{{ $typeLabel }}</label>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Não perturbe (opcional)</p>
                            <div class="flex items-center gap-2 mt-2">
                                <input type="time" name="preferences[{{ $typeKey }}][dnd_from]" value="{{ $dndFrom }}"
                                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm w-28"
                                    title="Início do silêncio">
                                <span class="text-gray-400 dark:text-slate-500 text-sm">até</span>
                                <input type="time" name="preferences[{{ $typeKey }}][dnd_to]" value="{{ $dndTo }}"
                                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm w-28"
                                    title="Fim do silêncio">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($channels as $channelKey => $channelLabel)
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="preferences[{{ $typeKey }}][channels][]" value="{{ $channelKey }}"
                                        {{ in_array($channelKey, $selectedChannels, true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800">
                                    <span class="text-sm font-medium text-gray-700 dark:text-slate-300">{{ $channelLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-4">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                <x-icon name="floppy-disk" class="w-5 h-5" />
                Salvar preferências
            </button>
        </div>
    </form>
</div>
@endsection
