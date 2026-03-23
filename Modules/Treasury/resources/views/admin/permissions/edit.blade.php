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
                            <span class="px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Atribuição</h1>
                        <p class="text-gray-300 max-w-xl">Ajuste as capacidades de acesso para {{ $treasuryPermission->user->name }}.</p>
                    </div>
                    <a href="{{ route('treasury.permissions.index') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                        <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                    </a>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Permissões' => route('treasury.permissions.index'), 'Editar' => null]])
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-2">
                <x-icon name="pen-to-square" style="duotone" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Ajuste de Configurações</h3>
            </div>
            <form action="{{ route('treasury.permissions.update', $treasuryPermission) }}" method="POST" class="p-6 space-y-8" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Atualizando...' } }))">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Colaborador</label>
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                            @if($treasuryPermission->user->photo)
                                <img src="{{ Storage::url($treasuryPermission->user->photo) }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500/20 shadow-sm" alt="">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl">
                                    {{ substr($treasuryPermission->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $treasuryPermission->user->name }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $treasuryPermission->user->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="permission_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nível <span class="text-red-500">*</span></label>
                        <select name="permission_level" id="permission_level" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="viewer" {{ old('permission_level', $treasuryPermission->permission_level) === 'viewer' ? 'selected' : '' }}>Visualizador</option>
                            <option value="editor" {{ old('permission_level', $treasuryPermission->permission_level) === 'editor' ? 'selected' : '' }}>Editor</option>
                            <option value="admin" {{ old('permission_level', $treasuryPermission->permission_level) === 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">A alteração do nível pode redefinir capacidades abaixo.</p>
                        @error('permission_level')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-6">Capacidades Específicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach([
                            ['name' => 'can_view_reports', 'label' => 'Ver Relatórios', 'icon' => 'chart-line', 'value' => $treasuryPermission->can_view_reports],
                            ['name' => 'can_create_entries', 'label' => 'Lançar Transações', 'icon' => 'plus-circle', 'value' => $treasuryPermission->can_create_entries],
                            ['name' => 'can_edit_entries', 'label' => 'Editar Transações', 'icon' => 'pencil', 'value' => $treasuryPermission->can_edit_entries],
                            ['name' => 'can_delete_entries', 'label' => 'Excluir Transações', 'icon' => 'trash', 'value' => $treasuryPermission->can_delete_entries],
                            ['name' => 'can_manage_campaigns', 'label' => 'Gerir Campanhas', 'icon' => 'bullhorn', 'value' => $treasuryPermission->can_manage_campaigns],
                            ['name' => 'can_manage_goals', 'label' => 'Gerir Metas', 'icon' => 'bullseye-arrow', 'value' => $treasuryPermission->can_manage_goals],
                            ['name' => 'can_export_data', 'label' => 'Exportar Dados', 'icon' => 'file-export', 'value' => $treasuryPermission->can_export_data],
                        ] as $sw)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-600">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-white dark:bg-gray-700">
                                        <x-icon name="{{ $sw['icon'] }}" style="duotone" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $sw['label'] }}</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $sw['name'] }}" value="1" {{ old($sw['name'], $sw['value']) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('treasury.permissions.index') }}" class="px-6 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="px-8 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/20 transition-all inline-flex items-center gap-2">
                        <x-icon name="check" style="duotone" class="w-4 h-4" /> Atualizar Acessos
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
