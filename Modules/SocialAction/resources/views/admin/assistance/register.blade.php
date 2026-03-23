@extends('admin::components.layouts.master')

@section('title', 'Registrar Assistência | Ação Social')

@section('content')
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('socialaction.admin.assistance.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-rose-600 transition-colors border border-gray-100 dark:border-gray-700">
            <x-icon name="arrow-left" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registrar Assistência</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lance uma saída do estoque ou auxílio financeiro para um beneficiário.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto pb-20" x-data="{ type: '{{ old('type', 'kit') }}' }">
        <form action="{{ route('socialaction.admin.assistance.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                {{-- Seleção de Beneficiário --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl">
                            <x-icon name="user-heart" />
                        </span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Beneficiário</h3>
                    </div>

                    <div class="relative">
                        <label for="social_beneficiary_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pessoa ou Família Assistida <span class="text-red-500">*</span></label>
                        <select name="social_beneficiary_id" id="social_beneficiary_id" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4 transition-colors">
                            <option value="">Selecione o beneficiário...</option>
                            @foreach($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}" {{ (old('social_beneficiary_id') == $beneficiary->id || request('beneficiary_id') == $beneficiary->id) ? 'selected' : '' }}>
                                    {{ $beneficiary->full_name }} ({{ $beneficiary->city }})
                                </option>
                            @endforeach
                        </select>
                        @error('social_beneficiary_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tipo de Assistência --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl">
                            <x-icon name="hand-holding-heart" />
                        </span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tipo de Assistência</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Option: Kit --}}
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50"
                               :class="type === 'kit' ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-900/10' : 'border-gray-100 dark:border-gray-700'">
                            <input type="radio" name="type" value="kit" x-model="type" class="sr-only">
                            <x-icon name="boxes-packing" class="text-2xl mb-2" ::class="type === 'kit' ? 'text-rose-500' : 'text-gray-400'" />
                            <span class="font-bold text-sm" :class="type === 'kit' ? 'text-rose-900 dark:text-rose-100' : 'text-gray-700 dark:text-gray-300'">Kit / Cesta Padrão</span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase font-black tracking-widest">Saída de Kits Montados</span>
                        </label>

                        {{-- Option: Item --}}
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50"
                               :class="type === 'item' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/10' : 'border-gray-100 dark:border-gray-700'">
                            <input type="radio" name="type" value="item" x-model="type" class="sr-only">
                            <x-icon name="utensils" class="text-2xl mb-2" ::class="type === 'item' ? 'text-blue-500' : 'text-gray-400'" />
                            <span class="font-bold text-sm" :class="type === 'item' ? 'text-blue-900 dark:text-blue-100' : 'text-gray-700 dark:text-gray-300'">Item Avulso</span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase font-black tracking-widest">Alimentos, Roupas, Etc</span>
                        </label>

                        {{-- Option: Financial --}}
                        @if($canCreateTreasuryEntries ?? false)
                            <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                   :class="type === 'financial' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-900/10' : 'border-gray-100 dark:border-gray-700'">
                                <input type="radio" name="type" value="financial" x-model="type" class="sr-only">
                                <x-icon name="sack-dollar" class="text-2xl mb-2" ::class="type === 'financial' ? 'text-amber-500' : 'text-gray-400'" />
                                <span class="font-bold text-sm" :class="type === 'financial' ? 'text-amber-900 dark:text-amber-100' : 'text-gray-700 dark:text-gray-300'">Auxílio Financeiro</span>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 uppercase font-black tracking-widest">Integração Tesouraria</span>
                            </label>
                        @endif
                    </div>
                </div>

                {{-- Detalhes da Assistência (Dinâmico) --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="p-2 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl">
                            <x-icon name="clipboard-check" />
                        </span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detalhes da Entrega</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Component: Kit Select --}}
                        <div class="col-span-2" x-show="type === 'kit'" x-transition>
                            <label for="kit_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Selecione o Kit <span class="text-red-500">*</span></label>
                            <select name="kit_id" id="kit_id" :required="type === 'kit'"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                                <option value="">Escolha um kit montado...</option>
                                @foreach($kits as $kit)
                                    <option value="{{ $kit->id }}" {{ old('kit_id') == $kit->id ? 'selected' : '' }}>
                                        {{ $kit->name }} ({{ $kit->items_count }} itens no kit)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5">
                                <x-icon name="triangle-exclamation" />
                                O estoque dos itens individuais será baixado automaticamente.
                            </p>
                        </div>

                        {{-- Component: Item Select --}}
                        <div class="col-span-2" x-show="type === 'item'" x-transition>
                            <label for="social_pantry_item_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Item do Estoque <span class="text-red-500">*</span></label>
                            <select name="social_pantry_item_id" id="social_pantry_item_id" :required="type === 'item'"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4">
                                <option value="">Escolha o item...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('social_pantry_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} (Disponível: {{ $item->current_quantity }} {{ $item->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Component: Financial Fields --}}
                        <template x-if="type === 'financial'">
                            <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-3xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50">
                                <div>
                                    <label for="amount" class="block text-sm font-bold text-amber-900 dark:text-amber-200 mb-2">Valor Total do Auxílio (R$)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-500 font-bold">R$</span>
                                        <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" min="0.01" required
                                            class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border-transparent rounded-xl focus:ring-amber-500 focus:border-amber-500 text-lg font-black text-amber-600">
                                    </div>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-bold text-amber-900 dark:text-amber-200 mb-2">Finalidade / Motivo</label>
                                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                                        class="w-full py-3 px-4 bg-white dark:bg-gray-800 border-transparent rounded-xl focus:ring-amber-500 focus:border-amber-500"
                                        placeholder="Ex: Auxílio transporte, remédios...">
                                </div>
                                <div class="col-span-2 text-[11px] text-amber-700 dark:text-amber-400 leading-tight">
                                    <x-icon name="shield-halved" class="mr-1" />
                                    Esta ação gerará automaticamente uma <strong>Saída de Despesa</strong> na Tesouraria vinculada a este beneficiário.
                                </div>
                            </div>
                        </template>

                        {{-- Common: Quantity & Date --}}
                        <div x-show="type !== 'financial'">
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Quantidade <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="0.01"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4"
                                :required="type !== 'financial'">
                        </div>

                        <div :class="type === 'financial' ? 'col-span-2' : ''">
                            <label for="registered_at" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Data do Registro</label>
                            <input type="date" name="registered_at" id="registered_at" value="{{ old('registered_at', date('Y-m-d')) }}" required
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 py-3 px-4">
                        </div>

                        {{-- Common: Notes --}}
                        <div class="col-span-2">
                            <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Observações Adicionais</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 p-4 transition-colors resize-none"
                                placeholder="Registre aqui detalhes importantes sobre a entrega ou situação da família no momento...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('socialaction.admin.assistance.index') }}"
                       class="px-8 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                        <x-icon name="hand-holding-heart" />
                        Confirmar Assistência
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
