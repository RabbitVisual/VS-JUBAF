@extends('memberpanel::components.layouts.master')

@section('title', 'Reuniões - ' . ($diretoria_display_name ?? 'Diretoria'))

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <!-- Hero Section -->
        <div
            class="relative bg-linear-to-r from-blue-900 to-blue-800 dark:from-slate-900 dark:to-slate-950 border-b border-blue-200 dark:border-blue-900/30 p-6 md:p-10 transition-colors duration-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <p class="text-blue-300 dark:text-blue-500 font-bold uppercase tracking-widest text-xs">Calendário
                        Oficial</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Reuniões do {{ $diretoria_display_name ?? 'Diretoria' }}
                    </h1>
                    <p class="text-blue-100 dark:text-slate-400 font-medium max-w-xl">
                        Acompanhe as reuniões agendadas, atas e deliberações.
                    </p>
                </div>
            </div>
        </div>

        <!-- Meetings List -->
        <div class="max-w-7xl mx-auto p-6">
            @if (session('success'))
                <div
                    class="mb-4 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                    <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mb-4 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                    <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                    {{ session('error') }}
                </div>
            @endif
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden transition-colors duration-200">
                @if ($meetings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                            <thead class="bg-gray-50 dark:bg-slate-900/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Data</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Reunião</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Local</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                @foreach ($meetings as $meeting)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700 dark:text-slate-300">
                                            {{ $meeting->scheduled_date->format('d/m/Y') }}
                                            <span
                                                class="block text-xs font-normal text-gray-500 dark:text-slate-500">{{ $meeting->scheduled_date->format('H:i') }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $meeting->title }}
                                            </div>
                                            @if ($meeting->description)
                                                <div
                                                    class="text-xs font-medium text-gray-500 dark:text-slate-500 mt-1 max-w-xs truncate">
                                                    {{ $meeting->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                                            {{ $meeting->location ?? 'Não definido' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                        @if ($meeting->status === 'scheduled') text-blue-600 bg-blue-50 border-blue-200 dark:text-blue-400 dark:border-blue-500/30 dark:bg-blue-500/10
                                        @elseif($meeting->status === 'in_progress') text-green-600 bg-green-50 border-green-200 dark:text-green-400 dark:border-green-500/30 dark:bg-green-500/10
                                        @elseif($meeting->status === 'completed') text-gray-500 bg-gray-100 border-gray-200 dark:text-slate-400 dark:border-slate-600 dark:bg-slate-800
                                        @else text-red-600 bg-red-50 border-red-200 dark:text-red-400 dark:border-red-500/30 dark:bg-red-500/10 @endif">
                                                {{ $meeting->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('memberpanel.Diretoria.meetings.show', $meeting) }}"
                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-bold text-gray-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                                                Ver Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($meetings->hasPages())
                        <div
                            class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                            {{ $meetings->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-16 text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                            <x-icon name="calendar" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma reunião encontrada</h3>
                        <p class="text-gray-500 dark:text-slate-400 mb-6">Não há reuniões agendadas no momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
