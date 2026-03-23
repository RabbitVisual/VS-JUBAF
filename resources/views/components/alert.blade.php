@props([
    'type' => 'info', // success, error, warning, info
    'message' => '',
    'dismissible' => true,
    'autoClose' => true,
    'duration' => 5000,
])

@php
    $alertClasses = [
        'success' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
        'error' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
        'warning' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200',
        'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
    ];

    $iconClasses = [
        'success' => 'text-green-400',
        'error' => 'text-red-400',
        'warning' => 'text-yellow-400',
        'info' => 'text-blue-400',
    ];

    $iconNames = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'triangle-exclamation',
        'info' => 'circle-info',
    ];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    @if($autoClose)
        x-init="setTimeout(() => show = false, {{ $duration }})"
    @endif
    class="relative flex items-center p-4 mb-4 border rounded-lg {{ $alertClasses[$type] }}"
    role="alert"
>
    <div class="flex-shrink-0 {{ $iconClasses[$type] }}">
        <x-icon :name="$iconNames[$type]" class="w-5 h-5" />
    </div>
    <div class="ml-3 flex-1">
        <p class="text-sm font-medium">{{ $message ?: $slot }}</p>
    </div>
    @if($dismissible)
        <button
            @click="show = false"
            class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex h-8 w-8 {{ $alertClasses[$type] }} hover:opacity-75 transition-opacity"
            aria-label="Fechar"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    @endif
</div>
