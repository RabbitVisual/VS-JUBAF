@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Transação #' . $payment->transaction_id;
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Transação #{{ $payment->transaction_id }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Detalhes e histórico de auditoria.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.transactions.receipt', $payment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">
                <x-icon name="print" class="w-5 h-5 mr-2" />
                Imprimir comprovante
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Dados do pagamento -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-icon name="receipt" class="w-5 h-5 mr-2 text-indigo-500" />
                Dados do pagamento
            </h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">ID / Transação</dt>
                    <dd class="font-mono font-medium text-gray-900 dark:text-white">{{ $payment->transaction_id }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Valor</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($payment->amount, 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:text-gray-400">
                    <dt>Gateway</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->gateway->display_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Método</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->payment_method ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                    <dd>
                        @if($payment->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Confirmado</span>
                        @elseif($payment->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pendente</span>
                        @elseif($payment->status === 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Cancelado</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ $payment->status }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Pagador</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->payer_name ?? 'Anônimo' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">E-mail</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->payer_email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Criado em</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->created_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @if($payment->paid_at)
                <div class="flex justify-between py-2">
                    <dt class="text-gray-500 dark:text-gray-400">Confirmado em</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Histórico de auditoria -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-icon name="clock-rotate-left" class="w-5 h-5 mr-2 text-indigo-500" />
                Histórico de auditoria
            </h2>
            @if($payment->auditLogs->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma alteração de status registrada (transação anterior à auditoria).</p>
            @else
                <ul class="space-y-3">
                    @foreach($payment->auditLogs as $log)
                        <li class="flex items-start gap-3 text-sm py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ \App\Helpers\TransactionStatusBadge::classes($log->to_status) }}">
                                {{ $log->from_status ?? '—' }} → {{ $log->to_status }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $log->source }}</span>
                            <span class="text-gray-400 dark:text-gray-500 ml-auto">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
