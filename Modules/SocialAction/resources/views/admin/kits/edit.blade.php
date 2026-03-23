@extends('admin::components.layouts.master')

@section('title', 'Editar Kit | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('socialaction.admin.kits.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Kit</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Modifique a composição do kit: {{ $kit->name }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto" x-data="kitEditForm({{ $kit->items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit, 'quantity' => $i->pivot->quantity])->toJson() }})">
        <form action="{{ route('socialaction.admin.kits.update', $kit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome do Kit <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $kit->name) }}" required
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 transition-colors">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4 transition-colors resize-none">{{ old('description', $kit->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Itens do Kit</h3>

                        <div class="space-y-4">
                            <template x-for="(item, index) in selectedItems" :key="index">
                                <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-200 dark:border-gray-600">
                                    <div class="flex-1">
                                         <p class="font-bold text-gray-900 dark:text-white" x-text="item.name"></p>
                                         <p class="text-xs text-gray-500" x-text="item.unit"></p>
                                         <input type="hidden" :name="'items['+index+'][id]'" :value="item.id">
                                    </div>
                                    <div class="w-24">
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" step="0.5"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center text-sm py-1.5" placeholder="Qtd">
                                    </div>
                                    <button type="button" @click="removeItem(index)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <x-icon name="trash-can" style="duotone" class="h-5 w-5" />
                                    </button>
                                </div>
                            </template>

                            <div x-show="selectedItems.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                Nenhum item adicionado.
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Adicionar Item</label>
                            <div class="flex gap-2">
                                <select x-model="newItemId" class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm">
                                    <option value="">Selecione um item...</option>
                                    @foreach($items as $inventoryItem)
                                        <option value="{{ $inventoryItem->id }}" data-name="{{ $inventoryItem->name }}" data-unit="{{ $inventoryItem->unit }}">
                                            {{ $inventoryItem->name }} ({{ $inventoryItem->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" @click="addItem()" :disabled="!newItemId" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-colors disabled:opacity-50">
                                    <x-icon name="plus" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary/Actions -->
                <div class="space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-3xl p-6 border border-blue-100 dark:border-blue-800">
                        <h4 class="font-bold text-blue-800 dark:text-blue-300 mb-2">Resumo</h4>
                        <div class="flex justify-between items-center text-sm font-semibold text-blue-900 dark:text-blue-200">
                            <span>Total de Itens:</span>
                            <span x-text="selectedItems.length"></span>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('socialaction.admin.kits.destroy', $kit->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este kit?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium hover:underline">
                                Excluir Kit
                            </button>
                        </form>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <x-icon name="check" class="h-5 w-5" />
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
             Alpine.data('kitEditForm', (initialItems) => ({
                selectedItems: initialItems || [],
                newItemId: '',

                addItem() {
                    if (!this.newItemId) return;

                    const select = document.querySelector('select[x-model="newItemId"]');
                    const option = select.options[select.selectedIndex];
                    const name = option.getAttribute('data-name');
                    const unit = option.getAttribute('data-unit');

                    // Check if already exists
                    if (this.selectedItems.find(i => i.id == this.newItemId)) {
                        alert('Item já adicionado.');
                        return;
                    }

                    this.selectedItems.push({
                        id: this.newItemId,
                        name: name,
                        unit: unit,
                        quantity: 1
                    });

                    this.newItemId = '';
                },

                removeItem(index) {
                    this.selectedItems.splice(index, 1);
                }
            }));
        });
    </script>
    @endpush
@endsection

