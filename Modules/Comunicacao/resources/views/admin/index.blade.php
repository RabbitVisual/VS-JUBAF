@extends('admin::components.layouts.master')

@section('title', 'Mural Oficial — Postagens')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Gestão do Mural</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Editais, atas, avisos e notícias para toda a associação.</p>
            </div>
            <a href="{{ route('admin.comunicacao.postagens.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/20 transition-all">
                <x-icon name="plus" class="w-4 h-4" />
                Nova postagem
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 p-4">
            <form method="GET" action="{{ route('admin.comunicacao.postagens.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                    <select name="tipo"
                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                        <option value="">Todos</option>
                        @foreach (['edital' => 'Edital', 'ata' => 'Ata', 'aviso' => 'Aviso', 'noticia' => 'Notícia'] as $v => $label)
                            <option value="{{ $v }}" @selected(request('tipo') === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-sm font-medium hover:bg-slate-700">Filtrar</button>
                @if (request()->filled('tipo'))
                    <a href="{{ route('admin.comunicacao.postagens.index') }}"
                        class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-gray-200 text-sm">Limpar</a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                    <thead class="bg-gray-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Autor</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse ($postagens as $p)
                            @php
                                $tipoClass = match ($p->tipo) {
                                    'edital' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                                    'ata' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200',
                                    'aviso' => 'bg-amber-100 text-amber-900 dark:bg-amber-900/30 dark:text-amber-100',
                                    'noticia' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $tipoLabel = match ($p->tipo) {
                                    'edital' => 'Edital',
                                    'ata' => 'Ata',
                                    'aviso' => 'Aviso',
                                    'noticia' => 'Notícia',
                                    default => $p->tipo,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white max-w-xs truncate">{{ $p->titulo }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tipoClass }}">{{ $tipoLabel }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $p->autor?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <a href="{{ route('admin.comunicacao.postagens.edit', $p) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium mr-3">Editar</a>
                                    <form action="{{ route('admin.comunicacao.postagens.destroy', $p) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Remover esta postagem?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline font-medium">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Nenhuma postagem ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($postagens->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-800">
                    {{ $postagens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
