@extends('admin::components.layouts.master')

@section('title', 'Detalhes da Aprovação - ' . $approval->title)

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex-1 min-w-0">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $approval->title }}</h1>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm
                        @if ($approval->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 animate-pulse
                        @elseif($approval->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                        @elseif($approval->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        <span class="w-2 h-2 rounded-full mr-2
                            @if ($approval->status === 'pending') bg-yellow-500
                            @elseif($approval->status === 'approved') bg-green-500
                            @elseif($approval->status === 'rejected') bg-red-500
                            @else bg-gray-500 @endif"></span>
                        {{ $approval->status_display }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        {{ $approval->approval_type_display }}
                    </span>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-6 text-sm font-medium text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <x-icon name="calendar" class="w-4 h-4" />
                    Solicitado em {{ $approval->created_at->format('d/m/Y \à\s H:i') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <x-icon name="user" class="w-4 h-4" />
                    Por {{ $approval->requester->name }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if ($approval->status === 'pending')
                <button onclick="approveRequest({{ $approval->id }})"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-green-500/30 flex items-center">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Aprovar
                </button>
                <button onclick="rejectRequest({{ $approval->id }})"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-red-500/30 flex items-center">
                    <x-icon name="x" class="w-5 h-5 mr-2" />
                    Rejeitar
                </button>
            @endif

            <a href="{{ route('admin.churchcouncil.approvals.index') }}"
                class="px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 font-bold transition-all shadow-sm flex items-center">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <x-icon name="document-text" class="w-5 h-5" />
                    </div>
                    Descrição da Solicitação
                </h3>

                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700/50">
                    {{ $approval->description }}
                </div>

                @if($approval->metadata && count($approval->metadata) > 0)
                    <div class="mt-8">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Informações Adicionais</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($approval->metadata as $key => $value)
                                <div class="bg-white dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                                    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ str_replace('_', ' ', $key) }}</span>
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $value }}">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Comments/History (Placeholder for future feature) -->
             <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 opacity-60">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                            <x-icon name="chat-alt" class="w-5 h-5" />
                        </div>
                        Histórico e Observações
                    </h3>
                    <span class="text-xs font-medium bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-500">Em Breve</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">O histórico de aprovações e comentários será exibido aqui.</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Requester Info -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Solicitante</h3>
                </div>
                <div class="p-6 flex items-center gap-4">
                     <div class="flex-shrink-0">
                        @if($approval->requester->photo)
                            <img src="{{ asset('storage/' . $approval->requester->photo) }}" class="w-16 h-16 rounded-3xl object-cover ring-4 ring-gray-50 dark:ring-gray-700">
                        @else
                            <div class="w-16 h-16 rounded-3xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-2xl ring-4 ring-blue-50 dark:ring-blue-900/20">
                                {{ substr($approval->requester->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $approval->requester->name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $approval->requester->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Approval Info -->
            <div class="bg-linear-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <x-icon name="shield-check" class="w-32 h-32" transform="rotate-12" />
                </div>

                <h3 class="text-lg font-bold mb-6 relative z-10">Status da Aprovação</h3>

                <div class="space-y-4 relative z-10">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/10">
                        <span class="block text-xs font-medium text-indigo-200 mb-1">Data da Solicitação</span>
                        <span class="block text-lg font-bold">{{ $approval->created_at->format('d/m/Y') }}</span>
                         <span class="block text-sm text-indigo-200">{{ $approval->created_at->format('H:i') }}</span>
                    </div>

                    @if($approval->expires_at)
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/10">
                            <span class="block text-xs font-medium text-indigo-200 mb-1">Expira em</span>
                            <span class="block text-lg font-bold text-yellow-300">{{ $approval->expires_at->format('d/m/Y') }}</span>
                            <span class="block text-sm text-indigo-200">{{ $approval->expires_at->diffForHumans() }}</span>
                        </div>
                    @endif

                    @if($approval->responded_at)
                         <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/10">
                            <span class="block text-xs font-medium text-indigo-200 mb-1">Respondido em</span>
                            <span class="block text-lg font-bold">{{ $approval->responded_at->format('d/m/Y H:i') }}</span>
                            @if($approval->responder)
                                <span class="block text-sm text-indigo-200 mt-1">por {{ $approval->responder->name }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function approveRequest(id) {
        if (confirm('Confirma a aprovação desta solicitação?')) {
            fetch(`{{ url('admin/conselho/aprovacoes') }}/${id}/aprovar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notes: '' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao processar: ' + (data.message || ''));
                }
            })
            .catch(error => {
                alert('Erro na comunicação com o servidor');
                console.error(error);
            });
        }
    }

    function rejectRequest(id) {
        const reason = prompt('Informe o motivo da rejeição:');
        if (reason !== null) {
            fetch(`{{ url('admin/conselho/aprovacoes') }}/${id}/rejeitar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reason: (reason && reason.trim()) ? reason.trim() : 'Rejeitado pelo conselho' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao processar: ' + (data.message || ''));
                }
            })
            .catch(error => {
                alert('Erro na comunicação com o servidor');
                console.error(error);
            });
        }
    }
</script>
@endsection

