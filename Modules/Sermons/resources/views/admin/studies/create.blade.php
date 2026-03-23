@extends('admin::components.layouts.master')

@section('title', 'Novo Estudo - Administração')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo Estudo Bíblico</h1>
        <a href="{{ route('admin.sermons.studies.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('admin.sermons.studies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Título</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                </div>

                <div class="md:col-span-2">
                    <label for="subtitle" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Subtítulo</label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                </div>

                <div>
                    <label for="series_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Série</label>
                    <select name="series_id" id="series_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                        <option value="">Selecione uma série (opcional)</option>
                        @foreach($series as $s)
                            <option value="{{ $s->id }}" {{ old('series_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Categoria</label>
                    <select name="category_id" id="category_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="cover_image_file" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Imagem de Capa</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="cover-preview" class="w-24 h-24 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0">
                             <x-icon name="photograph" class="w-10 h-10 text-gray-400" />
                        </div>
                        <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer"
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
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conteúdo Completo</label>
                <textarea name="content" id="content" rows="20" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('content') border-red-300 @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Audio Section -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="music" class="w-5 h-5 mr-2" />
                    Áudio do Estudo
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
                        <label for="audio_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Arquivo de Áudio
                        </label>
                        <input type="file" name="audio_file" id="audio_file" accept="audio/*"
                            class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('audio_file')
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="https://exemplo.com/audio.mp3">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Cole o link direto para o arquivo de áudio ou podcast
                        </p>
                    </div>
                </div>
            </div>

            <!-- Video URL -->
            <div>
                <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL do Vídeo (Opcional)</label>
                <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                    placeholder="https://youtube.com/watch?v=...">
            </div>

            <!-- Status & Visibility -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>
                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visibilidade</label>
                    <select name="visibility" id="visibility" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="public" {{ old('visibility', 'members') == 'public' ? 'selected' : '' }}>Público</option>
                        <option value="members" {{ old('visibility', 'members') == 'members' ? 'selected' : '' }}>Membros</option>
                        <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privado</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Destaque</label>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Criar Estudo
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
@endpush
@endsection

