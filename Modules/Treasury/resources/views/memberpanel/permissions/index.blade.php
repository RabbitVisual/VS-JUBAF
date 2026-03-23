@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Permissões')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Permissões</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Controle de acesso ao módulo Tesouraria.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    @if(isset($currentUserPermission) && ($currentUserPermission->isAdmin() || $currentUserPermission->canManagePermissions()))
                        <a href="{{ route('memberpanel.treasury.permissions.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                            <x-icon name="user-plus" style="duotone" class="w-4 h-4 mr-2" />
                            Atribuir permissão
                        </a>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 z-10">
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl">Gerencie hierarquias e permissões para as operações financeiras.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="users" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Colaboradores e níveis</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50">
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Colaborador</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Nível</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Privilégios</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($permissions as $perm)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="relative flex-shrink-0">
                                        @if($perm->user->photo)
                                            <img src="{{ Storage::url($perm->user->photo) }}" class="h-12 w-12 rounded-2xl object-cover ring-2 ring-slate-100 dark:ring-slate-800 shadow-sm transition-transform group-hover:scale-105" alt="{{ $perm->user->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-2xl bg-linear-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black transition-transform group-hover:scale-105">
                                                {{ substr($perm->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $perm->user->name }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 truncate max-w-[150px]">{{ $perm->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider
                                {{ $perm->permission_level === 'admin'
                                    ? 'bg-purple-50 dark:bg-purple-950/30 text-purple-600 border border-purple-100 dark:border-purple-800'
                                    : ($perm->permission_level === 'editor'
                                        ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 border border-blue-100 dark:border-blue-800'
                                        : 'bg-slate-50 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700') }}">
                                    <x-icon name="{{ $perm->permission_level === 'admin' ? 'crown' : ($perm->permission_level === 'editor' ? 'pen-nib' : 'eye') }}" style="duotone" class="w-3 h-3 mr-2" />
                                    {{ $perm->permission_level === 'admin' ? 'Administrador' : ($perm->permission_level === 'editor' ? 'Editor' : 'Visualizador') }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $badges = [
                                            ['check' => $perm->can_view_reports, 'label' => 'Relatórios', 'color' => 'indigo'],
                                            ['check' => $perm->can_create_entries, 'label' => 'Lançar', 'color' => 'emerald'],
                                            ['check' => $perm->can_edit_entries, 'label' => 'Ajustar', 'color' => 'amber'],
                                            ['check' => $perm->can_delete_entries, 'label' => 'Remover', 'color' => 'rose'],
                                        ];
                                        $hasAny = false;
                                    @endphp
                                    @foreach($badges as $badge)
                                        @if($badge['check'])
                                            @php $hasAny = true; @endphp
                                            <span class="px-2 py-1 bg-{{ $badge['color'] }}-500/5 text-{{ $badge['color'] }}-600 dark:text-{{ $badge['color'] }}-400 text-[9px] font-black uppercase tracking-widest rounded-lg border border-{{ $badge['color'] }}-500/10">
                                                {{ $badge['label'] }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if(!$hasAny)
                                        <span class="text-[10px] font-bold text-slate-300 italic">Consulta restrita</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                @if(isset($currentUserPermission) && ($currentUserPermission->isAdmin() || $currentUserPermission->canManagePermissions()))
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity translate-x-4 group-hover:translate-x-0 transition-transform">
                                    <a href="{{ route('memberpanel.treasury.permissions.edit', $perm) }}"
                                        class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600 rounded-xl transition-all" title="Ajustar Acesso">
                                        <x-icon name="sliders" style="duotone" class="w-4 h-4" />
                                    </a>
                                    <form action="{{ route('memberpanel.treasury.permissions.destroy', $perm) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('ATENÇÃO: Deseja revogar permanentemente o acesso deste colaborador?')"
                                            class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 rounded-xl transition-all" title="Revogar">
                                            <x-icon name="user-minus" style="duotone" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6 ring-1 ring-slate-100 dark:ring-slate-800">
                                    <x-icon name="users-slash" style="duotone" class="w-10 h-10 text-slate-300" />
                                </div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Sem Colaboradores</h3>
                                <p class="text-slate-400 font-medium max-w-xs mx-auto text-sm">Nenhum nível de acesso foi configurado para o módulo financeiro até o momento.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                </div>
            @if ($permissions->hasPages())
                <div class="px-8 py-6 border-t border-gray-100 dark:border-slate-800">
                    {{ $permissions->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>
@endsection
