@extends('memberpanel::components.layouts.master')

@section('title', 'Minhas transferências')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200">
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Minhas transferências</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1">
                    Acompanhe pedidos de carta de transferência realizados pela secretaria e pelo conselho.
                </p>
            </div>
            <a href="{{ route('memberpanel.transfers.create') }}"
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors shadow-sm flex items-center justify-center sm:w-auto w-full">
                <x-icon name="paper-plane" class="w-5 h-5 mr-2" />
                Solicitar carta
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="envelope-open-text" class="w-4 h-4" />
                    Histórico de cartas
                </h2>
            </div>

            @if($letters->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-slate-800">
                    @foreach($letters as $letter)
                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center gap-3 md:gap-6">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    @if($letter->direction === \Modules\ChurchCouncil\App\Models\TransferLetter::DIRECTION_OUTGOING)
                                        Saída para {{ $letter->to_church ?? 'igreja destino não informada' }}
                                    @else
                                        Entrada de {{ $letter->from_church ?? 'igreja origem não informada' }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    Criada em {{ $letter->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                    @if($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_COUNCIL)
                                        bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_ASSEMBLY)
                                        bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                                    @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_SENT)
                                        bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_ACKNOWLEDGED)
                                        bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                    @else
                                        bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $letter->status)) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-400">
                        <x-icon name="envelope" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Nenhum pedido registrado</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                        Quando você solicitar uma carta de transferência, ela aparecerá aqui com o andamento do processo.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

