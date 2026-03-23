@extends('pastoralpanel::components.layouts.master')

@section('title', 'Ministérios')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Ministérios</h1>
                    <p class="text-slate-300 text-sm md:text-base">Visão dos ministérios da congregação e planos estratégicos.</p>
                </div>
                <a href="{{ route('pastor.ministerios.plans.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="clipboard-list" class="w-5 h-5" />
                    Planos
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ministérios</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ativos</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aceitando voluntários</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inativos</p>
                <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1">{{ $stats['inactive'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pausados</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Membros</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['total_members'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Voluntários totais</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendentes</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['pending_approvals'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aguardando aprovação</p>
            </div>
        </div>

        @if($ministries->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ministério</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Liderança</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Membros</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @php
                                $ministryColorClasses = [
                                    'blue' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800/50',
                                    'green' => 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-800/50',
                                    'red' => 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800/50',
                                    'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-100 dark:border-yellow-800/50',
                                    'purple' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-100 dark:border-purple-800/50',
                                    'pink' => 'bg-pink-50 dark:bg-pink-900/20 border-pink-100 dark:border-pink-800/50',
                                    'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800/50',
                                ];
                            @endphp
                            @foreach($ministries as $m)
                                @php $colorClass = $ministryColorClasses[$m->color ?? 'blue'] ?? $ministryColorClasses['blue']; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl flex items-center justify-center border {{ $colorClass }}">
                                                @if($m->icon && \Str::startsWith($m->icon, 'fa:'))
                                                    <x-icon name="{{ \Str::after($m->icon, 'fa:') }}" class="w-5 h-5 text-current" />
                                                @else
                                                    <span class="text-xl">{{ $m->icon ?? '⛪' }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $m->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">{{ $m->description ?? 'Sem descrição' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        @if($m->leader) {{ $m->leader->name }} @endif
                                        @if($m->coLeader) <span class="text-gray-400">/</span> {{ $m->coLeader->name }} @endif
                                        @if(!$m->leader && !$m->coLeader) <span class="text-gray-400 italic">—</span> @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $m->active_members_count }}</span>
                                        @if($m->max_members) <span class="text-xs text-gray-500">/ {{ $m->max_members }}</span> @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $m->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                            {{ $m->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('pastor.ministerios.show', $m) }}" class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 hover:underline font-medium text-sm">
                                            <x-icon name="eye" class="w-4 h-4" /> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($ministries->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                        {{ $ministries->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                <x-icon name="church" class="w-12 h-12 text-gray-300 dark:text-slate-600 mx-auto mb-4" />
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum ministério cadastrado</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Os ministérios são configurados no painel administrativo.</p>
            </div>
        @endif
    </div>
@endsection
