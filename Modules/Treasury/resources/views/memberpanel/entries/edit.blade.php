@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Editar Entrada')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-4xl mx-auto space-y-8 px-6 pt-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar entrada</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Atualizar registro para auditoria.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    <a href="{{ route('memberpanel.treasury.entries.index') }}" class="text-sm font-bold text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">Voltar</a>
                </div>
            </div>

            <form action="{{ route('memberpanel.treasury.entries.update', $entry) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <x-icon name="arrow-right-arrow-left" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Tipo de movimentação</h3>
            </div>
            <div class="p-8 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="relative flex items-center p-6 border-2 rounded-4xl cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all border-slate-100 dark:border-slate-800 hover:border-emerald-500 group">
                    <input type="radio" name="type" value="income" {{ old('type', $entry->type) === 'income' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mr-5 group-hover:scale-110 transition-transform">
                        <x-icon name="circle-arrow-up" style="duotone" class="w-7 h-7" />
                    </div>
                    <div class="flex-1">
                        <div class="font-black text-slate-900 dark:text-white text-lg group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Entrada (Receita)</div>
                        <div class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-0.5">Dízimos, ofertas e doações</div>
                    </div>
                    <div class="absolute inset-0 rounded-4xl border-2 border-emerald-500 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none ring-4 ring-emerald-500/10"></div>
                </label>

                <label class="relative flex items-center p-6 border-2 rounded-4xl cursor-pointer hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-all border-slate-100 dark:border-slate-800 hover:border-rose-500 group">
                    <input type="radio" name="type" value="expense" {{ old('type', $entry->type) === 'expense' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center mr-5 group-hover:scale-110 transition-transform">
                        <x-icon name="circle-arrow-down" style="duotone" class="w-7 h-7" />
                    </div>
                    <div class="flex-1">
                        <div class="font-black text-slate-900 dark:text-white text-lg group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">Saída (Despesa)</div>
                        <div class="text-xs font-bold text-slate-400 dark:text-slate-500 mt-0.5">Contas e manutenções</div>
                    </div>
                    <div class="absolute inset-0 rounded-4xl border-2 border-rose-500 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none ring-4 ring-rose-500/10"></div>
                </label>
            </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <x-icon name="file-lines" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Detalhes do lançamento</h3>
            </div>
            <div class="p-8 md:p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <x-icon name="layer-group" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                            Categoria do Fluxo
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <select name="category" required
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">Selecione...</option>
                            <optgroup label="Receitas" class="font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20">
                                <option value="tithe" {{ old('category', $entry->category) === 'tithe' ? 'selected' : '' }}>Dízimo</option>
                                <option value="offering" {{ old('category', $entry->category) === 'offering' ? 'selected' : '' }}>Oferta</option>
                                <option value="donation" {{ old('category', $entry->category) === 'donation' ? 'selected' : '' }}>Doação</option>
                                <option value="ministry_donation" {{ old('category', $entry->category) === 'ministry_donation' ? 'selected' : '' }}>Doação para Ministério</option>
                                <option value="campaign" {{ old('category', $entry->category) === 'campaign' ? 'selected' : '' }}>Campanha</option>
                            </optgroup>
                            <optgroup label="Despesas" class="font-black text-rose-600 bg-rose-50 dark:bg-rose-950/20">
                                <option value="maintenance" {{ old('category', $entry->category) === 'maintenance' ? 'selected' : '' }}>Manutenção</option>
                                <option value="utilities" {{ old('category', $entry->category) === 'utilities' ? 'selected' : '' }}>Contas Fixas</option>
                                <option value="salary" {{ old('category', $entry->category) === 'salary' ? 'selected' : '' }}>Salários/Ajuda de Custo</option>
                                <option value="equipment" {{ old('category', $entry->category) === 'equipment' ? 'selected' : '' }}>Equipamentos</option>
                                <option value="event" {{ old('category', $entry->category) === 'event' ? 'selected' : '' }}>Eventos</option>
                                <option value="other" {{ old('category', $entry->category) === 'other' ? 'selected' : '' }}>Outros</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="space-y-2">
                         <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <x-icon name="pen-nib" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                            Título/Origem
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="text" name="title" required value="{{ old('title', $entry->title) }}"
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                            placeholder="Ex: Doação Anônima p/ Reforma">
                    </div>
                </div>

                <div class="space-y-2">
                     <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        <x-icon name="align-left" style="duotone" class="w-4 h-4 mr-2 text-slate-400" />
                        Observações de Auditoria
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                        placeholder="Informações adicionais para o conselho fiscal...">{{ old('description', $entry->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                         <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <x-icon name="money-bill-transfer" style="duotone" class="w-4 h-4 mr-2 text-emerald-500" />
                            Valor Nominal (R$)
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black group-focus-within:text-emerald-500 transition-colors">R$</div>
                            <input type="number" name="amount" step="0.01" min="0.01" required value="{{ old('amount', $entry->amount) }}"
                                class="w-full pl-12 pr-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-black text-xl tabular-nums focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                placeholder="0,00">
                        </div>
                    </div>

                    <div class="space-y-2">
                         <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <x-icon name="calendar-day" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                            Data Efetiva
                            <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="date" name="entry_date" required value="{{ old('entry_date', $entry->entry_date ? $entry->entry_date->toDateString() : now()->toDateString()) }}"
                            class="w-full px-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <x-icon name="credit-card" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Pagamento e vínculos</h3>
            </div>
            <div class="p-8 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-2">
                     <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        <x-icon name="credit-card" style="duotone" class="w-4 h-4 mr-2 text-indigo-500" />
                        Método Utilizado
                    </label>
                    <select name="payment_method"
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <option value="">Selecione...</option>
                        <option value="cash" {{ old('payment_method', $entry->payment_method) === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="transfer" {{ old('payment_method', $entry->payment_method) === 'transfer' ? 'selected' : '' }}>Transferência Bancária</option>
                        <option value="pix" {{ old('payment_method', $entry->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="credit_card" {{ old('payment_method', $entry->payment_method) === 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="debit_card" {{ old('payment_method', $entry->payment_method) === 'debit_card' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="check" {{ old('payment_method', $entry->payment_method) === 'check' ? 'selected' : '' }}>Cheque</option>
                        <option value="other" {{ old('payment_method', $entry->payment_method) === 'other' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <div class="space-y-2">
                     <label class="flex items-center text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        <x-icon name="receipt" style="duotone" class="w-4 h-4 mr-2 text-slate-400" />
                        Nº de Referência / Comprovante
                    </label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $entry->reference_number) }}"
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                        placeholder="Ex: PIX-08022025-A2">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 p-8 bg-gray-50 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800">
                <div class="space-y-2">
                     <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">
                        <x-icon name="bullhorn" style="duotone" class="w-3.5 h-3.5 mr-2 text-purple-500" />
                        Campanha Ativa
                    </label>
                    <select name="campaign_id"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-bold transition-all">
                        <option value="">Nenhuma</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" {{ old('campaign_id', $entry->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                     <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">
                        <x-icon name="bullseye" style="duotone" class="w-3.5 h-3.5 mr-2 text-rose-500" />
                        Meta Financeira
                    </label>
                     <select name="goal_id"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-bold transition-all">
                        <option value="">Nenhuma</option>
                        @foreach ($goals as $goal)
                            <option value="{{ $goal->id }}" {{ old('goal_id', $entry->goal_id) == $goal->id ? 'selected' : '' }}>{{ $goal->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">
                        <x-icon name="users" style="duotone" class="w-3.5 h-3.5 mr-2 text-indigo-500" />
                        Ministério Responsável
                    </label>
                    <select name="ministry_id"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-bold transition-all">
                        <option value="">Nenhum</option>
                        @foreach ($ministries as $ministry)
                            <option value="{{ $ministry->id }}" {{ old('ministry_id', $entry->ministry_id) == $ministry->id ? 'selected' : '' }}>{{ $ministry->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

            </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-6">
            <a href="{{ route('memberpanel.treasury.entries.index') }}" class="text-sm font-bold text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:underline">Cancelar</a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all">
                <x-icon name="check" style="duotone" class="w-4 h-4 mr-2" />
                Salvar alterações
            </button>
        </div>
            </form>
        </div>
    </div>
@endsection
