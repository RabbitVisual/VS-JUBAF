@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-white/20 text-white">{{ ucfirst($asset->status) }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $asset->name }}</h1>
                    <p class="text-gray-400 font-mono">{{ $asset->code }}</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('assets.admin.assets.edit', $asset->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="pencil" class="w-5 h-5" />
                        Editar
                    </a>
                    <a href="{{ route('assets.admin.movements.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="arrow-right-arrow-left" class="w-5 h-5 text-blue-600" />
                        Nova Movimentação
                    </a>
                    <a href="{{ route('assets.admin.assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="md:col-span-2 space-y-6">
                    @if($asset->description)
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-600 dark:text-gray-400">{{ $asset->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Categoria</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->category->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Localização Atual</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->location->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Condição</p>
                            <p class="font-medium text-gray-900 dark:text-white capitalize">{{ $asset->condition ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Valor de Compra</p>
                            <p class="font-medium text-gray-900 dark:text-white">R$ {{ number_format($asset->purchase_value ?? 0, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Photo & QR -->
                <div class="space-y-6">
                    @if($asset->photo_path)
                        <img src="{{ asset($asset->photo_path) }}" alt="Foto do item" class="w-full h-48 object-cover rounded-2xl bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                    @else
                        <div class="w-full h-48 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-600">
                            <x-icon name="image" class="w-12 h-12" />
                        </div>
                    @endif
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 text-center">
                        <div class="flex justify-center mb-2">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(route('assets.admin.assets.show', $asset->id)) !!}
                        </div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">QR Code Identificador</p>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div class="relative border-t border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="clock-rotate-left" class="w-5 h-5 text-gray-500" />
                        Histórico de Movimentações
                    </h3>
                    @if($asset->movements->count() > 0)
                        <div class="space-y-4">
                            @foreach($asset->movements()->latest()->take(5)->get() as $movement)
                                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div class="mt-1">
                                         @if($movement->type == 'transfer')
                                            <div class="p-2 bg-blue-100 text-blue-600 rounded-full"><x-icon name="arrow-right" class="w-4 h-4"/></div>
                                         @elseif($movement->type == 'loan')
                                            <div class="p-2 bg-yellow-100 text-yellow-600 rounded-full"><x-icon name="hand" class="w-4 h-4"/></div>
                                         @else
                                            <div class="p-2 bg-gray-100 text-gray-600 rounded-full"><x-icon name="clock" class="w-4 h-4"/></div>
                                         @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between">
                                            <p class="font-medium text-gray-900 dark:text-white capitalize">{{ $movement->type }}</p>
                                            <span class="text-xs text-gray-500">{{ $movement->date->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            De <span class="font-medium">{{ $movement->previousLocation->name ?? 'N/A' }}</span> para <span class="font-medium">{{ $movement->newLocation->name }}</span>
                                        </p>
                                        @if($movement->user)
                                            <p class="text-xs text-gray-500 mt-1">Por: {{ $movement->user->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Nenhuma movimentação registrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

