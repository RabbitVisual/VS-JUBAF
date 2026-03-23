@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Ministérios')

@section('content')
    <div class="space-y-8">
        <!-- Hero Header (padrão configuração) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Admin</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Congregação</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Ministérios</h1>
                    <p class="text-gray-300 max-w-xl">Visualize, adicione e gerencie os ministérios da congregação. Planos estratégicos e semáforo no conselho.</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('admin.ministries.plans.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all shadow-sm inline-flex items-center gap-2">
                        <x-icon name="clipboard-list" class="w-5 h-5" />
                        Planos
                    </a>
                    <a href="{{ route('admin.churchcouncil.ministries.dashboard') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all shadow-sm inline-flex items-center gap-2">
                        <x-icon name="chart-line" class="w-5 h-5" />
                        Semáforo
                    </a>
                    <a href="{{ route('admin.ministries.create') }}"
                        class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10 inline-flex items-center gap-2">
                        <x-icon name="plus" class="w-5 h-5 text-blue-600" />
                        Novo Ministério
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Total</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        Ministérios cadastrados
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Ativos</p>
                    <h3 class="text-3xl font-black text-green-600 dark:text-green-400 mt-2">{{ $stats['active'] }}</h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                        Aceitando voluntários
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-red-50 dark:bg-red-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Inativos</p>
                    <h3 class="text-3xl font-black text-red-600 dark:text-red-400 mt-2">{{ $stats['inactive'] }}</h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                        Pausados
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Membros</p>
                    <h3 class="text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-2">{{ $stats['total_members'] }}</h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                        Voluntários totais
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pendentes</p>
                    <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $stats['pending_approvals'] }}</h3>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                        Aguardando aprovação
                    </div>
                </div>
            </div>
        </div>

        @if($ministries->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                <div class="overflow-x-auto -mx-8">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ministério</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Liderança</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Membros</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
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
                            @foreach($ministries as $ministry)
                                @php $colorClass = $ministryColorClasses[$ministry->color ?? 'blue'] ?? $ministryColorClasses['blue']; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-xl flex items-center justify-center border shadow-sm group-hover:scale-110 transition-transform duration-300 {{ $colorClass }}">
                                                @if($ministry->icon && \Str::startsWith($ministry->icon, 'fa:'))
                                                    <x-icon name="{{ \Str::after($ministry->icon, 'fa:') }}" class="w-6 h-6 text-current" />
                                                @else
                                                    <span class="text-2xl">{{ $ministry->icon ?? '⛪' }}</span>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $ministry->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                                                    {{ $ministry->description ?? 'Sem descrição' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @if($ministry->leader)
                                                <div class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-2"></span>
                                                    {{ $ministry->leader->name }}
                                                </div>
                                            @endif
                                            @if($ministry->coLeader)
                                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="w-1.2 h-1.2 rounded-full bg-blue-400 mr-2 ml-0.5" style="width: 4px; height: 4px;"></span>
                                                    {{ $ministry->coLeader->name }}
                                                </div>
                                            @endif
                                            @if(!$ministry->leader && !$ministry->coLeader)
                                                <span class="text-xs text-gray-400 italic">Sem liderança definida</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $ministry->active_members_count }}</span>
                                            @if($ministry->max_members)
                                                <span class="text-[10px] text-gray-500">limite: {{ $ministry->max_members }}</span>
                                            @else
                                                <span class="text-[10px] text-gray-400">ilimitado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $ministry->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $ministry->is_active ? 'bg-green-600' : 'bg-gray-600' }}"></span>
                                            {{ $ministry->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('admin.ministries.show', $ministry) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Ver Detalhes">
                                                <x-icon name="eye" class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.ministries.edit', $ministry) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Editar">
                                                <x-icon name="pencil" class="w-5 h-5" />
                                            </a>
                                            <form action="{{ route('admin.ministries.destroy', $ministry) }}" method="POST" class="inline" onsubmit="if(confirm('ATENÇÃO: Deseja realmente excluir este ministério? Esta ação não pode ser desfeita.')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Excluindo...' } })); return true; } return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Excluir">
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

                @if($ministries->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/20 border-t border-gray-200 dark:border-gray-700 mt-4">
                        {{ $ministries->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                @endif
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mb-6">
                    <x-icon name="church" class="w-12 h-12 text-blue-500 dark:text-blue-400" />
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Nenhum ministério cadastrado</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-md mb-8 font-medium">Comece criando o primeiro ministério para organizar as atividades e membros da sua igreja.</p>
                <a href="{{ route('admin.ministries.create') }}" class="inline-flex items-center px-8 py-4 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                    <x-icon name="plus" class="w-6 h-6 mr-2" />
                    Criar primeiro ministério
                </a>
            </div>
        @endif
    </div>
@endsection

