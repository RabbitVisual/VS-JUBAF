@extends('admin::components.layouts.master')

@section('title', 'Pontos de retirada')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pontos de retirada</h1>
        <a href="{{ route('admin.marketplace.pickup-locations.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm">
            <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" /> Novo ponto
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Endereço</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ativo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($pickupLocations as $loc)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $loc->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($loc->address, 50) ?: '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($loc->is_active)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Sim</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Não</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.marketplace.pickup-locations.edit', $loc) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">Editar</a>
                        <form action="{{ route('admin.marketplace.pickup-locations.destroy', $loc) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Remover este ponto de retirada?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum ponto de retirada cadastrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
            {{ $pickupLocations->links() }}
        </div>
    </div>
</div>
@endsection
