@extends('liderancapanel::components.layouts.master')

@section('title', 'Caravana - ' . $event->title)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Caravana JUBAF - {{ $event->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Juventude da sua igreja inscrita neste evento.
                </p>
            </div>
            <a href="{{ route('lideranca.caravanas.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Voltar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total na Caravana</p>
                <p class="mt-1 text-3xl font-black text-gray-900 dark:text-white">{{ $totalNaCaravana }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pago</p>
                <p class="mt-1 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalPago }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
            <form method="GET" action="{{ route('lideranca.caravanas.show', $event) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar jovem ou WhatsApp..."
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white text-sm" />

                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white text-sm">
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Todos os pagamentos</option>
                    <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Somente pagos</option>
                    <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Somente pendentes</option>
                </select>

                <select name="sort" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white text-sm">
                    <option value="recent" {{ ($sort ?? 'recent') === 'recent' ? 'selected' : '' }}>Mais recentes</option>
                    <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Nome (A-Z)</option>
                </select>

                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm">
                    Aplicar filtros
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Nome do Jovem</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Tipo de Ingresso</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Status do Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse ($registrations as $registration)
                            @php
                                $phone = $registration->user->cellphone ?: $registration->user->phone;
                                $whatsQuery = $phone ? 'https://www.google.com/search?q=' . urlencode('api.whatsapp.com ' . $phone) : null;
                                $isPaid = $registration->status === 'confirmed'
                                    || in_array($registration->latestPayment?->status, ['paid', 'approved', 'completed'], true);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $registration->user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($whatsQuery)
                                        <a href="{{ $whatsQuery }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 text-emerald-700 dark:text-emerald-400 hover:underline">
                                            <x-icon name="phone" class="w-4 h-4" />
                                            {{ $phone }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">Nao informado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $registration->batch->name ?? 'Ingresso geral' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span title="{{ $isPaid ? 'Pagamento confirmado para participação no evento.' : 'Inscrição criada, aguardando confirmação do pagamento.' }}"
                                        class="cursor-help inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $isPaid ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                        {{ $isPaid ? 'Pago' : 'Pendente' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-0">
                                <div class="px-6 py-14 text-center">
                                    <div class="mx-auto mb-3 w-14 h-14 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                        <x-icon name="users" class="w-7 h-7 text-gray-400 dark:text-slate-300" />
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Nenhum jovem encontrado nesta caravana</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ajuste filtros ou aguarde novas inscrições da sua igreja.</p>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registrations->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
