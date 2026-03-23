@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar campanha</h1>
                        <p class="text-gray-300 max-w-xl">{{ $campaign->name }}</p>
                    </div>
                    <a href="{{ route('treasury.campaigns.index') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                        <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                    </a>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Campanhas' => route('treasury.campaigns.index'), 'Editar' => null]])
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative max-w-5xl">
            <div class="absolute right-0 top-0 w-40 h-40 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <form action="{{ route('treasury.campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" class="space-y-0" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando alterações...' } }))">
                @csrf
                @method('PUT')

                <div class="p-10 md:p-14 space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Name -->
                        <div class="space-y-3">
                            <label for="name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Nome da Campanha <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $campaign->name) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-3xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-slate-600">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="space-y-3">
                            <label for="slug" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Slug (URL amigável)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $campaign->slug) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-3xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-slate-600">
                            @error('slug')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-3">
                        <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Propósito e Descrição</label>
                        <textarea name="description" id="description" rows="5"
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-4xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-slate-600">{{ old('description', $campaign->description) }}</textarea>
                        @error('description')
                            <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Amount and Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="space-y-3">
                            <label for="target_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Meta Financeira (R$)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                    <span class="text-sm font-black tracking-tight">R$</span>
                                </div>
                                <input type="number" name="target_amount" id="target_amount" step="0.01" min="0" value="{{ old('target_amount', $campaign->target_amount) }}"
                                    class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-3xl text-sm font-black text-slate-900 dark:text-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all">
                            </div>
                            @error('target_amount')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="start_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Data de Início</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->start_date?->toDateString()) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-3xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all">
                            @error('start_date')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="end_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Data de Término</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date?->toDateString()) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-3xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all">
                            @error('end_date')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Image with Preview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-end">
                        <div class="md:col-span-2 space-y-4">
                            <label for="image" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Nova Capa (Opcional)</label>
                            <div class="group relative flex justify-center px-8 py-8 border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-4xl bg-slate-50 dark:bg-slate-800/20 hover:bg-slate-100/50 dark:hover:bg-slate-800/40 hover:border-amber-500 transition-all duration-300">
                                <div class="space-y-4 text-center">
                                    <div class="flex flex-col text-sm text-slate-600 dark:text-slate-400">
                                        <label for="image-upload" class="relative cursor-pointer font-black text-amber-600 dark:text-amber-400 hover:text-amber-500">
                                            <span>Alterar imagem da campanha</span>
                                            <input id="image-upload" name="image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">PNG, JPG, WEBP até 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($campaign->image)
                            <div class="space-y-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1 pr-4 text-right">Capa Atual</p>
                                <div class="relative h-32 rounded-3xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-700">
                                    <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center gap-6 p-8 bg-slate-50 dark:bg-slate-800/40 rounded-4xl border border-slate-100 dark:border-slate-800">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500 transition-all"></div>
                        </div>
                        <div class="space-y-1">
                            <label for="is_active" class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter">Status de Captação</label>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Define se a campanha permanece aberta para recebimento de doações no painel.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="relative px-6 py-6 md:px-10 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-8 py-3.5 bg-gray-900 dark:bg-amber-600 text-white rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-amber-700 shadow-lg transition-all">
                        <x-icon name="floppy-disk" style="duotone" class="w-5 h-5 mr-2" />
                        Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
