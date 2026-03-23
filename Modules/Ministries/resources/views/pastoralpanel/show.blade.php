@extends('pastoralpanel::components.layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $ministry->name)

@section('content')
    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif

        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ministerios.index') }}" class="hover:text-white transition-colors">Ministérios</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $ministry->name }}</span>
                    </nav>
                    @if($ministry->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-500/20 text-green-300 text-xs font-bold uppercase tracking-wider mb-2">Ativo</span>
                    @endif
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $ministry->name }}</h1>
                    <p class="text-slate-300 text-sm max-w-xl">{{ $ministry->description ? \Str::limit($ministry->description, 100) : 'Ministério da congregação.' }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('pastor.ministerios.plans.index') }}?ministry_id={{ $ministry->id }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="clipboard-list" class="w-5 h-5" />
                        Planos
                    </a>
                    <a href="{{ route('pastor.ministerios.reports.consolidated', $ministry) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="file-pdf" class="w-5 h-5" />
                        PDF Consolidado
                    </a>
                    <a href="{{ route('pastor.ministerios.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        @php
            $colorMap = [
                'blue' => 'from-blue-600 to-blue-700',
                'green' => 'from-green-600 to-green-700',
                'red' => 'from-red-600 to-red-700',
                'yellow' => 'from-yellow-500 to-yellow-600',
                'purple' => 'from-purple-600 to-purple-700',
                'pink' => 'from-pink-500 to-pink-600',
                'indigo' => 'from-indigo-600 to-indigo-700',
            ];
            $iconBgMap = [
                'blue' => 'bg-blue-500/20', 'green' => 'bg-green-500/20', 'red' => 'bg-red-500/20',
                'yellow' => 'bg-yellow-400/25', 'purple' => 'bg-purple-500/20', 'pink' => 'bg-pink-500/20', 'indigo' => 'bg-indigo-500/20',
            ];
            $gradientClass = $colorMap[$ministry->color] ?? $colorMap['blue'];
            $iconBgClass = $iconBgMap[$ministry->color] ?? $iconBgMap['blue'];
        @endphp
        <div class="rounded-2xl bg-gradient-to-br {{ $gradientClass }} p-6 md:p-8 text-white border border-white/10 flex flex-col sm:flex-row items-center gap-6">
            <div class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 {{ $iconBgClass }} rounded-2xl flex items-center justify-center border border-white/30">
                @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                    <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-8 h-8 md:w-10 md:h-10 text-white" />
                @else
                    <span class="text-3xl md:text-4xl">{{ $ministry->icon ?? '⛪' }}</span>
                @endif
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-xl font-bold">{{ $ministry->name }}</h2>
                <p class="text-white/80 text-sm mt-1">{{ $ministry->description ?? 'Sem descrição detalhada.' }}</p>
            </div>
            <div class="flex-shrink-0 px-6 py-4 bg-white/10 rounded-xl border border-white/20 text-center">
                <span class="text-3xl font-bold block">{{ $ministry->active_members_count }}</span>
                <span class="text-xs font-medium text-white/80 uppercase tracking-wider">Membros ativos</span>
                @if($ministry->max_members)
                    <div class="mt-2 w-24 h-1.5 bg-white/20 rounded-full overflow-hidden mx-auto">
                        <div class="bg-white h-full rounded-full" style="width: {{ min(($ministry->active_members_count / $ministry->max_members) * 100, 100) }}%"></div>
                    </div>
                    <span class="text-[10px] text-white/70">limite {{ $ministry->max_members }}</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="users" class="w-5 h-5 text-amber-500" />
                        Liderança
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/50 border border-gray-100 dark:border-slate-600">
                            @if($ministry->leader && $ministry->leader->photo)
                                <img class="h-11 w-11 rounded-xl object-cover" src="{{ Storage::url($ministry->leader->photo) }}" alt="">
                            @elseif($ministry->leader)
                                <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(mb_substr($ministry->leader->first_name ?? $ministry->leader->name, 0, 1)) }}
                                </div>
                            @else
                                <div class="h-11 w-11 rounded-xl bg-gray-200 dark:bg-slate-600 flex items-center justify-center">
                                    <x-icon name="user" class="w-5 h-5 text-gray-500" />
                                </div>
                            @endif
                            <div>
                                <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase">Líder</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ministry->leader ? $ministry->leader->name : 'Não definido' }}</p>
                                @if($ministry->leader && $ministry->leader->email)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ministry->leader->email }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/50 border border-gray-100 dark:border-slate-600">
                            @if($ministry->coLeader && $ministry->coLeader->photo)
                                <img class="h-11 w-11 rounded-xl object-cover" src="{{ Storage::url($ministry->coLeader->photo) }}" alt="">
                            @elseif($ministry->coLeader)
                                <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(mb_substr($ministry->coLeader->first_name ?? $ministry->coLeader->name, 0, 1)) }}
                                </div>
                            @else
                                <div class="h-11 w-11 rounded-xl bg-gray-200 dark:bg-slate-600 flex items-center justify-center">
                                    <x-icon name="user-group" class="w-5 h-5 text-gray-500" />
                                </div>
                            @endif
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Co-líder</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ministry->coLeader ? $ministry->coLeader->name : 'Não definido' }}</p>
                                @if($ministry->coLeader && $ministry->coLeader->email)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ministry->coLeader->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @php $settings = $ministry->settings ?? []; @endphp
                @if(!empty($settings))
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-icon name="sliders" class="w-5 h-5 text-amber-500" />
                            Informações
                        </h3>
                        <dl class="space-y-2 text-sm">
                            @if(isset($settings['meeting_day']) || isset($settings['meeting_time']))
                                <div class="flex justify-between gap-2">
                                    <dt class="text-gray-500 dark:text-gray-400">Reunião</dt>
                                    <dd class="text-gray-900 dark:text-white font-medium">{{ $settings['meeting_day'] ?? '' }} {{ isset($settings['meeting_time']) ? '· ' . $settings['meeting_time'] : '' }}</dd>
                                </div>
                            @endif
                            @if(isset($settings['meeting_place']))
                                <div class="flex justify-between gap-2">
                                    <dt class="text-gray-500 dark:text-gray-400">Local</dt>
                                    <dd class="text-gray-900 dark:text-white font-medium">{{ $settings['meeting_place'] }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-6">
                @if($ministry->pendingMembers->count() > 0)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-amber-200 dark:border-amber-800/50 overflow-hidden">
                        <div class="px-6 py-4 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50">
                            <h3 class="text-lg font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                                <x-icon name="clock" class="w-5 h-5" />
                                Solicitações pendentes ({{ $ministry->pendingMembers->count() }})
                            </h3>
                        </div>
                        <div class="p-6 divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($ministry->pendingMembers as $member)
                                <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 first:pt-0">
                                    <div class="flex items-center gap-3">
                                        @if($member->photo)
                                            <img class="h-11 w-11 rounded-xl object-cover" src="{{ Storage::url($member->photo) }}" alt="">
                                        @else
                                            <div class="h-11 w-11 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-700 dark:text-amber-300 font-bold">
                                                {{ strtoupper(mb_substr($member->first_name ?? $member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Solicitado em {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '—' }}
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('pastor.ministerios.members.approve', [$ministry, $member]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition-colors">
                                            <x-icon name="check" class="w-4 h-4" /> Aprovar
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/30">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Membros ativos</h3>
                        <span class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $ministry->active_members_count }} total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Membro</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Função</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Desde</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @forelse($ministry->activeMembers as $member)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($member->photo)
                                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($member->photo) }}" alt="">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-700 dark:text-amber-300 font-bold text-sm">
                                                        {{ strtoupper(mb_substr($member->first_name ?? $member->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $roleLabels = ['leader' => 'Líder', 'co_leader' => 'Co-líder', 'coordinator' => 'Coordenador', 'member' => 'Membro'];
                                                $currentRole = $member->pivot->role ?? 'member';
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300">
                                                {{ $roleLabels[$currentRole] ?? ucfirst($currentRole) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            Nenhum membro ativo cadastrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
