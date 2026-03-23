@extends('admin::components.layouts.master')

@section('title', 'Níveis de Gamificação | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-purple-600 text-white rounded">Gamificação</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Níveis de <span class="text-purple-600">Progressão</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Configure a jornada de aprendizado e os requisitos de XP para cada estágio do aluno.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add New Level Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden sticky top-8">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Novo Nível</h3>
                </div>
                <form action="{{ route('admin.ebd.gamification.levels.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Número do Nível</label>
                        <input type="number" name="level_number" required min="1"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome do Nível</label>
                        <input type="text" name="name" required placeholder="Ex: Aprendiz, Mestre..."
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">XP Necessário</label>
                        <input type="number" name="xp_required" required min="0"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Slug do Ícone (FontAwesome)</label>
                        <div class="relative group">
                            <x-icon name="icons" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-purple-600 transition-colors" />
                            <input type="text" name="icon_path" required placeholder="Ex: star, medal, trophy"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-purple-500/20 transition-all">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-purple-600/20 active:scale-95">
                        Cadastrar Nível
                    </button>
                </form>
            </div>
        </div>

        <!-- Levels List -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($levels as $level)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                        <x-icon name="{{ $level->icon_path }}" style="duotone" class="w-8 h-8" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 bg-purple-600 text-white text-[9px] font-black uppercase tracking-widest rounded">Nível {{ $level->level_number }}</span>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight uppercase">{{ $level->name }}</h3>
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1">
                                <x-icon name="bolt" class="w-3 h-3 text-amber-500" />
                                {{ number_format($level->xp_required, 0, ',', '.') }} XP Mínimo
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.ebd.gamification.levels.destroy', $level->id) }}" method="POST" onsubmit="return confirm('Excluir este nível?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-3 text-gray-400 hover:text-red-500 transition-colors">
                            <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="layer-group" style="duotone" class="w-10 h-10 text-gray-300" />
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Sem níveis configurados</h3>
                <p class="text-xs text-gray-500 mt-2 font-medium">Use o formulário ao lado para iniciar a progressão dos alunos.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

