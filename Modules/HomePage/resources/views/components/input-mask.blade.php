@props([
    'name',
    'label' => null,
    'type' => 'text',
    'mask' => null,
    'placeholder' => null,
    'required' => false,
    'value' => null,
    'id' => null,
    'class' => '',
    'error' => null,
    'help' => null,
])

@php
    $id = $id ?? $name;

    // Placeholders padrão por tipo de máscara
    $defaultPlaceholders = [
        'cpf' => '000.000.000-00',
        'cnpj' => '00.000.000/0000-00',
        'phone' => '(__) ____-____',
        'cellphone' => '(__) _____-____',
        'cep' => '_____-___',
        'date' => 'dd/mm/aaaa',
        'time' => '__:__',
        'money' => '0,00',
        'creditCard' => '____ ____ ____ ____',
    ];

    $placeholder = $placeholder ?? ($mask ? ($defaultPlaceholders[$mask] ?? '') : '');
@endphp

<div class="space-y-1">
    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            @if($mask) data-mask="{{ $mask }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm {{ $error ? 'border-red-500 dark:border-red-500' : '' }} {{ $class }}"
            {{ $attributes }}
        >
    </div>

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>

