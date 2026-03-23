@extends('liderancapanel::components.layouts.master')

@section('title', __('churchcouncil::messages.approval'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $approval->approval_type_display ?? 'Solicitação' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Por {{ $approval->requester->name ?? '' }} ·
                    {{ $approval->submitted_at ? $approval->submitted_at->format('d/m/Y H:i') : $approval->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if ($approval->status === 'pending')
                    <form action="{{ route('lideranca.conselho.approvals.approve', $approval) }}" method="POST" class="inline"
                        x-data
                        x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando...' } }))">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                            <x-icon name="check" class="w-5 h-5" /> {{ __('churchcouncil::messages.approve') }}
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('reject-form').classList.toggle('hidden')"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                        <x-icon name="xmark" class="w-5 h-5" /> {{ __('churchcouncil::messages.reject') }}
                    </button>
                    <form id="reject-form" action="{{ route('lideranca.conselho.approvals.reject', $approval) }}"
                        method="POST" class="hidden inline-flex items-center gap-2 flex-wrap" x-data
                        x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando...' } }))">
                        @csrf
                        <input type="text" name="reason" required placeholder="Motivo da rejeição"
                            class="rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm min-w-[200px]">
                        <button type="submit"
                            class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-xl text-sm font-bold">Enviar</button>
                    </form>
                @endif
                <a href="{{ route('lideranca.conselho.approvals') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('churchcouncil::messages.back') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">
                {{ __('churchcouncil::messages.description') }}</h2>
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                {{ $approval->request_details ?? '—' }}
            </div>
        </div>
    </div>
@endsection
