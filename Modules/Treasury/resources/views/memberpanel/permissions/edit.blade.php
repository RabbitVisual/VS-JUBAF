@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Editar Permissão')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-3xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar permissão</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Ajustar nível de acesso à Tesouraria.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.permissions.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">Voltar</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <form action="{{ route('memberpanel.treasury.permissions.update', $treasuryPermission) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-8 md:p-12 space-y-12">
                <!-- Section 1: User Identity (Read-Only) -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 font-black text-sm">01</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Colaborador Identificado</h3>
                    </div>

                    <div class="flex items-center gap-6 p-6 bg-slate-50 dark:bg-slate-800/40 rounded-4xl border border-slate-100 dark:border-slate-800/50 shadow-inner">
                        <div class="relative">
                            @if($treasuryPermission->user->photo)
                                <img src="{{ Storage::url($treasuryPermission->user->photo) }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white dark:ring-slate-900 shadow-md">
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-black text-2xl">
                                    {{ substr($treasuryPermission->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-[0.2em] mb-1">Membro Integrado</span>
                            <span class="text-xl font-black text-slate-900 dark:text-white leading-tight">{{ $treasuryPermission->user->name }}</span>
                            <span class="text-sm font-bold text-slate-400">{{ $treasuryPermission->user->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Scopes -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500 font-black text-sm">02</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Escopo de Atuação</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @php
                            $roles = [
                                ['id' => 'admin', 'name' => 'Administrador', 'desc' => 'Controle total. Acesso a configurações globais, auditoria e gestão de usuários.', 'icon' => 'crown', 'color' => 'purple'],
                                ['id' => 'editor', 'name' => 'Editor Operacional', 'desc' => 'Pode lançar entradas, ajustar metas e baixar relatórios. Sem acesso administrativo.', 'icon' => 'pen-to-square', 'color' => 'indigo'],
                                ['id' => 'viewer', 'name' => 'Visualizador', 'desc' => 'Acesso apenas para auditoria visual e geração de relatórios. Não pode alterar dados.', 'icon' => 'eye', 'color' => 'slate'],
                            ];
                        @endphp

                        @foreach($roles as $role)
                            <label class="relative flex items-center p-6 cursor-pointer rounded-4xl border-2 border-slate-100 dark:border-slate-800 hover:border-{{ $role['color'] }}-400 dark:hover:border-{{ $role['color'] }}-500 transition-all group overflow-hidden">
                                <input type="radio" name="permission_level" value="{{ $role['id'] }}" {{ old('permission_level', $treasuryPermission->permission_level) === $role['id'] ? 'checked' : '' }} class="sr-only peer">
                                <div class="absolute inset-0 bg-{{ $role['color'] }}-500/0 peer-checked:bg-{{ $role['color'] }}-500/[0.03] transition-colors"></div>

                                <div class="relative flex-1 flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-{{ $role['color'] }}-500 peer-checked:bg-{{ $role['color'] }}-500 peer-checked:text-white transition-all">
                                        <x-icon name="{{ $role['icon'] }}" style="duotone" class="w-6 h-6" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-black text-slate-900 dark:text-white group-hover:text-{{ $role['color'] }}-600 transition-colors">{{ $role['name'] }}</span>
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500">{{ $role['desc'] }}</span>
                                    </div>
                                </div>

                                <div class="relative w-6 h-6 rounded-full border-2 border-slate-200 dark:border-slate-700 peer-checked:border-{{ $role['color'] }}-500 peer-checked:bg-{{ $role['color'] }}-500 transition-all flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-white scale-0 peer-checked:scale-100 transition-transform"></div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Section 3: Fine-tuning -->
                <div class="space-y-8 bg-slate-50 dark:bg-slate-800/30 rounded-[3rem] p-8 md:p-12 border border-slate-100 dark:border-slate-800/50">
                    <div class="flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-[0.2em]">Privilégios Granulares</h3>
                            <p class="text-xs font-bold text-slate-400 italic">Personalize as capacidades específicas do colaborador de forma granular.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @php
                            $perms = [
                                ['id' => 'can_view_reports', 'label' => 'Acesso a Relatórios', 'icon' => 'file-chart-pie'],
                                ['id' => 'can_create_entries', 'label' => 'Realizar Lançamentos', 'icon' => 'square-plus'],
                                ['id' => 'can_edit_entries', 'label' => 'Editar Registros', 'icon' => 'pen-swirl'],
                                ['id' => 'can_delete_entries', 'label' => 'Remover Registros', 'icon' => 'trash-can'],
                            ];
                        @endphp

                        @foreach($perms as $p)
                            <label class="relative flex items-center p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 cursor-pointer group hover:shadow-lg hover:shadow-slate-200/40 transition-all duration-300">
                                <input type="checkbox" name="{{ $p['id'] }}" value="1" {{ old($p['id'], $treasuryPermission->{$p['id']}) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all mr-4">
                                    <x-icon name="{{ $p['icon'] }}" style="duotone" class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-black text-slate-700 dark:text-slate-300 transition-colors peer-checked:text-emerald-500">{{ $p['label'] }}</span>
                                <div class="ml-auto w-5 h-5 rounded border-2 border-slate-200 dark:border-slate-700 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 transition-all flex items-center justify-center">
                                    <x-icon name="check" class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" />
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-8 py-8 md:px-12 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-8">
                <a href="{{ route('memberpanel.treasury.permissions.index') }}"
                    class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-black uppercase tracking-widest text-xs transition-colors">
                    Manter Sem Alterar
                </a>
                <button type="submit"
                    class="inline-flex items-center px-12 py-5 bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white shadow-xl shadow-indigo-600/20 rounded-3xl font-black transition-all hover:-translate-y-1 active:scale-95">
                    <x-icon name="floppy-disks" style="duotone" class="w-5 h-5 mr-3" />
                    Salvar Alterações
                </button>
            </div>
        </form>
            </div>
        </div>
    </div>
@endsection
