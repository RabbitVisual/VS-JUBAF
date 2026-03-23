@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Membros do Conselho</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie os membros do conselho da igreja e suas atribuições.</p>
        </div>
        <a href="{{ route('admin.churchcouncil.members.create') }}"
           class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Adicionar Membro
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.churchcouncil.members.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label for="role" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                <select name="role" id="role" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos os cargos</option>
                    <option value="president" {{ request('role') === 'president' ? 'selected' : '' }}>Presidente</option>
                    <option value="vice_president" {{ request('role') === 'vice_president' ? 'selected' : '' }}>Vice-Presidente</option>
                    <option value="secretary" {{ request('role') === 'secretary' ? 'selected' : '' }}>Secretário</option>
                    <option value="treasurer" {{ request('role') === 'treasurer' ? 'selected' : '' }}>Tesoureiro</option>
                    <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Membro</option>
                    <option value="pastor" {{ request('role') === 'pastor' ? 'selected' : '' }}>Pastor</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                        placeholder="Nome do membro...">
                    <x-icon name="search" class="w-5 h-5 absolute right-3 top-2.5 text-gray-400" />
                </div>
            </div>

            <div class="md:col-span-3 flex items-end">
                <div class="flex gap-2 w-full">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center">
                        <x-icon name="filter" class="w-5 h-5 mr-2" />
                        Filtrar
                    </button>
                    @if (request()->hasAny(['role', 'status', 'search']))
                        <a href="{{ route('admin.churchcouncil.members.index') }}"
                           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                            <x-icon name="x" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Membro</th>
                        <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Cargo</th>
                        <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Status</th>
                        <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Período</th>
                        <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if ($member->user->photo)
                                        <img class="h-12 w-12 rounded-xl object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                            src="{{ asset('storage/' . $member->user->photo) }}"
                                            alt="{{ $member->user->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-linear-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center ring-2 ring-gray-50 dark:ring-gray-700">
                                            <span class="text-lg font-bold text-gray-600 dark:text-gray-300">
                                                {{ substr($member->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $member->user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->role_display }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $member->council_position }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                {{ $member->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $member->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $member->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900 dark:text-white font-medium">{{ $member->term_start->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if ($member->term_end)
                                    até {{ $member->term_end->format('d/m/Y') }}
                                @else
                                    Indeterminado
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.churchcouncil.members.edit', $member) }}"
                                   class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                   title="Editar">
                                    <x-icon name="pencil" class="w-5 h-5" />
                                </a>
                                <form method="POST" action="{{ route('admin.churchcouncil.members.destroy', $member) }}"
                                      onsubmit="return confirm('Tem certeza que deseja remover este membro do conselho?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        title="Remover">
                                        <x-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-icon name="users" class="w-10 h-10 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhum membro encontrado</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                @if (request()->hasAny(['role', 'status', 'search']))
                                    Não foram encontrados membros com os filtros aplicados. Tente simplificar sua busca.
                                @else
                                    O conselho ainda não possui membros registrados. Comece adicionando o primeiro membro.
                                @endif
                            </p>
                            @if (request()->hasAny(['role', 'status', 'search']))
                                <div class="mt-6">
                                    <a href="{{ route('admin.churchcouncil.members.index') }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        Limpar Filtros
                                    </a>
                                </div>
                            @else
                                <div class="mt-6">
                                    <a href="{{ route('admin.churchcouncil.members.create') }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-blue-500/30">
                                        <x-icon name="plus" class="w-5 h-5 mr-2" />
                                        Adicionar Primeiro Membro
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
        <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-100 dark:border-gray-700 border-dashed">
            {{ $members->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

