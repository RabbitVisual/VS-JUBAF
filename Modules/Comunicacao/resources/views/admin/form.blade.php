@php
    $editing = $postagem->exists;
@endphp
@extends('admin::components.layouts.master')

@section('title', $editing ? 'Editar postagem' : 'Nova postagem')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.comunicacao.postagens.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Voltar à listagem
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">{{ $editing ? 'Editar postagem' : 'Nova postagem' }}</h1>
        </div>

        <form method="POST"
            action="{{ $editing ? route('admin.comunicacao.postagens.update', $postagem) : route('admin.comunicacao.postagens.store') }}"
            enctype="multipart/form-data"
            class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 p-6 md:p-8 space-y-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div>
                <label for="titulo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $postagem->titulo) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" />
                @error('titulo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tipo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="tipo" id="tipo" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    @foreach (['edital' => 'Edital', 'ata' => 'Ata', 'aviso' => 'Aviso', 'noticia' => 'Notícia'] as $v => $label)
                        <option value="{{ $v }}" @selected(old('tipo', $postagem->tipo) === $v)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="conteudo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Conteúdo <span class="text-red-500">*</span></label>
                <textarea name="conteudo" id="conteudo" rows="14" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 font-sans text-sm leading-relaxed">{{ old('conteudo', $postagem->conteudo) }}</textarea>
                @error('conteudo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="anexo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Anexo (opcional)</label>
                <input type="file" name="anexo" id="anexo"
                    class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-300" />
                @error('anexo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if ($editing && $postagem->anexo_path)
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Arquivo atual:
                        <a href="{{ asset($postagem->anexo_path) }}" target="_blank" class="text-blue-600 dark:text-blue-400 underline">baixar</a>
                        — enviar um novo arquivo substitui o anterior.
                    </p>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.comunicacao.postagens.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-800 text-sm font-medium">Cancelar</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/20">
                    {{ $editing ? 'Salvar alterações' : 'Publicar' }}
                </button>
            </div>
        </form>
    </div>
@endsection
