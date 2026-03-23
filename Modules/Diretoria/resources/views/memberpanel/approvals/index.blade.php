@extends('memberpanel::components.layouts.master')

@section('title', 'Aprovações - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <!-- Hero Section -->
        <div
            class="relative bg-linear-to-r from-amber-600 to-amber-500 dark:from-slate-900 dark:to-slate-950 border-b border-amber-200 dark:border-amber-900/30 p-6 md:p-10 transition-colors duration-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <p class="text-amber-100 dark:text-amber-500 font-bold uppercase tracking-widest text-xs">Solicitações
                    </p>
                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Minhas Aprovações
                    </h1>
                    <p class="text-amber-50 dark:text-slate-400 font-medium max-w-xl">
                        Veja todas as suas solicitações de aprovação e acompanhe o andamento.
                    </p>
                </div>

                <button onclick="showApprovalRequestModal()"
                    class="group relative inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500 text-amber-600 dark:text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-xl dark:hover:shadow-amber-500/20 hover:-translate-y-0.5 border border-amber-200 dark:border-amber-500/50">
                    <x-icon name="plus"
                        class="w-5 h-5 mr-2 text-amber-600 dark:text-white group-hover:scale-110 transition-transform" />
                    Solicitar Aprovação
                </button>
            </div>
        </div>

        <!-- Approvals List -->
        <div class="max-w-7xl mx-auto p-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden transition-colors duration-200">
                @if ($approvals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                            <thead class="bg-gray-50 dark:bg-slate-900/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Tipo</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Solicitação</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                @foreach ($approvals as $approval)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                                {{ $approval->approval_type_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                                {{ $approval->title }}
                                            </div>
                                            <div class="flex flex-col gap-0.5 mt-1">
                                                <div
                                                    class="text-xs font-medium text-gray-500 dark:text-slate-500 mt-0.5 max-w-xs truncate">
                                                    {{ Str::limit($approval->description, 60) }}
                                                </div>
                                                <span
                                                    class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-slate-600">Solicitado
                                                    em {{ $approval->submitted_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider w-fit border
                                            @if ($approval->status === 'pending') bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/30
                                            @elseif($approval->status === 'approved') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30
                                            @elseif($approval->status === 'rejected') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30
                                            @else bg-gray-100 text-gray-600 border-gray-200 dark:text-slate-400 dark:border-slate-600 dark:bg-slate-800 @endif">
                                                    {{ $approval->status_display }}
                                                </span>
                                                @if ($approval->is_expired)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 dark:text-red-400 dark:border-red-500/30 dark:bg-red-500/10 uppercase tracking-wider w-fit">
                                                        Expirada
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('memberpanel.Diretoria.approvals.show', $approval) }}"
                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-bold text-gray-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                                                Ver Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($approvals->hasPages())
                        <div
                            class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                            {{ $approvals->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-16 text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                            <x-icon name="clipboard-check" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma solicitação encontrada</h3>
                        <p class="text-gray-500 dark:text-slate-400 mb-6">Você ainda não fez nenhuma solicitação de
                            aprovação.</p>
                        <button onclick="showApprovalRequestModal()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 hover:bg-amber-700 dark:hover:bg-amber-500 text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:shadow-amber-500/30 border border-transparent">
                            Fazer Primeira Solicitação
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Approval Request Modal -->
        <div id="approval-modal"
            class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm overflow-y-auto h-full w-full hidden"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div
                class="relative top-20 mx-auto p-0 border border-gray-200 dark:border-slate-700 w-full max-w-md shadow-2xl rounded-xl bg-white dark:bg-slate-900 transition-colors duration-200">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Solicitar Aprovação</h3>
                    <button onclick="closeApprovalRequestModal()"
                        class="text-gray-400 hover:text-gray-600 dark:text-slate-400 dark:hover:text-white transition-colors">
                        <x-icon name="xmark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6">
                    <form id="approval-form" method="POST" action="{{ route('memberpanel.Diretoria.submit-approval') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="approval_type"
                                    class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    Tipo de Aprovação
                                </label>
                                <select name="approval_type" id="approval_type" required
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                    <option value="">Selecione o tipo</option>
                                    <option value="budget">Orçamento</option>
                                    <option value="project">Projeto</option>
                                    <option value="personnel">Pessoal</option>
                                    <option value="policy">Política</option>
                                    <option value="facility">Instalação</option>
                                    <option value="other">Outro</option>
                                </select>
                            </div>

                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    Título
                                </label>
                                <input type="text" name="title" id="title" required
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    Descrição
                                </label>
                                <textarea name="description" id="description" rows="4" required
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"></textarea>
                            </div>

                            <div>
                                <label for="amount"
                                    class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    Valor (se aplicável)
                                </label>
                                <input type="number" name="amount" id="amount" step="0.01"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-8">
                            <button type="button" onclick="closeApprovalRequestModal()"
                                class="px-4 py-2 border border-gray-300 dark:border-slate-700 rounded-lg text-sm font-bold text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 dark:hover:bg-amber-500 transition-colors">
                                Enviar Solicitação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showApprovalRequestModal() {
            document.getElementById('approval-modal').classList.remove('hidden');
        }

        function closeApprovalRequestModal() {
            document.getElementById('approval-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('approval-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApprovalRequestModal();
            }
        });
    </script>
@endsection
