@extends('admin::components.layouts.master')

@section('title', 'Novo Beneficiário | Ação Social')

@section('content')
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('socialaction.admin.beneficiaries.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-rose-600 dark:text-gray-400 dark:hover:text-rose-400 transition-colors border border-gray-100 dark:border-gray-700">
            <x-icon name="arrow-left" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo Beneficiário</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Cadastre uma nova família ou pessoa para assistência social.</p>
        </div>
    </div>

    <form action="{{ route('socialaction.admin.beneficiaries.store') }}" method="POST" class="max-w-4xl mx-auto pb-20">
        @csrf

        <div class="space-y-8">
            {{-- Dados Pessoais e Identificação --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-1.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-lg">
                        <x-icon name="user" />
                    </span>
                    Dados Pessoais & Status
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="full_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4 transition-colors"
                            placeholder="Nome do chefe de família ou beneficiário">
                        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Telefone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4 transition-colors"
                            placeholder="(xx) xxxxx-xxxx">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Ativo (Apto a receber)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                            <option value="flagged" {{ old('status') === 'flagged' ? 'selected' : '' }}>Pendente / Requer Atenção</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Endereço e Localização --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg">
                        <x-icon name="map-location-dot" />
                    </span>
                    Endereço
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <div class="md:col-span-4">
                        <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rua, Número</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                            placeholder="Ex: Rua das Flores, 123">
                    </div>

                    <div class="md:col-span-2">
                        <label for="zip_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">CEP</label>
                        <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                            placeholder="00000-000">
                    </div>

                    <div class="md:col-span-2">
                        <label for="neighborhood" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Bairro</label>
                        <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                    </div>

                    <div class="md:col-span-3">
                        <label for="city" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Cidade</label>
                        <input type="text" name="city" id="city" value="{{ old('city', 'Ipatinga') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                    </div>

                    <div class="md:col-span-1">
                        <label for="state" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">UF</label>
                        <input type="text" name="state" id="state" value="{{ old('state', 'MG') }}" maxlength="2"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4 text-center uppercase">
                    </div>

                    <div class="md:col-span-3">
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Latitude</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                            placeholder="-19.xxxxxx">
                    </div>

                    <div class="md:col-span-3">
                        <label for="longitude" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Longitude</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                            placeholder="-42.xxxxxx">
                    </div>
                </div>
            </div>

            {{-- Perfil Socioeconômico --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-1.5 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-lg">
                        <x-icon name="hand-holding-dollar" />
                    </span>
                    Perfil Social & Necessidades
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="family_size" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tamanho da Família <span class="text-red-500">*</span></label>
                        <input type="number" name="family_size" id="family_size" value="{{ old('family_size', 1) }}" min="1" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                    </div>

                    <div>
                        <label for="monthly_income" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Renda Mensal Familiar (R$)</label>
                        <input type="number" step="0.01" name="monthly_income" id="monthly_income" value="{{ old('monthly_income') }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                            placeholder="0,00">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Necessidades Identificadas</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($needsLabels as $val => $label)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors has-[:checked]:bg-rose-50 has-[:checked]:border-rose-200 dark:has-[:checked]:bg-rose-900/20 dark:has-[:checked]:border-rose-800">
                                <input type="checkbox" name="needs[]" value="{{ $val }}"
                                    class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                    {{ in_array($val, old('needs', [])) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Notas e Observações Pastorais --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-1.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg">
                        <x-icon name="heart-pulse" />
                    </span>
                    Observações Pastorais
                </h3>

                <textarea name="pastoral_notes" id="pastoral_notes" rows="5"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 p-4 transition-colors resize-none text-sm leading-relaxed"
                    placeholder="Espaço dedicado para registrar o contexto da família, histórico de acompanhamento e notas confidenciais.">{{ old('pastoral_notes') }}</textarea>
                <div class="mt-3 flex items-center gap-2 text-xs text-amber-600 dark:text-amber-400 font-medium">
                    <x-icon name="lock" />
                    Informações sensíveis encriptadas e visíveis apenas para administradores autorizados.
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('socialaction.admin.beneficiaries.index') }}"
                   class="px-8 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                    <x-icon name="check" />
                    Salvar Beneficiário
                </button>
            </div>
        </div>
    </form>
@endsection
