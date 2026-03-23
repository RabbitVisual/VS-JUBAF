@extends('admin::components.layouts.master')

@section('title', 'Novo tema | Projeção')

@section('content')
<div class="space-y-8 max-w-2xl" x-data="themeForm('{{ old('background_type', 'solid') }}')">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.projection.themes.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                <a href="{{ route('admin.projection.index') }}" class="hover:underline">Projeção</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <a href="{{ route('admin.projection.themes.index') }}" class="hover:underline">Temas</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-400 dark:text-gray-500">Novo</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Novo tema</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Cor, gradiente, imagem ou vídeo de fundo.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 md:p-8">
        @if(session('warning'))
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-sm font-bold">
                {{ session('warning') }}
            </div>
        @endif
        <form action="{{ route('admin.projection.themes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Escuro, Culto Solene"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Slug (opcional)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto gerado"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tipo de fundo</label>
                <select name="background_type" x-model="backgroundType"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                    <option value="solid" {{ old('background_type', 'solid') === 'solid' ? 'selected' : '' }}>Cor sólida</option>
                    <option value="gradient" {{ old('background_type') === 'gradient' ? 'selected' : '' }}>Gradiente</option>
                    <option value="image" {{ old('background_type') === 'image' ? 'selected' : '' }}>Imagem</option>
                    <option value="video" {{ old('background_type') === 'video' ? 'selected' : '' }}>Vídeo</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300"
                    x-text="backgroundType === 'solid' ? 'Cor (ex: #0f172a)' : backgroundType === 'gradient' ? 'Gradiente CSS' : backgroundType === 'image' ? 'URL da imagem' : 'URL do vídeo'"></label>
                <input type="text" name="background_value" id="theme_bg_value" value="{{ old('background_value') }}"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                    :placeholder="backgroundType === 'solid' ? '#0f172a' : backgroundType === 'gradient' ? 'linear-gradient(to bottom, #1e293b, #0f172a)' : 'https://... ou envie um arquivo abaixo'">
                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="backgroundType === 'solid'" x-transition>Ex.: #0f172a, #1e3a8a, rgb(15, 23, 42)</p>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="backgroundType === 'gradient'" x-transition>Use o valor CSS completo. Ex.: <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">linear-gradient(to bottom, #1e293b, #0f172a)</code> ou <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">linear-gradient(135deg, #667eea 0%, #764ba2 100%)</code></p>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="backgroundType === 'image'" x-transition>Use a <strong>URL direta da imagem</strong> (que termina em .jpg, .png ou similar). Não use o link da página. No Unsplash: abra a foto → botão Download ou clique com o botão direito na imagem → &quot;Copiar endereço da imagem&quot;.</p>
            </div>

            <div class="space-y-2" x-show="backgroundType === 'image'" x-transition x-cloak>
                <span class="block text-xs font-bold text-gray-500 dark:text-gray-400">Ou envie uma imagem (JPG, PNG, GIF, WebP)</span>
                <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-blue-600 file:text-white"
                    @change="uploadThemeFile($event, 'theme_bg_value')">
            </div>

            <div class="space-y-2" x-show="backgroundType === 'video'" x-transition x-cloak>
                <span class="block text-xs font-bold text-gray-500 dark:text-gray-400">Ou envie um vídeo (MP4, WebM)</span>
                <input type="file" accept="video/mp4,video/webm" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-blue-600 file:text-white"
                    @change="uploadThemeFile($event, 'theme_bg_value')">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                <label for="is_default" class="text-sm font-bold text-gray-700 dark:text-gray-300">Definir como tema padrão</label>
            </div>

            <div class="flex flex-wrap gap-3 pt-4">
                <button type="submit" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Criar tema
                </button>
                <a href="{{ route('admin.projection.themes.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('themeForm', (initialType) => ({
        backgroundType: initialType,
        uploading: false,
        async uploadThemeFile(event, inputId) {
            const file = event.target.files?.[0];
            if (!file) return;
            const input = document.getElementById(inputId);
            if (!input) return;
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            try {
                const res = await fetch('{{ url("/api/v1/projection/assets") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });
                const data = await res.json();
                const url = data?.data?.url || data?.url;
                if (url) input.value = url;
                else alert('Falha ao enviar arquivo.');
            } catch (e) {
                alert('Erro ao enviar arquivo.');
            }
            event.target.value = '';
            this.uploading = false;
        }
    }));
});
</script>
@endpush
@endsection
