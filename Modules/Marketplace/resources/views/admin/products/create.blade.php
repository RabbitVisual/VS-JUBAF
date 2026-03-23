@extends('admin::components.layouts.master')

@section('title', 'Novo ' . __('marketplace::messages.product'))

@php
    $categories = \Modules\Marketplace\Models\Product::categories();
    $categoryKeys = array_keys($categories);
    $defaultCategory = old('category', $categoryKeys[0] ?? 'alimentacao');
@endphp

@section('content')
<div class="space-y-8" x-data="{
    step: 1,
    totalSteps: 5,
    steps: [1, 2, 3, 4, 5],
    category: '{{ $defaultCategory }}'
}">
    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">{{ __('marketplace::messages.product') }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">Novo {{ __('marketplace::messages.product') }}</h1>
                <p class="text-gray-300 text-sm mt-1">Wizard guiado por etapas. Escolha a categoria e preencha cada passo; ao final você poderá adicionar Tamanho, Cor e SKUs na edição.</p>
            </div>
            <a href="{{ route('admin.marketplace.products.index') }}" class="flex-shrink-0 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-medium text-sm hover:bg-white/20 transition-colors inline-flex items-center gap-2">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4" /> Voltar
            </a>
        </div>
    </div>

    @if($errors->any())
        <x-alert type="error" message="Corrija os erros abaixo antes de continuar." />
    @endif

    {{-- Wizard --}}
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
                        <span class="hidden sm:inline" x-text="s === 1 ? 'Informações' : s === 2 ? 'Detalhes' : s === 3 ? 'Galeria' : s === 4 ? 'Vídeo' : 'Próximo passo'"></span>
                    </button>
                </template>
            </div>
        </div>

        <form action="{{ route('admin.marketplace.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf

            {{-- Etapa 1: Informações --}}
            <div x-show="step === 1" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-blue-500" /> Informações básicas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug (opcional)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('slug')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                    <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Etapa 2: Detalhes e preço (variação por categoria) --}}
            <div x-show="step === 2" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="tags" style="duotone" class="w-5 h-5 text-blue-500" /> Detalhes e preço
                </h2>

                {{-- Categoria primeiro para definir variação --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria *</label>
                    <select name="category" required x-model="category" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL da imagem (fallback, opcional)</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                    @error('image_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Livros: amostra --}}
                <div x-show="category === 'livros'" x-cloak class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <x-icon name="book" style="duotone" class="w-4 h-4" /> Livros
                    </p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amostra / Preview (URL)</label>
                        <input type="text" name="sample_url" value="{{ old('sample_url') }}" placeholder="https://..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('sample_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Alimentação: ingredientes / composição --}}
                <div x-show="category === 'alimentacao'" x-cloak class="p-4 rounded-2xl bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                        <x-icon name="apple-whole" style="duotone" class="w-4 h-4" /> Alimentação
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Informe ingredientes e peso nas especificações e no campo Peso abaixo.</p>
                </div>

                {{-- Vestuário: aviso Tamanho/Cor na edição --}}
                <div x-show="category === 'vestuario'" x-cloak class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200 flex items-center gap-2">
                        <x-icon name="shirt" style="duotone" class="w-4 h-4" /> Roupas
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Após criar, você irá para a edição para adicionar <strong>Tamanho</strong> (P, M, G, GG) e <strong>Cor</strong> e gerar a grade de SKUs.</p>
                </div>

                {{-- Eventos / Oficinas --}}
                <div x-show="category === 'eventos_oficinas'" x-cloak class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800">
                    <p class="text-sm font-medium text-purple-800 dark:text-purple-200 flex items-center gap-2">
                        <x-icon name="calendar-days" style="duotone" class="w-4 h-4" /> Eventos e oficinas
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Use especificações para data, carga horária ou programação.</p>
                </div>

                {{-- Especificações (um único campo para todas as categorias) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especificações (JSON, opcional)</label>
                    <textarea name="specifications_json" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 font-mono text-sm" placeholder='{"Ingredientes": "...", "Peso": "500g"} ou {"Páginas": 120} ou {"Material": "Algodão"}'>{{ old('specifications_json') }}</textarea>
                    @error('specifications')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço (R$) *</label>
                        <input type="number" name="price" value="{{ old('price', '0') }}" step="0.01" min="0" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estoque *</label>
                        <input type="number" name="stock" value="{{ old('stock', '0') }}" min="0" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('stock')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campanha</label>
                        <select name="campaign_id" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                            <option value="">—</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}" {{ old('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('campaign_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de entrega *</label>
                    <select name="delivery_type" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @foreach(\Modules\Marketplace\Models\Product::deliveryTypes() as $key => $label)
                            <option value="{{ $key }}" {{ old('delivery_type', 'both') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('delivery_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso (g)</label>
                        <input type="number" name="weight_grams" value="{{ old('weight_grams') }}" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('weight_grams')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comprimento (cm)</label>
                        <input type="number" name="length_cm" value="{{ old('length_cm') }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('length_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Largura (cm)</label>
                        <input type="number" name="width_cm" value="{{ old('width_cm') }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('width_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Altura (cm)</label>
                        <input type="number" name="height_cm" value="{{ old('height_cm') }}" step="0.01" min="0" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                        @error('height_cm')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ativo</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-24 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            {{-- Etapa 3: Galeria --}}
            <div x-show="step === 3" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="images" style="duotone" class="w-5 h-5 text-blue-500" /> Galeria de imagens
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Imagens do produto</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">JPG, PNG, WebP ou GIF. Máx. 5 MB por arquivo.</p>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 file:font-medium">
                </div>
            </div>

            {{-- Etapa 4: Vídeo --}}
            <div x-show="step === 4" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="video" style="duotone" class="w-5 h-5 text-blue-500" /> Vídeo do produto
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL do vídeo (YouTube, Vimeo ou link direto)</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5">
                    @error('video_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opcional.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vídeo de demonstração (MP4)</label>
                    <input type="file" name="video" accept="video/mp4" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 file:text-sm file:font-medium">
                    @error('video')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opcional. Até 50 MB. Será exibido na galeria do produto.</p>
                </div>
            </div>

            {{-- Etapa 5: Próximo passo --}}
            <div x-show="step === 5" x-cloak class="space-y-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="circle-check" style="duotone" class="w-5 h-5 text-green-500" /> Próximo passo
                </h2>
                <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-6">
                    <p class="text-sm text-blue-900 dark:text-blue-100 font-medium">Ao clicar em <strong>Criar produto</strong>, o cadastro será salvo e você será redirecionado para a <strong>edição do produto</strong>, na etapa <strong>Opções e SKUs</strong>.</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Lá você poderá adicionar Tamanho, Cor e gerar a grade de SKUs (para roupas e itens com variação). Para alimentos, livros e eventos pode pular essa etapa.</p>
                </div>
            </div>

            {{-- Navegação --}}
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
                        <x-icon name="check" style="duotone" class="w-5 h-5 mr-2" /> Criar {{ __('marketplace::messages.product') }}
                    </button>
                    <a href="{{ route('admin.marketplace.products.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
</think>