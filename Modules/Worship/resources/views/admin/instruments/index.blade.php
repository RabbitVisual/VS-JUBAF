@extends('admin::components.layouts.master')

@section('title', 'Instrumentos | Worship')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-1.5">
                <span>Módulo de Louvor</span>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <span class="text-gray-400 dark:text-gray-500">Configurações</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Gestão de <span class="text-transparent bg-clip-text bg-linear-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">Instrumentos</span></h1>
        </div>
        <a href="{{ route('worship.admin.instruments.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Novo Instrumento
        </a>
    </div>

    @if($instruments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($instruments as $instrument)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all duration-300">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 bg-emerald-600/5 rounded-full blur-2xl group-hover:bg-emerald-600/10 transition-colors"></div>

                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-3xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform duration-500">
                                    <x-icon name="{{ $instrument->icon }}" class="w-8 h-8" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $instrument->name }}</h3>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-{{ $instrument->category->color ?? 'gray' }}-500 bg-{{ $instrument->category->color ?? 'gray' }}-50 dark:bg-{{ $instrument->category->color ?? 'gray' }}-900/50 px-2 py-0.5 rounded-lg border border-{{ $instrument->category->color ?? 'gray' }}-200 dark:border-{{ $instrument->category->color ?? 'gray' }}-800">
                                        {{ $instrument->category->name ?? 'Sem Categoria' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <a href="{{ route('worship.admin.instruments.edit', $instrument->id) }}" class="p-3 bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-emerald-500 rounded-2xl transition-all" title="Editar">
                                    <x-icon name="pencil" class="w-5 h-5" />
                                </a>
                                <form action="{{ route('worship.admin.instruments.destroy', $instrument->id) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este instrumento? Músicos escalados com ele serão afetados.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-3 bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-red-500 rounded-2xl transition-all" title="Excluir">
                                        <x-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Mini Stats (Placeholders for now) -->
                        <div class="mt-6 pt-6 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Uso na Escala</span>
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-800 border-2 border-white dark:border-gray-900 overflow-hidden flex items-center justify-center">
                                        <x-icon name="user" class="w-3 h-3 text-gray-400" />
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $instrument->rosters_count }} escalas</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 px-4">
                 {{ $instruments->links('pagination::tailwind') }}
            </div>
        @else
            <!-- Enhanced Empty State -->
            <div class="flex flex-col items-center justify-center py-24 px-4 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <x-icon name="collection" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum instrumento cadastrado</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-6">Defina os instrumentos e funções para organizar suas escalas de louvor.</p>
                <a href="{{ route('worship.admin.instruments.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-lg shadow-emerald-500/20 transition-all">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Cadastrar Primeiro Instrumento
                </a>
            </div>
        @endif
</div>
@endsection

