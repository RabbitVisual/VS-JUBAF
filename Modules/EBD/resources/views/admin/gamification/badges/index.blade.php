@extends('admin::components.layouts.master')

@section('title', 'Medalhas & Conquistas | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Recompensas</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Medalhas & <span class="text-amber-600">Badges</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gerencie o sistema de conquistas para incentivar a participação e o estudo contínuo.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add New Badge Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden sticky top-8">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nova Medalha</h3>
                </div>
                <form action="{{ route('admin.ebd.gamification.badges.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome da Medalha</label>
                        <input type="text" name="name" required placeholder="Ex: Primeiro Estudo, 100% Presença..."
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Identificador (Slug)</label>
                        <input type="text" name="slug" required placeholder="ex-primeiro-estudo"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição</label>
                        <textarea name="description" rows="3" placeholder="O que o aluno precisa fazer?"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ícone (FontAwesome)</label>
                        <div class="relative group">
                            <x-icon name="icons" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-amber-600 transition-colors" />
                            <input type="text" name="icon_path" required placeholder="Ex: certificate, star, award"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_hidden" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-600 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Oculta (Surpresa)</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">Apenas visível ao ganhar</span>
                        </div>
                    </label>

                    <button type="submit"
                        class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                        Criar Medalha
                    </button>
                </form>
            </div>
        </div>

        <!-- Badges List -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 auto-rows-min">
            @forelse($badges as $badge)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-4 group hover:shadow-md transition-all">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                        <x-icon name="{{ $badge->icon_path }}" style="duotone" class="w-8 h-8" />
                    </div>
                    @if($badge->is_hidden)
                        <span class="p-1 px-2 bg-gray-100 dark:bg-gray-800 text-[8px] font-black uppercase tracking-widest text-gray-400 rounded-lg">Secreta</span>
                    @endif
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $badge->name }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1 leading-relaxed">
                        {{ $badge->description ?? 'Sem descrição detalhada.' }}
                    </p>
                </div>
                <div class="pt-4 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between">
                    <span class="text-[9px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest">{{ $badge->slug }}</span>
                    <form action="{{ route('admin.ebd.gamification.badges.destroy', $badge->id) }}" method="POST" onsubmit="return confirm('Excluir esta medalha?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                            <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="award" style="duotone" class="w-10 h-10 text-gray-300" />
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nenhuma medalha criada</h3>
                <p class="text-xs text-gray-500 mt-2 font-medium">As conquistas motivam os alunos a irem mais longe.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

