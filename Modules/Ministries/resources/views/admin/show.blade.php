@extends('admin::components.layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $ministry->name)

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-gray-400 font-medium mb-2">
                        <a href="{{ route('admin.ministries.index') }}" class="hover:text-white transition-colors">Ministérios</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $ministry->name }}</span>
                    </nav>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Ministério</span>
                        @if($ministry->is_active)
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Ativo</span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $ministry->name }}</h1>
                    <p class="text-gray-300 max-w-xl">{{ $ministry->description ? \Str::limit($ministry->description, 80) : 'Ministério da congregação.' }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('admin.ministries.plans.create', $ministry) }}"
                        class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                        <x-icon name="pen-to-square" class="w-5 h-5 text-blue-600" />
                        Novo plano
                    </a>
                    <a href="{{ route('admin.ministries.plans.index', ['ministry_id' => $ministry->id]) }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all inline-flex items-center gap-2">
                        <x-icon name="clipboard-list" class="w-5 h-5" />
                        Planos
                    </a>
                    <a href="{{ route('admin.ministries.reports.consolidated', $ministry) }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all inline-flex items-center gap-2">
                        <x-icon name="file-pdf" class="w-5 h-5" />
                        PDF Consolidado
                    </a>
                    <a href="{{ route('admin.ministries.edit', $ministry) }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all inline-flex items-center gap-2">
                        <x-icon name="pencil" class="w-5 h-5" />
                        Editar
                    </a>
                    <a href="{{ route('admin.ministries.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all inline-flex items-center gap-2">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Ministry Hero Header -->
        @php
            $colorMap = [
                'blue' => 'from-blue-600 to-blue-700 shadow-blue-500/20',
                'green' => 'from-green-600 to-green-700 shadow-green-500/20',
                'red' => 'from-red-600 to-red-700 shadow-red-500/20',
                'yellow' => 'from-yellow-500 to-yellow-600 shadow-yellow-500/20',
                'purple' => 'from-purple-600 to-purple-700 shadow-purple-500/20',
                'pink' => 'from-pink-500 to-pink-600 shadow-pink-500/20',
                'indigo' => 'from-indigo-600 to-indigo-700 shadow-indigo-500/20',
            ];
            $iconBgMap = [
                'blue' => 'bg-blue-500/20',
                'green' => 'bg-green-500/20',
                'red' => 'bg-red-500/20',
                'yellow' => 'bg-yellow-400/25',
                'purple' => 'bg-purple-500/20',
                'pink' => 'bg-pink-500/20',
                'indigo' => 'bg-indigo-500/20',
            ];
            $iconRingMap = [
                'blue' => 'ring-4 ring-blue-300/60',
                'green' => 'ring-4 ring-green-300/60',
                'red' => 'ring-4 ring-red-300/60',
                'yellow' => 'ring-4 ring-amber-300/70',
                'purple' => 'ring-4 ring-purple-300/60',
                'pink' => 'ring-4 ring-pink-300/60',
                'indigo' => 'ring-4 ring-indigo-300/60',
            ];
            $gradientClass = $colorMap[$ministry->color] ?? $colorMap['blue'];
            $iconBgClass = $iconBgMap[$ministry->color] ?? $iconBgMap['blue'];
            $iconRingClass = $iconRingMap[$ministry->color] ?? $iconRingMap['blue'];
        @endphp
        <div class="relative overflow-hidden bg-gradient-to-br {{ $gradientClass }} rounded-3xl shadow-xl p-8 md:p-12 text-white border border-gray-200 dark:border-gray-700">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-4 opacity-10">
                <x-icon name="church" class="w-64 h-64" />
            </div>

            <div class="relative flex flex-col md:flex-row items-center gap-8">
                <div class="flex-shrink-0 w-24 h-24 md:w-32 md:h-32 {{ $iconBgClass }} backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/40 shadow-2xl animate-float {{ $iconRingClass }}">
                    @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                        <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-14 h-14 md:w-20 md:h-20 text-white drop-shadow-[0_0_18px_rgba(0,0,0,0.45)]" />
                    @else
                        <span class="text-5xl md:text-6xl drop-shadow-[0_0_18px_rgba(0,0,0,0.45)]">{{ $ministry->icon ?? '⛪' }}</span>
                    @endif
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mb-3">
                        @if ($ministry->is_active)
                            <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs font-bold uppercase tracking-wider text-white/70">
                                <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                Inativo
                            </span>
                        @endif

                        @if ($ministry->requires_approval)
                            <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold uppercase tracking-wider">
                                <x-icon name="lock-closed" class="w-3.5 h-3.5 mr-1.5" />
                                Privado
                            </span>
                        @endif
                    </div>

                    <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight drop-shadow-lg">{{ $ministry->name }}</h2>
                    <p class="text-lg text-white/80 max-w-2xl leading-relaxed">{{ $ministry->description ?? 'Nenhuma descrição detalhada disponível para este ministério.' }}</p>
                </div>

                <div class="flex flex-col items-center justify-center p-6 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 min-w-[150px]">
                    <span class="text-4xl font-black tracking-tighter">{{ $ministry->active_members_count }}</span>
                    <span class="text-xs font-bold uppercase tracking-widest text-white/70">Membros Ativos</span>
                    @if($ministry->max_members)
                        <div class="mt-3 w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-white h-full transition-all duration-1000" style="width: {{ min(($ministry->active_members_count / $ministry->max_members) * 100, 100) }}%"></div>
                        </div>
                        <span class="mt-1 text-[10px] font-medium">limite: {{ $ministry->max_members }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Leadership, Settings & Quick Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Leadership Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <x-icon name="users" class="w-5 h-5 mr-2 text-purple-500" />
                        Liderança
                    </h3>
                    <div class="space-y-6">
                        <!-- Leader -->
                        <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                            <div class="relative flex-shrink-0">
                                @if($ministry->leader && $ministry->leader->photo)
                                    <img class="h-12 w-12 rounded-xl object-cover border-2 border-purple-400/70 shadow-sm" src="{{ Storage::url($ministry->leader->photo) }}" alt="{{ $ministry->leader->name }}">
                                @elseif($ministry->leader)
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-lg border-2 border-purple-400/70 shadow-sm">
                                        {{ strtoupper(mb_substr($ministry->leader->first_name ?? $ministry->leader->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white border-2 border-slate-400/70 shadow-sm">
                                        <x-icon name="user" class="w-6 h-6" />
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] border-2 border-white dark:border-gray-900">
                                    <x-icon name="crown" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest">Líder</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $ministry->leader ? $ministry->leader->name : 'Ninguém definido' }}
                                </p>
                                @if($ministry->leader && $ministry->leader->email)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ministry->leader->email }}</p>
                                @endif
                                @if($ministry->leader)
                                    <div class="mt-2 flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', $ministry->leader) }}" class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                            <x-icon name="eye" class="w-3.5 h-3.5 mr-1" />
                                            Ver perfil
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Co-Leader -->
                        <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                            <div class="relative flex-shrink-0">
                                @if($ministry->coLeader && $ministry->coLeader->photo)
                                    <img class="h-12 w-12 rounded-xl object-cover border-2 border-blue-400/70 shadow-sm" src="{{ Storage::url($ministry->coLeader->photo) }}" alt="{{ $ministry->coLeader->name }}">
                                @elseif($ministry->coLeader)
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white font-bold text-lg border-2 border-blue-400/70 shadow-sm">
                                        {{ strtoupper(mb_substr($ministry->coLeader->first_name ?? $ministry->coLeader->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white border-2 border-slate-400/70 shadow-sm">
                                        <x-icon name="user-group" class="w-6 h-6" />
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] border-2 border-white dark:border-gray-900">
                                    <x-icon name="user-group" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Co-Líder</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $ministry->coLeader ? $ministry->coLeader->name : 'Ninguém definido' }}
                                </p>
                                @if($ministry->coLeader && $ministry->coLeader->email)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ministry->coLeader->email }}</p>
                                @endif
                                @if($ministry->coLeader)
                                    <div class="mt-2 flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', $ministry->coLeader) }}" class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                            <x-icon name="eye" class="w-3.5 h-3.5 mr-1" />
                                            Ver perfil
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ministry Settings Card (dynamic from settings JSON) -->
                @php
                    $settings = $ministry->settings ?? [];
                @endphp
                @if(!empty($settings))
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <x-icon name="sliders" class="w-5 h-5 mr-2 text-amber-500" />
                            Configurações do Ministério
                        </h3>
                        <dl class="space-y-3 text-sm">
                            @if(isset($settings['type']))
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Tipo</dt>
                                    <dd class="text-gray-900 dark:text-white font-semibold text-right">
                                        {{ ucfirst($settings['type']) }}
                                    </dd>
                                </div>
                            @endif
                            @if(isset($settings['meeting_day']) || isset($settings['meeting_time']))
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Reunião</dt>
                                    <dd class="text-gray-900 dark:text-white text-right">
                                        {{ $settings['meeting_day'] ?? '' }}
                                        @if(isset($settings['meeting_day']) && isset($settings['meeting_time']))
                                            ·
                                        @endif
                                        {{ $settings['meeting_time'] ?? '' }}
                                    </dd>
                                </div>
                            @endif
                            @if(isset($settings['meeting_place']))
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400 font-medium">Local</dt>
                                    <dd class="text-gray-900 dark:text-white text-right">
                                        {{ $settings['meeting_place'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(isset($settings['whatsapp_group']) || isset($settings['contact_whatsapp']))
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400 font-medium">WhatsApp</dt>
                                    <dd class="text-gray-900 dark:text-white text-right break-all">
                                        {{ $settings['whatsapp_group'] ?? $settings['contact_whatsapp'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(isset($settings['contact_email']))
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400 font-medium">E-mail</dt>
                                    <dd class="text-gray-900 dark:text-white text-right break-all">
                                        {{ $settings['contact_email'] }}
                                    </dd>
                                </div>
                            @endif

                            @php
                                $knownKeys = ['type', 'meeting_day', 'meeting_time', 'meeting_place', 'whatsapp_group', 'contact_whatsapp', 'contact_email'];
                            @endphp
                            @foreach($settings as $key => $value)
                                @if(!in_array($key, $knownKeys ?? [], true) && filled($value))
                                    <div class="flex items-start justify-between gap-3">
                                        <dt class="text-gray-500 dark:text-gray-400 font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </dt>
                                        <dd class="text-gray-900 dark:text-white text-right">
                                            @if(is_array($value))
                                                {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                @endif

                <!-- Quick Member Add -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-icon name="user-plus" class="w-5 h-5 text-blue-500" />
                            Novo Membro
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.ministries.members.add', $ministry) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="user_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Selecionar Usuário</label>
                                <select name="user_id" id="user_id" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">Escolha um usuário...</option>
                                    @foreach ($users as $user)
                                        @if (!$ministry->hasMember($user))
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="role" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Função</label>
                                <select name="role" id="role" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="member">Membro regular</option>
                                    <option value="coordinator">Coordenador</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition-all focus:ring-4 focus:ring-blue-300">
                                Adicionar ao Ministério
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Member Management -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pending Approvals -->
                @if ($ministry->pendingMembers->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-yellow-200 dark:border-yellow-700/50 overflow-hidden">
                        <div class="px-6 py-4 bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-700/50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-300 flex items-center">
                                <x-icon name="clock" class="w-5 h-5 mr-2" />
                                Solicitações Pendentes ({{ $ministry->pendingMembers->count() }})
                            </h3>
                        </div>
                        <div class="p-6 divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($ministry->pendingMembers as $member)
                                <div class="py-4 flex flex-col sm:flex-row items-center justify-between group first:pt-0 last:pb-0">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            @if ($member->photo)
                                                <img class="h-12 w-12 rounded-xl object-cover border-2 border-white dark:border-gray-700 shadow-sm" src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                                    {{ strtoupper(substr($member->first_name ?? $member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-yellow-400 border-2 border-white dark:border-gray-800 rounded-full animate-pulse"></div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $member->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">solicitado em {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : 'data desconhecida' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                                        <form action="{{ route('admin.ministries.members.approve', [$ministry, $member]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                                                Aprovar
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.ministries.members.remove', [$ministry, $member]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 text-xs font-bold rounded-lg transition-all">
                                                Recusar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Active Members Table -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/20">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Membros Ativos</h3>
                        <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full border border-blue-100 dark:border-blue-800/50">
                            {{ $ministry->active_members_count }} total
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Membro</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Função</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Desde</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($ministry->activeMembers as $member)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if ($member->photo)
                                                    <img class="h-10 w-10 rounded-lg object-cover mr-3 border border-gray-200 dark:border-gray-600 shadow-sm" src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm mr-3 border border-gray-200 dark:border-gray-600 shadow-sm">
                                                        {{ strtoupper(substr($member->first_name ?? $member->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $member->name }}</div>
                                                    <div class="text-[10px] text-gray-500">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $roleClasses = [
                                                    'leader' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border-purple-200 dark:border-purple-800/50',
                                                    'co_leader' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800/50',
                                                    'coordinator' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800/50',
                                                ];
                                                $currentRole = $member->pivot->role;
                                                $cls = $roleClasses[$currentRole] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-600';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tighter border {{ $cls }}">
                                                {{ ucfirst(str_replace('_', ' ', $currentRole)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if ($member->id !== $ministry->leader_id && $member->id !== $ministry->co_leader_id)
                                                <form action="{{ route('admin.ministries.members.remove', [$ministry, $member]) }}" method="POST" onsubmit="return confirm('Remover este membro do ministério?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30">
                                                        <x-icon name="user-remove" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-gray-400 italic font-bold uppercase tracking-widest">Protegido</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-icon name="user-group" class="w-12 h-12 text-gray-200 dark:text-gray-700 mb-3" />
                                                <p class="text-sm font-medium text-gray-500">Nenhum membro ativo cadastrado.</p>
                                            </div>
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

    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
@endsection

