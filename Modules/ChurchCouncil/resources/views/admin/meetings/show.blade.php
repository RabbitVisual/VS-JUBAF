@extends('admin::components.layouts.master')

@section('title', $meeting->title . ' - Reunião da Diretoria')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex-1 min-w-0">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $meeting->title }}</h1>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm
                        @if ($meeting->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                        @elseif($meeting->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 animate-pulse
                        @elseif($meeting->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        <span class="w-2 h-2 rounded-full mr-2
                            @if ($meeting->status === 'scheduled') bg-blue-500
                            @elseif($meeting->status === 'in_progress') bg-yellow-500
                            @elseif($meeting->status === 'completed') bg-green-500
                            @else bg-gray-500 @endif"></span>
                        {{ $meeting->status_display }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        {{ $meeting->type_display }}
                    </span>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-6 text-sm font-medium text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <x-icon name="calendar" class="w-4 h-4" />
                    {{ $meeting->scheduled_date->format('d/m/Y \à\s H:i') }}
                </span>
                @if ($meeting->location)
                    <span class="flex items-center gap-1.5">
                        <x-icon name="location-marker" class="w-4 h-4" />
                        {{ $meeting->location }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if ($meeting->status === 'scheduled')
                <button onclick="startMeeting({{ $meeting->id }})"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-green-500/30 flex items-center">
                    <x-icon name="play" class="w-5 h-5 mr-2" />
                    Iniciar Reunião
                </button>
            @elseif ($meeting->status === 'in_progress')
                <button onclick="endMeeting({{ $meeting->id }})"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-red-500/30 flex items-center">
                    <x-icon name="stop" class="w-5 h-5 mr-2" />
                    Encerrar Reunião
                </button>
            @endif

            <a href="{{ route('admin.churchcouncil.meetings.index') }}"
                class="px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 font-bold transition-all shadow-sm flex items-center">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Meeting Details -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <x-icon name="document-text" class="w-5 h-5" />
                    </div>
                    Detalhes da Reunião
                </h3>

                <div class="space-y-6">
                    @if ($meeting->description)
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Pauta Inicial / Descrição</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $meeting->description }}</p>
                        </div>
                    @endif

                    @if ($meeting->objectives)
                         <div class="bg-blue-50 dark:bg-blue-900/10 rounded-xl p-4 border border-blue-100 dark:border-blue-800/30">
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-2">Objetivos Principais</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200 leading-relaxed">{{ $meeting->objectives }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        @if ($meeting->president)
                            <div class="flex items-center gap-3">
                                @if($meeting->president && $meeting->president->user->photo)
                                     <img src="{{ asset('storage/' . $meeting->president->user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                        <span class="text-sm font-bold text-gray-500 dark:text-gray-400">{{ substr($meeting->president->user->name ?? 'P', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $meeting->president->user->name ?? 'Não definido' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Presidente da Reunião</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-3">
                             <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <x-icon name="clock" class="w-5 h-5" />
                             </div>
                             <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Duração / Horários</h4>
                                @if ($meeting->started_at)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Início: {{ $meeting->started_at->format('H:i') }}
                                        @if ($meeting->ended_at)
                                            • Fim: {{ $meeting->ended_at->format('H:i') }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Agendada para {{ $meeting->scheduled_date->format('H:i') }}</p>
                                @endif
                             </div>
                        </div>
                    </div>

                    @if ($meeting->minutes)
                        <div class="mt-4">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                <x-icon name="annotation" class="w-4 h-4" />
                                Ata / Minuta Atual
                            </h4>
                            <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200 bg-gray-50 dark:bg-gray-800/60 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                                <p class="whitespace-pre-line text-sm leading-relaxed">{{ $meeting->minutes }}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $currentMinutesVersion = $meeting->minutesVersions
                            ->sortByDesc('version')
                            ->firstWhere('state', 'assembly_approved')
                            ?? $meeting->minutesVersions->sortByDesc('version')->firstWhere('state', 'council_approved')
                            ?? $meeting->minutesVersions->sortByDesc('version')->first();
                    @endphp

                    @if ($meeting->minutesVersions->count() > 0)
                        <div class="mt-6 border-t border-dashed border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                    <x-icon name="clock" class="w-4 h-4" />
                                    Histórico de Versões da Ata
                                </h4>
                                <div class="space-y-2">
                                    @foreach ($meeting->minutesVersions as $version)
                                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/60 rounded-lg px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-[10px] font-bold uppercase tracking-wide">
                                                    v{{ $version->version }}
                                                </span>
                                                <span>{{ $version->created_at->format('d/m/Y H:i') }}</span>
                                                @if ($version->creator)
                                                    <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                        por {{ $version->creator->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-semibold {{ $version->state === 'assembly_approved' ? 'text-green-500' : ($version->state === 'council_approved' ? 'text-blue-500' : 'text-gray-500') }}">
                                                {{ ucfirst(str_replace('_', ' ', $version->state)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($currentMinutesVersion)
                                @php
                                    $signatures = $currentMinutesVersion->signatures;
                                    $alreadySigned = auth()->check()
                                        ? $signatures->contains(fn($s) => $s->user_id === auth()->id())
                                        : false;
                                @endphp
                                <div class="mt-2 bg-white dark:bg-gray-900/40 border border-dashed border-emerald-300/70 dark:border-emerald-500/60 rounded-xl p-4">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-300">
                                                <x-icon name="badge-check" class="w-4 h-4" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest">
                                                    Visto digital na ata
                                                </p>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    Versão atual: v{{ $currentMinutesVersion->version }} ({{ ucfirst(str_replace('_', ' ', $currentMinutesVersion->state)) }})
                                                </p>
                                            </div>
                                        </div>
                                        @if($canSignMinutes && !$alreadySigned)
                                            <button
                                                type="button"
                                                id="btn-sign-minutes"
                                                data-sign-url="{{ route('admin.churchcouncil.meetings.minutes-signatures.store', [$meeting, $currentMinutesVersion]) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm hover:shadow-md transition"
                                            >
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 mr-1" />
                                                Dar Visto na Ata
                                            </button>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-700 dark:text-gray-200">
                                        @if($signatures->isEmpty())
                                            <p class="text-gray-500 dark:text-gray-400">
                                                Nenhum visto registrado ainda para esta versão da ata.
                                            </p>
                                        @else
                                            <p class="font-semibold mb-1">
                                                Visto digital por:
                                            </p>
                                            <p>
                                                {{ $signatures->map(fn($s) => $s->user?->name)->filter()->implode(', ') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                    @endif
                </div>
            </div>

            <!-- Agendas -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <x-icon name="clipboard-list" class="w-5 h-5" />
                        </div>
                        Pautas da Reunião
                    </h3>
                    <a href="{{ route('admin.churchcouncil.agendas.index', $meeting) }}"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm hover:shadow-purple-500/30 flex items-center">
                        <x-icon name="pencil-alt" class="w-4 h-4 mr-2" />
                        Gerenciar
                    </a>
                </div>

                @if ($meeting->agendas->count() > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($meeting->agendas as $agenda)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                                {{ $loop->iteration }}
                                            </span>
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $agenda->title }}</h4>
                                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                                @if ($agenda->status === 'pending') bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300
                                                @elseif($agenda->status === 'approved') bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300
                                                @elseif($agenda->status === 'rejected') bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                {{ $agenda->status_display }}
                                            </span>
                                        </div>

                                        @if ($agenda->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 pl-9 line-clamp-2">{{ $agenda->description }}</p>
                                        @endif

                                        <div class="pl-9 flex items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                                            @if($agenda->presented_by_member)
                                                <span class="flex items-center gap-1">
                                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                                    {{ $agenda->presented_by_member->user->name }}
                                                </span>
                                            @endif
                                            @if ($agenda->voting_deadline)
                                                <span class="flex items-center gap-1 text-red-500 dark:text-red-400">
                                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                                    Prazo: {{ $agenda->voting_deadline->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <x-icon name="clipboard" class="w-8 h-8" />
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">Nenhuma pauta registrada</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Adicione pautas para organizar a discussão desta reunião.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Participants Checklist -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center justify-between">
                        <span>Participantes</span>
                        <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-lg">{{ $meeting->participant_members->count() }}</span>
                    </h3>
                </div>
                <div class="max-h-[400px] overflow-y-auto">
                    @forelse ($meeting->participant_members as $participant)
                        <div class="px-6 py-3 border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if ($participant->user->photo)
                                        <img class="h-10 w-10 rounded-xl object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                            src="{{ asset('storage/' . $participant->user->photo) }}"
                                            alt="{{ $participant->user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-500 dark:text-gray-400">
                                            {{ substr($participant->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $participant->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $participant->role_display }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            <p>Nenhum participante definido.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-linear-to-br from-blue-600 to-indigo-700 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <x-icon name="chart-pie" class="w-24 h-24" />
                </div>
                <h3 class="text-lg font-bold mb-6 relative z-10">Resumo da Reunião</h3>

                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                        <span class="text-sm font-medium text-blue-100">Total de Pautas</span>
                        <span class="text-xl font-black">{{ $meeting->agendas->count() }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-green-500/20 rounded-xl p-2 backdrop-blur-sm border border-green-400/20">
                            <span class="block text-lg font-bold text-green-300">{{ $meeting->agendas->where('status', 'approved')->count() }}</span>
                            <span class="text-[10px] font-bold text-green-100 uppercase tracking-wide">Aprovadas</span>
                        </div>
                        <div class="bg-red-500/20 rounded-xl p-2 backdrop-blur-sm border border-red-400/20">
                            <span class="block text-lg font-bold text-red-300">{{ $meeting->agendas->where('status', 'rejected')->count() }}</span>
                            <span class="text-[10px] font-bold text-red-100 uppercase tracking-wide">Rejeitadas</span>
                        </div>
                        <div class="bg-yellow-500/20 rounded-xl p-2 backdrop-blur-sm border border-yellow-400/20">
                            <span class="block text-lg font-bold text-yellow-300">{{ $meeting->agendas->where('status', 'pending')->count() }}</span>
                            <span class="text-[10px] font-bold text-yellow-100 uppercase tracking-wide">Pendente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function startMeeting(meetingId) {
        if (confirm('Tem certeza que deseja iniciar esta reunião?')) {
            window.dispatchEvent(new CustomEvent('loading-overlay:show'));
            fetch(`{{ url('admin/conselho/reunioes') }}/${meetingId}/iniciar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                if (data.success) {
                    location.reload();
                } else {
                    alert('{{ __('churchcouncil::messages.meeting_start_error') }}: ' + (data.message || ''));
                }
            })
            .catch(error => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                alert('{{ __('churchcouncil::messages.meeting_start_error') }}');
                console.error(error);
            });
        }
    }

    function endMeeting(meetingId) {
        if (confirm('Tem certeza que deseja encerrar esta reunião?')) {
            window.dispatchEvent(new CustomEvent('loading-overlay:show'));
            fetch(`{{ url('admin/conselho/reunioes') }}/${meetingId}/encerrar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                if (data.success) {
                    location.reload();
                } else {
                    alert('{{ __('churchcouncil::messages.meeting_end_error') }}: ' + (data.message || ''));
                }
            })
            .catch(error => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                alert('{{ __('churchcouncil::messages.meeting_end_error') }}');
                console.error(error);
            });
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('btn-sign-minutes');
        if (!btn) return;

        btn.addEventListener('click', async function () {
            if (!confirm('Confirmar visto digital nesta versão da ata?')) {
                return;
            }

            const url = btn.getAttribute('data-sign-url');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                if (!response.ok || data.success === false) {
                    alert(data.message || 'Não foi possível registrar o visto digital.');
                    return;
                }

                window.location.reload();
            } catch (e) {
                alert('Erro ao comunicar com o servidor. Tente novamente.');
            }
        });
    });
</script>
@endsection

