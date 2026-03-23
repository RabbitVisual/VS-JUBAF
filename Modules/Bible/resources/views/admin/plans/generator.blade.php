@extends('admin::components.layouts.master')

@section('title', 'Gerar Plano | Bíblia')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gerador Automático</h1>
        <a href="{{ route('admin.bible.plans.show', $plan->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancelar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-8 p-5 bg-linear-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800 rounded-xl flex items-start gap-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg text-blue-600 dark:text-blue-300">
                <x-icon name="information-circle" class="w-6 h-6" />
            </div>
            <div>
                <h3 class="font-bold text-blue-900 dark:text-blue-100 text-lg mb-1">Configuração do Plano: {{ $plan->title }}</h3>
                <p class="text-blue-700 dark:text-blue-300 text-sm leading-relaxed">
                    O sistema irá distribuir os livros selecionados uniformemente ao longo de <strong>{{ $plan->duration_days }} dias</strong>.
                </p>
            </div>
        </div>

        @if($plan->days()->count() > 0)
            <div class="mb-8 p-5 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl flex items-start gap-4">
                <div class="p-2 bg-red-100 dark:bg-red-800 rounded-lg text-red-600 dark:text-red-300">
                    <x-icon name="exclamation" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-red-900 dark:text-red-100 text-lg mb-1">Atenção: Modo de Regeneração</h3>
                    <p class="text-red-700 dark:text-red-300 text-sm leading-relaxed">
                        Este plano já possui <strong>{{ $plan->days()->count() }} dias gerados</strong>. Ao confirmar abaixo, <span class="font-bold underline">todo o conteúdo existente será apagado</span> e uma nova estrutura será criada.
                    </p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.bible.plans.process-generation', $plan->id) }}" method="POST">
            @csrf

            {{-- Global Version Selector for ALL modes --}}
            <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                    <x-icon name="translate" class="w-4 h-4 inline-block mr-1" />
                    Versão da Bíblia
                </label>
                <p class="text-xs text-gray-500 mb-4">Escolha a versão que será usada para buscar os textos e gerar o plano.</p>
                <select name="bible_version_id" id="versionSelector" onchange="filterBooksByVersion()" class="w-full md:w-1/3 px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    <option value="" disabled selected>Selecione uma versão...</option>
                    @foreach($versions as $version)
                        <option value="{{ $version->id }}">{{ $version->name }} ({{ $version->abbreviation }})</option>
                    @endforeach
                </select>
            </div>

            @if($plan->type === 'manual')
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mb-4">
                        <x-icon name="pencil-alt" class="w-8 h-8" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geração de Estrutura Manual</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8">
                        Este plano será criado apenas com os <strong>{{ $plan->duration_days }} dias</strong> em branco. O conteúdo deverá ser adicionado manualmente.
                    </p>
                    <button type="submit" class="inline-flex items-center px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                        <x-icon name="check" class="w-6 h-6 mr-2" />
                        Criar Estrutura Vazia
                    </button>
                </div>
            @elseif($plan->type === 'chronological' || in_array($plan->type, ['canonical', 'historical', 'christ_centered']))
                <div class="space-y-6">
                    <!-- Template Selection Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <label class="cursor-pointer relative block">
                            <input type="radio" name="order_type" value="canonical" class="peer sr-only" {{ $plan->type === 'chronological' || $plan->type === 'canonical' ? 'checked' : '' }}>
                            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-blue-300 transition-all h-full">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center mb-4">
                                    <x-icon name="book-open" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Bíblia Completa (Canônica)</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Leitura sequencial de Gênesis a Apocalipse. A ordem tradicional das Escrituras.
                                </p>
                            </div>
                        </label>

                        <label class="cursor-pointer relative block">
                            <input type="radio" name="order_type" value="historical" class="peer sr-only" {{ $plan->type === 'historical' ? 'checked' : '' }}>
                            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 hover:border-purple-300 transition-all h-full">
                                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 flex items-center justify-center mb-4">
                                    <x-icon name="clock" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Ordem Histórica</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Leia conforme os eventos aconteceram. Encaixa Profetas e Salmos em seu contexto histórico.
                                </p>
                            </div>
                        </label>

                        <label class="cursor-pointer relative block">
                            <input type="radio" name="order_type" value="christ_centered" class="peer sr-only" {{ $plan->type === 'christ_centered' ? 'checked' : '' }}>
                            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 hover:border-red-300 transition-all h-full">
                                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 flex items-center justify-center mb-4">
                                    <x-icon name="heart" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Cristo no Centro</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Ideal para novos convertidos. Foca nos Evangelhos, depois Atos/Cartas, e então o AT à luz de Cristo.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                        <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto mb-6">
                            O plano será gerado usando a <strong>Versão Selecionada</strong> acima. Certifique-se de que é a versão que você deseja usar para leitura.
                        </p>
                        <button type="submit" class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <x-icon name="lightning-bolt" class="w-6 h-6 mr-2" />
                            Gerar Plano Selecionado
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-white text-lg lowercase first-letter:uppercase tracking-tight">2. Selecione os Livros</h3>
                    <button type="button" onclick="toggleAllCheckboxes()" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline transition-colors">
                        Alternar Seleção
                    </button>
                </div>

                <div id="booksListing" class="space-y-8">
                    @php
                        $availableVersions = $books->groupBy('bible_version_id');
                    @endphp

                    @foreach($availableVersions as $vId => $versionBooks)
                        <div class="version-group" data-version-id="{{ $vId }}">
                            <div class="flex items-center gap-3 mb-4 opacity-70">
                                <div class="h-[1px] flex-1 bg-gray-100 dark:bg-gray-700"></div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $versionBooks->first()->bibleVersion->abbreviation }}</span>
                                <div class="h-[1px] flex-1 bg-gray-100 dark:bg-gray-700"></div>
                            </div>

                            @php
                                $groups = [
                                    ['label' => 'Antigo Testamento', 'books' => $versionBooks->where('testament', 'old')],
                                    ['label' => 'Novo Testamento', 'books' => $versionBooks->where('testament', 'new')],
                                ];
                            @endphp

                            @foreach($groups as $g)
                                @if($g['books']->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-3 px-1">{{ $g['label'] }}</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                            @foreach($g['books'] as $book)
                                                <label class="group relative flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-700 transition-all select-none shadow-sm">
                                                    <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $book->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800 py-4 z-10">
                    <button type="submit" class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                        <x-icon name="check" class="w-6 h-6 mr-2" />
                        Confirmar e Gerar
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    function filterBooksByVersion() {
        const selectedVersion = document.getElementById('versionSelector').value;
        const groups = document.querySelectorAll('.version-group');

        groups.forEach(group => {
            if (!selectedVersion || group.dataset.versionId === selectedVersion) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
                // Also uncheck hidden checkboxes to be safe
                group.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            }
        });
    }

    function toggleAllCheckboxes() {
        // Toggle only visible checkboxes
        const visibleCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]'))
            .filter(cb => cb.closest('.version-group').style.display !== 'none');

        const anyChecked = visibleCheckboxes.some(cb => cb.checked);
        visibleCheckboxes.forEach(cb => cb.checked = !anyChecked);
    }

    // Initial run
    document.addEventListener('DOMContentLoaded', () => {
        // If there's only one version, maybe auto-select it?
        const selector = document.getElementById('versionSelector');
        if (selector && selector.options.length === 2) { // 0: All, 1: The only one
            selector.selectedIndex = 1;
            filterBooksByVersion();
        }
    });
</script>
@endsection

