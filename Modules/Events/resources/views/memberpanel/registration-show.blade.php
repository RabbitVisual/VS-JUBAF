@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.registration_details') . ' - ' . __('memberpanel::messages.member_panel'))
@section('page-title', __('events::messages.registration_details'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6 sm:space-y-8">
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.events') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.my-registrations') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.my_registrations') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium">#{{ $registration->id }}</span>
        </nav>

        <!-- Hero card -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800">
            @if($registration->event->banner_path)
                <div class="absolute inset-0 z-0 opacity-20">
                    <img src="{{ Storage::url($registration->event->banner_path) }}" alt="" class="w-full h-40 sm:h-48 object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-slate-900 to-transparent"></div>
                </div>
            @else
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-500 rounded-full blur-[100px]"></div>
                </div>
            @endif
            <div class="relative px-4 sm:px-6 md:px-8 py-8 sm:py-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 z-10">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ __('events::messages.registration') }} #{{ $registration->id }}</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ __('events::messages.check_registration_details') }}</p>
                </div>
                <div class="px-4 py-2 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shrink-0">
                    <span class="text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-wider block">{{ __('events::messages.status') }}</span>
                    <span class="font-black text-gray-900 dark:text-white text-lg">{{ $registration->status_display }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Event Info -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <x-icon name="calendar" class="w-6 h-6" />
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">
                    {{ __('events::messages.about_event') }}
                </h2>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.event') }}</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white block">{{ $registration->event->title }}</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.start') }}</span>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300 block">{{ $registration->event->start_date->format('d/m/Y H:i') }}</span>
                    </div>
                     <div>
                        <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.end') }}</span>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300 block">
                            {{ $registration->event->end_date ? $registration->event->end_date->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.location') }}</span>
                    <span class="text-base font-medium text-gray-700 dark:text-gray-300 block">{{ $registration->event->location ?? __('events::messages.online_not_informed') }}</span>
                </div>
            </div>
        </div>

         <!-- Financial Info -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <x-icon name="currency-dollar" class="w-6 h-6" />
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">
                    {{ __('events::messages.financial') }}
                </h2>
            </div>

             <div class="space-y-4">
                <div>
                    <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.total_value') }}</span>
                    <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 block">
                        R$ {{ number_format($registration->total_amount, 2, ',', '.') }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.registration_date') }}</span>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300 block">{{ $registration->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                     <div>
                        <span class="text-sm font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('events::messages.payment') }}</span>
                        <div class="flex items-center gap-2">
                             <span class="text-base font-medium text-gray-700 dark:text-gray-300 block">
                                {{ $registration->paid_at ? $registration->paid_at->format('d/m/Y H:i') : __('events::messages.pending') }}
                            </span>
                            @if($registration->status === 'pending')
                                <span class="bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-bold px-2 py-0.5 rounded uppercase">Aguardando</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pix Data Display -->
                @php
                    $latestPayment = $registration->latestPayment;
                    $gatewayResponse = $latestPayment ? (is_array($latestPayment->gateway_response) ? $latestPayment->gateway_response : json_decode($latestPayment->gateway_response, true)) : null;

                    $pixCode = null;
                    $qrCodeBase64 = null;

                    if ($gatewayResponse) {
                        // Mercado Pago
                        if (isset($gatewayResponse['point_of_interaction']['transaction_data'])) {
                            $pixCode = $gatewayResponse['point_of_interaction']['transaction_data']['qr_code'] ?? null;
                            $qrCodeBase64 = $gatewayResponse['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
                        }
                        // Generic/Stripe fallback
                        else {
                            $pixCode = $gatewayResponse['pix_code'] ?? ($gatewayResponse['next_action']['pix_display_qr_code']['data'] ?? null);
                            $qrCodeBase64 = $gatewayResponse['qr_code_base64'] ?? null;
                        }
                    }

                    $hasPixData = $pixCode || $qrCodeBase64 || (isset($gatewayResponse['qr_code']));
                @endphp

                @if($registration->status === 'pending' && $hasPixData)
                    @php
                        $qrCodeToShow = $qrCodeBase64 ?? ($gatewayResponse['qr_code'] ?? null);
                    @endphp

                    <div class="mt-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-icon name="qrcode" class="w-5 h-5 text-emerald-500" />
                            Pagamento via Pix
                        </h4>

                        <div class="flex flex-col md:flex-row items-center gap-6">
                            @if($qrCodeToShow)
                                <div class="w-48 h-48 bg-white p-2 rounded-xl shadow-sm">
                                    @if(str_starts_with($qrCodeToShow, 'data:image') || str_starts_with($qrCodeToShow, 'http'))
                                        <img src="{{ $qrCodeToShow }}" alt="QR Code Pix" class="w-full h-full object-contain">
                                    @else
                                        <img src="data:image/jpeg;base64,{{ $qrCodeToShow }}" alt="QR Code Pix" class="w-full h-full object-contain">
                                    @endif
                                </div>
                            @endif

                            <div class="flex-1 w-full overflow-hidden">
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-2 font-medium">Copie e cole este código no seu app do banco:</p>
                                <div class="relative group">
                                    <code class="block w-full p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-600 dark:text-slate-300 break-all cursor-pointer hover:bg-slate-50 transition-colors"
                                          onclick="navigator.clipboard.writeText('{{ $pixCode }}'); alert('Código Pix copiado!');">
                                        {{ $pixCode }}
                                    </code>
                                    <button class="absolute top-2 right-2 p-2 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-slate-500 dark:text-slate-400"
                                            onclick="navigator.clipboard.writeText('{{ $pixCode }}'); alert('Código Pix copiado!');"
                                            title="Copiar Código">
                                        <x-icon name="copy" class="w-4 h-4" />
                                    </button>
                                </div>
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-3 font-bold flex items-center gap-1">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    O pagamento será reconhecido automaticamente em alguns segundos.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>

    <!-- Participants -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-6 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <x-icon name="users" class="w-6 h-6" />
            </div>
            <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">
                {{ __('events::messages.participants') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($registration->participants as $participant)
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 bg-gray-50/50 dark:bg-slate-800/30">
                <div class="space-y-3">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">{{ __('events::messages.name') }}</span>
                        <span class="text-base font-bold text-gray-900 dark:text-white block">{{ $participant->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">{{ __('events::messages.email') }}</span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 block">{{ $participant->email }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3 pt-4">
        @if($registration->event->hasTicketEnabled() && $registration->status === 'confirmed' && !empty($registration->uuid))
        <a href="{{ route('events.public.ticket.download', $registration->uuid) }}"
           class="w-full sm:w-auto px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl transition-colors text-center touch-manipulation active:scale-[0.98] inline-flex items-center justify-center gap-2">
            <x-icon name="file-arrow-down" class="w-5 h-5" />
            {{ __('events::messages.download_ticket') }}
        </a>
        @endif
        @if($registration->event->hasCertificateEnabled())
        <a href="{{ route('events.public.certificate.download', $registration->uuid) }}"
           class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors text-center touch-manipulation active:scale-[0.98] inline-flex items-center justify-center gap-2">
            <x-icon name="certificate" class="w-5 h-5" />
            {{ __('events::messages.download_certificate') }}
        </a>
        @endif
        <a href="{{ route('memberpanel.events.my-registrations') }}"
           class="w-full sm:w-auto px-6 py-3 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-center touch-manipulation active:scale-[0.98]">
            {{ __('events::messages.back') }}
        </a>
        <a href="{{ route('memberpanel.events.show', $registration->event) }}"
           class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-indigo-500/20 text-center touch-manipulation active:scale-[0.98]">
            {{ __('events::messages.view_event_page') }}
        </a>
    </div>
    </div>
</div>
@endsection
