@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Nova movimentação</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Nova Entrada Financeira</h1>
                        <p class="text-gray-300 max-w-xl">Registre receitas ou despesas no caixa da congregação.</p>
                    </div>
                    <a href="{{ route('treasury.entries.index') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                        <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" /> Voltar
                    </a>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Entradas' => route('treasury.entries.index'), 'Nova' => null]])
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-2">
                <x-icon name="receipt" style="duotone" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Informações da Transação</h3>
            </div>
            <div class="px-6 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800/50 flex items-start gap-3">
                <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                <p class="text-xs text-blue-800 dark:text-blue-200">
                    <strong>Gateways:</strong> Doações via PIX, cartão ou boleto são registradas automaticamente com o <strong>valor líquido</strong> (já abatidas as taxas).
                </p>
            </div>
            <form action="{{ route('treasury.entries.store') }}" method="POST" class="p-6 space-y-8" x-data x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }))">
                @csrf

                <!-- Type Selection with Custom Cards -->
                <div class="space-y-3">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo de Movimentação <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative block cursor-pointer group">
                            <input type="radio" name="type" value="income" checked class="sr-only peer">
                            <div class="p-4 rounded-xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/10 group-hover:border-green-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 mr-3">
                                            <x-icon name="trending-up" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">Entrada (Receita)</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Dízimos, ofertas e doações</p>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-200 peer-checked:border-green-500 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-green-500 transition-transform scale-0 peer-checked:scale-100"></div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative block cursor-pointer group">
                            <input type="radio" name="type" value="expense" class="sr-only peer">
                            <div class="p-4 rounded-xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-200 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/10 group-hover:border-red-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 mr-3">
                                            <x-icon name="trending-down" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">Saída (Despesa)</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Contas, manutenção e custos</p>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-200 peer-checked:border-red-500 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-red-500 transition-transform scale-0 peer-checked:scale-100"></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category (CBAV2026: from financial_categories) -->
                    <div class="space-y-2">
                        <label for="category" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria <span class="text-red-500">*</span></label>
                        <select name="category" id="category" required
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Selecione a categoria...</option>
                            @if(isset($financial_categories) && $financial_categories->isNotEmpty())
                                <optgroup label="Receitas">
                                    @foreach($financial_categories->where('type', 'income') as $cat)
                                        <option value="{{ $cat->slug }}" {{ old('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Despesas">
                                    @foreach($financial_categories->where('type', 'expense') as $cat)
                                        <option value="{{ $cat->slug }}" {{ old('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @else
                                <optgroup label="Receitas">
                                    <option value="tithe">Dízimo</option>
                                    <option value="offering">Oferta</option>
                                    <option value="donation">Doação</option>
                                    <option value="campaign">Campanha</option>
                                </optgroup>
                                <optgroup label="Despesas">
                                    <option value="maintenance">Manutenção</option>
                                    <option value="utilities">Contas</option>
                                    <option value="salary">Salários</option>
                                    <option value="other">Outros</option>
                                </optgroup>
                            @endif
                        </select>
                        @error('category')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="space-y-2">
                        <label for="title" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título/Descrição Curta <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            placeholder="Ex: Dízimo de João Silva">
                        @error('title')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label for="description" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Observações Detalhadas (opcional)</label>
                    <textarea name="description" id="description" rows="3"
                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        placeholder="Informações adicionais sobre esta entrada...">{{ old('description') }}</textarea>
                </div>

                <!-- Amount and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="amount" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">R$</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" required value="{{ old('amount') }}"
                                class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-bold"
                                placeholder="0,00">
                        </div>
                        @error('amount')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="entry_date" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data da Operação <span class="text-red-500">*</span></label>
                        <input type="date" name="entry_date" id="entry_date" required value="{{ old('entry_date', now()->toDateString()) }}"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        @error('entry_date')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700 space-y-6">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest border-b border-gray-200 dark:border-gray-600 pb-2">Informações Adicionais</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="payment_method" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Forma de Pagamento</label>
                            <select name="payment_method" id="payment_method"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Selecione...</option>
                                <option value="cash">Dinheiro</option>
                                <option value="transfer">Transferência</option>
                                <option value="pix">PIX</option>
                                <option value="credit_card">Cartão de Crédito</option>
                                <option value="debit_card">Cartão de Débito</option>
                                <option value="check">Cheque</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="reference_number" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Referência / Documento</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                placeholder="Nº Comprovante">
                        </div>
                    </div>

                    <!-- Payment Link -->
                    <div class="space-y-2">
                        <label for="payment_id" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vincular Transação Bancária (opcional)</label>
                        <select name="payment_id" id="payment_id"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="">Nenhuma transação vinculada</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->id }}">
                                    {{ $payment->transaction_id }} - R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(isset($financial_funds) && $financial_funds->isNotEmpty())
                    <div class="space-y-2">
                        <label for="fund_id" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fundo / Centro de Custo (opcional)</label>
                        <select name="fund_id" id="fund_id"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="">Caixa geral</option>
                            @foreach ($financial_funds as $fund)
                                <option value="{{ $fund->id }}" {{ old('fund_id') == $fund->id ? 'selected' : '' }}>{{ $fund->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="campaign_id" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campanha</label>
                            <select name="campaign_id" id="campaign_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Nenhuma</option>
                                @foreach ($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="goal_id" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meta</label>
                            <select name="goal_id" id="goal_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Nenhuma</option>
                                @foreach ($goals as $goal)
                                    <option value="{{ $goal->id }}">{{ $goal->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="ministry_id" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ministério</label>
                            <select name="ministry_id" id="ministry_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Nenhum</option>
                                @foreach ($ministries as $ministry)
                                    <option value="{{ $ministry->id }}">{{ $ministry->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('treasury.entries.index') }}"
                        class="px-6 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/20 transition-all focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 inline-flex items-center gap-2">
                        <x-icon name="check" style="duotone" class="w-4 h-4" /> Salvar Operação
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

