@extends('admin::components.layouts.master')

@section('title', 'Temas de projeção | Projeção')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.projection.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('admin.projection.index') }}" class="hover:underline">Projeção</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Temas</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Temas de projeção</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Fundo da tela: cor, gradiente, imagem ou vídeo.</p>
            </div>
        </div>
        <a href="{{ route('admin.projection.themes.create') }}"
            class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Novo tema
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($themes as $theme)
            @php $t = (object) $theme; @endphp
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $t->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">slug: {{ $t->slug ?? $t->id }}</p>
                        @if(!empty($t->background_type))
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Fundo: {{ $t->background_type }}</p>
                        @endif
                        @if(!empty($t->is_default))
                            <span class="inline-block mt-2 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30">Padrão</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.projection.themes.edit', $t->id) }}" class="p-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Editar">
                            <x-icon name="pencil" class="w-5 h-5" />
                        </a>
                        @if(empty($t->is_default))
                            <form action="{{ route('admin.projection.themes.default', $t->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-amber-500 transition-colors" title="Definir como padrão">
                                    <x-icon name="star" class="w-5 h-5" />
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.projection.themes.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este tema?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-red-500 transition-colors" title="Excluir">
                                <x-icon name="trash" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 px-6 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-5">
                    <x-icon name="palette" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">Nenhum tema cadastrado</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Crie temas com cor, gradiente, imagem ou vídeo de fundo para a tela de projeção.</p>
                <a href="{{ route('admin.projection.themes.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Criar primeiro tema
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
