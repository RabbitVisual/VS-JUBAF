@extends('pastoralpanel::components.layouts.master')

@section('title', 'Novo Comentário - Administração')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo Comentário Bíblico</h1>
        <a href="{{ route('pastor.sermoes.commentaries.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
<form action="{{ route('pastor.sermoes.commentaries.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Reference -->
            <!-- Reference -->
            <!-- Reference -->
            <div class="bible-reference-item grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                     <label for="bible_version_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Versão</label>
                     <select name="bible_version_id" id="bible_version_id" required
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                         <option value="">Selecione</option>
                         @foreach($bibleVersions as $version)
                             <option value="{{ $version->id }}">{{ $version->name }}</option>
                         @endforeach
                     </select>
                </div>
                <div>
                    <label for="book_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Livro</label>
                    <select name="book_id" id="book_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="">Selecione a versão primeiro</option>
                    </select>
                </div>
                <div>
                    <label for="chapter_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capítulo</label>
                    <select name="chapter_id" id="chapter_id" required
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                         <option value="">Selecione o livro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Versículos</label>
                    <input type="text" name="verses" readonly
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:text-white text-sm cursor-pointer"
                        placeholder="Clique para selecionar">
                    <p class="text-[10px] text-gray-500 mt-1 italic">Clique acima para selecionar os versículos.</p>
                </div>
            </div>

            <!-- Title (Optional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Título (Opcional)</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all"
                        placeholder="Título para este bloco de comentário">
                </div>

                <div>
                    <label for="cover_image_file" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Imagem de Capa (Opcional)</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="cover-preview" class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0">
                             <x-icon name="photograph" class="w-6 h-6 text-gray-400" />
                        </div>
                        <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                            class="block w-full text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-amber-500 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer"
                            onchange="previewCover(event)">
                        <p class="mt-1 text-[10px] text-gray-500">PNG, JPG ou GIF até 15MB</p>
                    </div>
                    @error('cover_image_file')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comentário</label>
                <textarea name="content" id="content" rows="8" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm @error('content') border-red-300 @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Audio Section -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="music" class="w-5 h-5 mr-2" />
                    Áudio do Comentário
                </h3>

                <div x-data="{ audioType: 'upload' }" class="space-y-4">
                    <!-- Tabs -->
                    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                        <button type="button" @click="audioType = 'upload'"
                                :class="audioType === 'upload' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                            <x-icon name="cloud-arrow-up" class="w-4 h-4 inline mr-1" />
                            Upload de Arquivo
                        </button>
                        <button type="button" @click="audioType = 'link'"
                                :class="audioType === 'link' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                            <x-icon name="link" class="w-4 h-4 inline mr-1" />
                            Link Externo
                        </button>
                    </div>

                    <!-- Upload Tab -->
                    <div x-show="audioType === 'upload'" x-transition>
                        <label for="audio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Arquivo de Áudio
                        </label>
                        <input type="file" name="audio" id="audio" accept="audio/*"
                            class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('audio')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <strong>Formatos aceitos:</strong> MP3, WAV, OGG, M4A, AAC<br>
                            <strong>Tamanho máximo:</strong> 40MB
                        </p>
                    </div>

                    <!-- Link Tab -->
                    <div x-show="audioType === 'link'" x-transition>
                        <label for="audio_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            URL do Áudio
                        </label>
                        <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="https://exemplo.com/audio.mp3">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Cole o link direto para o arquivo de áudio
                        </p>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                </div>
                <div class="flex items-center mt-6">
                    <input type="hidden" name="is_official" value="0">
                    <input type="checkbox" name="is_official" id="is_official" value="1" {{ old('is_official') ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_official" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Comentário Oficial</label>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-amber-500">
                    Salvar Comentário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewCover(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('cover-preview');
            preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endpush

