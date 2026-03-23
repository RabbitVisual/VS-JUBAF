@extends('admin::components.layouts.master')

@section('title', 'Moderação de Pedidos')

@section('content')
<div class="p-6 space-y-6" x-data="moderationHandler()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Moderação de Pedidos</h1>
            <p class="text-gray-600 dark:text-gray-400">Analise e aprove pedidos de oração pendentes.</p>
        </div>
        <div class="flex gap-2">
             <a href="{{ route('admin.intercessor.moderation.testimonies.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all duration-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:bg-indigo-900/40 border border-indigo-100 dark:border-indigo-800/50">
                <x-icon name="star" class="w-5 h-5 mr-2" />
                <span>Testemunhos</span>
            </a>
            <a href="{{ route('admin.intercessor.dashboard') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                <span>Painel</span>
            </a>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pendentes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
                </div>
                <div class="p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                    <x-icon name="clock" class="w-5 h-5" />
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aprovados (Hoje)</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $approvedToday }}</p>
                </div>
                <div class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                    <x-icon name="check-circle" class="w-5 h-5" />
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ações Rápidas</p>
                    <div class="flex space-x-1 mt-1">
                        <span class="text-[10px] font-medium text-gray-400"><kbd class="px-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">A</kbd> Aprovar</span>
                        <span class="text-[10px] font-medium text-gray-400"><kbd class="px-1 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600">R</kbd> Rejeitar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative min-h-[500px] flex items-center justify-center overflow-hidden">
        @forelse($pendingRequests as $request)
        <div
             x-show="currentIndex === {{ $loop->index }}"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-x-1/2 rotate-6"
             x-transition:enter-end="opacity-100 translate-x-0 rotate-0"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-x-0 rotate-0"
             x-transition:leave-end="opacity-0 -translate-x-full -rotate-12"
             class="absolute w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-black/10 border border-gray-100 dark:border-gray-700 overflow-hidden"
             style="z-index: {{ $loop->remaining + 10 }}"
        >
            <div class="p-8">
                <!-- User Profile -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-linear-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-white dark:border-gray-700 shadow-md">
                            {{ substr($request->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-none mb-1">{{ $request->user->name }}</h3>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 font-medium">
                                <x-icon name="clock" class="w-3 h-3 mr-1" />
                                {{ $request->created_at->diffForHumans() }}
                                <span class="mx-2">•</span>
                                <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold uppercase tracking-tight text-[10px]">
                                    {{ $request->category->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($request->urgency_level === 'critical')
                            <span class="px-3 py-1 bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-full text-[10px] font-bold uppercase tracking-widest animate-pulse">Critico</span>
                        @elseif($request->urgency_level === 'high')
                            <span class="px-3 py-1 bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 rounded-full text-[10px] font-bold uppercase tracking-widest">Alta</span>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 mb-8 border border-gray-100 dark:border-gray-700/50 min-h-[120px] relative">
                    <x-icon name="quote-right" class="absolute right-4 top-4 w-12 h-12 text-gray-200 dark:text-gray-800 opacity-30" />
                    <p class="text-gray-700 dark:text-gray-300 text-lg font-medium leading-relaxed italic z-10 relative">
                        {!! \Modules\Intercessor\App\Services\BibleParser::parse($request->description) !!}
                    </p>
                </div>

                <!-- Technical Details -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                     <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Privacidade</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200 capitalize">{{ str_replace('_', ' ', $request->privacy_level) }}</p>
                    </div>
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Membro desde</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $request->user->created_at->format('M/Y') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between space-x-4">
                    <button @click="rejectRequest({{ $request->id }})"
                            class="flex-1 px-8 py-4 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl font-bold transition-all border border-red-100 dark:border-red-800/50 flex items-center justify-center space-x-2 group">
                        <x-icon name="x-circle" class="w-6 h-6 group-hover:scale-110 transition-transform" />
                        <span>Rejeitar (R)</span>
                    </button>
                    <button @click="approveRequest({{ $request->id }})"
                            class="flex-1 px-8 py-4 bg-linear-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-green-500/30 flex items-center justify-center space-x-2 group">
                        <x-icon name="check-circle" class="w-6 h-6 group-hover:scale-110 transition-transform" />
                        <span>Aprovar (A)</span>
                    </button>
                </div>

                <div class="mt-6 flex justify-center">
                    <a href="{{ route('admin.intercessor.moderation.show', $request) }}" class="text-xs font-bold text-gray-400 hover:text-blue-500 transition-colors uppercase tracking-widest flex items-center">
                        Ver detalhes e comentários
                        <x-icon name="arrow-right" class="w-3 h-3 ml-2" />
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center text-center p-12 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 max-w-md w-full">
            <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="thumb-up" class="w-10 h-10 text-green-500" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Tudo limpo!</h3>
            <p class="text-gray-500 dark:text-gray-400">Não há novos pedidos para moderar no momento. Bom trabalho!</p>
            <a href="{{ route('admin.intercessor.dashboard') }}" class="mt-8 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold transition-all">
                Voltar ao Painel
            </a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function moderationHandler() {
        return {
            currentIndex: 0,
            pendingCount: {{ $pendingRequests->count() }},

            async approveRequest(id) {
                try {
                    const response = await fetch(`{{ url('/admin/intercessor/moderacao') }}/${id}/aprovar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.next();
                    }
                } catch (error) {
                    console.error('Error approving request:', error);
                }
            },

            async rejectRequest(id) {
                if (!confirm('Rejeitar este pedido?')) return;

                try {
                    const response = await fetch(`{{ url('/admin/intercessor/moderacao') }}/${id}/rejeitar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.next();
                    }
                } catch (error) {
                    console.error('Error rejecting request:', error);
                }
            },

            next() {
                this.currentIndex++;
                this.pendingCount--;
                if (this.pendingCount === 0) {
                     window.location.reload();
                }
            },

            init() {
                window.addEventListener('keydown', (e) => {
                    if (this.currentIndex >= {{ $pendingRequests->count() }}) return;

                    const activeRequest = @json($pendingRequests->pluck('id'));
                    const requestId = activeRequest[this.currentIndex];

                    if (e.key === 'a' || e.key === 'A') this.approveRequest(requestId);
                    if (e.key === 'r' || e.key === 'R') this.rejectRequest(requestId);
                });
            }
        }
    }
</script>
@endpush
@endsection

