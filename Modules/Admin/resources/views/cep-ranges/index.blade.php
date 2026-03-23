@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero (padrão Configurações) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Logística</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Geolocalização</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Faixas de CEP</h1>
                    <p class="text-gray-300 max-w-xl">Gerencie a abrangência territorial do sistema. Defina regiões por UF, cidade e intervalo de CEP para entregas e validação de endereços.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.cep-ranges.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="plus" class="w-5 h-5 text-blue-600" />
                        Nova Faixa
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="layer-group" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Total de Faixas</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <x-icon name="flag" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Estados Cobertos</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ isset($stats['por_uf']) ? $stats['por_uf']->count() : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" action="{{ route('admin.cep-ranges.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="uf" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Estado (UF)</label>
                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="map" class="w-4 h-4" /></span>
                        <select name="uf" id="uf"
                            class="flex-1 min-w-0 py-2.5 pr-4 bg-transparent border-0 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-0 appearance-none cursor-pointer">
                            <option value="">Todos</option>
                            @foreach ($ufs as $uf)
                                <option value="{{ $uf }}" {{ request('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="cidade" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cidade</label>
                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="city" class="w-4 h-4" /></span>
                        <input type="text" name="cidade" id="cidade" value="{{ request('cidade') }}" placeholder="Buscar cidade..."
                            class="flex-1 min-w-0 py-2.5 pr-4 pl-0 bg-transparent border-0 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-0 placeholder:text-gray-400">
                    </div>
                </div>
                <div>
                    <label for="cep" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">CEP</label>
                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="magnifying-glass" class="w-4 h-4" /></span>
                        <input type="text" name="cep" id="cep" value="{{ request('cep') }}" data-mask="cep" placeholder="00000-000"
                            class="flex-1 min-w-0 py-2.5 pr-4 pl-0 bg-transparent border-0 text-gray-900 dark:text-white text-sm font-mono focus:outline-none focus:ring-0 placeholder:text-gray-400">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-sm hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        Filtrar
                    </button>
                    @if (request()->hasAny(['uf', 'cidade', 'cep']))
                        <a href="{{ route('admin.cep-ranges.index') }}" class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 transition-colors" title="Limpar filtros">
                            <x-icon name="xmark" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">
                                <a href="{{ route('admin.cep-ranges.index', array_merge(request()->all(), ['sort_by' => 'uf', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                    UF <x-icon name="sort" class="w-3 h-3 text-gray-400" />
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ route('admin.cep-ranges.index', array_merge(request()->all(), ['sort_by' => 'cidade', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                    Cidade <x-icon name="sort" class="w-3 h-3 text-gray-400" />
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faixa de CEP</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($cepRanges as $cepRange)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold text-xs">
                                        {{ $cepRange->uf }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $cepRange->cidade }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 font-mono text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg w-fit border border-gray-200 dark:border-gray-600">
                                        <span class="font-semibold">{{ \App\Services\CepService::formatar($cepRange->cep_de) }}</span>
                                        <x-icon name="arrow-right" style="solid" class="w-3 h-3 text-gray-400" />
                                        <span class="font-semibold">{{ \App\Services\CepService::formatar($cepRange->cep_ate) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($cepRange->tipo)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border
                                            {{ $cepRange->tipo === 'total' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800' : ($cepRange->tipo === 'urbano' ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800') }}">
                                            @if($cepRange->tipo === 'urbano') <x-icon name="city" class="w-3 h-3" /> @endif
                                            @if($cepRange->tipo === 'rural') <x-icon name="tractor" class="w-3 h-3" /> @endif
                                            @if($cepRange->tipo === 'total') <x-icon name="globe" class="w-3 h-3" /> @endif
                                            {{ ucfirst($cepRange->tipo) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs font-medium">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.cep-ranges.show', $cepRange) }}" class="p-2 rounded-xl text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Ver">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.cep-ranges.edit', $cepRange) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="Editar">
                                            <x-icon name="pen-to-square" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.cep-ranges.destroy', $cepRange) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta faixa de CEP?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                                <x-icon name="trash-can" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <x-icon name="map-location-dot" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <p class="text-gray-900 dark:text-white font-bold text-lg mb-1">Nenhuma faixa de CEP encontrada</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mx-auto">Cadastre uma nova região geográfica para começar a gerenciar a logística.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cepRanges->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $cepRanges->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
