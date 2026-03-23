@extends('admin::components.layouts.master')

@section('title', 'Nova Categoria | Worship')

@section('content')
<div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center gap-5">
            <a href="{{ route('worship.admin.categories.index') }}"
                class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 flex items-center justify-center text-gray-400 hover:text-emerald-600 transition-all shadow-sm hover:shadow-xl group">
                <x-icon name="arrow-left" class="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
            </a>
            <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-1">
                    <span>Configurações</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500">Música</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight uppercase italic">Nova <span class="text-transparent bg-clip-text bg-linear-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">Categoria</span></h1>
            </div>
        </div>

        <div class="max-w-2xl mx-auto">
            <form action="{{ route('worship.admin.categories.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-8 md:p-10 space-y-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-12 -mt-12 w-48 h-48 bg-emerald-600/5 rounded-full blur-3xl"></div>

                    <div class="flex items-center gap-5 mb-4">
                        <div class="w-14 h-14 rounded-3xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                            <x-icon name="plus" class="w-7 h-7" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic">Definição da Categoria</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Grupos técnicos (Harmonia, Melodia, etc).</p>
                        </div>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <!-- Nome -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nome</label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Sopros"
                                    class="block w-full px-6 py-5 bg-gray-50 dark:bg-gray-950/50 border-transparent focus:border-emerald-500/50 focus:ring-0 rounded-3xl text-sm transition-all shadow-inner font-bold text-gray-900 dark:text-white">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <!-- Cor -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Cor do Tema</label>
                                <select name="color" required
                                        class="block w-full px-6 py-5 bg-gray-50 dark:bg-gray-950/50 border-transparent focus:border-emerald-500/50 focus:ring-0 rounded-3xl text-sm transition-all shadow-inner font-bold text-gray-900 dark:text-white appearance-none">
                                    <option value="purple">Roxo (Harmonia)</option>
                                    <option value="blue">Azul (Melodia)</option>
                                    <option value="orange">Laranja (Percussão)</option>
                                    <option value="pink">Rosa (Vocal)</option>
                                    <option value="green">Verde</option>
                                    <option value="yellow">Amarelo</option>
                                    <option value="gray">Cinza (Técnico)</option>
                                </select>
                            </div>

                            <!-- Icone -->
                             <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Identificador Visual (Heroicons)</label>
                                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="Ex: microphone"
                                       class="block w-full px-6 py-5 bg-gray-50 dark:bg-gray-950/50 border-transparent focus:border-emerald-500/50 focus:ring-0 rounded-3xl text-sm transition-all shadow-inner font-mono text-gray-900 dark:text-white">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-3 ml-1">Use ícones Font Awesome pelo componente <code class="text-emerald-600">&lt;x-icon name="nome-do-icone" /&gt;</code>.</p>
                            </div>
                        </div>

                         <!-- Descrição -->
                         <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Descrição</label>
                            <textarea name="description" rows="3" placeholder="Opcional..."
                                   class="block w-full px-6 py-5 bg-gray-50 dark:bg-gray-950/50 border-transparent focus:border-emerald-500/50 focus:ring-0 rounded-3xl text-sm transition-all shadow-inner font-medium text-gray-900 dark:text-white">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center sm:justify-end gap-6 pt-4">
                    <a href="{{ route('worship.admin.categories.index') }}" class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-10 py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-4xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all transform hover:scale-[1.02] active:scale-95">
                        Salvar Categoria
                    </button>
                </div>
            </form>
        </div>
</div>
@endsection

