@extends('liderancapanel::components.layouts.master')

@section('title', 'Nova Entrada Financeira')

@section('content')
    <div class="space-y-6">
        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <a href="{{ route('lideranca.tesouraria.entries.index') }}"
                            class="hover:text-white transition-colors">Lançamentos</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Nova entrada</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Nova Entrada Financeira</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Registre receita ou despesa. Dados detalhados para prestação
                        de contas.</p>
                </div>
                <a href="{{ route('lideranca.tesouraria.entries.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center gap-2">
                <x-icon name="receipt" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Dados da transação</h3>
            </div>
            <div
                class="px-5 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-100 dark:border-amber-800/50 flex items-start gap-3">
                <x-icon name="circle-info" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                <p class="text-xs text-amber-800 dark:text-amber-200">Doações via PIX/cartão podem ser vinculadas pela
                    transação bancária (valor líquido).</p>
            </div>
            @include('treasury::liderancapanel.entries._form')
        </div>
    </div>
@endsection
