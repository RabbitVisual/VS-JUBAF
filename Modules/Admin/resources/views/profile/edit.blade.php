@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Editar Perfil';
@endphp

@section('title', 'Editar Perfil')

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
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Perfil</h1>
                    <p class="text-gray-300 max-w-xl">Atualize suas informações pessoais, endereço e foto. A foto é exibida no painel e nas áreas em que você é identificado.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Dica -->
        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4 flex items-start gap-3">
            <x-icon name="information-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-amber-800 dark:text-amber-200">Recomendamos uma foto de perfil em boa qualidade (máx. 2MB). Seus dados são usados apenas no contexto da igreja e não são compartilhados com terceiros.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" data-address-form onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando perfil...' } }))">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-12 gap-8">
                <!-- Left Column: Personal Information -->
                <div class="col-span-12 lg:col-span-8 space-y-8">
                    <!-- Basic Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <x-icon name="user" class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informações Pessoais</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nome, e-mail e telefones</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Helper for cleaner inputs -->
                            @foreach([
                                ['id' => 'first_name', 'label' => 'Nome', 'required' => true],
                                ['id' => 'last_name', 'label' => 'Sobrenome', 'required' => true],
                                ['id' => 'email', 'type' => 'email', 'label' => 'E-mail', 'required' => true],
                                ['id' => 'phone', 'label' => 'Telefone Fixo', 'mask' => 'phone'],
                                ['id' => 'cellphone', 'label' => 'Celular', 'mask' => 'phone'],
                            ] as $field)
                                @php
                                    $val = old($field['id'], $user->{$field['id']});
                                    if(in_array($field['id'], ['phone', 'cellphone'])) $val = \App\Services\CepService::formatarTelefone($val);
                                @endphp
                                <div class="@if($field['id'] === 'email') md:col-span-2 @endif">
                                    <label for="{{ $field['id'] }}" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                                        {{ $field['label'] }} @if(($field['required'] ?? false)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <input type="{{ $field['type'] ?? 'text' }}"
                                           name="{{ $field['id'] }}"
                                           id="{{ $field['id'] }}"
                                           value="{{ $val }}"
                                           @if(($field['required'] ?? false)) required @endif
                                           @if(($field['mask'] ?? false)) data-mask="{{ $field['mask'] }}" @endif
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none font-medium">
                                    @error($field['id'])
                                        <p class="mt-2 text-sm text-red-500 font-medium flex items-center gap-1">
                                            <x-icon name="exclamation-circle" class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- Identificação -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center">
                                <x-icon name="identification" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Identificação</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="cpf" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">CPF</label>
                                <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}" data-mask="cpf"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none font-medium text-lg tracking-widest">
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Nascimento</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none font-medium">
                            </div>

                            <div>
                                <label for="gender" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Gênero</label>
                                <select name="gender" id="gender" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none font-medium">
                                    <option value="">Selecione...</option>
                                    <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                                </select>
                            </div>

                            <div>
                                <label for="marital_status" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Estado Civil</label>
                                <select name="marital_status" id="marital_status" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none font-medium">
                                    <option value="">Selecione...</option>
                                    <option value="solteiro" {{ old('marital_status', $user->marital_status) == 'solteiro' ? 'selected' : '' }}>Solteiro(a)</option>
                                    <option value="casado" {{ old('marital_status', $user->marital_status) == 'casado' ? 'selected' : '' }}>Casado(a)</option>
                                    <option value="divorciado" {{ old('marital_status', $user->marital_status) == 'divorciado' ? 'selected' : '' }}>Divorciado(a)</option>
                                    <option value="viuvo" {{ old('marital_status', $user->marital_status) == 'viuvo' ? 'selected' : '' }}>Viúvo(a)</option>
                                    <option value="uniao_estavel" {{ old('marital_status', $user->marital_status) == 'uniao_estavel' ? 'selected' : '' }}>União Estável</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                <x-icon name="location-marker" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Endereço</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-1">
                                <label for="zip_code" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">CEP</label>
                                <div class="relative group">
                                    <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $user->zip_code) }}" data-mask="cep"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-medium">
                                    <div id="zip_code-loading" class="hidden absolute right-4 top-3.5">
                                        <x-icon name="arrows-rotate" class="animate-spin h-5 w-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                                <p id="zip_code-error" class="hidden mt-1 text-[10px] text-red-500 font-bold uppercase"></p>
                                <p id="zip_code-success" class="hidden mt-1 text-[10px] text-emerald-500 font-bold uppercase"></p>
                            </div>
                            <div class="md:col-span-3">
                                <label for="address" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Logradouro</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-medium">
                            </div>

                            <div class="md:col-span-1">
                                <label for="address_number" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Número</label>
                                <input type="text" name="address_number" id="address_number" value="{{ old('address_number', $user->address_number) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-medium">
                            </div>
                            <div class="md:col-span-3">
                                <label for="address_complement" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Complemento</label>
                                <input type="text" name="address_complement" id="address_complement" value="{{ old('address_complement', $user->address_complement) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-medium">
                            </div>

                            <div class="md:col-span-2">
                                <label for="neighborhood" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Bairro</label>
                                <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-medium">
                            </div>
                            <div class="md:col-span-1">
                                <label for="city" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Cidade</label>
                                <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}" readonly
                                       class="w-full px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold cursor-not-allowed focus:ring-0">
                            </div>
                            <div class="md:col-span-1">
                                <label for="state" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Estado</label>
                                <input type="text" name="state" id="state" value="{{ old('state', $user->state) }}" maxlength="2" readonly
                                       class="w-full px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold cursor-not-allowed focus:ring-0 uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- Contato de Emergência -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center">
                                <x-icon name="phone-outgoing" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Contato de Emergência</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="emergency_contact_name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Nome do Contato</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all outline-none font-medium">
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Telefone</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" data-mask="phone"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all outline-none font-medium">
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                         <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <x-icon name="lock-closed" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Segurança</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                                    Nova Senha
                                </label>
                                <input type="password" name="password" id="password" minlength="8"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none font-medium text-lg tracking-widest"
                                       placeholder="••••••••">
                                <p class="mt-2 text-xs text-gray-400">Mínimo de 8 caracteres. Deixe em branco se não quiser alterar.</p>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                                    Confirmar Senha
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none font-medium text-lg tracking-widest"
                                       placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Avatar Upload -->
                <div class="col-span-12 lg:col-span-4 space-y-8">
                     <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 text-center">Foto de Perfil</h2>

                        <div class="flex flex-col items-center">
                            <div class="relative group cursor-pointer mb-6" onclick="document.getElementById('photo').click()">
                                @if ($user->photo)
                                    <img src="{{ Storage::url($user->photo) }}" alt="Current Photo" class="w-48 h-48 rounded-full object-cover border-8 border-gray-50 dark:border-gray-900 shadow-xl group-hover:opacity-75 transition-opacity">
                                @else
                                    <div class="w-48 h-48 rounded-full bg-linear-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 border-8 border-gray-50 dark:border-gray-900 shadow-xl flex items-center justify-center text-5xl font-black text-gray-400 group-hover:opacity-75 transition-opacity">
                                        {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="bg-black/50 rounded-full p-3 text-white backdrop-blur-sm">
                                        <x-icon name="camera" class="w-8 h-8" />
                                    </div>
                                </div>
                            </div>

                            <input type="file" name="photo" id="photo" accept="image/*" class="hidden">

                            <button type="button" onclick="document.getElementById('photo').click()"
                                    class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors uppercase tracking-wide">
                                Alterar Foto
                            </button>
                            <p class="text-xs text-gray-400 mt-2 text-center">Recomendado: 500x500px<br>JPG, PNG ou GIF (Max 2MB)</p>
                            @error('photo')
                                <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                             <button type="submit"
                                class="w-full py-4 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3">
                                <x-icon name="save" class="w-6 h-6" />
                                Salvar Alterações
                            </button>
                            <a href="{{ route('admin.profile.show') }}"
                               class="block text-center mt-4 text-sm font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

