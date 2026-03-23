@extends('admin::components.layouts.master')

@section('title', 'Gateways de Pagamento')

@section('content')
<div class="p-6">
    {{-- Version 1.1: Uses $gateways variable --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Gateways de Pagamento</h2>
            <p class="text-slate-600 dark:text-slate-400">Gerencie os provedores de pagamento ativos no sistema.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($gateways as $gateway)
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4 text-3xl text-indigo-600 dark:text-indigo-400">
                    {{-- Dynamically render icon or fallback --}}
                    @if($gateway->logo_url)
                         <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="max-w-full max-h-full p-2">
                    @else
                        <i class="{{ $gateway->icon ?? 'fas fa-credit-card' }}"></i>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">{{ $gateway->display_name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ $gateway->description ?? 'Gerencie pagamentos com ' . $gateway->display_name }}</p>

                <div class="mt-auto w-full">
                    <div class="flex items-center justify-center mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gateway->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ $gateway->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                        @if($gateway->is_test_mode)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Sandbox
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('admin.payment-gateways.edit', $gateway->id) }}" class="block w-full px-4 py-2 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">
                        Configurar
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
