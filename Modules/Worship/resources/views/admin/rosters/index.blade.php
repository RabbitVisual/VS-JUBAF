@extends('admin::components.layouts.master')

@section('title', 'Escalas de Louvor | Worship')

@section('content')
<div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-1.5">
                    <span>Módulo de Louvor</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500">Escalas</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">Escalas de <span class="text-transparent bg-clip-text bg-linear-to-r from-purple-600 to-blue-600 dark:from-purple-400 dark:to-blue-400">Louvor</span></h1>
            </div>
        </div>

    @if($setlists->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-white/5">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-white/5">
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Data</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Culto</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Músicos Escalados</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        @foreach ($setlists as $setlist)
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-all duration-300 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                                            <span class="text-lg font-black leading-none">{{ $setlist->scheduled_at->format('d') }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-tighter">{{ $setlist->scheduled_at->translatedFormat('M') }}</span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-400 dark:text-gray-500">
                                            {{ $setlist->scheduled_at->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-base font-black text-gray-900 dark:text-white leading-tight">
                                        {{ $setlist->title }}
                                    </div>
                                    <div class="text-xs font-bold text-gray-400 dark:text-gray-500 mt-1">
                                        Líder: {{ $setlist->leader->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        @foreach($setlist->roster->take(5) as $roster)
                                            <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900 overflow-hidden" title="{{ $roster->user->name }} ({{ $roster->instrument->name }})">
                                                @if($roster->user->photo)
                                                    <img src="{{ Storage::url($roster->user->photo) }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 text-[8px] font-black">
                                                        {{ substr($roster->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($setlist->roster->count() > 5)
                                            <div class="flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900 bg-gray-100 dark:bg-white/5 text-[8px] font-black text-gray-500">
                                                +{{ $setlist->roster->count() - 5 }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-2">
                                        {{ $setlist->roster->count() }} músico(s) escalado(s)
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <a href="{{ route('worship.admin.setlists.manage', $setlist->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl text-xs font-black uppercase tracking-widest border border-gray-100 dark:border-white/5 hover:shadow-xl hover:bg-gray-50 transition-all">
                                        <x-icon name="users" class="w-4 h-4 text-purple-600" />
                                        Gerenciar Escala
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 px-4">
                 {{ $setlists->links('pagination::tailwind') }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 px-4 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <x-icon name="users" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhuma escala encontrada</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Crie um culto primeiro para poder escalar os músicos.</p>
                <a href="{{ route('worship.admin.setlists.create') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold shadow-lg shadow-purple-500/20 transition-all">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Agendar Culto
                </a>
            </div>
        @endif
</div>
@endsection

