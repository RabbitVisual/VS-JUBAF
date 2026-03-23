@props([
    'prefix' => '',
    'required' => false,
    'showLabels' => true,
    'class' => '',
    'model' => null,
])

@php
    $rawPrefix = $prefix;
    $prefix = $prefix ? $prefix . '_' : '';
    $cepId = $prefix . 'zip_code';
    $cityId = $prefix . 'city';
    $stateId = $prefix . 'state';
    $addressId = $prefix . 'address';
    $numberId = $prefix . 'address_number';
    $complementId = $prefix . 'address_complement';
    $neighborhoodId = $prefix . 'neighborhood';
@endphp

<div class="space-y-6 {{ $class }}" data-address-form>
    <!-- CEP -->
    <div class="space-y-1">
        @if ($showLabels)
            <label for="{{ $cepId }}" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                CEP
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <div class="relative group">
            <input type="text" id="{{ $cepId }}" name="{{ $prefix }}zip_code" data-mask="cep"
                value="{{ old($prefix . 'zip_code', $model ? $model->{$prefix . 'zip_code'} : '') }}"
                placeholder="00000-000" @if ($required) required @endif
                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                autocomplete="postal-code">
            <div id="{{ $cepId }}-loading" class="hidden absolute right-4 top-3.5">
                <x-icon name="spinner" class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
        <p id="{{ $cepId }}-error" class="hidden mt-1 text-[10px] text-red-500 font-bold uppercase"></p>
        <p id="{{ $cepId }}-success" class="hidden mt-1 text-[10px] text-emerald-500 font-bold uppercase"></p>
    </div>

    <!-- Cidade e Estado (preenchidos automaticamente) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-1">
            @if ($showLabels)
                <label for="{{ $cityId }}"
                    class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                    Cidade
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $cityId }}" name="{{ $prefix }}city"
                value="{{ old($prefix . 'city', $model ? $model->{$prefix . 'city'} : '') }}"
                @if ($required) required @endif readonly
                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 font-semibold cursor-not-allowed"
                autocomplete="address-level2">
        </div>

        <div class="space-y-1">
            @if ($showLabels)
                <label for="{{ $stateId }}"
                    class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                    Estado (UF)
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $stateId }}" name="{{ $prefix }}state" maxlength="2"
                value="{{ old($prefix . 'state', $model ? $model->{$prefix . 'state'} : '') }}"
                @if ($required) required @endif readonly
                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 font-bold cursor-not-allowed uppercase"
                autocomplete="address-level1">
        </div>
    </div>

    <!-- Logradouro -->
    <div class="space-y-1">
        @if ($showLabels)
            <label for="{{ $addressId }}" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                Logradouro
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input type="text" id="{{ $addressId }}" name="{{ $prefix }}address"
            value="{{ old($prefix . 'address', $model ? $model->{$prefix . 'address'} : '') }}"
            placeholder="Rua, Avenida, etc." @if ($required) required @endif
            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
            autocomplete="street-address">
    </div>

    <!-- Número e Complemento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-1">
            @if ($showLabels)
                <label for="{{ $numberId }}"
                    class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                    Número
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $numberId }}" name="{{ $prefix }}address_number" placeholder="123"
                value="{{ old($prefix . 'address_number', $model ? $model->{$prefix . 'address_number'} : '') }}"
                @if ($required) required @endif
                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
        </div>

        <div class="md:col-span-2 space-y-1">
            @if ($showLabels)
                <label for="{{ $complementId }}"
                    class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                    Complemento
                </label>
            @endif
            <input type="text" id="{{ $complementId }}" name="{{ $prefix }}address_complement"
                value="{{ old($prefix . 'address_complement', $model ? $model->{$prefix . 'address_complement'} : '') }}"
                placeholder="Apto, Bloco, etc."
                class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
        </div>
    </div>

    <!-- Bairro -->
    <div class="space-y-1">
        @if ($showLabels)
            <label for="{{ $neighborhoodId }}" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">
                Bairro
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input type="text" id="{{ $neighborhoodId }}" name="{{ $prefix }}neighborhood"
            value="{{ old($prefix . 'neighborhood', $model ? $model->{$prefix . 'neighborhood'} : '') }}"
            placeholder="Nome do bairro" @if ($required) required @endif
            class="modern-input w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
            autocomplete="address-level3">
    </div>
</div>

