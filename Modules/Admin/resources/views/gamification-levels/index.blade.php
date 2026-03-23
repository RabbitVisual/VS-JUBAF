@extends('admin::components.layouts.master')

@section('title', 'Níveis de Gamificação')

@php
    $colorMap = [
        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400'],
        'yellow' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400'],
        'red' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400'],
        'gray' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400'],
    ];
@endphp

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Gamificação</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Níveis</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Níveis de Gamificação</h1>
                <p class="text-gray-300 max-w-xl">Gerencie os níveis do sistema de gamificação. Os níveis definem a progressão de pontos dos membros no painel e na EBD.</p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.gamification-levels.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="plus" class="w-5 h-5 text-amber-600" />
                    Criar Nível
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex items-center gap-3">
            <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Dica -->
    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4 flex items-start gap-3">
        <x-icon name="information-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
        <p class="text-sm text-amber-800 dark:text-amber-200">Os níveis definem a progressão de pontos dos membros. A ordem de exibição pode ser alterada no campo <strong>Ordem</strong> ao editar cada nível.</p>
    </div>

    @if($levels->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nível</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pontos</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($levels as $level)
                            @php $colors = $colorMap[$level->color] ?? $colorMap['gray']; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl {{ $colors['bg'] }} flex items-center justify-center {{ $colors['text'] }}">
                                            <x-icon name="{{ $level->icon }}" class="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $level->name }}</div>
                                            @if($level->description)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($level->description, 40) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $level->points_min }} @if($level->points_max) – {{ $level->points_max }} @else + @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($level->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.gamification-levels.edit', $level) }}" class="p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Editar">
                                            <x-icon name="pen-to-square" class="w-5 h-5" />
                                        </a>
                                        <form action="{{ route('admin.gamification-levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este nível?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                                <x-icon name="trash-can" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-6 text-amber-600 dark:text-amber-400">
                    <x-icon name="trophy" class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum nível criado</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">Comece criando níveis para o sistema de gamificação. Os membros acumulam pontos e sobem de nível conforme a participação.</p>
                <a href="{{ route('admin.gamification-levels.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-lg shadow-amber-500/20 transition-all">
                    <x-icon name="plus" class="w-5 h-5" />
                    Criar Primeiro Nível
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
