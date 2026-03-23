@extends('pastoralpanel::components.layouts.master')

@section('title', 'Pedidos de Oração')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pedidos de Oração</h1>
        <a href="{{ route('pastor.dashboard') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 font-medium hover:underline">
            <x-icon name="arrow-left" class="w-4 h-4" /> Voltar ao dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pendentes de moderação</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($pendingRequests as $req)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $req->title ?? 'Pedido de oração' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit($req->description ?? '', 120) }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $req->is_anonymous ? 'Anônimo' : ($req->user->name ?? '—') }} · {{ $req->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('pastor.oracao.show', $req) }}" class="px-3 py-2 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-700">Ver</a>
                        <form action="{{ route('pastor.oracao.marcar-orado', $req) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="from" value="list">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">Marcar como orado</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <x-icon name="hands-praying" class="w-12 h-12 mx-auto mb-3 opacity-50" />
                    <p>Nenhum pedido pendente.</p>
                </div>
            @endforelse
        </div>
        @if($pendingRequests->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-slate-700">
                {{ $pendingRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
