@extends('admin::components.layouts.master')

@section('title', 'Editar template de card | Projeção')

@section('content')
<div class="space-y-8 max-w-2xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.projection.card-templates.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                <a href="{{ route('admin.projection.index') }}" class="hover:underline">Projeção</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <a href="{{ route('admin.projection.card-templates.index') }}" class="hover:underline">Templates</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-400 dark:text-gray-500">Editar</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Editar template</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $template['name'] ?? '' }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 md:p-8">
        <form action="{{ route('admin.projection.card-templates.update', $template['id']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" name="name" value="{{ old('name', $template['name'] ?? '') }}" required
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $template['slug'] ?? '') }}"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Rótulo do título</label>
                    <input type="text" name="title_label" value="{{ old('title_label', $template['title_label'] ?? '') }}"
                        class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Rótulo do subtítulo</label>
                    <input type="text" name="subtitle_label" value="{{ old('subtitle_label', $template['subtitle_label'] ?? '') }}"
                        class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tipo de fundo</label>
                <select name="background_type" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                    <option value="solid" {{ old('background_type', $template['background_type'] ?? 'solid') === 'solid' ? 'selected' : '' }}>Cor sólida</option>
                    <option value="gradient" {{ old('background_type', $template['background_type'] ?? '') === 'gradient' ? 'selected' : '' }}>Gradiente</option>
                    <option value="image" {{ old('background_type', $template['background_type'] ?? '') === 'image' ? 'selected' : '' }}>Imagem</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Valor do fundo</label>
                <input type="text" name="background_value" value="{{ old('background_value', $template['background_value'] ?? '') }}"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Fonte (opcional)</label>
                <input type="text" name="font_family" value="{{ old('font_family', $template['font_family'] ?? '') }}"
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default', $template['is_default'] ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                <label for="is_default" class="text-sm font-bold text-gray-700 dark:text-gray-300">Definir como padrão</label>
            </div>

            <div class="flex flex-wrap gap-3 pt-4">
                <button type="submit" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Salvar alterações
                </button>
                <a href="{{ route('admin.projection.card-templates.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
