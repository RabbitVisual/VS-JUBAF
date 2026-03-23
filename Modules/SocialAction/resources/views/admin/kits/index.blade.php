@extends('admin::components.layouts.master')

@section('title', 'Kits de Alimentos | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Kits (Cestas Básicas)</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie composições padrão para agilizar o atendimento.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('socialaction.admin.stock.index') }}" class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                Voltar ao Estoque
            </a>
            <a href="{{ route('socialaction.admin.kits.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95">
                <x-icon name="plus" class="h-5 w-5 mr-2" />
                Novo Kit
            </a>
        </div>
    </div>

    @if($kits->isEmpty())
        <div class="col-span-full py-20 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <x-icon name="box-open" class="h-8 w-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhum Kit Cadastrado</h3>
            <p class="text-gray-500 mb-6">Crie "Cestas Básicas" ou "Kits de Higiene" para padronizar as doações.</p>
            <a href="{{ route('socialaction.admin.kits.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">Criar Primeiro Kit</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($kits as $kit)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 relative flex flex-col h-full">
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                        <a href="{{ route('socialaction.admin.kits.edit', $kit->id) }}" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-blue-100 hover:text-blue-600 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition-colors" title="Editar">
                            <x-icon name="pen-to-square" class="h-4 w-4" />
                        </a>
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-3xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <x-icon name="box-archive" class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">{{ $kit->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $kit->items_count }} itens inclusos</p>
                        </div>
                    </div>

                    <div class="flex-1 text-sm text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                        {{ $kit->description ?? 'Sem descrição.' }}
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Composição</span>
                         <!-- Popover or preview could go here -->
                        <div class="flex -space-x-2">
                            <!-- Placeholder avatars for items logic -->
                            @foreach($kit->items->take(3) as $item)
                                <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-[8px] font-bold" title="{{ $item->name }}">
                                    {{ substr($item->name, 0, 1) }}
                                </div>
                            @endforeach
                            @if($kit->items->count() > 3)
                                <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-[8px] font-bold text-gray-500">
                                    +{{ $kit->items->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

