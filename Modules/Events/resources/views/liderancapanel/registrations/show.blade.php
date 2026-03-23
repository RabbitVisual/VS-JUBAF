@extends('liderancapanel::components.layouts.master')

@section('title', __('events::messages.registration_details') . ' - ' . $event->title)

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('pastor.eventos.index') }}"
                        class="hover:text-white transition-colors">{{ __('events::messages.events') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('pastor.eventos.show', $event) }}"
                        class="hover:text-white transition-colors">{{ $event->title }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('pastor.eventos.registrations.index', $event) }}"
                        class="hover:text-white transition-colors">{{ __('events::messages.registrations') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">#{{ $registration->id }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('events::messages.registration_details') ?? 'Inscrição' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    {{ $registration->user->name ?? __('events::messages.visitor') }}</p>
            </div>
            <a href="{{ route('pastor.eventos.registrations.index', $event) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('events::messages.back') }}
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('events::messages.registration_info') ?? 'Dados da inscrição' }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('events::messages.enrolled') ?? 'Inscrito' }}</p>
                            <p class="mt-1 text-gray-900 dark:text-white">
                                {{ $registration->user->name ?? __('events::messages.visitor') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $registration->user->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('events::messages.status') }}</p>
                            <p class="mt-1">
                                <span
                                    class="px-2 py-0.5 text-xs font-medium rounded-full
                                @if ($registration->status === 'confirmed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($registration->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                                    {{ $registration->status_display }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('events::messages.total_value') ?? 'Valor' }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">R$
                                {{ number_format($registration->total_amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('events::messages.registration_date') ?? 'Data' }}</p>
                            <p class="mt-1 text-gray-900 dark:text-white">
                                {{ $registration->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('events::messages.participants') }} ({{ $registration->participants->count() }})</h2>
                    <div class="space-y-4">
                        @foreach ($registration->participants as $participant)
                            <div class="border border-gray-200 dark:border-slate-600 rounded-xl p-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $participant->name }}</p>
                                @if ($participant->email)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $participant->email }}</p>
                                @endif
                                @if ($participant->birth_date)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($participant->birth_date)->age }} anos</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.actions') }}
                    </h2>
                    <div class="space-y-3">
                        @if ($registration->status === 'pending')
                            <form action="{{ route('pastor.eventos.registrations.confirm', [$event, $registration]) }}"
                                method="POST" x-data
                                x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando...' } }))">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors">
                                    {{ __('events::messages.confirm_registration') ?? 'Confirmar inscrição' }}
                                </button>
                            </form>
                        @endif
                        @if ($registration->status !== 'cancelled')
                            <form action="{{ route('pastor.eventos.registrations.cancel', [$event, $registration]) }}"
                                method="POST"
                                onsubmit="return confirm('{{ __('events::messages.cancel_confirm') ?? 'Cancelar esta inscrição?' }}');"
                                x-data
                                x-on:submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando...' } }))">
                                @csrf
                                <input type="text" name="reason" placeholder="Motivo (opcional)"
                                    class="w-full mb-2 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm">
                                <button type="submit"
                                    class="block w-full text-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                                    {{ __('events::messages.cancel_registration') ?? 'Cancelar inscrição' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
