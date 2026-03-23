@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 md:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('marketplace::messages.customer_account') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('marketplace::messages.customer_account_subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <x-icon name="store" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.back_to_store') }}
                </a>
                <form action="{{ route('marketplace.customer.logout') }}" method="POST" onsubmit="return confirm('{{ __('marketplace::messages.logout_confirm') }}');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold">
                        <x-icon name="right-from-bracket" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.logout') }}
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 px-4 py-3 text-sm text-blue-800 dark:text-blue-200">
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <aside class="md:col-span-1">
                <nav class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 space-y-1">
                    <a href="{{ route('marketplace.customer.dashboard') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('marketplace.customer.dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                        <x-icon name="bag-shopping" class="w-4 h-4 mr-2" />
                        {{ __('marketplace::messages.my_orders') }}
                    </a>
                    <a href="{{ route('marketplace.customer.profile') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('marketplace.customer.profile') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                        <x-icon name="user" class="w-4 h-4 mr-2" />
                        {{ __('marketplace::messages.profile') }}
                    </a>
                </nav>
            </aside>

            <main class="md:col-span-3">
                @yield('customer-content')
            </main>
        </div>
    </div>
</div>
@endsection

