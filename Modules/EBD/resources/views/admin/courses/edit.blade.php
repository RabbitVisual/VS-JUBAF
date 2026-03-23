@extends('admin::components.layouts.master')

@section('title', 'Editar Curso EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Currículo</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">EBD</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Editar <span class="text-blue-600">Curso</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">{{ $course->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.courses.show', $course) }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="eye" style="duotone" class="mr-2 h-4 w-4" />
                Ver
            </a>
            <a href="{{ route('admin.ebd.courses.index') }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <form action="{{ route('admin.ebd.courses.update', $course) }}" method="POST" class="p-8 space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando curso...' } }))">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome do curso</label>
                    <input type="text" name="name" value="{{ old('name', $course->name) }}" required
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 @error('name') ring-2 ring-red-500/20 @enderror">
                    @error('name') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $course->slug) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20">
                    @error('slug') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ordem</label>
                    <input type="number" name="order" value="{{ old('order', $course->order) }}" min="0"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 resize-none">{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Curso ativo</label>
                </div>
            </div>
            <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg">
                    <x-icon name="check" style="duotone" class="w-4 h-4 inline-block mr-2" />
                    Salvar
                </button>
                <a href="{{ route('admin.ebd.courses.index') }}" class="px-6 py-3 text-gray-500 hover:text-gray-900 dark:hover:text-white font-bold text-sm transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
