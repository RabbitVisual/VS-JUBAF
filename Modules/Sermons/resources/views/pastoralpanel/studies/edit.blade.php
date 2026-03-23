@extends('pastoralpanel::components.layouts.master')

@section('title', 'Editar Estudo - Administração')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Estudo Bíblico</h1>
        <a href="{{ route('pastor.sermoes.studies.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('pastor.sermoes.studies.update', $study) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Título</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $study->title) }}" required
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                </div>

                <div class="md:col-span-2">
                    <label for="subtitle" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Subtítulo</label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $study->subtitle) }}"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                </div>

                <div>
                    <label for="series_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Série</label>
                    <select name="series_id" id="series_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                        <option value="">Selecione uma série (opcional)</option>
                        @foreach($series as $s)
                            <option value="{{ $s->id }}" {{ old('series_id', $study->series_id) == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Categoria</label>
                    <select name="category_id" id="category_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm transition-all">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id', $study->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="cover_image_file" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Imagem de Capa</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="cover-preview" class="w-24 h-24 rounded-xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0">
                             @if($study->cover_image)
                                <img src="{{ asset('storage/' . $study->cover_image) }}" class="w-full h-full object-cover">
                             @else
                                <x-icon name="photograph" class="w-10 h-10 text-gray-400" />
                             @endif
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-amber-500 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer"
                                onchange="previewCover(event)">
                            <p class="mt-1 text-[10px] text-gray-500">PNG, JPG ou GIF até 15MB</p>
                            @error('cover_image_file')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if($study->cover_image)
                                <label class="flex items-center text-xs text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_cover" value="1" class="mr-1 h-3 w-3 rounded text-red-600">
                                    Remover imagem atual
                                </label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conteúdo Completo</label>
                <textarea name="content" id="content" rows="20" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm @error('content') border-red-300 @enderror">{{ old('content', $study->content) }}</textarea>
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
                <input type="hidden" name="remove_audio" id="remove_audio" value="0">

                @if($study->audio_file || $study->audio_url)
                    <div id="audio-current-container" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-300">Áudio Atual:</p>
                            <button type="button" onclick="if(confirm('Tem certeza que deseja remover o áudio?')) { document.getElementById('remove_audio').value = '1'; document.getElementById('audio-current-container').remove(); }"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium flex items-center">
                                <x-icon name="trash-can" style="duotone" class="w-4 h-4 mr-1" />
                                Remover
                            </button>
                        </div>
                        @if($study->audio_file)
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center">
                                <x-icon name="volume-high" class="w-4 h-4 mr-1" />
                                Arquivo: {{ basename($study->audio_file) }}
                            </p>
                        @elseif($study->audio_url)
                            <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center">
                                <x-icon name="link" class="w-4 h-4 mr-1" />
                                Link: {{ Str::limit($study->audio_url, 50) }}
                            </p>
                        @endif
                        <audio controls class="w-full mt-3" style="max-height: 40px;">
                            <source src="{{ $study->audio_source }}" type="audio/mpeg">
                            <source src="{{ $study->audio_source }}" type="audio/ogg">
                            <source src="{{ $study->audio_source }}" type="audio/wav">
                            <source src="{{ $study->audio_source }}" type="audio/mp4">
                            <source src="{{ $study->audio_source }}" type="audio/aac">
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
                        <label for="audio_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Novo Arquivo de Áudio @if($study->audio_file || $study->audio_url)<span class="text-xs text-gray-500">(substituirá o atual)</span>@endif
                        </label>
                        <input type="file" name="audio_file" id="audio_file" accept="audio/*"
                            class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            URL do Áudio @if($study->audio_file || $study->audio_url)<span class="text-xs text-gray-500">(substituirá o atual)</span>@endif
                        </label>
                        <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url', $study->audio_url) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm"
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
                <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $study->video_url) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm"
                    placeholder="https://youtube.com/watch?v=...">
            </div>

            <!-- Status & Visibility -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="draft" {{ old('status', $study->status) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', $study->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ old('status', $study->status) == 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>
                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visibilidade</label>
                    <select name="visibility" id="visibility" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="public" {{ old('visibility', $study->visibility) == 'public' ? 'selected' : '' }}>Público</option>
                        <option value="members" {{ old('visibility', $study->visibility) == 'members' ? 'selected' : '' }}>Membros</option>
                        <option value="private" {{ old('visibility', $study->visibility) == 'private' ? 'selected' : '' }}>Privado</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $study->is_featured) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Destaque</label>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-amber-500">
                    Atualizar Estudo
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

