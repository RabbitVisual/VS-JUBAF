@extends('admin::components.layouts.master')

@section('title', 'Editar Comentário - Administração')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Comentário Bíblico</h1>
        <a href="{{ route('admin.sermons.commentaries.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('admin.sermons.commentaries.update', $commentary) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bible-reference-item grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-1">
                     <label for="bible_version_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Versão</label>
                     <select name="bible_version_id" id="bible_version_id" required
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                         <option value="">Selecione</option>
                         @foreach($bibleVersions as $version)
                             <option value="{{ $version->id }}" {{ $selectedVersionId == $version->id ? 'selected' : '' }}>{{ $version->name }}</option>
                         @endforeach
                     </select>
                </div>
                <div>
                    <label for="book_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Livro</label>
                    <select name="book_id" id="book_id" required data-selected="{{ $selectedBookId }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="">{{ $commentary->book ?: 'Selecione a versão' }}</option>
                        @foreach($bibleBooks as $book)
                            <option value="{{ $book->id }}" {{ $selectedBookId == $book->id ? 'selected' : '' }}>{{ $book->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="chapter_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capítulo</label>
                    <select name="chapter_id" id="chapter_id" required data-selected="{{ $selectedChapterId }}"
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                         <option value="">{{ $commentary->chapter ?: 'Selecione o livro' }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Versículos</label>
                    <input type="text" name="verses" readonly
                        data-selected-verses="{{ $versesString }}"
                        value="{{ $versesString }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm cursor-pointer"
                        placeholder="Clique para selecionar">
                    <p class="text-[10px] text-gray-500 mt-1 italic">Clique acima para selecionar os versículos.</p>
                </div>
            </div>

            <!-- Title (Optional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Título (Opcional)</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $commentary->title) }}"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all"
                        placeholder="Título para este bloco de comentário">
                </div>

                <div>
                    <label for="cover_image_file" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Imagem de Capa (Opcional)</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="cover-preview" class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0">
                             @if($commentary->cover_image)
                                <img src="{{ asset('storage/' . $commentary->cover_image) }}" class="w-full h-full object-cover">
                             @else
                                <x-icon name="photograph" class="w-6 h-6 text-gray-400" />
                             @endif
                        </div>
                        <div class="flex-1 space-y-1">
                            <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                                class="block w-full text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer"
                                onchange="previewCover(event)">
                            <p class="mt-1 text-[10px] text-gray-500">PNG, JPG ou GIF até 15MB</p>
                            @error('cover_image_file')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if($commentary->cover_image)
                                <label class="flex items-center text-[10px] text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_cover" value="1" class="mr-1 h-2.5 w-2.5 rounded text-red-600">
                                    Remover imagem
                                </label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comentário</label>
                <textarea name="content" id="content" rows="8" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('content') border-red-300 @enderror">{{ old('content', $commentary->content) }}</textarea>
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

                <input type="hidden" name="remove_audio" id="remove_audio" value="0">
                @if($commentary->audio_path || $commentary->audio_url)
                    <div id="audio-current-container" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-300">Áudio Atual:</p>
                            <button type="button" onclick="if(confirm('Tem certeza que deseja remover o áudio?')) { document.getElementById('remove_audio').value = '1'; document.getElementById('audio-current-container').remove(); }"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium flex items-center">
                                <x-icon name="trash-can" style="duotone" class="w-4 h-4 mr-1" />
                                Remover
                            </button>
                        </div>
                        <input type="hidden" name="remove_audio" id="remove_audio" value="0">
                        @if($commentary->audio_path)
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center mb-2">
                                <x-icon name="volume-high" class="w-4 h-4 mr-1" />
                                Arquivo: {{ basename($commentary->audio_path) }}
                            </p>
                        @elseif($commentary->audio_url)
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center mb-2">
                                <x-icon name="link" class="w-4 h-4 mr-1" />
                                Link: {{ Str::limit($commentary->audio_url, 50) }}
                            </p>
                        @endif
                        <audio controls class="w-full mt-3" style="max-height: 40px;">
                            <source src="{{ $commentary->audio_source }}" type="audio/mpeg">
                            <source src="{{ $commentary->audio_source }}" type="audio/ogg">
                            <source src="{{ $commentary->audio_source }}" type="audio/wav">
                            <source src="{{ $commentary->audio_source }}" type="audio/mp4">
                            <source src="{{ $commentary->audio_source }}" type="audio/aac">
                            Seu navegador não suporta áudio.
                        </audio>
                    </div>
                @endif

                <div x-data="{ audioType: '{{ old('audio_url') ? 'link' : 'upload' }}' }" class="space-y-4">
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
                            Novo Arquivo de Áudio @if($commentary->audio_path || $commentary->audio_url)<span class="text-xs text-gray-500">(substituirá o atual)</span>@endif
                        </label>
                        <input type="file" name="audio" id="audio" accept="audio/*"
                            class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            URL do Áudio @if($commentary->audio_path || $commentary->audio_url)<span class="text-xs text-gray-500">(substituirá o atual)</span>@endif
                        </label>
                        <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url', $commentary->audio_url) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="draft" {{ old('status', $commentary->status) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', $commentary->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                </div>
                <div class="flex items-center mt-6">
                    <input type="hidden" name="is_official" value="0">
                    <input type="checkbox" name="is_official" id="is_official" value="1" {{ old('is_official', $commentary->is_official) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_official" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Comentário Oficial</label>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Atualizar Comentário
                </button>
            </div>
        </form>
    </div>
</div>
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
@endsection

