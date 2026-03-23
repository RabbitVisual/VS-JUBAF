@extends('admin::components.layouts.master')

@section('title', 'Aprovações da Diretoria - Administração')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Aprovações da Diretoria</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie solicitações de aprovação e liberações.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.churchcouncil.approvals.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos os status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovada</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitada</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirada</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                <select name="type" id="type" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos os tipos</option>
                    <option value="ministry_membership" {{ request('type') === 'ministry_membership' ? 'selected' : '' }}>Filiação a Ministério</option>
                    <option value="ministry_plan" {{ request('type') === 'ministry_plan' ? 'selected' : '' }}>Plano de Ministério</option>
                    <option value="event_creation" {{ request('type') === 'event_creation' ? 'selected' : '' }}>Criação de Evento</option>
                    <option value="financial_request" {{ request('type') === 'financial_request' ? 'selected' : '' }}>Solicitação Financeira</option>
                    <option value="membership_transfer_out" {{ request('type') === 'membership_transfer_out' ? 'selected' : '' }}>Carta de Transferência</option>
                    <option value="ebd_curriculum" {{ request('type') === 'ebd_curriculum' ? 'selected' : '' }}>Currículo EBD</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="date_from" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Inicial</label>
                <div class="relative">
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>

            <div class="md:col-span-3 flex items-end">
                <div class="flex gap-2 w-full">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center">
                        <x-icon name="filter" class="w-5 h-5 mr-2" />
                        Filtrar
                    </button>
                    @if (request()->hasAny(['status', 'type', 'date_from']))
                        <a href="{{ route('admin.churchcouncil.approvals.index') }}"
                           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                            <x-icon name="x" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Approvals List -->
    <div class="space-y-4">
        @forelse ($approvals as $approval)
            <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Approval Info -->
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-3xl flex items-center justify-center
                                @if ($approval->status === 'pending') bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400
                                @elseif($approval->status === 'approved') bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400
                                @elseif($approval->status === 'rejected') bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400
                                @else bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                @if ($approval->status === 'pending')
                                    <x-icon name="clock" class="w-7 h-7" />
                                @elseif ($approval->status === 'approved')
                                    <x-icon name="check-circle" class="w-7 h-7" />
                                @elseif ($approval->status === 'rejected')
                                    <x-icon name="x-circle" class="w-7 h-7" />
                                @else
                                    <x-icon name="archive" class="w-7 h-7" />
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $approval->approval_type_display }}
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                    @if ($approval->approval_type === 'ministry_plan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($approval->approval_type === 'ministry_membership') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                    @elseif($approval->approval_type === 'event_creation') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                    @elseif($approval->approval_type === 'financial_request') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($approval->approval_type === 'membership_transfer_out') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300
                                    @elseif($approval->approval_type === 'ebd_curriculum') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $approval->approval_type_display }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <x-icon name="calendar" class="w-4 h-4 mr-1.5" />
                                    {{ $approval->submitted_at->format('d/m/Y H:i') }}
                                </span>
                                @if ($approval->requester)
                                    <span class="flex items-center">
                                        <x-icon name="user" class="w-4 h-4 mr-1.5" />
                                        Solicitante: <span class="font-medium ml-1 text-gray-700 dark:text-gray-300">{{ $approval->requester->name }}</span>
                                    </span>
                                @endif
                            </div>

                            @if ($approval->approval_type === 'ministry_membership' && !empty($approval->metadata['ministry_id']) && class_exists(\Modules\Ministries\App\Models\Ministry::class))
                                @php $ministryObj = \Modules\Ministries\App\Models\Ministry::find($approval->metadata['ministry_id']); @endphp
                                <p class="mt-1 text-xs font-semibold text-purple-600 dark:text-purple-400">
                                    Ministério: {{ $ministryObj ? $ministryObj->name : 'ID ' . $approval->metadata['ministry_id'] }}
                                </p>
                            @endif
                            @if ($approval->request_details)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit(is_array($approval->request_details) ? json_encode($approval->request_details) : $approval->request_details, 120) }}
                                </p>
                            @endif

                            @if ($approval->isExpired())
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                    <x-icon name="exclamation" class="w-4 h-4 mr-1" />
                                    Esta solicitação expirou
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions & Status -->
                    <div class="flex flex-col md:items-end gap-3 flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if ($approval->status === 'pending') bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300
                            @elseif($approval->status === 'approved') bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300
                            @elseif($approval->status === 'rejected') bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300
                            @else bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300 @endif">
                            <span class="w-2 h-2 rounded-full mr-2
                                @if ($approval->status === 'pending') bg-yellow-500 animate-pulse
                                @elseif($approval->status === 'approved') bg-green-500
                                @elseif($approval->status === 'rejected') bg-red-500
                                @else bg-gray-500 @endif"></span>
                            {{ $approval->status_display }}
                        </span>

                        <div class="flex items-center gap-2">
                            @if ($approval->status === 'pending' && !$approval->isExpired())
                                <button onclick="approveRequest({{ $approval->id }})"
                                    class="text-sm px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                                    Aprovar
                                </button>
                                <button onclick="rejectRequest({{ $approval->id }})"
                                    class="text-sm px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                                    Rejeitar
                                </button>
                            @endif

                            <a href="{{ route('admin.churchcouncil.approvals.show', $approval) }}"
                               class="text-sm px-3 py-1.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                                Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="check-circle" class="w-10 h-10 text-blue-500 dark:text-blue-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhuma solicitação encontrada</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    @if (request()->hasAny(['status', 'type', 'date_from']))
                        Não encontramos resultados para sua busca. Tente remover os filtros.
                    @else
                        Não há solicitações pendentes no momento. As solicitações aparecerão aqui quando enviadas pelos membros.
                    @endif
                </p>
                @if (request()->hasAny(['status', 'type', 'date_from']))
                    <div class="mt-6">
                        <a href="{{ route('admin.churchcouncil.approvals.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Limpar Filtros
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($approvals->hasPages())
    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-100 dark:border-gray-700 border-dashed rounded-b-2xl">
        {{ $approvals->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
    function approveRequest(approvalId) {
        if (confirm('Tem certeza que deseja aprovar esta solicitação?')) {
            fetch(`{{ url('admin/conselho/aprovacoes') }}/${approvalId}/aprovar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao aprovar solicitação: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro ao aprovar solicitação');
                console.error(error);
            });
        }
    }

    function rejectRequest(approvalId) {
        const reason = prompt('Motivo da rejeição (opcional):');
        if (reason !== null) {
            fetch(`{{ url('admin/conselho/aprovacoes') }}/${approvalId}/rejeitar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao rejeitar solicitação: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro ao rejeitar solicitação');
                console.error(error);
            });
        }
    }
</script>
@endsection

