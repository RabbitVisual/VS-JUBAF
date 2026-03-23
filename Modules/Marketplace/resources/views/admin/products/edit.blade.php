@extends('admin::components.layouts.master')

@section('title', 'Editar ' . __('marketplace::messages.product'))

@php
    $editStep = (int) session('edit_step', 1);
    $editStep = min(5, max(1, $editStep));
    $thumbUrl = $product->images->isNotEmpty() ? $product->images->first()->url : ($product->image_url ?? null);
@endphp

@section('content')
<div class="space-y-8" x-data="{
    step: {{ $editStep }},
    lightbox: null,
    totalSteps: 5,
    steps: [1, 2, 3, 4, 5]
}">
    {{-- Hero + mini preview --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">{{ __('marketplace::messages.product') }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">Editar {{ $product->title }}</h1>
                <p class="text-gray-300 text-sm mt-1">Guia por etapas: preencha cada passo e salve quando quiser. Ao salvar, você permanece na etapa atual.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                <a href="{{ route('admin.marketplace.products.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-medium text-sm hover:bg-white/20 transition-colors inline-flex items-center gap-2">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4" /> Voltar
                </a>
                <a href="{{ route('marketplace.storefront.show', $product->slug ?? $product->uuid) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-white text-gray-900 font-medium text-sm hover:bg-gray-100 transition-colors inline-flex items-center gap-2">
                    <x-icon name="eye" style="duotone" class="w-4 h-4" /> Ver na loja
                </a>
                {{-- Mini preview --}}
                @if($thumbUrl)
                <div class="hidden sm:block w-20 h-20 rounded-2xl border-2 border-white/20 overflow-hidden bg-gray-800 flex-shrink-0">
                    <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover">
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif
    @if($errors->any())
        <x-alert type="error" message="Corrija os erros abaixo antes de salvar." />
    @endif

    {{-- Wizard: barra de progresso (etapas clicáveis) --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Etapa <span x-text="step"></span> de <span x-text="totalSteps"></span></p>
            <div class="flex flex-wrap gap-2">
                <template x-for="s in steps" :key="s">
                    <button type="button"
                        @click="step = s"
                        :class="step === s ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600 hover:border-blue-400'"
                        class="px-4 py-2 rounded-xl border text-sm font-medium transition-colors flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="step === s ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600'" x-text="s"></span>
                        <span class="hidden sm:inline" x-text="s === 1 ? 'Informações' : s === 2 ? 'Detalhes' : s === 3 ? 'Galeria' : s === 4 ? 'Vídeo' : 'Opções e SKUs'"></span>
                    </button>
                </template>
            </div>
        </div>

        <form id="product-form-{{ $product->id }}" action="{{ route('admin.marketplace.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="edit_step" :value="step">

            {{-- Etapa 1: Informações --}}
            <div x-show="step === 1" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-blue-500" /> Informações básicas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                        <input type="text" name="title" value="{{ old('title', $product->title) }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug (opcional)</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('slug')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                    <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">{{ old('description', $product->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Etapa 2: Detalhes e preço --}}
            <div x-show="step === 2" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="tags" style="duotone" class="w-5 h-5 text-blue-500" /> Detalhes e preço
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL da imagem (fallback, opcional)</label>
                    <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                    @error('image_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @if($product->category === \Modules\Marketplace\Models\Product::CATEGORY_LIVROS)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amostra (URL ou link)</label>
                    <input type="text" name="sample_url" value="{{ old('sample_url', $product->sample_url) }}" placeholder="https://..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                    @error('sample_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especificações / Ingredientes (JSON)</label>
                    <textarea name="specifications_json" rows="4" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 font-mono text-sm" placeholder='{"Ingredientes": "..."}'>{{ old('specifications_json', $product->specifications ? json_encode($product->specifications, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '') }}</textarea>
                    @error('specifications')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço (R$) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estoque *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('stock')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria *</label>
                        <select name="category" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                            @foreach(\Modules\Marketplace\Models\Product::categories() as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $product->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campanha</label>
                        <select name="campaign_id" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                            <option value="">—</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}" {{ old('campaign_id', $product->campaign_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('campaign_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de entrega *</label>
                        <select name="delivery_type" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                            @foreach(\Modules\Marketplace\Models\Product::deliveryTypes() as $key => $label)
                                <option value="{{ $key }}" {{ old('delivery_type', $product->delivery_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('delivery_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso (g)</label>
                        <input type="number" name="weight_grams" value="{{ old('weight_grams', $product->weight_grams) }}" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('weight_grams')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comprimento (cm)</label>
                        <input type="number" name="length_cm" value="{{ old('length_cm', $product->length_cm) }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('length_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Largura (cm)</label>
                        <input type="number" name="width_cm" value="{{ old('width_cm', $product->width_cm) }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('width_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Altura (cm)</label>
                        <input type="number" name="height_cm" value="{{ old('height_cm', $product->height_cm) }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('height_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ativo</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0" class="w-24 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            {{-- Etapa 3: Galeria (só upload aqui; preview abaixo, fora do form) --}}
            <div x-show="step === 3" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="images" style="duotone" class="w-5 h-5 text-blue-500" /> Galeria de imagens
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar novas imagens</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">JPG, PNG, WebP ou GIF. Máx. 5 MB por arquivo.</p>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 file:font-medium">
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">A galeria atual e o zoom aparecem abaixo. Clique numa imagem para ampliar.</p>
            </div>

            {{-- Etapa 4: Vídeo --}}
            <div x-show="step === 4" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="video" style="duotone" class="w-5 h-5 text-blue-500" /> Vídeo do produto
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL do vídeo (YouTube, Vimeo ou link direto)</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $product->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                    @error('video_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opcional. Apresentação do produto na página da loja.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vídeo de demonstração (MP4)</label>
                    <input type="file" name="video" accept="video/mp4" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 file:text-sm file:font-medium">
                    @error('video')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    @if($product->video_path ?? null)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Vídeo atual: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{{ $product->video_path }}</code>. Envie um novo arquivo para substituir.</p>
                    @else
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opcional. Até 50 MB. Será exibido na galeria do produto.</p>
                    @endif
                </div>
                @php
                    $videoUrl = old('video_url', $product->video_url ?? '');
                    $ytId = $videoUrl && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $m) ? $m[1] : null;
                @endphp
                @if($ytId)
                <div class="rounded-2xl border border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700/50">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 px-4 py-2 border-b border-gray-200 dark:border-gray-600">Preview</p>
                    <div class="aspect-video">
                        <iframe src="https://www.youtube.com/embed/{{ $ytId }}" title="Vídeo" class="w-full h-full" allowfullscreen></iframe>
                    </div>
                </div>
                @endif
            </div>

            {{-- Etapa 5: conteúdo é a seção Opções/SKUs que fica abaixo, fora do form (x-show step === 5) --}}
            <div x-show="step === 5" x-cloak class="space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="layer-group" style="duotone" class="w-5 h-5 text-blue-500" /> Opções e SKUs
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Use as seções abaixo para adicionar opções (Tamanho, Cor) e gerar a grade de SKUs. Cada ação tem seu próprio botão.</p>
            </div>

            {{-- Navegação do wizard + Salvar --}}
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                <div class="flex gap-3">
                    <button type="button" x-show="step > 1" @click="step--" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">
                        <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" /> Anterior
                    </button>
                    <button type="button" x-show="step < totalSteps" @click="step++" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 font-medium text-sm">
                        Próximo <x-icon name="arrow-right" style="duotone" class="w-4 h-4 ml-2" />
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/25 transition-colors">
                        <x-icon name="check" style="duotone" class="w-5 h-5 mr-2" /> Salvar alterações
                    </button>
                    <a href="{{ route('admin.marketplace.products.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">Cancelar</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Galeria atual — arraste para reordenar (etapa 3, fora do form) --}}
    <div x-show="step === 3" x-cloak class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8"
         x-data="{
             imageOrder: @js($product->images->pluck('id')->values()->toArray()),
             imageUrls: @js($product->images->keyBy('id')->map(fn ($i) => $i->url)->toArray()),
             destroyImageBase: '{{ route('admin.marketplace.products.images.destroy', [$product, $product->images->first()->id ?? 0]) }}'.replace(/\/\d+$/, ''),
             draggedId: null,
             async saveOrder() {
                 const order = this.imageOrder;
                 const res = await fetch('{{ route('admin.marketplace.products.images.reorder', $product) }}', {
                     method: 'PUT',
                     headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                     body: JSON.stringify({ order })
                 });
                 if (res.ok) return;
                 alert('Não foi possível salvar a ordem.');
             },
             dragStart(id) { this.draggedId = id; },
             dragEnd() { this.draggedId = null; },
             dragOver(e, index) {
                 e.preventDefault();
                 if (this.draggedId === null) return;
                 const from = this.imageOrder.indexOf(this.draggedId);
                 if (from === index) return;
                 const order = [...this.imageOrder];
                 order.splice(from, 1);
                 order.splice(index, 0, this.draggedId);
                 this.imageOrder = order;
                 this.saveOrder();
             }
         }">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-2">
            <x-icon name="images" style="duotone" class="w-5 h-5 text-blue-500" /> Galeria — arraste para reordenar
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Arraste os cards para alterar a ordem na vitrine. A ordem é salva automaticamente.</p>
        @if($product->images->isNotEmpty())
            <ul class="flex flex-wrap gap-4" role="list">
                <template x-for="(id, index) in imageOrder" :key="id">
                    <li class="group relative rounded-2xl border-2 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700/50 cursor-grab active:cursor-grabbing"
                        :class="{ 'opacity-60 border-blue-500': draggedId === id }"
                        draggable="true"
                        @dragstart="dragStart(id); $event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('text/plain', id)"
                        @dragend="dragEnd()"
                        @dragover.prevent="dragOver($event, index)">
                        <div class="flex items-center gap-2 p-2 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <x-icon name="grip-vertical" style="duotone" class="w-5 h-5 text-gray-400" />
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="'#' + (index + 1)"></span>
                        </div>
                        <button type="button" @click="lightbox = imageUrls[id]" class="block w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-b-2xl overflow-hidden">
                            <img :src="imageUrls[id]" alt="" class="w-32 h-32 sm:w-40 sm:h-40 object-cover transition-transform group-hover:scale-105 mx-auto">
                        </button>
                        <form :action="destroyImageBase + '/' + id" method="POST" class="absolute top-12 right-2" onsubmit="return confirm('Remover esta imagem?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg bg-red-500/90 hover:bg-red-600 text-white shadow" title="Remover">
                                <x-icon name="trash" style="duotone" class="w-4 h-4" />
                            </button>
                        </form>
                    </li>
                </template>
            </ul>
            @if($product->video_path ?? null)
                <div class="mt-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 flex items-center gap-3">
                    <x-icon name="video" style="duotone" class="w-8 h-8 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                    <div>
                        <p class="font-medium text-blue-900 dark:text-blue-100">Vídeo de demonstração</p>
                        <p class="text-sm text-blue-700 dark:text-blue-300">O vídeo aparece como segundo item na galeria do produto. Para trocar, use o passo «Vídeo do produto» acima.</p>
                    </div>
                </div>
            @endif
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma imagem na galeria. Adicione arquivos acima e salve.</p>
        @endif
    </div>

    {{-- Opções e Grade de SKUs — só na etapa 5 --}}
    <div x-show="step === 5" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                <x-icon name="layer-group" style="duotone" class="w-5 h-5 text-blue-500" /> Opções (Tamanho, Cor, etc.)
            </h3>
            @if($product->options->isNotEmpty())
                <ul class="space-y-3 mb-6">
                    @foreach($product->options as $opt)
                        <li class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-700/30">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $opt->name }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $opt->values->pluck('value')->join(', ') }}</span>
                            <form action="{{ route('admin.marketplace.products.options.destroy', [$product, $opt]) }}" method="POST" class="inline" onsubmit="return confirm('Remover esta opção e seus valores?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"><x-icon name="trash" style="duotone" class="w-4 h-4" /></button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
            <form action="{{ route('admin.marketplace.products.options.store', $product) }}" method="POST" class="flex flex-wrap items-end gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nome da opção</label>
                    <input type="text" name="name" required placeholder="Ex: Tamanho" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm w-40">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Valores (vírgula ou um por linha)</label>
                    <input type="text" name="values_text" placeholder="P, M, G ou um por linha" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-medium">Adicionar opção</button>
            </form>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Depois use "Gerar grade de SKUs" abaixo.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                <x-icon name="grid-2" style="duotone" class="w-5 h-5 text-blue-500" /> Grade de SKUs
            </h3>
            <form action="{{ route('admin.marketplace.products.skus.generate', $product) }}" method="POST" class="mb-6">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                    <x-icon name="grid-2" style="duotone" class="w-4 h-4 mr-2" /> Gerar grade de SKUs
                </button>
            </form>
            @if($product->skus->isNotEmpty())
                <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-600">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Atributos</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Preço (override)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estoque</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cód. barras</th>
                                <th class="px-4 py-3 w-24"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($product->skus as $sku)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $sku->sku_code }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $sku->display_name }}</span>
                                    </td>
                                    <td class="px-4 py-3" colspan="3">
                                        <form action="{{ route('admin.marketplace.products.skus.update', [$product, $sku]) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="price_override" value="{{ old('price_override.'.$sku->id, $sku->price_override) }}" step="0.01" min="0" placeholder="Preço" class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1.5 text-sm">
                                            <input type="number" name="stock" value="{{ old('stock.'.$sku->id, $sku->stock) }}" min="0" required class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1.5 text-sm">
                                            <input type="text" name="barcode" value="{{ old('barcode.'.$sku->id, $sku->barcode) }}" placeholder="Cód. barras" class="w-28 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1.5 text-sm">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium">Salvar</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('admin.marketplace.products.skus.destroy', [$product, $sku]) }}" method="POST" class="inline" onsubmit="return confirm('Remover este SKU?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"><x-icon name="trash" style="duotone" class="w-4 h-4" /></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum SKU. Adicione opções acima e clique em "Gerar grade de SKUs".</p>
            @endif
        </div>
    </div>
</div>

{{-- Lightbox zoom --}}
<div x-show="lightbox" x-cloak @click="lightbox = null" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" x-transition>
    <template x-if="lightbox">
        <img :src="lightbox" alt="Ampliar" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl cursor-zoom-out" @click.stop>
    </template>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
