@extends('memberpanel::components.layouts.master')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6 sm:space-y-8">
            <!-- Header alinhado ao dashboard -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                        <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Painel</a>
                        <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                        <span class="text-gray-900 dark:text-white font-medium">Meu Perfil</span>
                    </nav>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Meu Perfil</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm max-w-md">Sua jornada, conquistas e informações cadastrais em um só lugar.</p>
                </div>
                <a href="{{ route('memberpanel.profile.edit') }}"
                    data-tour="profile-edit"
                    class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-xl font-bold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-500/20 active:scale-[0.98] touch-manipulation group shrink-0">
                    <x-icon name="pen-to-square" class="w-5 h-5 group-hover:scale-110 transition-transform" />
                    Editar Cadastro
                </a>
            </div>

            <!-- Hero Card: Profile Overview -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <!-- Decorative Mesh Gradient Background -->
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-indigo-300 dark:bg-indigo-500 rounded-full blur-[80px]"></div>
                </div>

                <div class="relative px-4 sm:px-6 md:px-8 py-8 sm:py-10 flex flex-col lg:flex-row items-center gap-8 sm:gap-10">
                    <!-- Avatar Section with Radial Progress -->
                    <div class="relative group shrink-0" data-tour="profile-photo">
                        <div class="relative w-36 h-36 sm:w-44 sm:h-44 flex items-center justify-center">
                            <!-- Progress Ring -->
                            <svg class="absolute inset-0 w-full h-full -rotate-90 transform" viewBox="0 0 100 100">
                                <!-- Background Circle -->
                                <circle class="text-gray-200 dark:text-slate-800" stroke-width="6" stroke="currentColor" fill="transparent" r="44" cx="50" cy="50"/>
                                <!-- Progress Circle -->
                                <circle class="text-blue-500 transition-all duration-1000 ease-out"
                                    stroke-width="6"
                                    stroke-dasharray="{{ 2 * pi() * 44 }}"
                                    stroke-dashoffset="{{ (2 * pi() * 44) * (1 - $user->getProfileCompletionPercentage() / 100) }}"
                                    stroke-linecap="round"
                                    stroke="currentColor"
                                    fill="transparent" r="44" cx="50" cy="50"/>
                            </svg>

                            <!-- Avatar -->
                            <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full overflow-hidden border-4 border-white dark:border-slate-800 shadow-xl bg-gray-100 dark:bg-slate-800 relative z-10">
                                @if ($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl sm:text-5xl font-black text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-slate-900">
                                        {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Completion Badge -->
                            <div class="absolute -bottom-2 right-2 bg-blue-600 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border-2 border-white dark:border-slate-900 shadow-xl z-20">
                                {{ $user->getProfileCompletionPercentage() }}% Completo
                            </div>
                        </div>

                        <!-- Compact Gallery Overlay -->
                        <div class="mt-6 flex justify-center gap-2">
                            @php $photos = $user->profilePhotos()->get(); @endphp
                            @foreach($photos as $photo)
                                <form action="{{ route('memberpanel.profile.photo.active', $photo) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-10 h-10 rounded-full border-2 {{ $photo->is_active ? 'border-blue-500 scale-110' : 'border-gray-200 dark:border-slate-700 opacity-50 hover:opacity-100' }} transition-all overflow-hidden shadow-lg bg-gray-100 dark:bg-slate-800">
                                        <img src="{{ Storage::url($photo->path) }}" class="w-full h-full object-cover">
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <!-- Identity info -->
                    <div class="flex-1 text-center lg:text-left space-y-4 relative z-10" data-tour="profile-identity">
                        <div>
                            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 mb-2">
                                <h2 class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ $user->name }}</h2>
                                @if($user->is_active)
                                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-black tracking-widest uppercase px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-500/30 backdrop-blur-md">Ativo</span>
                                @endif
                            </div>
                            <p class="text-blue-600/80 dark:text-blue-200/60 font-medium text-lg tracking-wide">{{ $user->email }}</p>
                        </div>

                        <!-- Stats / Level Bar -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl">
                            <div class="bg-white/60 dark:bg-slate-800/50 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl p-4 flex items-center gap-4 shadow-sm" data-tour="profile-level">
                                <div class="p-3 bg-blue-100 dark:bg-blue-500/20 rounded-xl text-blue-600 dark:text-blue-400">
                                    <x-icon name="{{ $level['icon'] }}" class="w-8 h-8" />
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-white/40 font-black uppercase tracking-widest">Seu Nível</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-xl">{{ $level['name'] }}</p>
                                </div>
                            </div>
                            <div class="bg-white/60 dark:bg-slate-800/50 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl p-4 flex items-center gap-4 shadow-sm" data-tour="profile-points">
                                <div class="p-3 bg-purple-100 dark:bg-purple-500/20 rounded-xl text-purple-600 dark:text-purple-400">
                                    <x-icon name="sparkles" class="w-8 h-8" />
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-white/40 font-black uppercase tracking-widest">Experiência</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-xl">{{ $points }} <span class="text-xs text-gray-500 dark:text-white/50">pontos</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Level Progress Bar -->
                        <div class="max-w-xl mt-6 space-y-2">
                            <div class="flex justify-between items-end px-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black text-gray-500 dark:text-white/40 uppercase tracking-widest">Progresso para o próximo nível</span>
                                    @if(isset($pointsMax))
                                        <span class="text-[9px] font-bold text-blue-500 dark:text-blue-300/60 uppercase tracking-tighter">Meta: {{ $pointsMax }} pts</span>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-white/80">{{ round($progress) }}%</span>
                            </div>
                            <div class="h-2 w-full bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden p-0.5 border border-transparent dark:border-white/5 backdrop-blur-sm">
                                <div class="h-full bg-linear-to-r from-indigo-500 via-indigo-500 to-purple-500 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(99,102,241,0.5)]" style="width: {{ min(100, max(0, $progress)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guided Information Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details Cards -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Category: Personal & Contact -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl shrink-0">
                                    <x-icon name="user" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">Informações Pessoais</h3>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Seus dados oficiais e meios de contato.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                @php
                                    $maritalLabels = ['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'uniao_estavel' => 'União Estável'];
                                    $personalFields = [
                                        ['label' => 'CPF', 'value' => $user->cpf, 'icon' => 'id-card'],
                                        ['label' => 'Nascimento', 'value' => $user->date_of_birth?->format('d/m/Y'), 'icon' => 'calendar'],
                                        ['label' => 'Gênero', 'value' => $user->gender == 'M' ? 'Masculino' : ($user->gender == 'F' ? 'Feminino' : '—'), 'icon' => 'venus-mars'],
                                        ['label' => 'Estado Civil', 'value' => $maritalLabels[$user->marital_status] ?? ($user->marital_status ? ucfirst($user->marital_status) : null), 'icon' => 'heart'],
                                        ['label' => 'Celular', 'value' => $user->cellphone, 'icon' => 'mobile-screen'],
                                        ['label' => 'Telefone Fixo', 'value' => $user->phone, 'icon' => 'phone'],
                                    ];
                                @endphp

                                @foreach($personalFields as $field)
                                    <div class="flex items-start gap-4 group">
                                        <div class="mt-1 text-gray-400 dark:text-slate-600 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors">
                                            <x-icon name="{{ $field['icon'] }}" class="w-4 h-4" />
                                        </div>
                                        <div>
                                            <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-0.5">{{ $field['label'] }}</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $field['value'] ?? 'Não informado' }}</dd>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Category: Address & Location -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                    <x-icon name="map-location-dot" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">Localização</h3>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Onde você reside atualmente.</p>
                                </div>
                            </div>
                            <div class="group relative">
                                <x-icon name="circle-info" class="w-5 h-5 text-gray-300 dark:text-slate-600 cursor-help" />
                                <div class="hidden group-hover:block absolute right-0 top-full mt-2 w-64 p-3 bg-slate-900 text-white text-[11px] leading-relaxed rounded-2xl shadow-2xl z-20 border border-slate-700">
                                    <p><strong>Dica Antigravity:</strong> Manter seu endereço atualizado é crucial para comunicações oficiais e eventos presenciais da sua região.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                 <div class="flex-1 space-y-6">
                                    <div>
                                        <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Endereço Completo</dt>
                                        <dd class="text-base font-bold text-gray-900 dark:text-slate-100 leading-snug">
                                            @if($user->address)
                                                {{ $user->address }}, {{ $user->address_number }}<br>
                                                <span class="text-gray-500 dark:text-slate-400 text-sm font-medium">
                                                    {{ $user->neighborhood }} • {{ $user->city }} — {{ $user->state }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-slate-600 font-medium italic">Dados de endereço incompletos</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="flex gap-10">
                                        <div>
                                            <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-0.5">Complemento</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $user->address_complement ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-0.5">CEP</dt>
                                            <dd class="text-sm font-mono font-bold text-blue-600 dark:text-blue-400">{{ $user->zip_code ? \App\Services\CepService::formatarCep($user->zip_code) : '-' }}</dd>
                                        </div>
                                    </div>
                                 </div>
                                 <!-- Mini Map Placeholder / Graphic -->
                                 <div class="w-full md:w-32 h-32 bg-gray-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center border border-gray-200 dark:border-slate-700 text-gray-300 dark:text-slate-600">
                                    <x-icon name="map" class="w-10 h-10" />
                                 </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category: Spiritual & Journey -->
                    <div class="bg-linear-to-br from-indigo-50 to-white dark:from-slate-900 dark:to-slate-800 rounded-3xl border border-blue-50 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-6 border-b border-blue-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-500/20">
                                    <x-icon name="church" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">Jornada Espiritual</h3>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Seu histórico na congregação.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Membro Desde</dt>
                                <dd class="text-lg font-bold text-gray-900 dark:text-slate-100">{{ $user->membership_date?->format('d/m/Y') ?: 'N/A' }}</dd>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Tempo acumulado: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $user->time_congregating_months ?: 0 }} meses</span></p>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Status de Batismo</dt>
                                <dd class="text-lg font-bold {{ $user->is_baptized ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-slate-400' }}">
                                    {{ $user->is_baptized ? 'Batizado' : 'Visitante / Em Preparo' }}
                                </dd>
                                @if($user->baptism_date)
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Data: {{ $user->baptism_date->format('d/m/Y') }} em {{ $user->baptism_place ?: 'Local não informado' }}</p>
                                @endif
                            </div>
                            <!-- Guided Tip -->
                            <div class="col-span-full bg-blue-600/5 dark:bg-blue-900/10 rounded-2xl p-4 border border-blue-100 dark:border-blue-900/30">
                                <div class="flex items-start gap-4">
                                    <div class="p-2 bg-blue-600 text-white rounded-lg"><x-icon name="lightbulb" class="w-4 h-4" /></div>
                                    <div>
                                        <h4 class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-tighter">Dica Antigravity</h4>
                                        <p class="text-[11px] text-blue-800 dark:text-blue-300 leading-relaxed mt-0.5">
                                            Seu tempo congregando e status de batismo ajudam a liderança no cuidado pastoral e no acompanhamento da sua jornada. Mantenha essas datas sempre corretas!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Achievements & Career -->
                <div class="space-y-8">
                    <!-- Career Card -->
                    <div class="bg-slate-900 dark:bg-black rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group">
                         <!-- Background pattern -->
                        <div class="absolute -right-4 -bottom-4 text-white/5 group-hover:text-blue-500/10 transition-colors">
                            <x-icon name="briefcase" class="w-24 h-24" />
                        </div>

                        <div class="flex items-center gap-3 mb-6 relative">
                            <div class="p-2 bg-white/10 rounded-xl"><x-icon name="briefcase" class="w-5 h-5" /></div>
                            <h3 class="font-bold text-sm tracking-tight text-white">Setor Profissional</h3>
                        </div>

                        <div class="space-y-6 relative">
                            <div>
                                <dt class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-1">Profissão Atual</dt>
                                <dd class="text-base font-bold text-white">{{ $user->profession ?: 'Não informada' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-1">Escolaridade</dt>
                                            <dd class="text-sm font-semibold opacity-80 text-white">{{ $user->education_level ? (\Illuminate\Support\Str::replace('_', ' ', ucfirst($user->education_level))) : 'Não informada' }}</dd>
                            </div>
                            <div class="pt-4 border-t border-white/5 text-[10px] leading-relaxed text-white/40 italic">
                                 "Seus dados profissionais ajudam a congregação a conhecer seus talentos para ações sociais."
                            </div>
                        </div>
                    </div>

                    <!-- Família & Vínculos -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl shrink-0">
                                <x-icon name="people-group" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Família & Vínculos</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Parentes vinculados ao seu perfil. Membros exibem foto.</p>
                            </div>
                        </div>
                        @php $acceptedRels = $user->relationships->where('status', 'accepted'); $pendingCount = $pendingInvitesCount ?? 0; @endphp
                        @if($acceptedRels->isNotEmpty())
                            <ul class="space-y-3 mb-4">
                                @foreach($acceptedRels as $rel)
                                    <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700">
                                        @if($rel->related_user_id && $rel->relatedUser)
                                            @if($rel->relatedUser->photo)
                                                <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200 dark:border-slate-600 shrink-0" src="{{ Storage::url($rel->relatedUser->photo) }}" alt="{{ $rel->relatedUser->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm shrink-0">{{ strtoupper(substr($rel->relatedUser->first_name ?? $rel->relatedUser->name, 0, 1)) }}</div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <span class="font-medium text-gray-900 dark:text-white block">{{ $rel->relatedUser->name }}</span>
                                                <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase">{{ $rel->relationship_type_label }}</span>
                                            </div>
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center text-gray-500 shrink-0">
                                                <x-icon name="user" class="w-6 h-6" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <span class="font-medium text-gray-900 dark:text-white block">{{ $rel->related_name ?? '—' }}</span>
                                                <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase">{{ $rel->relationship_type_label }} · Não membro</span>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Nenhum vínculo familiar confirmado.</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('memberpanel.relationships.pending') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                <x-icon name="envelope-open" class="w-4 h-4" />
                                {{ $pendingCount > 0 ? 'Convites de parentesco (' . $pendingCount . ' pendente' . ($pendingCount > 1 ? 's' : '') . ')' : 'Ver vínculos e convites' }}
                            </a>
                            <a href="{{ route('memberpanel.relationships.create') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                <x-icon name="plus" class="w-4 h-4" />
                                Adicionar vínculo
                            </a>
                        </div>
                        <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-3 italic">Solicite vínculos com membros pelo CPF (eles receberão um convite para aceitar) ou adicione parentes que não são membros pelo nome.</p>
                    </div>

                    <!-- Emergency Widget -->
                    @if ($user->emergency_contact_name)
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-3xl p-6 border border-red-100 dark:border-red-900/20">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-red-700 dark:text-red-400">Contato de Emergência</h3>
                        </div>
                        <div class="space-y-1">
                            <p class="text-base font-black text-gray-900 dark:text-white">{{ $user->emergency_contact_name }}</p>
                            <p class="text-lg font-mono font-bold text-red-600 dark:text-red-400">{{ $user->emergency_contact_phone }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-2">{{ $user->emergency_contact_relationship }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

