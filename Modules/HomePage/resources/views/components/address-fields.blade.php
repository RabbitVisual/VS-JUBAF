@props([
    'prefix' => '',
    'required' => false,
    'showLabels' => true,
    'class' => '',
])

@php
    $prefix = $prefix ? $prefix . '_' : '';
    $cepId = $prefix . 'zip_code';
    $cityId = $prefix . 'city';
    $stateId = $prefix . 'state';
    $addressId = $prefix . 'address';
    $numberId = $prefix . 'address_number';
    $complementId = $prefix . 'address_complement';
    $neighborhoodId = $prefix . 'neighborhood';
@endphp

<div class="space-y-4 {{ $class }}" data-address-form>
    <!-- CEP -->
    <div>
        @if ($showLabels)
            <label for="{{ $cepId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                CEP
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <div class="relative">
            <input type="text" id="{{ $cepId }}" name="{{ $prefix }}zip_code" data-mask="cep"
                placeholder="00000-000" @if ($required) required @endif
                class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white sm:text-sm transition-colors"
                autocomplete="postal-code">
            <div id="{{ $cepId }}-loading" class="hidden absolute right-3 top-2.5">
                <x-icon name="spinner" class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
        <p id="{{ $cepId }}-error" class="hidden mt-1 text-sm text-red-500 dark:text-red-400"></p>
        <p id="{{ $cepId }}-success" class="hidden mt-1 text-sm text-green-600 dark:text-green-400"></p>
    </div>

    <!-- Cidade e Estado (preenchidos automaticamente) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            @if ($showLabels)
                <label for="{{ $cityId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cidade
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $cityId }}" name="{{ $prefix }}city"
                @if ($required) required @endif readonly
                class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm cursor-not-allowed transition-colors"
                autocomplete="address-level2">
        </div>

        <div>
            @if ($showLabels)
                <label for="{{ $stateId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Estado (UF)
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $stateId }}" name="{{ $prefix }}state" maxlength="2"
                @if ($required) required @endif readonly
                class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm cursor-not-allowed uppercase transition-colors"
                autocomplete="address-level1">
        </div>
    </div>

    <!-- Logradouro -->
    <div>
        @if ($showLabels)
            <label for="{{ $addressId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Logradouro
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input type="text" id="{{ $addressId }}" name="{{ $prefix }}address"
            placeholder="Rua, Avenida, etc." @if ($required) required @endif
            class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white sm:text-sm transition-colors"
            autocomplete="street-address">
    </div>

    <!-- Número e Complemento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            @if ($showLabels)
                <label for="{{ $numberId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Número
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $numberId }}" name="{{ $prefix }}address_number" placeholder="123"
                @if ($required) required @endif
                class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white sm:text-sm transition-colors">
        </div>

        <div class="md:col-span-2">
            @if ($showLabels)
                <label for="{{ $complementId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Complemento
                </label>
            @endif
            <input type="text" id="{{ $complementId }}" name="{{ $prefix }}address_complement"
                placeholder="Apto, Bloco, etc."
                class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white sm:text-sm transition-colors">
        </div>
    </div>

    <!-- Bairro -->
    <div>
        @if ($showLabels)
            <label for="{{ $neighborhoodId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Bairro
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        <input type="text" id="{{ $neighborhoodId }}" name="{{ $prefix }}neighborhood"
            placeholder="Nome do bairro" @if ($required) required @endif
            class="appearance-none block w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white sm:text-sm transition-colors"
            autocomplete="address-level3">
    </div>
</div>

