@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Recomendações à Assembleia</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Pautas já aprovadas pela diretoria que aguardam deliberação da assembleia da igreja.
                </p>
            </div>
            <a href="{{ route('admin.Diretoria.meetings.index') }}"
                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="calendar" class="w-5 h-5 mr-2" />
                Ver reuniões
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="scale-balanced" class="w-5 h-5" />
                    Pautas pendentes de assembleia
                </h2>
            </div>

            @if ($agendas->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($agendas as $agenda)
                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center gap-3 md:gap-6">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                                    Reunião: {{ $agenda->meeting->title ?? '—' }}
                                    @if ($agenda->meeting?->scheduled_date)
                                        • {{ $agenda->meeting->scheduled_date->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $agenda->title }}
                                </p>
                                @if ($agenda->decision)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Decisão da diretoria: {{ $agenda->decision }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-[11px] font-bold uppercase tracking-wide">
                                    Aguardando assembleia
                                </span>
                                <a href="{{ route('admin.Diretoria.assembly.edit', $agenda) }}"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-blue-500/30 transition-all">
                                    <x-icon name="pencil-alt" class="w-4 h-4" />
                                    Registrar decisão
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $agendas->links() }}
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                        <x-icon name="check-circle" class="w-8 h-8" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Nenhuma recomendação pendente</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Todas as pautas encaminhadas à assembleia já tiveram sua decisão registrada.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
