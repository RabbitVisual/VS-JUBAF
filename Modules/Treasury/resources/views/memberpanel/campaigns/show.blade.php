@extends('memberpanel::components.layouts.master')

@section('page-title', $campaign->name)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $campaign->name }}</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Detalhes e progresso da campanha.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.campaigns.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors">Voltar à listagem</a>
                    @if ($permission->canManageCampaigns())
                        <a href="{{ route('memberpanel.treasury.campaigns.edit', $campaign) }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                            <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                            Editar
                        </a>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center gap-8 z-10">
                    @if ($campaign->image)
                        <div class="shrink-0 w-48 h-32 md:w-56 md:h-36 rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700">
                            <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-1">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $campaign->isActive() ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400' }}">
                            {{ $campaign->isActive() ? 'Ativa' : 'Inativa' }}
                        </span>
                        @if ($campaign->description)
                            <p class="text-gray-500 dark:text-slate-300 font-medium mt-3 max-w-2xl">{{ Str::limit($campaign->description, 200) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    @if ($campaign->target_amount)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                <x-icon name="chart-mixed" style="duotone" class="w-5 h-5" />
                            </div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Metas</h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="p-6 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Arrecadado</p>
                                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</p>
                                </div>
                                <div class="p-6 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Meta</p>
                                    <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                    <span>{{ number_format($campaign->progress_percentage, 1) }}% do objetivo</span>
                                    <span>Faltam R$ {{ number_format(max(0, $campaign->target_amount - $campaign->current_amount), 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full h-3 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-500" style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if ($campaign->description)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                <x-icon name="file-lines" style="duotone" class="w-5 h-5" />
                            </div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Descrição</h3>
                        </div>
                        <div class="p-8 text-gray-600 dark:text-slate-400 leading-relaxed">
                            {!! nl2br(e($campaign->description)) !!}
                        </div>
                    </div>
                    @endif

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                <x-icon name="money-check-dollar" style="duotone" class="w-5 h-5" />
                            </div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Contribuições recentes</h3>
                        </div>
                        @if ($campaign->financialEntries->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                        <tr>
                                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Data</th>
                                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Histórico</th>
                                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                        @foreach ($campaign->financialEntries as $entry)
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                                <td class="px-8 py-5 text-sm font-medium text-gray-600 dark:text-slate-400">{{ $entry->entry_date->format('d/m/Y') }}</td>
                                                <td class="px-8 py-5 text-sm font-bold text-gray-900 dark:text-white">{{ $entry->title }}</td>
                                                <td class="px-8 py-5 text-right text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">R$ {{ number_format($entry->amount, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <x-icon name="handshake-slash" style="duotone" class="w-12 h-12 text-gray-300 dark:text-slate-600 mx-auto mb-4" />
                                <p class="text-gray-500 dark:text-slate-400 text-sm font-medium">Aguardando as primeiras contribuições.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-8">
                    @if ($campaign->image)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="relative rounded-t-3xl overflow-hidden">
                            <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-auto">
                        </div>
                    </div>
                    @endif

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                            <div class="p-2 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-xl">
                                <x-icon name="circle-info" style="duotone" class="w-5 h-5" />
                            </div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Informações</h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <x-icon name="calendar-range" style="duotone" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Vigência</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        @if ($campaign->start_date) {{ $campaign->start_date->format('d/m/Y') }} @else — @endif
                                        até @if ($campaign->end_date) {{ $campaign->end_date->format('d/m/Y') }} @else Contínua @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <x-icon name="chart-simple" style="duotone" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Lançamentos</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $campaign->financialEntries->count() }} transações</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
