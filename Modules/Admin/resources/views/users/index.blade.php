@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Membros')

@php
    $levelColorMap = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-green-600 dark:text-green-400',
        'yellow' => 'text-yellow-600 dark:text-yellow-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        'red' => 'text-red-600 dark:text-red-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'cyan' => 'text-cyan-600 dark:text-cyan-400',
    ];
    $currentSortBy = request('sort_by', 'created_at');
    $currentSortDir = request('sort_dir', 'desc');
    $nextSortDir = $currentSortDir === 'asc' ? 'desc' : 'asc';
@endphp

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Membros</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Congregação</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Gerenciar Membros</h1>
                    <p class="text-gray-300 max-w-xl">Visualize e gerencie os membros da congregação. Use os filtros para buscar por nome, e-mail, CPF, função e status.</p>
                </div>
                <div class="flex-shrink-0 flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.users.import') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="upload" class="w-5 h-5" />
                        Importar
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="user-plus" class="w-5 h-5 text-blue-600" />
                        Novo Membro
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</p>
                        <p class="mt-1 text-3xl font-black text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="users" class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ativos</p>
                        <p class="mt-1 text-3xl font-black text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <x-icon name="check-circle" class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-red-50 dark:bg-red-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inativos</p>
                        <p class="mt-1 text-3xl font-black text-red-600 dark:text-red-400">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                        <x-icon name="circle-xmark" class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-cyan-50 dark:bg-cyan-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batizados</p>
                        <p class="mt-1 text-3xl font-black text-cyan-600 dark:text-cyan-400">{{ $stats['baptized'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                        <x-icon name="baptism" class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Taxa Ativa</p>
                        <p class="mt-1 text-3xl font-black text-purple-600 dark:text-purple-400">{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0 }}%</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <x-icon name="chart-pie" class="w-6 h-6" />
                    </div>
                </div>
            </div>
        </div>

        @if(isset($stats['by_role']) && $stats['by_role']->isNotEmpty())
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Por função</p>
                <div class="flex flex-wrap gap-3">
                    @foreach($stats['by_role'] as $byRole)
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $byRole->role->name ?? 'Sem função' }}: <strong>{{ $byRole->total }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative">
                <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
                    <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">
                    <div class="md:col-span-4">
                        <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon name="search" class="h-4 w-4 text-gray-400" />
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nome, e-mail, CPF..."
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition-all">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="role_id" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Função</label>
                        <select name="role_id" id="role_id" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todas</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="is_active" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <select name="is_active" id="is_active" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="is_baptized" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Batizado</label>
                        <select name="is_baptized" id="is_baptized" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_baptized') === '1' ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ request('is_baptized') === '0' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex flex-wrap items-end gap-2">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-colors">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'role_id', 'is_active', 'is_baptized']))
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium transition-colors">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($users->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                <div class="absolute right-0 top-0 w-40 h-40 bg-gray-50 dark:bg-gray-700/30 rounded-bl-full -mr-12 -mt-12"></div>
                <div class="relative overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($currentSortBy === 'name' ? $nextSortDir : 'asc')])) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Membro</a>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Função</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contato</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nível</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Família</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $user)
                                @php
                                    $level = $user->getGamificationLevel();
                                    $points = $user->getGamificationPoints();
                                    $levelColorClass = isset($level['color']) ? ($levelColorMap[$level['color']] ?? 'text-gray-600 dark:text-gray-400') : 'text-gray-600 dark:text-gray-400';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($user->photo)
                                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-sm" src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                                        {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors block">{{ $user->name }}</a>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->cpf ?? 'CPF não informado' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role->slug === 'admin' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">{{ $user->role->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        <div class="flex flex-col">
                                            <span>{{ $user->email }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->cellphone ?? $user->phone ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                        @if($user->is_baptized)
                                            <span class="inline-flex items-center text-[10px] text-cyan-600 dark:text-cyan-400 font-medium mt-1">
                                                <x-icon name="baptism" class="w-3 h-3 mr-1" /> Batizado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if(isset($level['icon']) && $level['icon'])
                                                <div class="{{ $levelColorClass }}">
                                                    <x-icon name="{{ $level['icon'] }}" class="w-5 h-5" />
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $level['name'] ?? '—' }}</div>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ $points }} pts</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($user->relationships_count > 0)
                                            <span class="inline-flex items-center justify-center gap-1 px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400" title="{{ $user->relationships_count }} vínculo(s)">
                                                <x-icon name="people-group" class="w-4 h-4" />
                                                <span class="text-xs font-bold">{{ $user->relationships_count }}</span>
                                            </span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Ver perfil">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="Editar">
                                                <x-icon name="pen-to-square" class="w-4 h-4" />
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este membro?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                                        <x-icon name="trash-can" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                <div class="relative">
                    <div class="w-20 h-20 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-6 text-blue-600 dark:text-blue-400">
                        <x-icon name="users" class="w-10 h-10" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum membro encontrado</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">Comece cadastrando os membros da sua congregação ou ajuste os filtros da busca.</p>
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                        <x-icon name="user-plus" class="w-5 h-5" />
                        Cadastrar Primeiro Membro
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
