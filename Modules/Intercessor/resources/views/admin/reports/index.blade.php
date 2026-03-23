@extends('admin::components.layouts.master')

@section('title', 'Relatórios de Intercessão')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Relatórios de Intercessão</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">
                Acompanhe o engajamento em oração e a quantidade de pedidos respondidos.
            </p>
        </div>
        <a href="{{ route('admin.intercessor.dashboard') }}"
           class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="gauge-high" class="w-4 h-4 mr-2" />
            Painel
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">De</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Até</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm flex-1">
                    <x-icon name="magnifying-glass" class="w-4 h-4 mr-2" />
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pedidos Criados</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalRequests }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pedidos Respondidos</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $answeredRequests }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pedidos com Oração</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $requestsWithPrayer }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Registros de Oração</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCommitments }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Taxa de Engajamento</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $engagementRate }}%</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Pedidos que receberam pelo menos um compromisso de oração.
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Taxa de Respostas</p>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $answerRate }}%</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Pedidos marcados como respondidos no período.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
        <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-widest">Pedidos por Categoria</h2>
        <div class="space-y-2">
            @forelse($byCategory as $row)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700 dark:text-gray-200">
                        {{ optional($row->category)->name ?? 'Sem categoria' }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 font-semibold">
                        {{ $row->total }} pedidos
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum dado para o período selecionado.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

