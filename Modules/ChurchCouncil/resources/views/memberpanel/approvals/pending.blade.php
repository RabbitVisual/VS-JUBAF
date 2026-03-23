@extends('memberpanel::components.layouts.master')

@section('title', 'Aprovações Pendentes - Conselho da Igreja')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Aprovações Pendentes</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1">Revisar e aprovar solicitações pendentes</p>
            </div>
            <a href="{{ route('memberpanel.churchcouncil.approvals.index') }}"
               class="px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-amber-600 dark:hover:text-white transition-colors font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700 flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <!-- Approvals Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden relative transition-colors duration-200">
             <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                <x-icon name="clock" class="w-64 h-64 text-amber-500" />
            </div>

            <div class="overflow-x-auto relative z-10">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                    <thead class="bg-gray-50 dark:bg-slate-950/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Solicitação</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Solicitante</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Data</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @forelse($approvals as $approval)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                        {{ $approval->title ?? $approval->approval_type_display }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-slate-500 mt-1 leading-snug">{{ Str::limit($approval->description, 60) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border
                                    @if ($approval->approval_type === 'budget') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20
                                    @elseif($approval->approval_type === 'project') bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                                    @elseif($approval->approval_type === 'personnel') bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                                    @elseif($approval->approval_type === 'policy') bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20
                                    @elseif($approval->approval_type === 'facility') bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20
                                    @else bg-gray-100 text-gray-600 border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 @endif">
                                    {{ $approval->approval_type_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-slate-400 mr-2">
                                        {{ substr($approval->requester->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-slate-300">{{ $approval->requester->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-700 dark:text-slate-300">{{ $approval->submitted_at->format('d/m/Y') }}</span>
                                <div class="text-xs text-gray-500 dark:text-slate-500 font-medium">{{ $approval->submitted_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('memberpanel.churchcouncil.approvals.show', $approval) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500 hover:text-amber-700 dark:hover:text-white rounded-lg transition-all border border-amber-200 dark:border-amber-500/20 font-bold text-xs uppercase tracking-wide">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                        <x-icon name="circle-check" class="h-8 w-8 text-gray-400 dark:text-slate-600" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Nenhuma aprovação pendente</h3>
                                    <p class="text-gray-500 dark:text-slate-500 max-w-sm mx-auto">
                                        Não há solicitações de aprovação pendentes no momento.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($approvals->hasPages())
            <div class="bg-gray-50 dark:bg-slate-900 px-4 py-4 border-t border-gray-200 dark:border-slate-800 sm:px-6">
                {{ $approvals->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

