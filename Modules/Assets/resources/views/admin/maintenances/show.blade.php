@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $maintenance->status == 'completed' ? 'bg-green-500/30 text-green-100' : ($maintenance->status == 'cancelled' ? 'bg-gray-500/30 text-gray-200' : 'bg-amber-500/30 text-amber-100') }}">{{ ucfirst($maintenance->status) }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $maintenance->asset->name }}</h1>
                    <p class="text-gray-400 font-mono">{{ $maintenance->asset->code }}</p>
                </div>
                <a href="{{ route('assets.admin.maintenances.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 space-y-6 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative space-y-6">
            <div class="pt-2">
                 <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Descrição</h3>
                 <p class="text-gray-900 dark:text-white">{{ $maintenance->description }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                <div>
                     <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Fornecedor</h3>
                     <p class="text-gray-900 dark:text-white">{{ $maintenance->supplier_name ?? 'N/A' }}</p>
                </div>
                <div>
                     <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Custo</h3>
                     <p class="text-gray-900 dark:text-white">R$ {{ number_format($maintenance->cost ?? 0, 2, ',', '.') }}</p>
                </div>
                <div>
                     <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Início</h3>
                     <p class="text-gray-900 dark:text-white">{{ $maintenance->start_date->format('d/m/Y') }}</p>
                </div>
                <div>
                     <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Retorno Previsto</h3>
                     <p class="text-gray-900 dark:text-white">{{ $maintenance->expected_return_date ? $maintenance->expected_return_date->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>

            </div>
        </div>
    </div>
@endsection

