@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.registration_pending') . ' - ' . __('memberpanel::messages.member_panel'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12 flex items-center justify-center p-4">
    <div class="max-w-7xl mx-auto w-full">
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-6 justify-center">
            <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.events') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium">{{ __('events::messages.waiting_payment') }}</span>
        </nav>
    <div class="max-w-xl w-full mx-auto bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 md:p-12 text-center relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-t-2xl"></div>
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 w-20 h-20 sm:w-24 sm:h-24 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8">
            <x-icon name="clock" class="w-10 h-10 sm:w-12 sm:h-12 text-amber-600 dark:text-amber-400 animate-pulse" />
        </div>

        <h2 class="relative z-10 text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-3 sm:mb-4 tracking-tight">
            {{ __('events::messages.waiting_payment') }}
        </h2>

        <p class="relative z-10 text-base sm:text-lg text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 leading-relaxed px-2">
            {!! __('events::messages.registration_pending_msg', ['event' => '<strong>' . $registration->event->title . '</strong>']) !!}
        </p>

        <div class="relative z-10 bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-slate-700 mb-6 sm:mb-8 text-left">
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('events::messages.registration') }}</span>
                <span class="font-black text-gray-900 dark:text-white">#{{ $registration->id }}</span>
            </div>
            <div class="flex justify-between items-center">
                 <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('events::messages.pending_value') }}</span>
                 <span class="font-bold text-amber-600 dark:text-amber-400">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</span>
            </div>

            <!-- Pix Data Display -->
            @if($registration->status === 'pending' && $registration->latestPayment && ($registration->latestPayment->payment_method === 'pix' || $registration->latestPayment->payment_method === 'bank_transfer') && isset($registration->latestPayment->gateway_response['point_of_interaction']['transaction_data']))
                @php
                    $pixData = $registration->latestPayment->gateway_response['point_of_interaction']['transaction_data'];
                    $qrCodeBase64 = $pixData['qr_code_base64'] ?? null;
                    $qrCode = $pixData['qr_code'] ?? null;
                @endphp

                @if($qrCode || $qrCodeBase64)
                <div class="mt-6 p-4 bg-white dark:bg-slate-800 rounded-xl border border-dashed border-amber-300 dark:border-amber-700/50">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                        <x-icon name="qrcode" class="w-5 h-5 text-emerald-500" style="duotone" />
                        Pagamento via Pix
                    </h4>

                    <div class="flex flex-col items-center gap-4">
                        @if($qrCodeBase64)
                            <div class="w-40 h-40 bg-white p-2 rounded-xl shadow-sm">
                                <img src="data:image/jpeg;base64,{{ $qrCodeBase64 }}" alt="QR Code Pix" class="w-full h-full object-contain">
                            </div>
                        @endif

                        <div class="w-full">
                            <p class="text-xs text-center text-slate-500 dark:text-slate-400 mb-2 font-medium">Copie e cole este código:</p>
                            <div class="relative group">
                                <code class="block w-full p-3 bg-gray-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-mono text-slate-600 dark:text-slate-300 break-all cursor-pointer hover:bg-slate-100 transition-colors text-center"
                                      onclick="navigator.clipboard.writeText('{{ $qrCode }}'); alert('Código Pix copiado!');">
                                    {{ Str::limit($qrCode, 30) }}...
                                </code>
                                <button class="mt-2 w-full py-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-bold hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors flex items-center justify-center gap-2"
                                        onclick="navigator.clipboard.writeText('{{ $qrCode }}'); alert('Código Pix copiado!');">
                                    <x-icon name="copy" class="w-3 h-3" />
                                    Copiar Código Completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="{{ route('memberpanel.events.my-registrations') }}"
               class="px-6 sm:px-8 py-3 sm:py-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-amber-500/20 active:scale-[0.98] touch-manipulation">
                {{ __('events::messages.view_registrations') }}
            </a>
            <a href="{{ route('memberpanel.events.index') }}"
               class="px-6 sm:px-8 py-3 sm:py-4 bg-white dark:bg-slate-800 text-gray-700 dark:text-white border border-gray-200 dark:border-slate-700 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all touch-manipulation active:scale-[0.98]">
                {{ __('events::messages.back') }}
            </a>
        </div>
    </div>
    </div>
</div>
@endsection
