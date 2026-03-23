@extends('admin::components.layouts.master')

@section('title', isset($slide) ? 'Editar Slide' : 'Novo Slide')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.homepage.carousel.index') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <x-icon name="arrow-left" class="w-6 h-6" />
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($slide) ? 'Editar Slide' : 'Novo Slide' }}</h1>
            </div>
        </div>

        <form action="{{ isset($slide) ? route('admin.homepage.carousel.update', $slide) : route('admin.homepage.carousel.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($slide))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Image Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Imagem do Slide</h2>

                        <div class="space-y-4">
                            <!-- Preview Container -->
                            <div id="image-preview-container" class="relative w-full h-64 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 mb-4 {{ isset($slide) && $slide->image_url ? '' : 'hidden' }}">
                                <img id="image-preview" src="{{ isset($slide) && $slide->image_url ? $slide->image_url : '' }}" alt="Preview" class="w-full h-full object-cover">
                            </div>

                            <div class="flex justify-center items-center w-full">
                                <label for="slide_image" class="flex flex-col justify-center items-center w-full h-32 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-gray-300 dark:border-gray-600 border-dashed cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="image-placeholder">
                                    <div class="flex flex-col justify-center items-center pt-5 pb-6">
                                        <x-icon name="cloud-arrow-up" class="mb-3 w-10 h-10 text-gray-400" />
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Clique para trocar</span> a imagem</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP (Max 10MB)</p>
                                    </div>
                                    <input id="slide_image" name="image" type="file" class="hidden" accept="image/*" {{ isset($slide) ? '' : 'required' }}>
                                </label>
                            </div>
                            @error('image')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Logo Settings -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <x-icon name="image" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuração da Logo</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Logo Preview Container -->
                            <div id="logo-preview-container" class="relative w-full h-40 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 flex items-center justify-center p-4 {{ isset($slide) && $slide->logo_url ? '' : 'hidden' }}">
                                <img id="logo-preview" src="{{ isset($slide) && $slide->logo_url ? $slide->logo_url : '' }}" alt="Logo Preview" class="object-contain transition-all" style="height: {{ isset($slide) ? ($slide->logo_scale ?? 100) : 100 }}%">
                            </div>

                            <div class="flex justify-center items-center w-full">
                                <label for="slide_logo" class="flex flex-col justify-center items-center w-full h-24 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-gray-300 dark:border-gray-600 border-dashed cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="logo-placeholder">
                                    <div class="flex flex-col justify-center items-center pt-5 pb-6">
                                        <x-icon name="image" class="mb-2 w-8 h-8 text-gray-400" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Clique para selecionar</p>
                                    </div>
                                    <input id="slide_logo" name="logo" type="file" class="hidden" accept="image/*">
                                </label>
                            </div>
                            @error('logo')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Posição</label>
                                    <select name="logo_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        @foreach([
                                            'top_center' => 'Topo Centro',
                                            'top_left' => 'Topo Esquerda',
                                            'top_right' => 'Topo Direita',
                                            'center' => 'Centro do Slide',
                                            'bottom_center' => 'Abaixo do Texto (Centro)',
                                            'bottom_left' => 'Abaixo do Texto (Esq)',
                                            'bottom_right' => 'Abaixo do Texto (Dir)'
                                        ] as $val => $label)
                                            <option value="{{ $val }}" {{ old('logo_position', $slide->logo_position ?? 'top_center') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanho (Escala: <span id="logo_scale_val">{{ old('logo_scale', $slide->logo_scale ?? 100) }}</span>%)</label>
                                    <input type="range" name="logo_scale" min="10" max="200" value="{{ old('logo_scale', $slide->logo_scale ?? 100) }}" class="w-full" oninput="document.getElementById('logo_scale_val').innerText = this.value; document.getElementById('logo-preview').style.height = this.value + '%'">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Texts Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <x-icon name="heading" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Conteúdo Textual</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
                                <input type="text" name="title" value="{{ old('title', $slide->title ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtítulo / Descrição</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('description', $slide->description ?? '') }}</textarea>
                                @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto do Botão</label>
                                    <input type="text" name="link_text" value="{{ old('link_text', $slide->link_text ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link do Botão</label>
                                    <input type="url" name="link" value="{{ old('link', $slide->link ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    @error('link') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar settings -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h2>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label class="block text-xs font-medium uppercase text-gray-500 dark:text-gray-400 mb-2">Agendamento</label>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Início</label>
                                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($slide->starts_at) ? $slide->starts_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Fim</label>
                                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($slide->ends_at) ? $slide->ends_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="palette" class="w-5 h-5 text-gray-500" />
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Estilização</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Typography -->
                            <div>
                                <label class="block text-xs font-medium uppercase text-gray-500 dark:text-gray-400 mb-3">Tipografia e Posição</label>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alinhamento do Conteúdo</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach(['left' => 'Esquerda', 'center' => 'Centro', 'right' => 'Direita'] as $val => $label)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="text_alignment" value="{{ $val }}" class="peer sr-only" {{ old('text_alignment', $slide->text_alignment ?? 'center') == $val ? 'checked' : '' }}>
                                                    <div class="text-center py-2 px-1 rounded border border-gray-200 dark:border-gray-600 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 dark:peer-checked:bg-blue-900/30 dark:peer-checked:text-blue-400 text-sm">
                                                        {{ $label }}
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                     <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posição no Slide</label>
                                        <select name="text_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            @foreach(['center' => 'Centro', 'left' => 'Esquerda', 'right' => 'Direita', 'top' => 'Topo', 'bottom' => 'Fundo'] as $val => $label)
                                                <option value="{{ $val }}" {{ old('text_position', $slide->text_position ?? 'center') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor do Texto</label>
                                        <div class="flex rounded-md shadow-sm">
                                            <input type="color" name="text_color" value="{{ old('text_color', $slide->text_color ?? '#ffffff') }}" class="h-9 w-12 border border-gray-300 rounded-l-md p-0.5 cursor-pointer">
                                            <input type="text" name="text_color_hex" value="{{ old('text_color', $slide->text_color ?? '#ffffff') }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700">

                            <!-- Overlay -->
                            <div>
                                <label class="block text-xs font-medium uppercase text-gray-500 dark:text-gray-400 mb-3">Fundo (Overlay)</label>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor</label>
                                        <div class="flex rounded-md shadow-sm">
                                            <input type="color" name="overlay_color" value="{{ old('overlay_color', $slide->overlay_color ?? '#000000') }}" class="h-9 w-12 border border-gray-300 rounded-l-md p-0.5 cursor-pointer">
                                            <input type="text" name="overlay_color_hex" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white sm:text-sm uppercase" value="{{ old('overlay_color', $slide->overlay_color ?? '#000000') }}">
                                        </div>
                                    </div>
                                    <div>
                                         <label class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            <span>Opacidade</span>
                                            <span id="opacity_val">{{ old('overlay_opacity', $slide->overlay_opacity ?? 50) }}%</span>
                                        </label>
                                        <input type="range" name="overlay_opacity" min="0" max="90" value="{{ old('overlay_opacity', $slide->overlay_opacity ?? 50) }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" oninput="document.getElementById('opacity_val').innerText = this.value + '%'">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    {{ isset($slide) ? 'Salvar Alterações' : 'Criar Slide' }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function setupPreview(inputId, previewContainerId, imgId, placeholderId) {
            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewContainerId);
            const img = document.getElementById(imgId);
            const placeholder = document.getElementById(placeholderId);

            if (!input) return;

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        if(placeholder) placeholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupPreview('slide_image', 'image-preview-container', 'image-preview', 'image-placeholder');
            setupPreview('slide_logo', 'logo-preview-container', 'logo-preview', 'logo-placeholder');
        });
    </script>
    @endpush
@endsection

