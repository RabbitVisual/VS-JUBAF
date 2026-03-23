@extends('pastoralpanel::components.layouts.master')

@section('title', 'Editar Série - Administração')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Série</h1>
        <a href="{{ route('pastor.sermoes.series.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('pastor.sermoes.series.update', $series) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                <input type="text" name="title" id="title" value="{{ old('title', $series->title) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white sm:text-sm">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white sm:text-sm">{{ old('description', $series->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white sm:text-sm transition-all">
                        <option value="draft" {{ old('status', $series->status) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', $series->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ old('status', $series->status) == 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>

                <!-- Image -->
                <div>
                    <label for="image_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagem de Capa</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="image-preview" class="w-20 h-20 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-slate-600">
                             @if($series->image)
                                <img src="{{ asset('storage/' . $series->image) }}" class="w-full h-full object-cover">
                             @else
                                <x-icon name="photograph" class="w-8 h-8 text-gray-400" />
                             @endif
                        </div>
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 transition-all cursor-pointer"
                            onchange="previewImage(event)">
                    </div>
                    @error('image_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Options -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30">
                <div class="flex items-center">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $series->is_featured) ? 'checked' : '' }}
                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg transition-all">
                    <label for="is_featured" class="ml-3 block text-sm font-bold text-blue-900 dark:text-blue-300">
                        Destacar esta série na página inicial
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex items-center justify-center py-3 px-8 border border-transparent shadow-lg text-base font-black rounded-xl text-white bg-amber-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all hover:-translate-y-0.5">
                    <x-icon name="save" class="w-5 h-5 mr-2" />
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
@endsection

