@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-purple-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold uppercase tracking-wider">Acesso</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Permissões da Tesouraria</h1>
                        <p class="text-gray-300 max-w-xl">Controle quem pode visualizar e gerenciar entradas, campanhas, metas e relatórios.</p>
                    </div>
                    <a href="{{ route('treasury.permissions.create') }}"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                        <x-icon name="plus" style="duotone" class="w-5 h-5 text-purple-600 mr-2" />
                        Nova Atribuição
                    </a>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Permissões' => null]])
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Responsável</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Papel</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Capacidades</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($permissions as $perm)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($perm->user->photo)
                                            <img src="{{ Storage::url($perm->user->photo) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500/20 shadow-sm" alt="">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                                                {{ substr($perm->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $perm->user->name }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $perm->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase
                                        {{ $perm->permission_level === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : ($perm->permission_level === 'editor' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400') }}">
                                        {{ $perm->permission_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5 max-w-sm">
                                        @if ($perm->can_view_reports) <span class="px-1.5 py-0.5 rounded bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-bold uppercase border border-green-100 dark:border-green-800/30">Relatórios</span> @endif
                                        @if ($perm->can_create_entries) <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-bold uppercase border border-blue-100 dark:border-blue-800/30">Criar</span> @endif
                                        @if ($perm->can_edit_entries) <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase border border-amber-100 dark:border-amber-800/30">Editar</span> @endif
                                        @if ($perm->can_delete_entries) <span class="px-1.5 py-0.5 rounded bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-bold uppercase border border-red-100 dark:border-red-800/30">Excluir</span> @endif
                                        @if ($perm->can_manage_campaigns) <span class="px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 text-xs font-bold uppercase border border-indigo-100 dark:border-indigo-800/30">Campanhas</span> @endif
                                        @if ($perm->can_manage_goals) <span class="px-1.5 py-0.5 rounded bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400 text-xs font-bold uppercase border border-pink-100 dark:border-pink-800/30">Metas</span> @endif
                                        @if ($perm->can_export_data) <span class="px-1.5 py-0.5 rounded bg-slate-50 dark:bg-slate-700/30 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase border border-slate-200 dark:border-slate-600">Exportar</span> @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('treasury.permissions.edit', $perm) }}" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all" title="Editar">
                                            <x-icon name="pencil" style="duotone" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('treasury.permissions.destroy', $perm) }}" method="POST" class="inline-block"
                                            onsubmit="if(confirm('Remover o acesso deste usuário à tesouraria?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show')); return true; } return false;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all" title="Remover">
                                                <x-icon name="trash" style="duotone" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-6">
                                            <x-icon name="users" style="duotone" class="w-10 h-10 text-gray-300 dark:text-gray-600" />
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma permissão especial</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-8">Defina quem poderá operar a tesouraria além dos administradores.</p>
                                        <a href="{{ route('treasury.permissions.create') }}"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-gray-800 dark:hover:bg-blue-700 transition-all">
                                            <x-icon name="plus" style="duotone" class="w-4 h-4" /> Criar primeira atribuição
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($permissions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
