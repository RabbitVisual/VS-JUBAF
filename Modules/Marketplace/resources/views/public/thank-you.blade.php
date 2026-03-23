@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 py-16">
    <div class="max-w-lg w-full text-center">
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-12 shadow-sm">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-6">
                <x-icon name="hand-holding-heart" style="duotone" class="w-10 h-10 text-green-600 dark:text-green-400" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Obrigado!</h1>
            @if(session('success'))
                <p class="mt-4 text-gray-600 dark:text-gray-400">{{ session('success') }}</p>
            @else
                <p class="mt-4 text-gray-600 dark:text-gray-400">Obrigado por apoiar a obra de Deus! Seu pedido está sendo processado.</p>
            @endif
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                @auth
                    <a href="{{ route('memberpanel.marketplace.orders.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium">
                        <x-icon name="bag-shopping" style="duotone" class="w-5 h-5 mr-2" /> Minhas Compras
                    </a>
                @endauth
                <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <x-icon name="store" style="duotone" class="w-5 h-5 mr-2" /> Voltar à loja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
