@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Cartas de transferência</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Saídas e entradas de membros por carta, integradas ao cuidado liderancaal do conselho.
                </p>
            </div>
            <a href="{{ route('admin.churchcouncil.transfers.create') }}"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors shadow-sm flex items-center justify-center sm:w-auto w-full">
                <x-icon name="paper-plane" class="w-5 h-5 mr-2" />
                Registrar carta
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="envelope-open-text" class="w-5 h-5" />
                    Cartas registradas
                </h2>
            </div>

            @if ($letters->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($letters as $letter)
                        <a href="{{ route('admin.churchcouncil.transfers.show', $letter) }}"
                            class="flex flex-col md:flex-row md:items-center gap-3 md:gap-6 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $letter->member->name ?? 'Membro removido' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    @if ($letter->direction === \Modules\ChurchCouncil\App\Models\TransferLetter::DIRECTION_OUTGOING)
                                        Saída para {{ $letter->to_church ?? 'igreja destino não informada' }}
                                    @else
                                        Entrada vinda de {{ $letter->from_church ?? 'igreja origem não informada' }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                @if ($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_COUNCIL) bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_ASSEMBLY)
                                    bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_SENT)
                                    bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_ACKNOWLEDGED)
                                    bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                @else
                                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $letter->status)) }}
                                </span>
                                @if ($letter->issued_at)
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                        Emitida em {{ $letter->issued_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $letters->links() }}
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                        <x-icon name="envelope" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text:white">Nenhuma carta registrada</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Use esta área para acompanhar transferências de membros entre igrejas batistas.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
