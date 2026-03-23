@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 md:py-16">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> {{ __('marketplace::messages.back_to_store') }}
            </a>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('marketplace::messages.customer_login') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('marketplace::messages.customer_login_subtitle') }}
            </p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-3 py-2 text-sm text-red-700 dark:text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('marketplace.customer.login.post') }}"
                  onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: '{{ __('marketplace::messages.logging_in') }}' } }))">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('marketplace::messages.email') }}
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('marketplace::messages.password') }}
                        </label>
                        <input type="password" name="password" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('marketplace::messages.remember_me') }}</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <x-icon name="right-to-bracket" class="w-4 h-4 mr-2" />
                        {{ __('marketplace::messages.login') }}
                    </button>

                    <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ __('marketplace::messages.no_customer_account') }}
                        <a href="{{ route('marketplace.customer.register') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                            {{ __('marketplace::messages.create_account') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

