@extends('admin::components.layouts.master')

@section('title', 'Mensagens de Contato')

@section('content')
<div class="container-fluid px-4">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Mensagens de Contato</h1>
        <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">Configurações do Sistema (e-mail/endereço)</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-500/50 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filtros e busca --}}
    <form method="GET" action="{{ route('admin.homepage.contacts.index') }}" class="mb-6 flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
            <option value="">Todos</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Não lidas</option>
            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Lidas</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, e-mail ou mensagem..." class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm min-w-[200px]">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Filtrar</button>
        @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('admin.homepage.contacts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Limpar</a>
        @endif
    </form>

    <form method="POST" action="{{ route('admin.homepage.contacts.mark-read') }}" id="contactsForm">
        @csrf
        @if(request()->hasAny(['status', 'search']))
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-100 dark:border-gray-700">
        @if($messages->count() > 0)
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <button type="submit" id="markReadBtn" disabled class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50">Marcar selecionadas como lida(s)</button>
        </div>
        @endif
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" title="Selecionar todas">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assunto/Mensagem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($messages as $message)
                    <tr class="{{ $message->read_at ? '' : 'bg-blue-50 dark:bg-blue-900/10' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(!$message->read_at)
                                <input type="checkbox" name="ids[]" value="{{ $message->id }}" class="row-check rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($message->read_at)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Lida</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">Nova</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $message->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $message->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300 truncate max-w-xs">{{ $message->message }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.homepage.contacts.show', $message->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">Ver</a>
                            <form action="{{ route('admin.homepage.contacts.destroy', $message->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma mensagem encontrada. {{ request()->hasAny(['status', 'search']) ? 'Tente outros filtros.' : '' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $messages->total() }} mensagem(ns)</span>
            {{ $messages->withQueryString()->links() }}
        </div>
    </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAll');
    var checks = document.querySelectorAll('.row-check');
    var markReadBtn = document.getElementById('markReadBtn');
    if (selectAll && checks.length) {
        selectAll.addEventListener('change', function() {
            checks.forEach(function(c) { c.checked = selectAll.checked; });
            markReadBtn.disabled = !Array.from(checks).some(function(c) { return c.checked; });
        });
    }
    if (checks.length && markReadBtn) {
        checks.forEach(function(c) {
            c.addEventListener('change', function() {
                markReadBtn.disabled = !Array.from(checks).some(function(c) { return c.checked; });
            });
        });
    }
});
</script>
@endsection

