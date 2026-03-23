@extends('admin::components.layouts.master')

@section('title', 'Cultos e Escalas | Worship')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-1.5">
                <a href="{{ route('worship.admin.dashboard') }}" class="hover:underline">Módulo de Louvor</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-400 dark:text-gray-500">Agendamentos</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Cultos e <span class="text-transparent bg-clip-text bg-linear-to-r from-purple-600 to-blue-600 dark:from-purple-400 dark:to-blue-400">Escalas</span></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Agende cultos, monte o repertório e projeta letras na tela.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.projection.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="presentation-screen" class="w-5 h-5 mr-2" />
                Projeção
            </a>
            <a href="{{ route('worship.admin.setlists.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-lg shadow-purple-500/20 transition-all active:scale-95">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Agendar Culto
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden">
        <div class="absolute top-0 left-0 -ml-16 -mt-16 w-64 h-64 bg-purple-600/5 rounded-full blur-3xl" aria-hidden="true"></div>
        <form action="{{ route('worship.admin.setlists.index') }}" method="GET" class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome do evento</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <x-icon name="search" class="h-5 w-5" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Culto de Celebração..."
                        class="block w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white">
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-bold transition-colors shadow-lg shadow-purple-500/20">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'date']))
                    <a href="{{ route('worship.admin.setlists.index') }}" class="px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center" title="Limpar filtros">
                        <x-icon name="x" class="w-5 h-5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($setlists->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data & Horário</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Evento & Dirigente</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach ($setlists as $setlist)
                        <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-gray-600 shrink-0">
                                        <span class="text-base font-black leading-none text-gray-900 dark:text-white">{{ $setlist->scheduled_at->format('d') }}</span>
                                        <span class="text-[9px] font-bold uppercase text-gray-500 dark:text-gray-400">{{ $setlist->scheduled_at->translatedFormat('M') }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400">{{ $setlist->scheduled_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl border-2 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        @if($setlist->leader && $setlist->leader->photo)
                                            <img src="{{ Storage::url($setlist->leader->photo) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-sm font-black text-gray-600 dark:text-gray-300 uppercase">{{ Str::limit($setlist->leader->name ?? '?', 1, '') }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $setlist->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Líder: {{ $setlist->leader->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $setlist->status->color() }}-100 text-{{ $setlist->status->color() }}-800 dark:bg-{{ $setlist->status->color() }}-900/30 dark:text-{{ $setlist->status->color() }}-400 border border-{{ $setlist->status->color() }}-200 dark:border-{{ $setlist->status->color() }}-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $setlist->status->color() }}-500 mr-1.5 {{ $setlist->status->value === 'live' ? 'animate-pulse' : '' }}"></span>
                                        {{ $setlist->status->label() }}
                                        <x-icon name="chevron-down" class="ml-1 w-3 h-3 opacity-60" />
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak
                                        class="absolute left-0 mt-2 w-44 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100">
                                        @foreach(\Modules\Worship\App\Enums\SetlistStatus::cases() as $status)
                                            <form action="{{ route('worship.admin.setlists.updateStatus', $setlist->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $status->value }}">
                                                <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-center justify-between">
                                                    <span>{{ $status->label() }}</span>
                                                    @if($setlist->status === $status)
                                                        <x-icon name="check" class="w-4 h-4 text-{{ $status->color() }}-500" />
                                                    @endif
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('worship.admin.setlists.manage', $setlist->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold transition-colors">
                                        <x-icon name="cog" class="w-4 h-4" />
                                        Gerenciar
                                    </a>
                                    <a href="{{ route('admin.projection.console', $setlist->id) }}" target="_blank" rel="noopener" class="p-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 transition-colors" title="Console de Projeção">
                                        <x-icon name="presentation-screen" class="w-5 h-5" />
                                    </a>
                                    <form action="{{ route('worship.admin.setlists.destroy', $setlist->id) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este culto e todo o repertório?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors" title="Excluir">
                                            <x-icon name="trash" class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $setlists->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 px-6 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-5">
                <x-icon name="calendar" class="w-8 h-8 text-purple-600 dark:text-purple-400" />
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">Nenhum culto agendado</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Crie um culto para montar o repertório e usar o console de projeção na hora do louvor.</p>
            <a href="{{ route('worship.admin.setlists.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-lg shadow-purple-500/20 transition-all">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Agendar primeiro culto
            </a>
        </div>
    @endif
</div>
@endsection
