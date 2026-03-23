@extends('admin::components.layouts.master')

@section('title', 'Cupons de desconto')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cupons de desconto</h1>
        <a href="{{ route('admin.marketplace.coupons.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm">
            <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" /> Novo cupom
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.marketplace.coupons.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Código</label>
                <input type="text" name="code" value="{{ request('code') }}" placeholder="Ex: MISSÕES10" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm w-40">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="active" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Ativos</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium">Filtrar</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Uso</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Validade</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ativo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($coupons as $c)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-mono font-medium text-gray-900 dark:text-white">{{ $c->code }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $c->type === 'fixed' ? 'Fixo (R$)' : 'Percentual (%)' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                        @if($c->type === 'fixed')
                            R$ {{ number_format($c->value, 2, ',', '.') }}
                        @else
                            {{ number_format($c->value, 0) }}%
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $c->used_count }}{{ $c->max_uses ? ' / ' . $c->max_uses : '' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                        @if($c->valid_from || $c->valid_until)
                            {{ $c->valid_from ? $c->valid_from->format('d/m/Y') : '—' }} a {{ $c->valid_until ? $c->valid_until->format('d/m/Y') : '—' }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($c->is_active)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Sim</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Não</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.marketplace.coupons.edit', $c) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">Editar</a>
                        <form action="{{ route('admin.marketplace.coupons.destroy', $c) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Remover este cupom?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum cupom cadastrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
