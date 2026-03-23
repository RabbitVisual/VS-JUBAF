@extends('memberpanel::components.layouts.master')

@section('title', 'Minhas Pautas - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <!-- Hero Section -->
        <div
            class="relative bg-linear-to-r from-emerald-900 to-emerald-800 dark:from-slate-900 dark:to-slate-950 border-b border-emerald-200 dark:border-emerald-900/30 p-6 md:p-10 transition-colors duration-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <p class="text-emerald-200 dark:text-emerald-500 font-bold uppercase tracking-widest text-xs">Minhas
                        Propostas</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Pautas e Deliberações
                    </h1>
                    <p class="text-emerald-100 dark:text-slate-400 font-medium max-w-xl">
                        Gerencie suas propostas apresentadas e acompanhe o status de aprovação.
                    </p>
                </div>

                <a href="{{ route('memberpanel.Diretoria.agendas.create') }}"
                    class="group relative inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500 text-emerald-700 dark:text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-xl dark:hover:shadow-emerald-500/20 hover:-translate-y-0.5 border border-emerald-200 dark:border-emerald-500/50">
                    <x-icon name="plus"
                        class="w-5 h-5 mr-2 text-emerald-600 dark:text-white group-hover:scale-110 transition-transform" />
                    Nova Pauta
                </a>
            </div>
        </div>

        <!-- Agendas List -->
        <div class="max-w-7xl mx-auto p-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden transition-colors duration-200">
                @if ($agendas->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                            <thead class="bg-gray-50 dark:bg-slate-900/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Pauta</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Reunião</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Prioridade</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                @foreach ($agendas as $agenda)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                {{ $agenda->title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-slate-500 mt-1 leading-snug">
                                                {{ Str::limit($agenda->description, 60) }}</div>
                                            <div
                                                class="flex items-center gap-2 text-[10px] text-gray-400 dark:text-slate-600 mt-1 uppercase tracking-wider">
                                                <span>Criado em {{ $agenda->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                                            @if ($agenda->meeting)
                                                {{ $agenda->meeting->title }}
                                                <span
                                                    class="block text-xs font-normal text-gray-500 dark:text-slate-500">{{ $agenda->meeting->scheduled_date->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-gray-400 dark:text-slate-500 italic">Não agendado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                        @if ($agenda->priority === 'high') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20
                                        @elseif($agenda->priority === 'medium') bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20
                                        @else bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 @endif">
                                                {{ $agenda->priority_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                        @if ($agenda->status === 'approved') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20
                                        @elseif($agenda->status === 'rejected') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20
                                        @elseif($agenda->status === 'pending') bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-500 dark:border-yellow-500/20
                                        @else bg-gray-200 text-gray-600 border-gray-300 dark:text-slate-400 dark:border-slate-600 dark:bg-slate-800 @endif">
                                                {{ $agenda->status_display }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($agendas->hasPages())
                        <div
                            class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                            {{ $agendas->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-16 text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                            <x-icon name="file-lines" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma pauta encontrada</h3>
                        <p class="text-gray-500 dark:text-slate-400 mb-6">Você ainda não apresentou nenhuma pauta.</p>
                        <a href="{{ route('memberpanel.Diretoria.agendas.create') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-emerald-500/30 border border-transparent">
                            Criar Primeira Pauta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
