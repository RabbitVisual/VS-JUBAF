@extends('admin::components.layouts.master')

@section('title', 'Áudios por capítulo: ' . $version->name)

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Áudios por capítulo</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $version->name }} ({{ $version->abbreviation }})</p>
            </div>
            <a href="{{ route('admin.bible.edit', $version) }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar à versão
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:text-green-300 dark:border-green-800 flex items-center" role="alert">
                <x-icon name="circle-check" class="w-5 h-5 mr-3 flex-shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:text-red-300 dark:border-red-800 flex items-center" role="alert">
                <x-icon name="triangle-exclamation" class="w-5 h-5 mr-3 flex-shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-200">
            <p class="font-semibold mb-1">Google Drive</p>
            <p>Use o link de visualização do arquivo (ex.: <code class="bg-amber-100 dark:bg-amber-800/50 px-1 rounded">https://drive.google.com/file/d/FILE_ID/view</code>). O sistema converte automaticamente em link de download para o player. Cada linha do CSV deve ter: <code class="bg-amber-100 dark:bg-amber-800/50 px-1 rounded">book_number</code>, <code class="bg-amber-100 dark:bg-amber-800/50 px-1 rounded">chapter_number</code>, <code class="bg-amber-100 dark:bg-amber-800/50 px-1 rounded">audio_url</code>.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="file-arrow-up" style="duotone" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    Importar CSV
                </h2>
            </div>
            <form action="{{ route('admin.bible.chapter-audio.store', $version) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="csv" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Arquivo CSV</label>
                        <input type="file" id="csv" name="csv" accept=".csv,.txt" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                        @error('csv')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors">
                        Importar
                    </button>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Primeira linha = cabeçalho: book_number, chapter_number, audio_url.
                    <a href="{{ route('admin.bible.chapter-audio.template', $version) }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-1">Baixar modelo CSV</a>
                </p>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="list" style="duotone" class="w-5 h-5 mr-2 text-gray-500" />
                    Capítulos com áudio ({{ $audios->total() }})
                </h2>
            </div>
            @if ($audios->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    Nenhum áudio por capítulo cadastrado. Importe um CSV com as colunas book_number, chapter_number, audio_url.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Livro</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Cap.</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">URL</th>
                                <th class="px-4 py-3 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($audios as $audio)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $audio->book_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $audio->chapter_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs max-w-md truncate" title="{{ $audio->audio_url }}">{{ $audio->audio_url }}</td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('admin.bible.chapter-audio.destroy', [$version, $audio]) }}" method="POST" onsubmit="return confirm('Remover este áudio?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" aria-label="Excluir">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $audios->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
