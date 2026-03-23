@extends('pastoralpanel::components.layouts.master')

@section('title', $sermon->title . ' - Administração')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sermon->title }}</h1>
                @if ($sermon->subtitle)
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $sermon->subtitle }}</p>
                @endif
                <div class="flex items-center gap-2 mt-2">
                    <span class="status-badge status-{{ $sermon->status }}">{{ $sermon->status_display }}</span>
                    <span class="status-badge visibility-{{ $sermon->visibility }}">{{ $sermon->visibility_display }}</span>
                    @if ($sermon->is_featured)
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">Destaque</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3" x-data="{ exportModalOpen: false, format: 'full', size: 'a5' }">
                <button type="button" @click="exportModalOpen = true"
                    class="inline-flex items-center px-4 py-2 border border-amber-300 dark:border-amber-600 text-sm font-medium rounded-md text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30">
                    <x-icon name="file-pdf" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                    Exportar para púlpito
                </button>
                <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                    <div @click.outside="exportModalOpen = false" class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-icon name="file-pdf" class="w-5 h-5 text-amber-500" />
                            Exportar para o púlpito
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Formato</label>
                                <div class="flex gap-3">
                                    <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'full' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                        <input type="radio" name="export_format" value="full" x-model="format" class="sr-only">
                                        <span class="text-sm font-medium">Esboço completo</span>
                                    </label>
                                    <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'topics' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                        <input type="radio" name="export_format" value="topics" x-model="format" class="sr-only">
                                        <span class="text-sm font-medium">Apenas tópicos</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tamanho do papel</label>
                                <div class="flex gap-4">
                                    <label class="flex-1 cursor-pointer" @click="size = 'a4'">
                                        <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a4' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                            <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 42px; height: 59px;"></div>
                                            <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A4</span>
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer" @click="size = 'a5'">
                                        <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a5' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                            <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 30px; height: 42px;"></div>
                                            <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A5</span>
                                        </div>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Proporção visual do documento.</p>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3 justify-end">
                            <button type="button" @click="exportModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</button>
                            <a :href="'{{ route('pastor.sermoes.sermons.export-pdf', $sermon) }}?format=' + format + '&size=' + size" target="_blank" @click="exportModalOpen = false"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg">
                                <x-icon name="download" class="w-4 h-4 mr-2" />
                                Gerar PDF
                            </a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('pastor.sermoes.sermons.edit', $sermon) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <x-icon name="pen-to-square" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                    Editar
                </a>
                <a href="{{ route('pastor.sermoes.sermons.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <x-icon name="arrow-left" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                    Voltar
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Visualizações</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($sermon->views) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Likes</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($sermon->likes) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Comentários</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $sermon->comments->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Colaboradores</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $sermon->acceptedCollaborators->count() }}</p>
            </div>
        </div>

        <!-- Bible References -->
        @if ($sermon->bibleReferences->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Referências Bíblicas</h3>
                <div class="space-y-3">
                    @foreach ($sermon->bibleReferences as $ref)
                        <div
                            class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <span
                                class="bible-reference-badge bible-reference-{{ $ref->type }}">{{ $ref->type_display }}</span>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $ref->formatted_reference }}</p>
                                @if ($ref->context)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $ref->context }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Sermon Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            @if ($sermon->description)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Descrição</h3>
                    <p class="text-gray-900 dark:text-white">{{ $sermon->description }}</p>
                </div>
            @endif

            @if ($sermon->full_content)
                <div class="mb-6 sermon-content-with-refs">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Conteúdo do Sermão</h3>
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-900 dark:text-gray-200">
                        {!! $sermon->full_content !!}
                    </div>
                </div>
            @endif

            @if ($sermon->introduction)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Introdução</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($sermon->introduction)) !!}
                    </div>
                </div>
            @endif

            @if ($sermon->development)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Desenvolvimento</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($sermon->development)) !!}
                    </div>
                </div>
            @endif

            @if ($sermon->conclusion)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Conclusão</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($sermon->conclusion)) !!}
                    </div>
                </div>
            @endif

            @if ($sermon->application)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Aplicação Prática</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($sermon->application)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Tags -->
        @if ($sermon->tags->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($sermon->tags as $tag)
                        <span class="tag-badge bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Metadata -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informações</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Autor</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $sermon->user->name }}</dd>
                </div>
                @if ($sermon->category)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoria</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $sermon->category->name }}</dd>
                    </div>
                @endif
                @if ($sermon->published_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Publicado em</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $sermon->published_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                @if ($sermon->sermon_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data do Sermão</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $sermon->sermon_date->format('d/m/Y') }}
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Criado em</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $sermon->created_at->format('d/m/Y H:i') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Atualizado em</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $sermon->updated_at->format('d/m/Y H:i') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if ($sermon->full_content)
    <script>
        (function() {
            var baseUrl = @json(route('memberpanel.bible.search'));
            document.querySelectorAll('.sermon-content-with-refs .bible-ref').forEach(function(el) {
                var ref = el.getAttribute('data-bible-ref');
                if (!ref) return;
                var wrap = document.createElement('div');
                wrap.className = 'mt-2';
                var link = document.createElement('a');
                link.href = baseUrl + '?q=' + encodeURIComponent(ref);
                link.textContent = 'Ver na Bíblia';
                link.className = 'text-sm text-amber-600 dark:text-amber-400 hover:underline';
                link.target = '_blank';
                wrap.appendChild(link);
                el.appendChild(wrap);
            });
        })();
    </script>
    @endif
@endsection

