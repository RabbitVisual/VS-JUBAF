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
                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm"
                autocomplete="postal-code">
            <div id="{{ $cepId }}-loading" class="hidden absolute right-3 top-2.5">
                <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
        <p id="{{ $cepId }}-error" class="hidden mt-1 text-sm text-red-600 dark:text-red-400"></p>
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
                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm cursor-not-allowed"
                autocomplete="address-level2">
        </div>

        <div>
            @if ($showLabels)
                <label for="{{ $stateId }}"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Estado (UF)
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $stateId }}" name="{{ $prefix }}state" maxlength="2"
                @if ($required) required @endif readonly
                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm cursor-not-allowed uppercase"
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
            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm"
            autocomplete="street-address">
    </div>

    <!-- Número e Complemento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            @if ($showLabels)
                <label for="{{ $numberId }}"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Número
                    @if ($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            <input type="text" id="{{ $numberId }}" name="{{ $prefix }}address_number" placeholder="123"
                @if ($required) required @endif
                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
        </div>

        <div class="md:col-span-2">
            @if ($showLabels)
                <label for="{{ $complementId }}"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Complemento
                </label>
            @endif
            <input type="text" id="{{ $complementId }}" name="{{ $prefix }}address_complement"
                placeholder="Apto, Bloco, etc."
                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
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
            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm"
            autocomplete="address-level3">
    </div>
</div>

