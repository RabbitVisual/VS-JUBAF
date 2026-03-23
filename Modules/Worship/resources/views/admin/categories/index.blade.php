@extends('admin::components.layouts.master')

@section('title', 'Categorias de Instrumentos | Worship')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-1.5">
                <span>Módulo de Louvor</span>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <span class="text-gray-400 dark:text-gray-500">Configurações</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Categorias <span class="text-transparent bg-clip-text bg-linear-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">Técnicas</span></h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('worship.admin.instruments.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="music-note" class="w-4 h-4 mr-2" />
                Voltar para Instrumentos
            </a>
            <a href="{{ route('worship.admin.categories.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Nova Categoria
            </a>
        </div>
    </div>

    @if($categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($categories as $category)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all duration-300">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 bg-{{ $category->color }}-500/5 rounded-full blur-2xl group-hover:bg-{{ $category->color }}-500/10 transition-colors"></div>

                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-3xl bg-{{ $category->color }}-100 dark:bg-{{ $category->color }}-900/30 flex items-center justify-center text-{{ $category->color }}-600 dark:text-{{ $category->color }}-400 shadow-lg shadow-{{ $category->color }}-500/10 group-hover:scale-110 transition-transform duration-500">
                                    <x-icon name="{{ $category->icon ?? 'tag' }}" class="w-8 h-8" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $category->name }}</h3>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 font-mono">{{ $category->instruments_count }} Instrumentos</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <a href="{{ route('worship.admin.categories.edit', $category->id) }}" class="p-3 bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-emerald-500 rounded-2xl transition-all" title="Editar">
                                    <x-icon name="pencil" class="w-5 h-5" />
                                </a>
                                @if($category->instruments_count === 0)
                                    <form action="{{ route('worship.admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Excluir esta categoria?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-3 bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-red-500 rounded-2xl transition-all" title="Excluir">
                                            <x-icon name="trash" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 px-4">
                 {{ $categories->links('pagination::tailwind') }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 px-4 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <x-icon name="collection" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhuma categoria encontrada</h3>
                <a href="{{ route('worship.admin.categories.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-lg shadow-emerald-500/20 transition-all">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Criar Primeira Categoria
                </a>
            </div>
        @endif
</div>
@endsection

