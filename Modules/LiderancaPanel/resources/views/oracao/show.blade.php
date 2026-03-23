@extends('liderancapanel::components.layouts.master')

@section('title', 'Solicitação Pastoral')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <a href="{{ route('lideranca.oracao.index') }}"
                class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 font-medium hover:underline">
                <x-icon name="arrow-left" class="w-4 h-4" /> Voltar
            </a>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-6">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $prayerRequest->title ?? 'Solicitação pastoral' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ $prayerRequest->is_anonymous ? 'Anônimo' : $prayerRequest->user->name ?? '—' }} ·
                {{ $prayerRequest->created_at->format('d/m/Y H:i') }}
            </p>
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                {{ $prayerRequest->description ?? '—' }}
            </div>
            @if ($prayerRequest->status === 'pending')
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <form action="{{ route('lideranca.oracao.marcar-orado', $prayerRequest) }}" method="POST"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold">Concluir</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
