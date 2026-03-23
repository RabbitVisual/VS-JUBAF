@extends('memberpanel::components.layouts.master')

@php
    use Illuminate\Support\Str;
@endphp

@section('page-title', 'Minhas Doações')

@section('content')
    <div class="space-y-8 pb-12" data-tour="donations-area">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
            <!-- Decorative Mesh Gradient Background -->
            <div class="absolute inset-0 opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 right-10 w-80 h-80 bg-blue-600 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-teal-500 rounded-full blur-[80px]"></div>
            </div>

            <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <p class="text-emerald-200/80 font-bold uppercase tracking-widest text-xs">Histórico Financeiro</p>
                    <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                        Minhas Doações
                    </h1>
                    <p class="text-slate-300 font-medium max-w-xl">
                        Acompanhe seu histórico de contribuições, ofertas e dízimos.
                    </p>
                </div>

                <a href="{{ route('memberpanel.donations.create') }}"
                   class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 rounded-xl font-black text-sm hover:bg-emerald-50 transition-all shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5"
                   data-tour="donations-create-link">
                    <x-icon name="heart" style="duotone" class="w-5 h-5 mr-2 text-red-500 group-hover:scale-110 transition-transform" />
                    Nova Doação
                </a>
            </div>
        </div>

        <!-- Donations List -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-xl shadow-blue-500/5 overflow-hidden">
            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50/50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Data</th>
                                <th scope="col" class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Descrição</th>
                                <th scope="col" class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Valor</th>
                                <th scope="col" class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Método</th>
                                <th scope="col" class="px-8 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th scope="col" class="relative px-8 py-5"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-white dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-8 py-5 whitespace-nowrap text-sm font-bold text-gray-600 dark:text-gray-300">
                                    {{ $payment->created_at->format('d/m/Y') }}
                                    <span class="block text-xs font-normal text-gray-400">{{ $payment->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ Str::limit($payment->description ?: 'Doação', 40) }}
                                    </div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $payment->gateway->display_name ?? 'Online' }}
                                    </div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-sm font-black text-gray-900 dark:text-white">
                                    R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    @if($payment->payment_method === 'pix' || $payment->payment_method === 'bank_transfer')
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                                <x-icon name="qrcode" style="duotone" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                            </div>
                                            <span class="font-bold">PIX</span>
                                        </div>
                                    @elseif($payment->payment_method === 'credit_card')
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                <x-icon name="credit-card" style="duotone" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            </div>
                                            <span class="font-bold">Cartão</span>
                                        </div>
                                    @else
                                        <span class="font-bold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    @if($payment->status === 'completed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-black uppercase tracking-wider rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            Confirmado
                                        </span>
                                    @elseif($payment->status === 'pending' || $payment->status === 'processing')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-black uppercase tracking-wider rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            Pendente
                                        </span>
                                    @elseif($payment->status === 'cancelled' || $payment->status === 'failed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-black uppercase tracking-wider rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Cancelado
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-black uppercase tracking-wider rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    @if($payment->status === 'pending' || $payment->status === 'processing')
                                        <div class="flex flex-col gap-2">
                                            <a href="{{ route('checkout.show', $payment->transaction_id) }}"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-xs transition-colors shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40">
                                                Pagar Agora
                                            </a>
                                            <a href="{{ route('memberpanel.donations.retry', $payment->transaction_id) }}"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-bold text-xs transition-colors">
                                                Alterar Método
                                            </a>
                                        </div>
                                    @elseif($payment->status === 'cancelled' || $payment->status === 'failed')
                                        <div class="flex flex-col gap-2">
                                            <a href="{{ route('memberpanel.donations.retry', $payment->transaction_id) }}"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-bold text-xs transition-colors">
                                                Tentar Novamente
                                            </a>
                                            <a href="{{ route('memberpanel.donations.create', ['amount' => $payment->amount, 'description' => $payment->description]) }}"
                                               class="text-xs text-gray-400 hover:text-blue-600 transition-colors">
                                                Nova Doação
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('checkout.show', $payment->transaction_id) }}"
                                           class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <x-icon name="eye" style="duotone" class="w-5 h-5" />
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <x-icon name="hand-holding-dollar" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma doação encontrada</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Você ainda não realizou nenhuma doação.</p>
                    <a href="{{ route('memberpanel.donations.create') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg hover:shadow-blue-500/30">
                        Fazer Primeira Doação
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
