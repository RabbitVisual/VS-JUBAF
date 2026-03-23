@extends('admin::components.layouts.master')

@section('title', 'Histórico de Assistências | Ação Social')

@section('content')
    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-check" /> {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Histórico de Assistências</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Registro consolidado de todo impacto social gerado.</p>
        </div>
        <a href="{{ route('socialaction.admin.assistance.create') }}" class="inline-flex items-center px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all hover:scale-105 active:scale-95 gap-2">
            <x-icon name="hand-holding-heart" />
            Registrar Nova
        </a>
    </div>

    {{-- List Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        @if($assistances->isEmpty())
            <div class="py-24 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="history" class="text-4xl" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma assistência encontrada</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Comece a registrar as ajudas para visualizar o histórico de impacto.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                        <tr>
                            <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data & Hora</th>
                            <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Beneficiário</th>
                            <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo / Origem</th>
                            <th scope="col" class="px-6 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor/Qtde</th>
                            <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Observações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($assistances as $assistance)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $assistance->registered_at?->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $assistance->registered_at?->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('socialaction.admin.beneficiaries.show', $assistance->beneficiary->id) }}" class="flex items-center group">
                                        <div class="h-9 w-9 rounded-xl bg-linear-to-br from-rose-500 to-orange-400 text-white flex items-center justify-center text-xs font-black mr-3 shadow-md group-hover:scale-110 transition-transform">
                                            {{ substr($assistance->beneficiary->full_name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-rose-600 transition-colors">
                                            {{ $assistance->beneficiary->full_name }}
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($assistance->type === 'financial')
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                <x-icon name="sack-dollar" class="mr-1.5" /> AUXÍLIO FINANCEIRO
                                            </span>
                                            @if($assistance->financial_entry_id)
                                                <x-icon name="link" class="text-[10px] text-gray-400" title="Integrado à Tesouraria" />
                                            @endif
                                        </div>
                                    @elseif($assistance->kit)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <x-icon name="box-open" class="mr-1.5" /> {{ $assistance->kit->name }}
                                        </span>
                                    @elseif($assistance->pantryItem)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <x-icon name="utensils" class="mr-1.5" /> {{ $assistance->pantryItem->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($assistance->type === 'financial')
                                        <span class="text-sm font-black text-amber-600 dark:text-amber-400">R$ {{ number_format($assistance->amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($assistance->quantity, 1, ',', '.') }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">
                                            {{ $assistance->kit ? 'kits' : ($assistance->pantryItem ? $assistance->pantryItem->unit : '') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic line-clamp-1 max-w-[200px]" title="{{ $assistance->notes }}">
                                        {{ $assistance->notes ?: 'Nenhuma observação' }}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($assistances->hasPages())
                <div class="px-6 py-6 border-t border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/50">
                    {{ $assistances->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
