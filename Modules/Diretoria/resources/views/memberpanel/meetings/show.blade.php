@extends('memberpanel::components.layouts.master')

@section('title', $meeting->title . ' - Reunião da Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ $meeting->title }}</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ $meeting->scheduled_date->format('d/m/Y \à\s H:i') }}
                        @if ($meeting->location)
                            <span class="text-gray-400 dark:text-slate-600 mx-1">•</span>
                            <x-icon name="location-dot" class="w-4 h-4" />
                            {{ $meeting->location }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border
                    @if ($meeting->status === 'scheduled') bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                    @elseif($meeting->status === 'in_progress') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 animate-pulse
                    @elseif($meeting->status === 'completed') bg-gray-100 text-gray-600 border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700
                    @else bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 @endif">
                        {{ $meeting->status_display }}
                    </span>
                    <a href="{{ route('memberpanel.Diretoria.meetings.index') }}"
                        class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-white transition-colors text-sm font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700">
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Meeting Details -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 relative overflow-hidden transition-colors duration-200">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <x-icon name="users" class="w-32 h-32 text-blue-600 dark:text-blue-500" />
                </div>

                <h3 class="text-xs font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-6">Informações
                    da Reunião</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">Tipo de
                            Reunião</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ ucfirst(str_replace('_', ' ', $meeting->meeting_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                            Presidente</p>
                        <div class="flex items-center gap-3">
                            @if ($meeting->president && $meeting->president->user->photo)
                                <img src="{{ asset('storage/' . $meeting->president->user->photo) }}"
                                    class="w-8 h-8 rounded-full border border-gray-200 dark:border-slate-700"
                                    alt="">
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-slate-400">
                                    {{ $meeting->president ? substr($meeting->president->user->name, 0, 1) : '?' }}
                                </div>
                            @endif
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $meeting->president->user->name ?? 'Não definido' }}</p>
                        </div>
                    </div>
                    @if ($meeting->description)
                        <div class="md:col-span-2">
                            <p class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2">
                                Descrição</p>
                            <div
                                class="bg-gray-50 dark:bg-slate-950/50 rounded-lg p-4 border border-gray-100 dark:border-slate-800">
                                <p class="text-base text-gray-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                                    {{ $meeting->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Agendas -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="clipboard-list" class="w-5 h-5 text-blue-600 dark:text-blue-500" />
                        Pautas da Reunião
                    </h3>
                    @if ($meeting->status === 'in_progress')
                        <span
                            class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-3 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-500/20 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-500"></span>
                            Votações Abertas
                        </span>
                    @endif
                </div>

                @if ($meeting->agendas->count() > 0)
                    <div class="space-y-4">
                        @foreach ($meeting->agendas as $agenda)
                            <div
                                class="border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950/30 rounded-xl p-5 hover:border-blue-300 dark:hover:border-slate-700 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                                {{ $agenda->title }}</h4>
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border
                                        @if ($agenda->priority === 'high') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20
                                        @elseif($agenda->priority === 'medium') bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20
                                        @else bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 @endif">
                                                {{ ucfirst($agenda->priority) }}
                                            </span>
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border
                                        @if ($agenda->status === 'pending') bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-500 dark:border-yellow-500/20
                                        @elseif($agenda->status === 'approved') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20
                                        @elseif($agenda->status === 'rejected') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20
                                        @else bg-gray-200 text-gray-600 border-gray-300 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 @endif">
                                                {{ $agenda->status_display }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-slate-400 mb-4 leading-relaxed">
                                            {{ $agenda->description }}</p>

                                        @if ($agenda->member_vote)
                                            <div
                                                class="mt-4 p-4 bg-gray-100 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 flex items-start gap-4">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    @if ($agenda->member_vote->vote == 'yes')
                                                        <x-icon name="circle-check"
                                                            class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                                                    @elseif($agenda->member_vote->vote == 'no')
                                                        <x-icon name="circle-xmark"
                                                            class="w-5 h-5 text-red-500 dark:text-red-400" />
                                                    @else
                                                        <x-icon name="circle-minus"
                                                            class="w-5 h-5 text-gray-400 dark:text-slate-400" />
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">Seu
                                                        voto: <span
                                                            class="uppercase">{{ ucfirst($agenda->member_vote->vote) }}</span>
                                                    </p>
                                                    @if ($agenda->member_vote->comments)
                                                        <p class="text-xs text-gray-500 dark:text-slate-400 italic">
                                                            "{{ $agenda->member_vote->comments }}"</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($meeting->status === 'in_progress' && $agenda->status === 'pending')
                                            <form method="POST"
                                                action="{{ route('memberpanel.Diretoria.meetings.vote', $agenda) }}"
                                                class="mt-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-500/20 p-4 rounded-xl">
                                                @csrf
                                                <p
                                                    class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
                                                    Registrar Voto</p>
                                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                                    <select name="vote" required
                                                        class="px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                                                        <option value="">Selecione...</option>
                                                        <option value="yes">Sim</option>
                                                        <option value="no">Não</option>
                                                        <option value="abstain">Abstenção</option>
                                                    </select>
                                                    <input type="text" name="comments"
                                                        placeholder="Comentários (opcional)"
                                                        class="px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm flex-1 w-full focus:ring-blue-500 focus:border-blue-500">
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-colors text-sm font-bold shadow-lg shadow-blue-500/20 w-full sm:w-auto">
                                                        Confirmar Voto
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 border-2 border-dashed border-gray-200 dark:border-slate-800 rounded-xl">
                        <x-icon name="clipboard-list" class="w-10 h-10 text-gray-400 dark:text-slate-700 mx-auto mb-3" />
                        <p class="text-gray-500 dark:text-slate-500 font-medium">Nenhuma pauta adicionada a esta reunião
                            ainda.</p>
                    </div>
                @endif
            </div>

            <!-- Participants -->
            @if ($meeting->participant_members->count() > 0)
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <x-icon name="users" class="w-5 h-5 text-purple-600 dark:text-purple-500" />
                        Participantes
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($meeting->participant_members as $participant)
                            <div
                                class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-slate-950/50 border border-gray-200 dark:border-slate-800">
                                <div class="flex-shrink-0">
                                    @if ($participant->user->photo)
                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-slate-700"
                                            src="{{ asset('storage/' . $participant->user->photo) }}"
                                            alt="{{ $participant->user->name }}">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-200 dark:bg-slate-800 flex items-center justify-center border border-gray-200 dark:border-slate-700">
                                            <span
                                                class="text-sm font-bold text-gray-500 dark:text-slate-400">{{ substr($participant->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $participant->user->name }}</p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-slate-500 font-medium uppercase tracking-wide">
                                        {{ $participant->role_display }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
