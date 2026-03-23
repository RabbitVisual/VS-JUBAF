@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Membro</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Atualize as informações do membro do conselho.</p>
        </div>
        <a href="{{ route('admin.churchcouncil.members.index') }}"
           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            Voltar
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form id="editMemberForm" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Member Information -->
            <div>
                 <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="user" class="w-5 h-5" />
                    </div>
                    Dados do Membro
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Info (Read-only) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Usuário Vinculado
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($member->user->photo)
                                    <img src="{{ asset('storage/' . $member->user->photo) }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-lg">
                                        {{ substr($member->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $member->user->name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Council Position -->
                    <div>
                        <label for="council_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Cargo no Conselho <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="council_position" id="council_position" required
                            value="{{ old('council_position', $member->council_position) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            placeholder="Ex: Presidente, Secretário">
                    </div>

                    <!-- Council Role -->
                    <div>
                        <label for="council_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Função do Sistema <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="council_role" id="council_role" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                <option value="president" {{ $member->council_role === 'president' ? 'selected' : '' }}>Presidente</option>
                                <option value="vice_president" {{ $member->council_role === 'vice_president' ? 'selected' : '' }}>Vice-Presidente</option>
                                <option value="secretary" {{ $member->council_role === 'secretary' ? 'selected' : '' }}>Secretário</option>
                                <option value="treasurer" {{ $member->council_role === 'treasurer' ? 'selected' : '' }}>Tesoureiro</option>
                                <option value="pastor" {{ $member->council_role === 'pastor' ? 'selected' : '' }}>Pastor</option>
                                <option value="deacon" {{ $member->council_role === 'deacon' ? 'selected' : '' }}>Diácono</option>
                                <option value="member" {{ $member->council_role === 'member' ? 'selected' : '' }}>Membro</option>
                            </select>
                            <x-icon name="chevron-down" class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="term_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Data de Início <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="term_start" id="term_start" required
                            value="{{ old('term_start', $member->term_start->format('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="term_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Data de Término
                        </label>
                        <input type="date" name="term_end" id="term_end"
                            value="{{ old('term_end', $member->term_end ? $member->term_end->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                         <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Deixe em branco se ainda estiver ativo.</p>
                    </div>

                     <!-- Responsibilities -->
                    <div class="md:col-span-2">
                        <label for="responsibilities" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Responsabilidades
                        </label>
                        <textarea name="responsibilities" id="responsibilities" rows="4"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            placeholder="Descreva as principais responsabilidades...">{{ old('responsibilities', $member->responsibilities) }}</textarea>
                    </div>

                    <!-- Active Status -->
                    <div class="md:col-span-2">
                         <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                            <input type="checkbox" name="is_active" value="1"
                                {{ $member->is_active ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-gray-700">
                             <div class="ml-3">
                                <span class="block text-sm font-bold text-gray-900 dark:text-white">Membro Ativo</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Desmarcar esta opção ocultará o membro das listas principais.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.churchcouncil.members.index') }}"
                   class="px-6 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold transition-all text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('editMemberForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    data.is_active = document.querySelector('input[name="is_active"]').checked ? 1 : 0;

    try {
        const response = await fetch('{{ route("admin.churchcouncil.members.update", $member) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect || '{{ route("admin.churchcouncil.members.index") }}';
        } else {
            alert(result.message || 'Erro ao atualizar membro');
        }
    } catch (error) {
        console.error(error);
        alert('Erro ao processar solicitação');
    }
});
</script>
@endsection

