@extends('memberpanel::components.layouts.master')

@section('page-title', 'Concluir Pedido')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li>Intercessão</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-blue-600 dark:text-blue-400">Concluir Pedido</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Concluir Pedido</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Compartilhe sua vitória ou encerre este ciclo de oração.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('member.intercessor.requests.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Voltar aos Meus Pedidos
             </a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30 rounded-3xl p-8 mb-8">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <x-icon name="information-circle" class="w-6 h-6" />
            </div>
            <div>
                <h3 class="text-xl font-black text-indigo-900 dark:text-indigo-300">{{ $request->title }}</h3>
                <p class="text-xs font-bold text-indigo-600 dark:text-indigo-500 uppercase tracking-widest mt-1">Pedido registrado em {{ $request->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <p class="text-indigo-800/70 dark:text-indigo-300/60 font-medium italic">"{{ Str::limit($request->description, 200) }}"</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <form action="{{ route('member.intercessor.requests.answered', $request) }}" method="POST" class="p-8 md:p-12">
            @csrf
            <div class="space-y-8" x-data="{ mode: '{{ old('mode', 'archive') }}' }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Just Archive -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="mode" value="archive" x-model="mode" class="peer sr-only">
                        <div class="h-full p-8 rounded-4xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 peer-checked:border-blue-600 peer-checked:bg-blue-50/30 dark:peer-checked:bg-blue-900/10 transition-all group-hover:border-blue-200 shadow-sm peer-checked:shadow-lg peer-checked:shadow-blue-500/10">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 peer-checked:bg-blue-600 rounded-2xl flex items-center justify-center text-gray-500 dark:text-gray-400 peer-checked:text-white transition-colors group-hover:scale-110">
                                    <x-icon name="archive" class="w-6 h-6" />
                                </div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Arquivar</h4>
                            </div>
                            <p class="text-xs font-bold text-gray-400 group-hover:text-gray-500 transition-colors leading-relaxed uppercase tracking-widest">
                                Apenas encerra o pedido sem publicar um testemunho público.
                            </p>
                            <div class="absolute top-6 right-6 w-5 h-5 border-2 border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        </div>
                    </label>

                    <!-- Publish Testimony -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="mode" value="testimony" x-model="mode" class="peer sr-only">
                        <div class="h-full p-8 rounded-4xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/30 dark:peer-checked:bg-emerald-900/10 transition-all group-hover:border-emerald-200 shadow-sm peer-checked:shadow-lg peer-checked:shadow-emerald-500/10">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 peer-checked:bg-emerald-600 rounded-2xl flex items-center justify-center text-gray-500 dark:text-gray-400 peer-checked:text-white transition-colors group-hover:scale-110">
                                    <x-icon name="speakerphone" class="w-6 h-6" />
                                </div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Testemunhar</h4>
                            </div>
                            <p class="text-xs font-bold text-gray-400 group-hover:text-gray-500 transition-colors leading-relaxed uppercase tracking-widest">
                                Compartilha sua bênção com toda a igreja no mural de testemunhos.
                            </p>
                            <div class="absolute top-6 right-6 w-5 h-5 border-2 border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center peer-checked:border-emerald-600 peer-checked:bg-emerald-600 transition-all">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Testimony Textarea (Conditional) -->
                <div x-show="mode === 'testimony'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6 pt-6 border-t border-gray-50 dark:border-gray-700">
                    <div>
                        <label class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-4">Seu Testemunho</label>
                        <textarea name="testimony" rows="6" class="w-full px-6 py-4 border border-gray-200 dark:border-gray-600 rounded-3xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-medium leading-relaxed" placeholder="Conte como Deus respondeu à sua oração...">{{ old('testimony') }}</textarea>
                        @error('testimony')
                            <p class="text-xs text-red-500 font-bold mt-2 uppercase tracking-widest">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100/50">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" class="w-5 h-5 rounded border-emerald-200 text-emerald-600 focus:ring-emerald-500">
                        <label for="is_anonymous" class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Publicar como anônimo</label>
                    </div>

                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100/50 flex items-start gap-3">
                        <x-icon name="information-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed">
                            Seu testemunho passará por moderação antes de aparecer no Mural de Testemunhos.
                        </p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-8 border-t border-gray-50 dark:border-gray-700">
                    <a href="{{ route('member.intercessor.requests.index') }}" class="text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-10 py-4 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-2xl transition-all shadow-xl shadow-slate-900/20 uppercase tracking-widest active:scale-95">
                        Confirmar e Concluir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

