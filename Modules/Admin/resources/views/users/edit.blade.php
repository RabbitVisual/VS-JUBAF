@extends('admin::components.layouts.master')

@section('title', 'Editar Membro')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Membros</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Membro</h1>
                <p class="text-gray-300 max-w-xl">Atualize as informações de <strong>{{ $user->name }}</strong>. Vínculos familiares pendentes podem ser aceitos pelo próprio membro no painel.</p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="eye" class="w-5 h-5" />
                    Ver perfil
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
                <button type="submit" form="user-edit-form" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="check" class="w-5 h-5 text-blue-600" />
                    Salvar
                </button>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex">
                <x-icon name="circle-exclamation" class="w-6 h-6 text-red-500 mr-3 shrink-0" />
                <div>
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Corrija os erros abaixo:</h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="user-edit-form" action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-8" data-address-form onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando alterações...' } }))">
        @csrf
        @method('PUT')

        <!-- Section 1: Identificação -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-3xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <x-icon name="user" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Identificação Pessoal</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dados básicos do membro.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <!-- Sobrenome -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sobrenome *</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <!-- CPF -->
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}" data-mask="cpf"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <!-- Data Nascimento -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Nascimento</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <!-- Gênero -->
                 <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gênero</label>
                    <select name="gender" id="gender" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">Selecione...</option>
                        <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>
                 <!-- Estado Civil -->
                 <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado Civil</label>
                    <select name="marital_status" id="marital_status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">Selecione...</option>
                        <option value="solteiro" {{ old('marital_status', $user->marital_status) == 'solteiro' ? 'selected' : '' }}>Solteiro(a)</option>
                        <option value="casado" {{ old('marital_status', $user->marital_status) == 'casado' ? 'selected' : '' }}>Casado(a)</option>
                        <option value="divorciado" {{ old('marital_status', $user->marital_status) == 'divorciado' ? 'selected' : '' }}>Divorciado(a)</option>
                        <option value="viuvo" {{ old('marital_status', $user->marital_status) == 'viuvo' ? 'selected' : '' }}>Viúvo(a)</option>
                         <option value="uniao_estavel" {{ old('marital_status', $user->marital_status) == 'uniao_estavel' ? 'selected' : '' }}>União Estável</option>
                    </select>
                </div>
                <!-- Foto -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto de Perfil</label>
                    <div class="flex items-center gap-4">
                        <div class="shrink-0">
                             @if ($user->photo)
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700"
                                    src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                    <x-icon name="user" class="w-8 h-8" />
                                </div>
                            @endif
                        </div>
                        <label class="cursor-pointer bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-gray-300 dark:border-gray-600 py-2 px-4 rounded-xl font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <span>Alterar foto</span>
                            <input type="file" name="photo" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Contato e Endereço -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <div class="flex items-start gap-4 mb-6">
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-purple-600 dark:text-purple-400">
                    <x-icon name="phone" class="w-6 h-6" /> <!-- Assuming phone or map-pin icon -->
                </div>
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Contato e Endereço</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Informações de localização e contato.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Celular -->
                 <div>
                    <label for="cellphone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Celular (WhatsApp)</label>
                    <input type="text" name="cellphone" id="cellphone" value="{{ old('cellphone', $user->cellphone) }}" data-mask="phone"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Telefone Fixo -->
                 <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone Fixo</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" data-mask="phone"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                <!-- CEP -->
                 <div>
                    <label for="zip_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                     <div class="relative">
                        <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $user->zip_code) }}" data-mask="cep"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500 pr-10">
                        <div id="zip_code-loading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                            <x-icon name="arrows-rotate" class="w-4 h-4 text-purple-500 animate-spin" />
                        </div>
                    </div>
                    <p id="zip_code-error" class="mt-1 text-xs text-red-500 hidden"></p>
                    <p id="zip_code-success" class="mt-1 text-xs text-green-500 hidden"></p>
                </div>
                 <!-- Endereço -->
                 <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Número -->
                 <div>
                    <label for="address_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número</label>
                    <input type="text" name="address_number" id="address_number" value="{{ old('address_number', $user->address_number) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Complemento -->
                 <div>
                    <label for="address_complement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Complemento</label>
                    <input type="text" name="address_complement" id="address_complement" value="{{ old('address_complement', $user->address_complement) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Bairro -->
                 <div>
                    <label for="neighborhood" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bairro</label>
                    <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                </div>
                 <!-- Cidade / UF -->
                 <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}" readonly
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 cursor-not-allowed focus:ring-purple-500 focus:border-purple-500">
                    </div>
                     <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $user->state) }}" maxlength="2" readonly
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 cursor-not-allowed focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>
        </div>

         <!-- Section 3: Vida Eclesiástica -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <div class="flex items-start gap-4 mb-6">
                 <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600 dark:text-indigo-400">
                    <x-icon name="church" class="w-6 h-6" />
                </div>
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Vida Eclesiástica</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dados sobre a membresia e ministério.</p>
                </div>
            </div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="igreja_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Igreja Pertencente</label>
                    <select name="igreja_id" id="igreja_id"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach ($igrejas as $igreja)
                            <option value="{{ $igreja->id }}" {{ old('igreja_id', $user->igreja_id) == $igreja->id ? 'selected' : '' }}>
                                {{ $igreja->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo na JUBAF *</label>
                    <select name="role" id="role" required
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->role?->name) == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Status -->
                 <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status da Conta</label>
                    <select name="is_active" id="is_active" required
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Ativo (Pode acessar o sistema)</option>
                         <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Inativo (Acesso bloqueado)</option>
                    </select>
                </div>
                 <!-- Data Membresia -->
                 <div>
                    <label for="membership_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Membresia</label>
                    <input type="date" name="membership_date" id="membership_date" value="{{ old('membership_date', $user->membership_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                 <!-- Batizado? -->
                 <div>
                    <label for="is_baptized" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">É Batizado?</label>
                    <select name="is_baptized" id="is_baptized"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1" {{ old('is_baptized', $user->is_baptized) == '1' ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ old('is_baptized', $user->is_baptized) == '0' ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                 <!-- Data Batismo -->
                  <div>
                    <label for="baptism_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Batismo</label>
                    <input type="date" name="baptism_date" id="baptism_date" value="{{ old('baptism_date', $user->baptism_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>
             </div>
        </div>

        <!-- Section 4: Profissional e Emergência -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <div class="flex items-start gap-4 mb-6">
                 <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 dark:text-amber-400">
                    <x-icon name="briefcase" class="w-6 h-6" />
                </div>
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Profissional e Emergência</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dados profissionais e contatos de emergência.</p>
                </div>
            </div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profissão -->
                <div>
                    <label for="profession" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profissão</label>
                    <input type="text" name="profession" id="profession" value="{{ old('profession', $user->profession) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                </div>
                 <!-- Escolaridade -->
                 <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Escolaridade</label>
                    <select name="education_level" id="education_level" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Selecione...</option>
                        <option value="fundamental_incompleto" {{ old('education_level', $user->education_level) == 'fundamental_incompleto' ? 'selected' : '' }}>Fundamental Incompleto</option>
                        <option value="fundamental_completo" {{ old('education_level', $user->education_level) == 'fundamental_completo' ? 'selected' : '' }}>Fundamental Completo</option>
                        <option value="medio_incompleto" {{ old('education_level', $user->education_level) == 'medio_incompleto' ? 'selected' : '' }}>Médio Incompleto</option>
                        <option value="medio_completo" {{ old('education_level', $user->education_level) == 'medio_completo' ? 'selected' : '' }}>Médio Completo</option>
                        <option value="superior_incompleto" {{ old('education_level', $user->education_level) == 'superior_incompleto' ? 'selected' : '' }}>Superior Incompleto</option>
                        <option value="superior_completo" {{ old('education_level', $user->education_level) == 'superior_completo' ? 'selected' : '' }}>Superior Completo</option>
                        <option value="pos_graduacao" {{ old('education_level', $user->education_level) == 'pos_graduacao' ? 'selected' : '' }}>Pós-graduação</option>
                    </select>
                </div>
                 <!-- Local de Trabalho -->
                 <div class="md:col-span-2">
                    <label for="workplace" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Local de Trabalho</label>
                    <input type="text" name="workplace" id="workplace" value="{{ old('workplace', $user->workplace) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="md:col-span-2 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Contato de Emergência</h3>
                </div>

                 <!-- Nome Contato -->
                 <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Contato</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                </div>
                 <!-- Telefone Contato -->
                 <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone do Contato</label>
                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" data-mask="phone"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                </div>
                 <!-- Parentesco -->
                 <div class="md:col-span-2">
                    <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parentesco</label>
                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $user->emergency_contact_relationship) }}" placeholder="Ex: Pai, Mãe, Cônjuge"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                </div>
             </div>
        </div>

        <!-- Section: Vínculos Familiares -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <x-icon name="people-group" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Vínculos Familiares</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Vincule apenas parentes que são membros informando o <strong>CPF</strong>. Para quem não é membro, use só o nome.</p>
                </div>
            </div>
            <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 flex items-start gap-2 mb-6">
                <x-icon name="information-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                <p class="text-xs text-amber-800 dark:text-amber-200">Para vincular um <strong>membro</strong>, digite o CPF e clique em Buscar. Só assim o vínculo será com a ficha do membro. Se não informar CPF, use "Nome (não membro)".</p>
            </div>
            <div class="space-y-4" x-data="familyRelationshipsComponent({{ json_encode($user->relationships->map(function($r) {
                $found = null;
                if ($r->relatedUser) {
                    $found = ['id' => $r->relatedUser->id, 'name' => $r->relatedUser->name, 'email' => $r->relatedUser->email ?? '', 'photo' => $r->relatedUser->photo ? \Illuminate\Support\Facades\Storage::url($r->relatedUser->photo) : null];
                }
                return ['relationship_type' => $r->relationship_type, 'related_user_id' => $r->related_user_id ?? '', 'related_name' => $r->related_name ?? '', 'cpf_query' => '', 'loading' => false, 'error' => '', 'found_user' => $found];
            })->values()->all()) }})">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="flex flex-wrap gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 relative">
                        <div class="w-full sm:w-36">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Parentesco</label>
                            <select :name="'relationships[' + index + '][relationship_type]'" x-model="row.relationship_type" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                                @foreach(\App\Models\UserRelationship::relationshipTypeLabels() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[140px]">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">CPF do parente (membro)</label>
                            <div class="flex gap-2">
                                <input type="text" placeholder="000.000.000-00" x-model="row.cpf_query"
                                       data-mask="cpf"
                                       class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                                <button type="button" @click="searchByCpf(row)" :disabled="row.loading"
                                        class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold disabled:opacity-50 flex items-center gap-1">
                                    <x-icon name="search" class="w-4 h-4" x-show="!row.loading" />
                                    <span x-show="row.loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                                    Buscar
                                </button>
                            </div>
                            <input type="hidden" :name="'relationships[' + index + '][related_user_id]'" x-model="row.related_user_id">
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400" x-show="row.error" x-text="row.error"></p>
                            <div x-show="row.found_user" x-cloak class="mt-2 flex items-center gap-3 p-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                                <template x-if="row.found_user">
                                    <div class="flex items-center gap-3">
                                        <img x-show="row.found_user.photo" :src="row.found_user.photo" alt="" class="w-10 h-10 rounded-full object-cover border-2 border-emerald-200">
                                        <div x-show="!row.found_user.photo" class="w-10 h-10 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-300 font-bold text-sm" x-text="(row.found_user.name || '').charAt(0)"></div>
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="row.found_user ? row.found_user.name : ''"></span>
                                            <span class="text-xs text-gray-500 block" x-text="row.found_user ? row.found_user.email : ''"></span>
                                        </div>
                                        <button type="button" @click="clearMember(row)" class="ml-auto p-1.5 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600" title="Limpar">
                                            <x-icon name="xmark" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex-1 min-w-[180px]" x-show="!row.related_user_id">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nome (não membro)</label>
                            <input type="text" :name="'relationships[' + index + '][related_name]'" x-model="row.related_name" placeholder="Ex: Maria Silva (se não for membro)"
                                   class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <button type="button" @click="removeRow(index)" class="p-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20" title="Remover">
                            <x-icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                </template>
                <button type="button" @click="addRow()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-medium hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    <x-icon name="plus" class="w-5 h-5" /> Adicionar vínculo
                </button>
            </div>
        </div>

        <!-- Section 5: Segurança -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
             <div class="flex items-start gap-4 mb-6">
                 <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl text-red-600 dark:text-red-400">
                    <x-icon name="lock" class="w-6 h-6" />
                </div>
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Segurança (Senha)</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Deixe em branco para manter a senha atual.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nova Senha</label>
                    <input type="password" name="password" id="password"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4">
             <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-linear-to-r from-blue-600 to-blue-700 text-white rounded-xl font-bold hover:from-blue-700 hover:to-blue-800 shadow-lg shadow-blue-500/30 transition-all transform hover:scale-[1.02]">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', function() {
    Alpine.data('familyRelationshipsComponent', function(initialRows) {
        const searchByCpfUrl = '{{ route('admin.api.users.search-by-cpf') }}';
        return {
            rows: Array.isArray(initialRows) && initialRows.length ? initialRows.map(function(r) {
                return {
                    relationship_type: r.relationship_type || 'pai',
                    related_user_id: r.related_user_id || '',
                    related_name: r.related_name || '',
                    cpf_query: r.cpf_query || '',
                    loading: false,
                    error: '',
                    found_user: r.found_user || null
                };
            }) : [],
            addRow() {
                this.rows.push({
                    relationship_type: 'pai',
                    related_user_id: '',
                    related_name: '',
                    cpf_query: '',
                    loading: false,
                    error: '',
                    found_user: null
                });
            },
            removeRow(index) {
                this.rows.splice(index, 1);
            },
            searchByCpf(row) {
                var cpf = (row.cpf_query || '').replace(/\D/g, '');
                if (cpf.length !== 11) {
                    row.error = 'Informe um CPF válido com 11 dígitos.';
                    row.found_user = null;
                    row.related_user_id = '';
                    return;
                }
                row.loading = true;
                row.error = '';
                fetch(searchByCpfUrl + '?cpf=' + encodeURIComponent(cpf))
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        row.loading = false;
                        if (d.data) {
                            row.found_user = d.data;
                            row.related_user_id = d.data.id;
                            row.related_name = '';
                        } else {
                            row.found_user = null;
                            row.related_user_id = '';
                            row.error = d.message || 'Nenhum membro encontrado com este CPF.';
                        }
                    })
                    .catch(function() {
                        row.loading = false;
                        row.error = 'Erro ao buscar. Tente novamente.';
                        row.found_user = null;
                        row.related_user_id = '';
                    });
            },
            clearMember(row) {
                row.found_user = null;
                row.related_user_id = '';
                row.cpf_query = '';
                row.error = '';
            }
        };
    });
});
</script>
@endpush
@endsection

