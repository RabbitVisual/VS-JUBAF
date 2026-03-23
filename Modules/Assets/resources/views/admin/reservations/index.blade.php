@extends('admin::components.layouts.master')

@section('title', 'Reservas de equipamentos')

@section('content')
    <div class="space-y-8">
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-indigo-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10">
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Reservas</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Reservas de Equipamentos</h1>
                <p class="text-gray-300 max-w-xl">Solicitações de ministérios para uso de patrimônio.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <form method="GET" class="relative flex flex-wrap gap-4 items-end">
                <div>
                    <label for="ministry_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Ministério</label>
                    <select name="ministry_id" id="ministry_id" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($ministries as $m)
                            <option value="{{ $m->id }}" {{ request('ministry_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" id="status" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>Solicitado</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="denied" {{ request('status') === 'denied' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Filtrar</button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-32 bg-gray-50 dark:bg-gray-900/30 rounded-bl-full -mr-8 -mt-8"></div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Equipamento / Ministério</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Período</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Solicitante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($reservations as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $r->asset->name ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $r->ministry->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $r->start_at->format('d/m/Y H:i') }} – {{ $r->end_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $r->requester->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusLabels = ['requested' => 'Solicitado', 'approved' => 'Aprovado', 'denied' => 'Rejeitado', 'completed' => 'Concluído'];
                                    $statusClass = match($r->status) {
                                        'requested' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'denied' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded text-xs font-medium {{ $statusClass }}">{{ $statusLabels[$r->status] ?? $r->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($r->status === 'requested')
                                    <form action="{{ route('assets.admin.reservations.approve', $r) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 font-medium text-sm">Aprovar</button>
                                    </form>
                                    <span class="mx-1">|</span>
                                    <form action="{{ route('assets.admin.reservations.deny', $r) }}" method="POST" class="inline" onsubmit="var r=prompt('Motivo (opcional)'); if(r===null) return false; this.querySelector('[name=reason]').value=r;">
                                        @csrf
                                        <input type="hidden" name="reason" value="">
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 font-medium text-sm">Rejeitar</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhuma reserva encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($reservations->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $reservations->appends(request()->query())->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
@endsection
