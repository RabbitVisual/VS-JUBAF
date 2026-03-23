@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Meu Perfil';
@endphp

@section('title', 'Meu Perfil')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Perfil</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">{{ $user->role->name ?? 'Administrador' }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Meu Perfil</h1>
                    <p class="text-gray-300 max-w-xl">Gerencie suas informações pessoais e de segurança. Mantenha seus dados atualizados para que a igreja possa entrar em contato.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="pencil" class="w-5 h-5 text-blue-600" />
                        Editar Perfil
                    </a>
                </div>
            </div>
        </div>

        <!-- Dica -->
        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex items-start gap-3">
            <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-blue-800 dark:text-blue-200">Mantenha seus dados atualizados para que a igreja possa entrar em contato. Use a autenticação em duas etapas (2FA) para maior segurança da conta.</p>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- Left Column: Profile Card -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700 sticky top-6 relative">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                    <!-- Banner Background -->
                    <div class="h-32 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 relative">
                        <div class="absolute inset-0 bg-white/10 dark:bg-black/10 backdrop-blur-sm pattern-dots"></div>
                    </div>

                    <!-- Profile Info -->
                    <div class="px-6 pb-6 relative">
                        <!-- Avatar -->
                        <div class="relative -mt-16 mb-4 flex justify-center">
                            @if ($user->photo)
                                <img class="h-32 w-32 rounded-3xl object-cover border-4 border-white dark:border-gray-800 shadow-2xl transition-transform hover:scale-105 duration-300"
                                    src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-3xl bg-linear-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 border-4 border-white dark:border-gray-800 shadow-2xl flex items-center justify-center text-4xl font-black text-gray-400 dark:text-gray-500">
                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Name & Role -->
                        <div class="text-center space-y-2 mb-6">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">
                                {{ $user->name }}
                            </h2>
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                    {{ $user->role->name ?? 'Administrador' }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Stats / Badges -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700 text-center">
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Status</p>
                                <div class="flex items-center justify-center gap-1 text-green-600 dark:text-green-400 font-bold text-sm">
                                    <x-icon name="check-circle" class="w-4 h-4" />
                                    <span>Ativo</span>
                                </div>
                            </div>
                            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700 text-center">
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Verificado</p>
                                <div class="flex items-center justify-center gap-1 text-blue-600 dark:text-blue-400 font-bold text-sm">
                                    <x-icon name="shield-check" class="w-4 h-4" />
                                    <span>Sim</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2FA -->
                        @if(config('auth.2fa.enabled', false))
                            <div class="mb-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-2">Autenticação em duas etapas</p>
                                @if($user->hasTwoFactorEnabled())
                                    <div class="flex items-center gap-2 text-green-600 dark:text-green-400 font-bold text-sm mb-2">
                                        <x-icon name="shield-check" class="w-4 h-4" />
                                        Ativado
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-sm mb-2">
                                        <x-icon name="shield-exclamation" class="w-4 h-4" />
                                        Desativado
                                    </div>
                                @endif
                                <a href="{{ route('admin.profile.2fa.show') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                    {{ $user->hasTwoFactorEnabled() ? 'Gerenciar 2FA' : 'Ativar 2FA' }}
                                </a>
                            </div>
                        @endif

                        <!-- Contact Actions -->
                        <div class="space-y-3">
                            <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 group transition-colors hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <x-icon name="envelope" class="w-5 h-5" />
                                </div>
                                <div class="ml-3 overflow-hidden">
                                    <p class="text-xs text-gray-400 font-medium">E-mail</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details Lists -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                <!-- General Information -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <x-icon name="user" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Informações Pessoais</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Dados cadastrais do usuário</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nome Completo</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">E-mail Principal</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Telefone</label>
                            <div class="flex items-center gap-2">
                                <x-icon name="phone" class="w-4 h-4 text-gray-400" />
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user->phone ?: 'Não informado' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Celular / WhatsApp</label>
                            <div class="flex items-center gap-2">
                                <x-icon name="device-mobile" class="w-4 h-4 text-gray-400" />
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user->cellphone ?: 'Não informado' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Família & Vínculos (acesso rápido para o próprio perfil em Usuários) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                    <div class="relative flex items-center justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <x-icon name="people-group" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Família & Vínculos</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Parentesco e vínculos familiares no cadastro de usuários</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-sm">
                            <x-icon name="pen-to-square" class="w-5 h-5" />
                            Gerenciar meus vínculos
                        </a>
                    </div>
                    @if($user->relationships->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            @foreach($user->relationships as $rel)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                                    @if($rel->related_user_id && $rel->relatedUser)
                                        <a href="{{ route('admin.users.show', $rel->relatedUser) }}" class="shrink-0">
                                            @if($rel->relatedUser->photo)
                                                <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600" src="{{ Storage::url($rel->relatedUser->photo) }}" alt="{{ $rel->relatedUser->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-700 dark:text-emerald-300 font-bold text-sm">{{ strtoupper(substr($rel->relatedUser->first_name ?? $rel->relatedUser->name, 0, 1)) }}</div>
                                            @endif
                                        </a>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.users.show', $rel->relatedUser) }}" class="font-bold text-gray-900 dark:text-white hover:text-emerald-600 block truncate">{{ $rel->relatedUser->name }}</a>
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $rel->relationship_type_label }}</span>
                                        </div>
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 shrink-0">
                                            <x-icon name="user" class="w-6 h-6" />
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-gray-900 dark:text-white block truncate">{{ $rel->related_name ?? '—' }}</span>
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $rel->relationship_type_label }} · Não membro</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400">Adicione ou edite parentes (cônjuge, filhos, etc.) no seu cadastro em Usuários. O vínculo com outro membro gera um convite para ele aceitar no painel do membro.</p>
                </div>

                <!-- Identification Information -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center">
                            <x-icon name="identification" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Identificação</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Documentos e dados civis</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">CPF</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->cpf ?: 'Não informado' }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Data de Nascimento</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ optional($user->date_of_birth)->format('d/m/Y') ?: 'Não informado' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Gênero</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                @switch($user->gender)
                                    @case('M') Masculino @break
                                    @case('F') Feminino @break
                                    @default Não informado
                                @endswitch
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Estado Civil</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $user->marital_status ?: 'não informado') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                            <x-icon name="location-marker" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Endereço</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Localização e residência</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        @if($user->address)
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Endereço Completo</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $user->address }}, {{ $user->address_number }}
                                @if($user->address_complement) - {{ $user->address_complement }} @endif
                            </p>
                            <p class="text-base text-gray-600 dark:text-gray-400 mt-1">
                                {{ $user->neighborhood }} - {{ $user->city }}/{{ $user->state }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">CEP: {{ $user->zip_code }}</p>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400 italic">Endereço não cadastrado</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center">
                            <x-icon name="phone-outgoing" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Contato de Emergência</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Para casos de urgência</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                         <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nome</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $user->emergency_contact_name ?: 'Não informado' }}
                            </p>
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Telefone</label>
                            <div class="flex items-center gap-2">
                                <x-icon name="phone" class="w-4 h-4 text-gray-400" />
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $user->emergency_contact_phone ?: 'Não informado' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <x-icon name="server" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dados do Sistema</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Informações de acesso e registro</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="flex items-center p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/30">
                            <div class="mr-4">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                    <x-icon name="calendar" class="w-5 h-5" />
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase">Membro Desde</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/30">
                            <div class="mr-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                                    <x-icon name="lock-closed" class="w-5 h-5" />
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase">Última Atualização</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($user->role && $user->role->slug === 'admin' && config('auth.2fa.enabled', false))
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <a href="{{ route('admin.profile.2fa.show') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                    <x-icon name="shield-check" class="w-4 h-4" />
                                    {{ $user->hasTwoFactorEnabled() ? 'Gerenciar autenticação em duas etapas (2FA)' : 'Ativar autenticação em duas etapas (2FA)' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

