@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar meta</h1>
                        <p class="text-gray-300 max-w-xl">{{ $goal->name }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('treasury.goals.show', $goal) }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                            <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                        </a>
                    </div>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Metas' => route('treasury.goals.index'), 'Editar' => null]])
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative max-w-5xl">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <form action="{{ route('treasury.goals.update', $goal) }}" method="POST" class="divide-y divide-slate-100 dark:divide-slate-800" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando alterações...' } }))">
                @csrf
                @method('PUT')

                <div class="p-10 md:p-14 space-y-10">
                    <!-- Main Info Grid -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 font-black text-sm">01</div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Definição do Objetivo</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="md:col-span-2 space-y-4">
                                <label for="name" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Identificação da Meta</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                        <x-icon name="bullseye-arrow" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <input type="text" name="name" id="name" required value="{{ old('name', $goal->name) }}"
                                        class="block w-full pl-16 pr-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-lg"
                                        placeholder="Ex: Reforma do Templo Principal 2025">
                                </div>
                                @error('name')
                                    <p class="text-[10px] font-black text-red-500 uppercase tracking-widest ml-4">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 space-y-4">
                                <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Propósito Detalhado</label>
                                <textarea name="description" id="description" rows="4"
                                    class="block w-full px-8 py-6 rounded-4xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium"
                                    placeholder="Descreva os objetivos desta meta e o impacto esperado...">{{ old('description', $goal->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Visual Identity Section (NEW) -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 font-black text-sm">02</div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Identidade Visual</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <!-- Icon Selector -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Escolha um Ícone</label>
                                <div class="grid grid-cols-5 gap-3">
                                    @php
                                        $commonIcons = ['bullseye-arrow', 'flag-checkered', 'sack-dollar', 'hand-holding-seedling', 'building-columns', 'church', 'heart', 'users-medical', 'microphone', 'book-bible'];
                                        $currentIcon = old('icon', $goal->icon ?? 'bullseye-arrow');
                                    @endphp
                                    @foreach($commonIcons as $icon)
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="icon_select" value="{{ $icon }}" class="peer sr-only" {{ $currentIcon === $icon ? 'checked' : '' }}>
                                            <div class="w-12 h-12 rounded-xl border-2 border-slate-100 dark:border-slate-800 flex items-center justify-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 group-hover:bg-slate-50 dark:group-hover:bg-slate-800">
                                                <x-icon name="{{ $icon }}" style="duotone" class="w-6 h-6 text-slate-400 peer-checked:text-blue-600 transition-colors" />
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <label for="custom_icon" class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Ou digite o nome oficial (FontAwesome Pro)</label>
                                    <div class="relative group">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">fa-</div>
                                        <input type="text" id="custom_icon"
                                            class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-sm"
                                            placeholder="globe, rocket, star..."
                                            value="{{ !in_array($currentIcon, $commonIcons) ? $currentIcon : '' }}"
                                            oninput="document.querySelectorAll('input[name=icon_select]').forEach(r => r.checked = false);">
                                    </div>
                                </div>
                            </div>

                            <!-- Color Selector -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Cor de Destaque</label>
                                <div class="grid grid-cols-4 gap-3">
                                    @foreach(['indigo' => 'bg-indigo-500', 'purple' => 'bg-purple-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500', 'amber' => 'bg-amber-500', 'blue' => 'bg-blue-500', 'slate' => 'bg-slate-600', 'pink' => 'bg-pink-500'] as $color => $class)
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" {{ old('color', $goal->color ?? 'blue') === $color ? 'checked' : '' }}>
                                            <div class="w-full h-10 rounded-lg {{ $class }} opacity-60 transition-all peer-checked:opacity-100 peer-checked:ring-4 peer-checked:ring-{{ $color }}-500/20 peer-checked:scale-105 border-4 border-white dark:border-slate-900 shadow-sm"></div>
                                            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[9px] font-black uppercase opacity-0 peer-checked:opacity-100 transition-opacity whitespace-nowrap text-{{ $color }}-500">{{ $color }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameters Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                             <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 font-black text-sm">03</div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Parâmetros de Vínculo</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <label for="type" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Tipo de Medição</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center text-slate-400">
                                        <x-icon name="chart-simple" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <select name="type" id="type" required
                                        class="block w-full pl-16 pr-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold appearance-none">
                                        <option value="monthly" {{ old('type', $goal->type) === 'monthly' ? 'selected' : '' }}>Mensal</option>
                                        <option value="yearly" {{ old('type', $goal->type) === 'yearly' ? 'selected' : '' }}>Anual</option>
                                        <option value="campaign" {{ old('type', $goal->type) === 'campaign' ? 'selected' : '' }}>Campanha</option>
                                        <option value="custom" {{ old('type', $goal->type) === 'custom' ? 'selected' : '' }}>Personalizada</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label for="category" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Categoria Base</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center text-slate-400">
                                        <x-icon name="tags" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <select name="category" id="category"
                                        class="block w-full pl-16 pr-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold appearance-none">
                                        <option value="">Todas as categorias</option>
                                        <option value="tithe" {{ old('category', $goal->category) === 'tithe' ? 'selected' : '' }}>Dízimos</option>
                                        <option value="offering" {{ old('category', $goal->category) === 'offering' ? 'selected' : '' }}>Ofertas</option>
                                        <option value="donation" {{ old('category', $goal->category) === 'donation' ? 'selected' : '' }}>Doações</option>
                                        <option value="total_income" {{ old('category', $goal->category) === 'total_income' ? 'selected' : '' }}>Receita Total</option>
                                        <option value="campaign" {{ old('category', $goal->category) === 'campaign' ? 'selected' : '' }}>Campanhas</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-4 md:col-span-2">
                                <label for="campaign_id" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Campanha Vinculada (Opcional)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-6 flex items-center text-slate-400">
                                        <x-icon name="bullhorn" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <select name="campaign_id" id="campaign_id"
                                        class="block w-full pl-16 pr-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold appearance-none">
                                        <option value="">Nenhuma campanha selecionada</option>
                                        @foreach ($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}" {{ old('campaign_id', $goal->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Values and Dates Card -->
                    <div class="space-y-8">
                         <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 font-black text-sm">04</div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Dimensionamento e Prazos</h3>
                        </div>

                        <div class="p-10 bg-slate-50 dark:bg-slate-800/30 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-4">
                                    <label for="target_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Valor Objetivo</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-slate-400 font-bold">
                                            R$
                                        </div>
                                        <input type="number" name="target_amount" id="target_amount" step="0.01" min="0.01" required value="{{ old('target_amount', $goal->target_amount) }}"
                                            class="block w-full pl-14 pr-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-black text-xl"
                                            placeholder="0,00">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label for="start_date" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Início do Ciclo</label>
                                    <input type="date" name="start_date" id="start_date" required value="{{ old('start_date', $goal->start_date->toDateString()) }}"
                                        class="block w-full px-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold">
                                </div>

                                <div class="space-y-4">
                                    <label for="end_date" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-4">Data Final</label>
                                    <input type="date" name="end_date" id="end_date" required value="{{ old('end_date', $goal->end_date->toDateString()) }}"
                                        class="block w-full px-8 py-5 rounded-3xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold">
                                </div>
                            </div>

                            <div class="flex items-center gap-6 p-4">
                                 <div class="relative inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $goal->is_active) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600 transition-all"></div>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Meta Ativa e Monitorada</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Desative se desejar interromper temporariamente o acompanhamento.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Icon Input -->
                <input type="hidden" name="icon" id="final_icon" value="{{ old('icon', $goal->icon ?? 'bullseye-arrow') }}">

                <!-- Action Buttons -->
                <div class="px-10 py-8 md:px-14 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end gap-8">
                    <button type="submit"
                        class="inline-flex items-center px-12 py-5 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-xl shadow-blue-600/20 rounded-3xl font-black transition-all hover:-translate-y-1 active:scale-95">
                        <x-icon name="check" style="duotone" class="w-5 h-5 mr-3" />
                        SALVAR ALTERAÇÕES
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const finalIcon = document.getElementById('final_icon');
        const customIcon = document.getElementById('custom_icon');
        const iconRadios = document.querySelectorAll('input[name="icon_select"]');

        iconRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    finalIcon.value = this.value;
                    customIcon.value = '';
                }
            });
        });

        customIcon.addEventListener('input', function() {
            if (this.value) {
                finalIcon.value = this.value;
                iconRadios.forEach(r => r.checked = false);
            }
        });
    });
    </script>
@endsection
