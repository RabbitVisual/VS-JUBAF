@extends('admin::components.layouts.master')

@section('title', 'Newsletter')

@section('content')
<div class="container-fluid px-4">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Assinantes da Newsletter</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $totalCount ?? 0 }} assinante(s) no total, {{ $activeCount ?? 0 }} ativo(s).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.homepage.newsletter.export', request()->only('status')) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm flex items-center gap-2">
                <x-icon name="file-export" class="w-4 h-4" />
                Exportar CSV
            </a>
            <a href="{{ route('admin.homepage.newsletter.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <x-icon name="paper-plane" class="w-4 h-4" />
                Enviar E-mail para Todos
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-500/50 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filtro --}}
    <form method="GET" action="{{ route('admin.homepage.newsletter.index') }}" class="mb-6 flex flex-wrap items-center gap-4">
        <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
            <option value="">Todos</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Filtrar</button>
        @if(request('status'))
            <a href="{{ route('admin.homepage.newsletter.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Limpar</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-100 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">E-mail</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data de Inscrição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $subscriber->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $subscriber->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $subscriber->subscribed_at ? $subscriber->subscribed_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subscriber->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('admin.homepage.newsletter.destroy', $subscriber->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja remover este assinante?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-500 dark:text-gray-400">
                                <x-icon name="envelope-open" class="w-12 h-12 text-gray-400" />
                                <p class="font-medium">Nenhum assinante encontrado.</p>
                                <p class="text-sm">{{ request('status') ? 'Tente outro filtro.' : 'Os inscritos na newsletter aparecerão aqui.' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $subscribers->total() }} assinante(s) nesta lista</span>
            {{ $subscribers->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

