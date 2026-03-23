@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Editar Campanha')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-5xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar campanha</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">{{ $campaign->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.campaigns.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">Voltar</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <form action="{{ route('memberpanel.treasury.campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" class="space-y-0">
                @csrf
                @method('PUT')

                <div class="p-10 md:p-14 space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Name -->
                        <div class="space-y-3">
                            <label for="name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Nome da Campanha <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $campaign->name) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all"
                                placeholder="Ex: Reforma do Templo Central">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="space-y-3">
                            <label for="slug" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Slug (URL amigável)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $campaign->slug) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all"
                                placeholder="reforma-do-templo">
                            @error('slug')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-3">
                        <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Propósito e Descrição</label>
                        <textarea name="description" id="description" rows="5"
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-4xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all"
                            placeholder="Descreva o objetivo espiritual e financeiro desta mobilização...">{{ old('description', $campaign->description) }}</textarea>
                        @error('description')
                            <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Amount and Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="space-y-3">
                            <label for="target_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Meta Financeira (R$)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-slate-400 group-focus-within:text-purple-500 transition-colors">
                                    <span class="text-sm font-black tracking-tight">R$</span>
                                </div>
                                <input type="number" name="target_amount" id="target_amount" step="0.01" min="0" value="{{ old('target_amount', $campaign->target_amount) }}"
                                    class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-black text-slate-900 dark:text-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all"
                                    placeholder="0,00">
                            </div>
                            @error('target_amount')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="start_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Data de Início</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->start_date?->toDateString()) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all">
                            @error('start_date')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="end_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Data de Término</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date?->toDateString()) }}"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all">
                            @error('end_date')
                                <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Image Section -->
                    <div class="space-y-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Identidade Visual da Campanha</label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                            @if ($campaign->image)
                                <div class="relative group rounded-[2.5rem] overflow-hidden border border-slate-200 dark:border-slate-700 aspect-video bg-slate-100 dark:bg-slate-800">
                                    <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-white text-xs font-black uppercase tracking-widest">Capa Atual</span>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <div class="group relative flex justify-center px-6 py-8 border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-4xl bg-slate-50 dark:bg-slate-800/20 hover:bg-slate-100/50 dark:hover:bg-slate-800/40 hover:border-purple-500 transition-all duration-300">
                                    <div class="space-y-2 text-center">
                                        <x-icon name="cloud-arrow-up" style="duotone" class="h-8 w-8 text-slate-400 group-hover:text-purple-500 transition-colors mx-auto" />
                                        <div class="flex text-xs text-slate-600 dark:text-slate-400">
                                            <label for="image-upload" class="relative cursor-pointer font-black text-purple-600 dark:text-purple-400 hover:text-purple-500">
                                                <span>Alterar Cobertura Visual</span>
                                                <input id="image-upload" name="image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest text-center">Deixe vazio para manter a atual • PNG, JPG até 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center gap-6 p-8 bg-slate-50 dark:bg-slate-800/40 rounded-4xl border border-slate-100 dark:border-slate-700">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600 transition-all"></div>
                        </div>
                        <div class="space-y-1">
                            <label for="is_active" class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter">Status Operacional</label>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Define se a campanha está publicamente visível e aceitando contribuições.</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 md:px-10 bg-gray-50/50 dark:bg-slate-800/30 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all">
                        <x-icon name="check" style="duotone" class="w-4 h-4 mr-2" />
                        Salvar alterações
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection
