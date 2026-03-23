@extends('admin::components.layouts.master')

@section('title', 'Estoque | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Despensa Solidária</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie o estoque de alimentos e itens essenciais.</p>
        </div>
        <div class="flex gap-3">
             <a href="{{ route('socialaction.admin.kits.index') }}" class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                <x-icon name="box-open" class="h-5 w-5 text-purple-500" />
                Gerenciar Kits
            </a>
            <a href="{{ route('socialaction.admin.stock.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95">
                <x-icon name="plus" class="h-5 w-5 mr-2" />
                Adicionar Item
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $lowStockCount = $items->filter(fn($i) => $i->current_quantity <= $i->min_quantity && $i->current_quantity > 0)->count();
            $outOfStockCount = $items->filter(fn($i) => $i->current_quantity <= 0)->count();
            $totalItems = $items->count();
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                <x-icon name="cube" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase">Total de Itens</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalItems }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-xl">
                <x-icon name="triangle-exclamation" style="duotone" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase">Baixo Estoque</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lowStockCount }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
            <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl">
                <x-icon name="ban" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase">Esgotados</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $outOfStockCount }}</p>
            </div>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            @php
                $statusColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800';
                $ringColor = 'ring-green-500';
                if($item->current_quantity <= 0) {
                    $statusColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800';
                    $ringColor = 'ring-red-500';
                } elseif($item->current_quantity <= $item->min_quantity) {
                    $statusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800';
                    $ringColor = 'ring-yellow-500';
                }
            @endphp
            <div class="group relative bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                <!-- Hover Actions Overlay (Desktop) -->
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                    <a href="{{ route('socialaction.admin.stock.edit', $item->id) }}" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-blue-100 hover:text-blue-600 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition-colors" title="Editar">
                        <x-icon name="pen-to-square" class="h-4 w-4" />
                    </a>
                </div>

                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-2xl">
                            📦 <!-- Placeholder emoji, could be dynamic icon based on category -->
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight line-clamp-1">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Unidade: {{ $item->unit }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Disponível</p>
                                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                                    {{ number_format($item->current_quantity, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColor }}">
                                    @if($item->current_quantity <= 0) Esgotado @elseif($item->current_quantity <= $item->min_quantity) Baixo @else Normal @endif
                                </span>
                            </div>
                        </div>

                        <!-- Mini Progress Bar for stock visualization (visual flair) -->
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <!-- Arbitrary scale logic for visual representation: e.g., if > min*2 is full -->
                            @php
                                $maxRef = $item->min_quantity * 3;
                                if($maxRef == 0) $maxRef = 10;
                                $width = min(($item->current_quantity / $maxRef) * 100, 100);
                                $barColor = $item->current_quantity <= $item->min_quantity ? 'bg-yellow-500' : 'bg-blue-500';
                                if($item->current_quantity <= 0) $barColor = 'bg-red-500';
                            @endphp
                            <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                    <span>Mínimo ideal: {{ $item->min_quantity }}</span>
                    <!-- Optional Quick Action Placeholder -->
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <x-icon name="box-open" class="h-16 w-16 mx-auto text-gray-300 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Estoque Vazio</h3>
                <p class="text-gray-500 mb-6">Comece adicionando itens essenciais à despensa.</p>
                <a href="{{ route('socialaction.admin.stock.create') }}" class="text-blue-600 hover:underline">Adicionar Primeiro Item</a>
            </div>
        @endforelse
    </div>
@endsection

