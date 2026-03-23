@extends('liderancapanel::components.layouts.master')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Campanhas')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif

        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Campanhas</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Campanhas ministeriais</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Mobilize recursos para projetos e missões.</p>
                </div>
                @if ($permission->canManageCampaigns())
                    <a href="{{ route('pastor.tesouraria.campaigns.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="plus" class="w-5 h-5" /> Nova campanha
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden flex flex-col">
                    <div class="relative h-40 overflow-hidden">
                        @if ($campaign->image)
                            <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-amber-500/20 to-slate-700 flex items-center justify-center">
                                <x-icon name="bullhorn" class="w-12 h-12 text-amber-500/50" />
                            </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-bold {{ $campaign->isActive() ? 'bg-green-500/90 text-white' : 'bg-gray-500/90 text-white' }}">
                                {{ $campaign->isActive() ? 'Ativa' : 'Encerrada' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ $campaign->name }}</h3>
                        @if ($campaign->target_amount)
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Arrecadado</span>
                                    <span class="font-bold text-green-600 dark:text-green-400">R$
                                        {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-500 h-full rounded-full transition-all"
                                        style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Meta: R$
                                    {{ number_format($campaign->target_amount, 2, ',', '.') }}
                                    ({{ number_format($campaign->progress_percentage, 1) }}%)</p>
                            </div>
                        @endif
                        <div
                            class="mt-auto pt-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400">{{ $campaign->end_date ? 'Até ' . $campaign->end_date->format('d/m/Y') : 'Contínua' }}</span>
                            <div class="flex items-center gap-2">
                                @if ($permission->canManageCampaigns())
                                    <a href="{{ route('pastor.tesouraria.campaigns.edit', $campaign) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl"
                                        title="Editar"><x-icon name="pencil" class="w-4 h-4" /></a>
                                @endif
                                <a href="{{ route('pastor.tesouraria.campaigns.show', $campaign) }}"
                                    class="inline-flex items-center gap-1 px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600 transition-all">Ver
                                    mais</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700">
                    <x-icon name="bullhorn" class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" />
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma campanha</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Crie campanhas de arrecadação.</p>
                    @if ($permission->canManageCampaigns())
                        <a href="{{ route('pastor.tesouraria.campaigns.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all">
                            <x-icon name="plus" class="w-5 h-5" /> Nova campanha
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        @if ($campaigns->hasPages())
            <div class="flex justify-center">{{ $campaigns->links() }}</div>
        @endif
    </div>
@endsection
