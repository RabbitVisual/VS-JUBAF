@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Adicionar Membro</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Adicione um novo membro à diretoria da igreja.</p>
            </div>
            <a href="{{ route('admin.Diretoria.members.index') }}"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form id="createMemberForm" class="space-y-8">
                @csrf

                <!-- Member Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="user-add" class="w-5 h-5" />
                        </div>
                        Dados do Membro
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Selection -->
                        <div class="md:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Usuário <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="user_id" id="user_id" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Selecione um usuário para adicionar à diretoria</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <x-icon name="information-circle" class="w-3.5 h-3.5 mr-1" />
                                Selecione um usuário existente no sistema.
                            </p>
                        </div>

                        <!-- diretoria Position -->
                        <div>
                            <label for="diretoria_position"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Cargo na Diretoria <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="diretoria_position" id="diretoria_position" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                placeholder="Ex: Presidente, Secretário, Vogal">
                        </div>

                        <!-- diretoria Role -->
                        <div>
                            <label for="diretoria_role"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Função do Sistema <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="diretoria_role" id="diretoria_role" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Selecione a função</option>
                                    <option value="president">Presidente</option>
                                    <option value="vice_president">Vice-Presidente</option>
                                    <option value="secretary">Secretário</option>
                                    <option value="treasurer">Tesoureiro</option>
                                    <option value="lideranca">lideranca</option>
                                    <option value="deacon">Diácono</option>
                                    <option value="member">Membro</option>
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="term_start"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Data de Início <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="term_start" id="term_start" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="term_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Data de Término
                            </label>
                            <input type="date" name="term_end" id="term_end"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Deixe em branco para mandatos por
                                tempo indeterminado.</p>
                        </div>

                        <!-- Responsibilities -->
                        <div class="md:col-span-2">
                            <label for="responsibilities"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Responsabilidades
                            </label>
                            <textarea name="responsibilities" id="responsibilities" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                placeholder="Descreva as principais responsabilidades, se aplicável..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.Diretoria.members.index') }}"
                        class="px-6 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold transition-all text-center">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        Salvar Membro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('createMemberForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('{{ route('admin.Diretoria.members.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect ||
                        '{{ route('admin.Diretoria.members.index') }}';
                } else {
                    alert(result.message || 'Erro ao criar membro');
                }
            } catch (error) {
                console.error(error);
                alert('Erro ao processar solicitação');
            }
        });
    </script>
@endsection
