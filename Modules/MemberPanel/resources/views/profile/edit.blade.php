@extends('memberpanel::components.layouts.master')

@section('title', 'Editar Perfil')
@section('page-title', 'Editar Perfil')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-28 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6">
            <!-- Header alinhado ao dashboard -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4 sm:mb-6">
                <div class="min-w-0">
                    <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                        <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Painel</a>
                        <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                        <a href="{{ route('memberpanel.profile.show') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Meu Perfil</a>
                        <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                        <span class="text-gray-900 dark:text-white font-medium truncate">Editar Cadastro</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar Perfil</h1>
                        <div class="flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1.5 rounded-full border border-indigo-100 dark:border-indigo-800">
                            <span class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">Completude: {{ $user->getProfileCompletionPercentage() }}%</span>
                            <div class="w-16 sm:w-20 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-600 dark:bg-indigo-500 rounded-full transition-all duration-1000" style="width: {{ min(100, $user->getProfileCompletionPercentage()) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('memberpanel.profile.show') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shrink-0 touch-manipulation active:scale-[0.98] group">
                    <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                    Voltar ao Perfil
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-6 sm:mb-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <x-icon name="circle-exclamation" class="w-5 h-5 text-red-500 dark:text-red-400 mt-0.5" />
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-red-600 dark:text-red-300 mb-1">Atenção</p>
                            <p class="text-xs text-red-700 dark:text-red-200 mb-2">Alguns campos precisam ser corrigidos antes de salvar seu perfil.</p>
                            <ul class="text-xs text-red-700 dark:text-red-200 space-y-0.5 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8 items-start">

                <!-- Sticky Sidebar Navigation (mobile: scroll horizontal opcional) -->
                <aside class="w-full lg:w-64 lg:min-w-[16rem] lg:sticky lg:top-24 space-y-4 self-start shrink-0">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-2 shadow-sm overflow-hidden" data-tour="profile-edit-nav">
                        @php
                            $navItems = [
                                ['id' => 'personal', 'label' => 'Dados Básicos', 'icon' => 'user'],
                                ['id' => 'contact', 'label' => 'Contato', 'icon' => 'phone'],
                                ['id' => 'address', 'label' => 'Endereço', 'icon' => 'map'],
                                ['id' => 'spiritual', 'label' => 'Jornada Espiritual', 'icon' => 'church'],
                                ['id' => 'professional', 'label' => 'Profissional', 'icon' => 'briefcase'],
                                ['id' => 'emergency', 'label' => 'Emergência', 'icon' => 'circle-exclamation'],
                                ['id' => 'preferences', 'label' => 'Preferências', 'icon' => 'robot'],
                                ['id' => 'security', 'label' => 'Segurança & Fotos', 'icon' => 'shield-check'],
                            ];
                        @endphp

                        @foreach($navItems as $item)
                            <button type="button"
                                onclick="scrollToSection('{{ $item['id'] }}')"
                                data-nav="{{ $item['id'] }}"
                                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all border-l-4 border-transparent group text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800 touch-manipulation active:scale-[0.99] text-left">
                                <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 transition-colors group-hover:text-blue-500 dark:group-hover:text-blue-400" />
                                {{ $item['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Guided Help Card (Contextual) -->
                    <div id="contextual-tip" class="hidden lg:block bg-blue-600 dark:bg-blue-700 rounded-3xl p-6 text-white shadow-xl shadow-blue-500/20 transition-all duration-500 transform translate-y-2 opacity-0">
                        <div class="flex items-center gap-2 mb-3">
                            <x-icon name="lightbulb" class="w-5 h-5 text-blue-200" />
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-blue-200">Dica Antigravity</h4>
                        </div>
                        <p id="tip-text" class="text-xs font-medium leading-relaxed opacity-90 italic">
                            Selecione uma seção para ver dicas úteis de preenchimento.
                        </p>
                    </div>
                </aside>

                <!-- Main Form Content -->
                <div class="flex-1 w-full lg:max-w-4xl min-w-0">
                    <form id="profile-form" action="{{ route('memberpanel.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Section: Personal Info -->
                        <section id="personal" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md" data-tour="profile-edit-personal">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-500/10"><x-icon name="user" class="w-5 h-5" /></div>
                                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Dados Básicos</h2>
                                </div>
                                <span class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Campos Obrigatórios *</span>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                    <div class="space-y-1">
                                        <label for="first_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Nome *</label>
                                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                        @error('first_name') <p class="text-[10px] text-red-500 font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-1">
                                        <label for="last_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Sobrenome *</label>
                                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                        @error('last_name') <p class="text-[10px] text-red-500 font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-1">
                                        <label for="cpf" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">CPF</label>
                                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}" data-mask="cpf" placeholder="000.000.000-00"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-mono font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="date_of_birth" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Data de Nascimento</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="gender" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Gênero</label>
                                        <select name="gender" id="gender" class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                            <option value="">Selecione...</option>
                                            <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="marital_status" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Estado Civil</label>
                                        <select name="marital_status" id="marital_status" class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
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
                        </section>

                        <!-- Section: Contact -->
                        <section id="contact" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md" data-tour="profile-edit-contact">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                                <div class="p-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-500/10"><x-icon name="phone" class="w-5 h-5" /></div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white">Meios de Contato</h2>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <div class="space-y-1">
                                    <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">E-mail *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                    @error('email') <p class="text-[10px] text-red-500 font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label for="cellphone" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Celular</label>
                                    <input type="text" name="cellphone" id="cellphone" value="{{ old('cellphone', $user->cellphone) }}" data-mask="phone" placeholder="(00) 0 0000-0000"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Telefone Fixo</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" data-mask="phone" placeholder="(00) 0000-0000"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                </div>
                            </div>
                        </section>

                        <!-- Section: Address -->
                        <section id="address" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md" data-tour="profile-edit-address">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-500/10"><x-icon name="map" class="w-5 h-5" /></div>
                                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Localização Rural/Urbana</h2>
                                </div>
                                <div id="cep-status" class="hidden animate-pulse flex items-center gap-2 text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full">
                                    <x-icon name="spinner" class="animate-spin h-3 w-3 text-blue-600 dark:text-blue-400" />
                                    Buscando Endereço...
                                </div>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8">
                                @include('memberpanel::components.address-fields', ['prefix' => '', 'required' => false, 'showLabels' => true, 'model' => $user])
                                <p class="mt-6 text-[11px] text-gray-400 dark:text-slate-500 font-medium italic border-t border-gray-50 dark:border-slate-800 pt-4">
                                    * O preenchimento total do endereço contribui para a completude de 100% do seu perfil.
                                </p>
                            </div>
                        </section>

                        <!-- Section: Spiritual -->
                        <section id="spiritual" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md" data-tour="profile-edit-spiritual">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                                <div class="p-2 bg-purple-600 text-white rounded-xl shadow-lg shadow-purple-500/10"><x-icon name="church" class="w-5 h-5" /></div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white">Jornada Espiritual</h2>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8 space-y-6 sm:space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                    <div class="space-y-1">
                                        <label for="membership_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Data de Entrada</label>
                                        <input type="date" name="membership_date" id="membership_date" value="{{ old('membership_date', $user->membership_date?->format('Y-m-d')) }}"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="time_congregating_months" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Tempo de Congregação (Meses)</label>
                                        <input type="number" name="time_congregating_months" id="time_congregating_months" value="{{ old('time_congregating_months', $user->time_congregating_months) }}" min="0" placeholder="Ex: 24"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="baptism_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Data de Batismo</label>
                                        <input type="date" name="baptism_date" id="baptism_date" value="{{ old('baptism_date', $user->baptism_date?->format('Y-m-d')) }}"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="baptism_place" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Local de Batismo</label>
                                        <input type="text" name="baptism_place" id="baptism_place" value="{{ old('baptism_place', $user->baptism_place) }}" placeholder="Nome da Igreja / Local"
                                            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                    </div>
                                </div>

                                <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/10 rounded-2xl border border-purple-100 dark:border-purple-900/20">
                                    <label class="flex items-center cursor-pointer group">
                                        <div class="relative">
                                            <input type="checkbox" name="is_baptized" id="is_baptized" value="1" {{ old('is_baptized', $user->is_baptized) ? 'checked' : '' }} class="sr-only peer">
                                            <div class="block bg-gray-200 dark:bg-slate-700 w-12 h-7 rounded-full transition-colors peer-checked:bg-purple-600 dark:peer-checked:bg-purple-500"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-transform transform peer-checked:translate-x-5"></div>
                                        </div>
                                        <span class="ml-4 text-sm font-black text-purple-900 dark:text-purple-300 uppercase tracking-tighter">Confirmar Status de Batismo</span>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <!-- Section: Professional -->
                        <section id="professional" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                                <div class="p-2 bg-slate-800 dark:bg-slate-700 text-white rounded-xl shadow-lg"><x-icon name="briefcase" class="w-5 h-5" /></div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white">Setor Profissional</h2>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <div class="space-y-1">
                                    <label for="profession" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">O que você faz?</label>
                                    <input type="text" name="profession" id="profession" value="{{ old('profession', $user->profession) }}" placeholder="Ex: Engenheiro Civil"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="education_level" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Escolaridade</label>
                                    <select name="education_level" id="education_level" class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                        <option value="">Selecione...</option>
                                        <option value="fundamental" {{ old('education_level', $user->education_level) == 'fundamental' ? 'selected' : '' }}>Fundamental</option>
                                        <option value="medio" {{ old('education_level', $user->education_level) == 'medio' ? 'selected' : '' }}>Médio</option>
                                        <option value="superior" {{ old('education_level', $user->education_level) == 'superior' ? 'selected' : '' }}>Superior</option>
                                        <option value="pos_graduacao" {{ old('education_level', $user->education_level) == 'pos_graduacao' ? 'selected' : '' }}>Pós-Graduação</option>
                                    </select>
                                </div>
                                <div class="space-y-1 lg:col-span-full">
                                    <label for="workplace" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Local de Trabalho</label>
                                    <input type="text" name="workplace" id="workplace" value="{{ old('workplace', $user->workplace) }}" placeholder="Nome da Empresa / Instituição"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                </div>
                            </div>
                        </section>

                        <!-- Section: Emergency -->
                        <section id="emergency" class="scroll-mt-24 sm:scroll-mt-28 bg-red-50/30 dark:bg-red-900/5 rounded-2xl sm:rounded-3xl border border-red-100 dark:border-red-900/20 shadow-sm overflow-hidden transition-all hover:shadow-md">
                            <div class="p-4 sm:p-6 border-b border-red-100 dark:border-red-900/20 bg-red-50/50 dark:bg-red-900/10 flex items-center gap-3">
                                <div class="p-2 bg-red-600 text-white rounded-xl shadow-lg shadow-red-500/10"><x-icon name="circle-exclamation" class="w-5 h-5" /></div>
                                <h2 class="text-lg font-black text-red-900 dark:text-red-400">Plano de Emergência</h2>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <div class="space-y-1">
                                    <label for="emergency_contact_name" class="block text-[10px] font-black uppercase tracking-widest text-red-700/60 dark:text-red-400/60">Contato de Emergência (Nome)</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-red-200 dark:border-red-900/50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="emergency_contact_phone" class="block text-[10px] font-black uppercase tracking-widest text-red-700/60 dark:text-red-400/60">Telefone do Contato de Emergência</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" data-mask="phone"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-red-200 dark:border-red-900/50 dark:bg-slate-800 text-red-600 dark:text-red-400 font-bold focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="emergency_contact_relationship" class="block text-[10px] font-black uppercase tracking-widest text-red-700/60 dark:text-red-400/60">Vínculo</label>
                                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $user->emergency_contact_relationship) }}" placeholder="Ex: Mãe, Irmão, Amigo"
                                        class="modern-input w-full px-4 py-3 rounded-xl border border-red-200 dark:border-red-900/50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all">
                                </div>
                            </div>
                        </section>

                        <!-- Section: Preferências do painel -->
                        <section id="preferences" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                                <div class="p-2 bg-amber-500/20 dark:bg-amber-500/30 text-amber-600 dark:text-amber-400 rounded-xl"><x-icon name="robot" class="w-5 h-5" /></div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white">Preferências do painel</h2>
                            </div>
                            <div class="p-4 sm:p-6 md:p-8">
                                <div class="p-4 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/20 space-y-2">
                                    <label class="flex items-center cursor-pointer group">
                                        <div class="relative">
                                            <input type="checkbox" name="cbav_bot_enabled" id="cbav_bot_enabled" value="1" {{ old('cbav_bot_enabled', $user->cbav_bot_enabled ?? true) ? 'checked' : '' }} class="sr-only peer">
                                            <div class="block bg-gray-200 dark:bg-slate-700 w-12 h-7 rounded-full transition-colors peer-checked:bg-amber-500 dark:peer-checked:bg-amber-500"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-5 h-5 rounded-full shadow transition-transform transform peer-checked:translate-x-5"></div>
                                        </div>
                                        <span class="ml-4 text-sm font-bold text-gray-900 dark:text-white">Receber dicas do bot Elias</span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 pl-16">Quando ativado, o Elias aparece no painel com dicas e a recomendação de leitura do dia. Você pode desativar ou ativar a qualquer momento.</p>
                                </div>
                            </div>
                        </section>

                        <!-- Section: Security & Photos -->
                        <section id="security" class="scroll-mt-24 sm:scroll-mt-28 bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden pb-8 sm:pb-10 transition-all hover:shadow-md" data-tour="profile-edit-security">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-slate-900 dark:bg-blue-600 text-white rounded-xl"><x-icon name="shield-check" class="w-5 h-5" /></div>
                                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Segurança & Fotos</h2>
                                </div>
                                <div id="privacy-level" class="flex items-center gap-2">
                                    <span class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest leading-none">Proteção:</span>
                                    <div class="flex gap-1">
                                        <div class="lv-bar w-4 h-1.5 rounded-full bg-gray-200 dark:bg-slate-700 transition-colors"></div>
                                        <div class="lv-bar w-4 h-1.5 rounded-full bg-gray-200 dark:bg-slate-700 transition-colors"></div>
                                        <div class="lv-bar w-4 h-1.5 rounded-full bg-gray-200 dark:bg-slate-700 transition-colors"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 xl:grid-cols-5 gap-6 sm:gap-10">
                                <!-- Password Area -->
                                <div class="xl:col-span-2 space-y-6">
                                    <div class="space-y-1">
                                        <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Nova Senha</label>
                                        <div class="relative group">
                                            <input type="password" name="password" id="password" minlength="8"
                                                onkeyup="updateSecurityUI(this.value); checkPasswordMatch()"
                                                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <span id="strength-text" class="text-[9px] font-black uppercase tracking-widest"></span>
                                            </div>
                                        </div>
                                        <div class="h-1 w-full bg-gray-100 dark:bg-slate-700 rounded-full mt-2 overflow-hidden">
                                            <div id="strength-bar-modern" class="h-full w-0 transition-all duration-500"></div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-1 italic leading-tight">Sugestão: 8+ caracteres, misture letras e símbolos.</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Confirmar Senha</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation" id="password_confirmation" onkeyup="checkPasswordMatch()"
                                                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                                            <div id="match-status-icon" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                                <x-icon name="circle-check" class="w-4 h-4 text-emerald-500" />
                                            </div>
                                        </div>
                                        <p id="match-status-text" class="text-[10px] font-bold hidden mt-1 uppercase tracking-tighter"></p>
                                    </div>
                                </div>

                                <!-- Photo Selector Area (Instagram Style) -->
                                <div class="xl:col-span-3 flex flex-col items-center justify-center p-6 bg-gray-50/50 dark:bg-slate-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-slate-700">
                                    <div class="relative mb-6">
                                        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white dark:border-slate-800 shadow-2xl relative">
                                            @if ($user->photo)
                                                <img id="main-preview" src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover">
                                            @else
                                                <div id="main-placeholder" class="w-full h-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-3xl font-black text-blue-600 dark:text-blue-400 font-serif">
                                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full p-1.5 shadow-xl">
                                            <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800 rounded-full shadow-xl shadow-gray-200/20 dark:shadow-none border border-gray-100 dark:border-slate-700">
                                        @php $photos = $user->profilePhotos()->get(); @endphp
                                        @foreach($photos as $photo)
                                            <div class="relative group">
                                                <button type="button"
                                                    onclick="submitPhotoAction('{{ route('memberpanel.profile.photo.active', $photo) }}', 'POST')"
                                                    class="w-12 h-12 rounded-full overflow-hidden border-2 {{ $photo->is_active ? 'border-blue-500 ring-4 ring-blue-500/10' : 'border-gray-100 dark:border-slate-700 grayscale hover:grayscale-0' }} transition-all transform active:scale-95 shadow-sm">
                                                    <img src="{{ Storage::url($photo->path) }}" class="w-full h-full object-cover">
                                                </button>

                                                <button type="button"
                                                    onclick="if(confirm('Deseja realmente excluir esta memória?')) submitPhotoAction('{{ route('memberpanel.profile.photo.destroy', $photo) }}', 'DELETE')"
                                                    class="absolute -top-1 -right-1 opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100 bg-red-500 text-white rounded-full p-1 shadow-2xl hover:bg-red-600 border border-white dark:border-slate-900">
                                                    <x-icon name="xmark" class="w-3 h-3" />
                                                </button>
                                            </div>
                                        @endforeach

                                        @if($photos->count() < 3)
                                            <label for="photos" class="w-12 h-12 rounded-full border-2 border-dashed border-gray-300 dark:border-slate-600 flex items-center justify-center hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-500 transition-all cursor-pointer group/add">
                                                <x-icon name="plus" class="w-6 h-6 text-gray-400 group-hover/add:text-blue-500 transition-colors" />
                                            </label>
                                            <input type="file" name="photos[]" id="photos" accept="image/*" multiple class="hidden" onchange="previewImagesModern(this)">
                                        @endif
                                    </div>
                                    @error('photos.*')
                                        <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-widest">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p id="upload-status" class="mt-4 text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">
                                        @if($photos->count() < 3)
                                            Adicione até {{ 3 - $photos->count() }} fotos para alternar seu visual
                                        @else
                                            Limite de fotos atingido
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar (responsivo, safe area) -->
    <div class="fixed bottom-4 sm:bottom-8 left-0 right-0 z-50 px-4 pointer-events-none safe-area-inset-bottom">
        <div class="max-w-7xl mx-auto flex justify-end pointer-events-auto">
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl p-2 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-2xl flex items-center gap-3 sm:gap-4">
                <button type="button" onclick="window.location='{{ route('memberpanel.profile.show') }}'"
                    class="px-4 sm:px-6 py-2.5 text-sm font-black text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white uppercase tracking-widest transition-colors touch-manipulation active:scale-[0.98]">
                    Descartar
                </button>
                <button type="submit" form="profile-form"
                    class="px-6 sm:px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-sm uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all active:scale-[0.98] touch-manipulation"
                    data-tour="profile-edit-submit">
                    Salvar Mudanças
                </button>
            </div>
        </div>
    </div>

    <style>
        .modern-input:focus {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            transform: translateY(-2px);
        }
        .dot {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    @push('scripts')
    <script>
        const tips = {
            'personal': 'Seus dados básicos são usados para documentação oficial. Mantenha seu nome completo sempre atualizado.',
            'contact': 'Garantir que seu e-mail e celular estejam corretos é vital para não perder comunicados importantes da igreja.',
            'address': 'Informe seu CEP e o restante do endereço será preenchido automaticamente.',
            'spiritual': 'Essas datas celebram sua história conosco e ajudam a planejar homenagens por tempo de congregação.',
            'professional': 'Sua profissão ajuda a congregação a direcionar voluntários para as áreas certas em missões.',
            'emergency': 'Em caso de imprevistos em eventos, precisamos saber quem chamar por você.',
            'preferences': 'Ative ou desative as dicas do bot Elias no painel conforme sua preferência.',
            'security': 'Use uma senha forte e fotos nítidas para maior segurança da sua conta.'
        };

        function scrollToSection(id) {
            const el = document.getElementById(id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                updateNavUI(id);
            }
        }

        function updateNavUI(id) {
            document.querySelectorAll('.nav-item').forEach(btn => {
                const navId = btn.getAttribute('data-nav');
                const icon = btn.querySelector('svg');

                if (navId === id) {
                    btn.classList.add('bg-indigo-50', 'dark:bg-indigo-900/20', 'text-indigo-600', 'dark:text-indigo-400', 'border-indigo-600', 'dark:border-indigo-500');
                    btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-slate-400', 'hover:bg-gray-50', 'dark:hover:bg-slate-800');
                    if(icon) {
                        icon.classList.remove('text-gray-400');
                        icon.classList.add('text-indigo-600', 'dark:text-indigo-400');
                    }
                } else {
                    btn.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/20', 'text-indigo-600', 'dark:text-indigo-400', 'border-indigo-600', 'dark:border-indigo-500');
                    btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-slate-400', 'hover:bg-gray-50', 'dark:hover:bg-slate-800');
                    if(icon) {
                        icon.classList.add('text-gray-400');
                        icon.classList.remove('text-indigo-600', 'dark:text-indigo-400');
                    }
                }
            });

            // Update Tip
            const tipBox = document.getElementById('contextual-tip');
            const tipText = document.getElementById('tip-text');

            if (tipBox && tipText) {
                // Animation reset
                tipBox.classList.remove('opacity-100', 'translate-y-0');
                tipBox.classList.add('opacity-0', 'translate-y-2');

                setTimeout(() => {
                    tipText.innerText = tips[id] || tips['personal'];
                    tipBox.classList.remove('opacity-0', 'translate-y-2', 'hidden');
                    tipBox.classList.add('opacity-100', 'translate-y-0');
                }, 150);
            }
        }

        let lastActiveSection = 'personal';
        window.addEventListener('scroll', () => {
             const sections = ['personal', 'contact', 'address', 'spiritual', 'professional', 'emergency', 'preferences', 'security'];
             let current = lastActiveSection;
             const threshold = 150;

             for (const s of sections) {
                 const el = document.getElementById(s);
                 if (el) {
                     const rect = el.getBoundingClientRect();
                     if (rect.top <= threshold && rect.bottom >= threshold) {
                         current = s;
                     }
                 }
             }

             if (current !== lastActiveSection) {
                 lastActiveSection = current;
                 updateNavUI(current);
             }
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateNavUI('personal');
            if (window.location.hash) {
                const id = window.location.hash.replace('#', '');
                if (document.getElementById(id)) setTimeout(() => scrollToSection(id), 100);
            }

            const zipField = document.getElementById('zip_code');
            if(zipField) {
                zipField.addEventListener('keyup', (e) => {
                    const cleaned = e.target.value.replace(/\D/g, '');
                    if(cleaned.length >= 8) {
                        document.getElementById('cep-status').classList.remove('hidden');
                        setTimeout(() => document.getElementById('cep-status').classList.add('hidden'), 2000);
                    }
                });
            }
        });

        function updateSecurityUI(val) {
            const bar = document.getElementById('strength-bar-modern');
            const txt = document.getElementById('strength-text');
            const privBars = document.querySelectorAll('.lv-bar');
            let strength = 0;

            if (val.length >= 8) strength += 25;
            if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 25;
            if (val.match(/\d/)) strength += 25;
            if (val.match(/[^a-zA-Z\d]/)) strength += 25;

            bar.style.width = strength + '%';

            privBars.forEach(b => b.classList.remove('bg-emerald-500', 'bg-orange-500', 'bg-red-500'));

            if (strength <= 25) {
                bar.className = 'h-full transition-all duration-500 bg-red-500';
                txt.innerText = val ? 'FRACA' : '';
                txt.className = 'text-[9px] font-black uppercase tracking-widest text-red-500';
                if(val) privBars[0].classList.add('bg-red-500');
            } else if (strength <= 50) {
                bar.className = 'h-full transition-all duration-500 bg-orange-500';
                txt.innerText = 'MÉDIA';
                txt.className = 'text-[9px] font-black uppercase tracking-widest text-orange-500';
                privBars[0].classList.add('bg-orange-500');
                privBars[1].classList.add('bg-orange-500');
            } else {
                bar.className = 'h-full transition-all duration-500 bg-emerald-500';
                txt.innerText = strength > 75 ? 'IMPENETRÁVEL' : 'BOA';
                txt.className = 'text-[9px] font-black uppercase tracking-widest text-emerald-500';
                privBars.forEach(b => b.classList.add('bg-emerald-500'));
            }
        }

        function checkPasswordMatch() {
            const p = document.getElementById('password').value;
            const c = document.getElementById('password_confirmation').value;
            const icon = document.getElementById('match-status-icon');
            const txt = document.getElementById('match-status-text');

            if(!c) { icon.classList.add('hidden'); txt.classList.add('hidden'); return; }

            txt.classList.remove('hidden');
            if(p === c) {
                txt.innerText = 'Excelente! Senhas Iguais';
                txt.className = 'text-[10px] font-bold text-emerald-500 uppercase mt-1 tracking-tighter';
                icon.classList.remove('hidden');
            } else {
                txt.innerText = 'Ops! Senhas Diferentes';
                txt.className = 'text-[10px] font-bold text-red-500 uppercase mt-1 tracking-tighter';
                icon.classList.add('hidden');
            }
        }

        function previewImagesModern(input) {
            const status = document.getElementById('upload-status');
            if (input.files && input.files.length > 0) {
                status.innerText = `Pronto! ${input.files.length} foto(s) para upload.`;
                status.className = "mt-4 text-[9px] font-black uppercase tracking-widest text-emerald-500 animate-pulse";
            }
        }

        function submitPhotoAction(url, method) {
            const form = document.getElementById('photo-action-form');
            const methodField = document.getElementById('photo-action-method');
            form.action = url;
            methodField.value = method;
            form.submit();
        }
    </script>

    <!-- Hidden form for photo actions -->
    <form id="photo-action-form" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="photo-action-method" value="POST">
    </form>
    @endpush
@endsection

