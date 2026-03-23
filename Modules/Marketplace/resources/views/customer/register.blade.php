@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10 md:py-16">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> {{ __('marketplace::messages.back_to_store') }}
            </a>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('marketplace::messages.create_customer_account') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('marketplace::messages.create_customer_account_subtitle') }}
            </p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-3 py-2 text-sm text-red-700 dark:text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('marketplace.customer.register.post') }}"
                  onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: '{{ __('marketplace::messages.creating_account') }}' } }))">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('marketplace::messages.name') }}
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('marketplace::messages.email') }}
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('marketplace::messages.password') }}
                            </label>
                            <input type="password" name="password" required
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('marketplace::messages.password_confirmation') }}
                            </label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('marketplace::messages.phone') }}
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('marketplace::messages.document_optional') }}
                            </label>
                            <input type="text" name="document" value="{{ old('document') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <x-icon name="user-plus" class="w-4 h-4 mr-2" />
                        {{ __('marketplace::messages.create_account') }}
                    </button>

                    <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ __('marketplace::messages.already_customer') }}
                        <a href="{{ route('marketplace.customer.login') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                            {{ __('marketplace::messages.login') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

