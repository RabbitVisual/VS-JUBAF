@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Nova Meta')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-4xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Nova meta</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Definir objetivo financeiro.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.goals.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">Voltar</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <form action="{{ route('memberpanel.treasury.goals.store') }}" method="POST">
            @csrf

            <div class="p-8 md:p-12 space-y-12">
                <!-- Section 1: Definition -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 font-black text-sm">01</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Definição do Objetivo</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-8">
                        <div class="space-y-2">
                            <label for="name" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="flag-checkered" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                                Título da Meta
                                <span class="text-rose-500 ml-1">*</span>
                            </label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium placeholder:text-slate-400"
                                placeholder="Ex: Aquisição do Terreno Adjacente">
                            @error('name')
                                <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="align-left" style="duotone" class="w-4 h-4 mr-2 text-slate-400" />
                                Detalhamento Estratégico
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium placeholder:text-slate-400"
                                placeholder="Descreva o impacto e a necessidade deste objetivo...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Visual Identity -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 font-black text-sm">02</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Identidade Visual</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Icon Selector -->
                        <div class="space-y-4">
                            <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="icons" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                                Escolha um Ícone
                            </label>
                            <div class="grid grid-cols-5 gap-3">
                                @foreach(['bullseye-arrow', 'flag-checkered', 'sack-dollar', 'hand-holding-seedling', 'building-columns', 'church', 'heart', 'users-medical', 'microphone', 'book-bible'] as $icon)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="icon_select" value="{{ $icon }}" class="peer sr-only" {{ old('icon', 'bullseye-arrow') === $icon ? 'checked' : '' }}>
                                        <div class="w-12 h-12 rounded-xl border-2 border-slate-100 dark:border-slate-800 flex items-center justify-center transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 group-hover:bg-slate-50 dark:group-hover:bg-slate-800">
                                            <x-icon name="{{ $icon }}" style="duotone" class="w-6 h-6 text-slate-400 peer-checked:text-indigo-600 transition-colors" />
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <label for="custom_icon" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Ou digite o nome oficial (FontAwesome Pro)</label>
                                <div class="relative group">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">fa-</div>
                                    <input type="text" id="custom_icon"
                                        class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                                        placeholder="ex: globe, rocket, star..."
                                        oninput="document.querySelectorAll('input[name=icon]').forEach(r => r.checked = false); if(this.value) { /* live preview could go here */ }">
                                </div>
                                <p class="mt-2 text-[9px] text-slate-400 italic">Dica: Use os nomes do <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-500 underline">FontAwesome</a></p>
                            </div>
                        </div>

                        <!-- Color Selector -->
                        <div class="space-y-4">
                            <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="palette" style="duotone" class="w-4 h-4 mr-2 text-purple-500" />
                                Cor de Destaque
                            </label>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach(['indigo' => 'bg-indigo-500', 'purple' => 'bg-purple-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500', 'amber' => 'bg-amber-500', 'blue' => 'bg-blue-500', 'slate' => 'bg-slate-600', 'pink' => 'bg-pink-500'] as $color => $class)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" {{ old('color', 'indigo') === $color ? 'checked' : '' }}>
                                        <div class="w-full h-10 rounded-lg {{ $class }} opacity-60 transition-all peer-checked:opacity-100 peer-checked:ring-4 peer-checked:ring-{{ $color }}-500/20 peer-checked:scale-105 border-4 border-white dark:border-slate-900 shadow-sm"></div>
                                        <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 text-[8px] font-black uppercase opacity-0 peer-checked:opacity-100 transition-opacity whitespace-nowrap text-{{ $color }}-500">{{ $color }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Parameters -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500 font-black text-sm">03</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Parâmetros de Vínculo</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="type" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="layers-group" style="duotone" class="w-4 h-4 mr-2 text-purple-500" />
                                Ciclo da Meta
                                <span class="text-rose-500 ml-1">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all cursor-pointer">
                                <option value="">Selecione o horizonte...</option>
                                <option value="monthly" {{ old('type') === 'monthly' ? 'selected' : '' }}>Mensal (Recorrente)</option>
                                <option value="yearly" {{ old('type') === 'yearly' ? 'selected' : '' }}>Anual (Estratégica)</option>
                                <option value="campaign" {{ old('type') === 'campaign' ? 'selected' : '' }}>Campanha (Específica)</option>
                                <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>Personalizada</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="category" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="tag" style="duotone" class="w-4 h-4 mr-2 text-purple-500" />
                                Categoria Monitorada
                            </label>
                            <select name="category" id="category"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all cursor-pointer">
                                <option value="">Todas as fontes de receita</option>
                                <option value="tithe" {{ old('category') === 'tithe' ? 'selected' : '' }}>Dízimos</option>
                                <option value="offering" {{ old('category') === 'offering' ? 'selected' : '' }}>Ofertas</option>
                                <option value="donation" {{ old('category') === 'donation' ? 'selected' : '' }}>Doações</option>
                                <option value="total_income" {{ old('category') === 'total_income' ? 'selected' : '' }}>Receita Bruta Total</option>
                                <option value="campaign" {{ old('category') === 'campaign' ? 'selected' : '' }}>Entradas de Campanhas</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="campaign_id" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <x-icon name="bullhorn" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                            Vínculo com Campanha Ativa
                        </label>
                        <select name="campaign_id" id="campaign_id"
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">Nenhuma campanha específica</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Section 4: Financial & Timing -->
                <div class="space-y-8 bg-slate-50 dark:bg-slate-800/30 rounded-[2.5rem] p-8 md:p-10 border border-slate-100 dark:border-slate-800/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 font-black text-sm">04</div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Financeiro e Prazo</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label for="target_amount" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="money-check-dollar" style="duotone" class="w-4 h-4 mr-2 text-emerald-500" />
                                Alvo Global (R$)
                                <span class="text-rose-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black group-focus-within:text-emerald-500 transition-colors">R$</div>
                                <input type="number" name="target_amount" id="target_amount" step="0.01" min="0.01" required value="{{ old('target_amount') }}"
                                    class="w-full pl-12 pr-5 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-black tabular-nums focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                    placeholder="0,00">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="start_date" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="calendar-plus" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                                Data de Ativação
                                <span class="text-rose-500 ml-1">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                                class="w-full px-5 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                        </div>

                        <div class="space-y-2">
                            <label for="end_date" class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                <x-icon name="calendar-check" style="duotone" class="w-4 h-4 mr-2 text-rose-500" />
                                Data de Término
                                <span class="text-rose-500 ml-1">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                                class="w-full px-5 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                        </div>
                    </div>
                </div>

                <!-- Status Select -->
                <div class="p-6 bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-slate-100 dark:border-slate-800">
                     <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                        <span class="ml-4 text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Ativar Meta Imediatamente</span>
                    </label>
                </div>
            </div>

            <!-- Hidden Icon Input -->
            <input type="hidden" name="icon" id="final_icon" value="{{ old('icon', 'bullseye-arrow') }}">

            <div class="px-8 py-8 md:px-12 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-8">
                <a href="{{ route('memberpanel.treasury.goals.index') }}"
                    class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-black uppercase tracking-widest text-xs transition-colors">
                    Descartar
                </a>
                <button type="submit"
                    class="inline-flex items-center px-12 py-5 bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white shadow-xl shadow-indigo-600/20 rounded-3xl font-black transition-all hover:-translate-y-1 active:scale-95">
                    <x-icon name="rocket-launch" style="duotone" class="w-5 h-5 mr-3" />
                    Lançar Meta
                </button>
            </div>
        </form>
            </div>
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
        } else {
            // Re-select default if custom is cleared? Or leave empty?
            // For now, if cleared, it stays whatever was typed last or empty.
        }
    });
});
</script>
@endsection
