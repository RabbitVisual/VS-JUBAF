@extends('homepage::components.layouts.master')

@section('content')
@php
    $isEventRegistration = $payment->payment_type === 'event_registration' && $payment->payable;
    $eventTitle = $isEventRegistration && $payment->payable->relationLoaded('event') ? $payment->payable->event?->title : ($payment->metadata['event_title'] ?? null);
    $registrationConfirmed = $isEventRegistration && $payment->payable->status === 'confirmed';
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            @if($payment->status === 'completed')
                <!-- Success -->
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="check" style="duotone" class="w-8 h-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Pagamento confirmado!</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        @if($eventTitle)
                            Sua inscrição no evento <strong>{{ $eventTitle }}</strong> foi confirmada. Em breve você receberá mais informações por e-mail.
                        @else
                            Seu pagamento foi recebido com sucesso. Obrigado!
                        @endif
                    </p>
                </div>
            @elseif($payment->status === 'pending' || $payment->status === 'processing')
                <!-- Pending -->
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="rotate" style="duotone" class="w-8 h-8 text-yellow-600 dark:text-yellow-400 animate-spin" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Aguardando pagamento</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Conclua o pagamento abaixo (PIX) ou aguarde a confirmação do gateway.
                    </p>
                </div>

                <!-- PIX Instructions -->
                @php
                    $gatewayResponse = is_array($payment->gateway_response) ? $payment->gateway_response : json_decode($payment->gateway_response, true);

                    $pixCode = null;
                    $qrCodeBase64 = null;

                    if ($gatewayResponse) {
                        // Mercado Pago structure
                        if (isset($gatewayResponse['point_of_interaction']['transaction_data'])) {
                            $data = $gatewayResponse['point_of_interaction']['transaction_data'];
                            $pixCode = $data['qr_code'] ?? null;
                            $qrCodeBase64 = $data['qr_code_base64'] ?? null;
                        }
                        // Manual/Fallback structure
                        else {
                            $pixCode = $gatewayResponse['pix_code'] ?? null;
                            $qrCodeBase64 = $gatewayResponse['qr_code_base64'] ?? null;
                        }

                        // Stripe fallback
                        if (!$pixCode && isset($gatewayResponse['next_action']['pix_display_qr_code'])) {
                            $pixCode = $gatewayResponse['next_action']['pix_display_qr_code']['data'] ?? null;
                        }
                    }

                    $qrCodeUrl = $qrCodeBase64 ?? ($gatewayResponse['qr_code'] ?? null);
                    $hasPixData = $pixCode || $qrCodeUrl || $qrCodeBase64;
                @endphp

                @if($hasPixData && $payment->gateway_response)
                        <div class="bg-gray-900 rounded-3xl p-8 mb-6 shadow-2xl relative overflow-hidden border border-gray-800">
                            <!-- Background elements -->
                            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-green-500/10 blur-3xl pointer-events-none"></div>
                            
                            <div class="text-center mb-8 relative z-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-linear-to-br from-green-400 to-green-600 shadow-lg shadow-green-500/30 mb-4">
                                    <x-icon name="qrcode" class="w-8 h-8 text-white" />
                                </div>
                                <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Pagamento via PIX</h2>
                                <p class="text-gray-400 font-medium">Abra o app do seu banco, escolha a opção PIX e escaneie o código abaixo.</p>
                            </div>

                            <div class="flex flex-col items-center justify-center mb-8 relative z-10">
                                <div class="bg-white p-4 rounded-2xl shadow-xl shadow-black/50 ring-4 ring-green-500/20">
                                    @if($qrCodeUrl)
                                        @if(str_starts_with($qrCodeUrl, 'data:image') || str_starts_with($qrCodeUrl, 'http'))
                                            <img src="{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto rounded-xl">
                                        @else
                                            <img src="data:image/png;base64,{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto rounded-xl">
                                        @endif
                                    @elseif($pixCode)
                                        <img src="{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']), 'size' => 300]) }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto rounded-xl">
                                    @endif
                                </div>
                                <div class="mt-6 flex items-center justify-center space-x-2 text-green-400 animate-pulse">
                                    <x-icon name="rotate" class="w-5 h-5 animate-spin" />
                                    <span class="text-sm font-semibold uppercase tracking-wider">Aguardando Pagamento...</span>
                                </div>
                            </div>

                            @if($pixCode)
                                <div class="bg-gray-800/80 backdrop-blur-md rounded-2xl p-5 mb-2 border border-gray-700 relative z-10">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center">Ou utilize o Pix Copia e Cola</label>
                                    <div class="flex flex-col sm:flex-row items-center gap-3">
                                        <div class="flex-1 w-full bg-gray-900 rounded-xl px-4 py-3 border border-gray-700 flex items-center">
                                            <input type="text" id="pix-code-input" value="{{ $pixCode }}" readonly class="w-full bg-transparent border-none text-gray-300 text-sm font-mono focus:ring-0 p-0 truncate outline-none">
                                        </div>
                                        <button type="button" onclick="copyPixCode()" class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-500 transition-colors shadow-lg shadow-green-600/20 flex items-center justify-center gap-2">
                                            <x-icon name="copy" class="w-5 h-5" />
                                            Copiar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            @else
                <!-- Failed/Cancelled -->
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="xmark" style="duotone" class="w-8 h-8 text-red-600 dark:text-red-400" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Pagamento não realizado</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Não foi possível concluir seu pagamento. Você pode tentar novamente ou escolher outra forma de pagamento.</p>
                </div>
            @endif

            <!-- Transaction Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-8 text-left">
                @if($eventTitle)
                    <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Evento</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $eventTitle }}</p>
                    </div>
                @endif
                <dl class="space-y-4">
                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-600 pb-2">
                        <dt class="text-gray-600 dark:text-gray-400">Total</dt>
                        <dd class="text-xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($payment->amount, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600 dark:text-gray-400">Transação</dt>
                        <dd class="font-mono font-medium text-gray-900 dark:text-white text-xs">{{ $payment->transaction_id }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600 dark:text-gray-400">Processado por:</dt>
                        <dd class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            {{ $payment->gateway->display_name }}
                            @if($payment->gateway->name === 'mercado_pago')
                                <img src="https://logospng.org/wp-content/uploads/mercado-pago.png" class="h-6 w-auto" alt="MP">
                            @elseif($payment->gateway->name === 'stripe')
                                <x-icon name="stripe" style="brands" class="h-4 w-4 text-purple-600" />
                            @endif
                        </dd>
                    </div>
                    @if($payment->description)
                    <div class="text-sm">
                        <dt class="text-gray-600 dark:text-gray-400 mb-1">Descrição:</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $payment->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($payment->status === 'completed' && $isEventRegistration && $registrationConfirmed && $payment->payable->uuid)
                    @if(auth()->check())
                        <a href="{{ route('memberpanel.events.show-registration', $payment->payable) }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                            <x-icon name="ticket" style="duotone" class="w-5 h-5 mr-2" />
                            Ver minha inscrição
                        </a>
                        @if($payment->payable->event && $payment->payable->event->hasTicketEnabled())
                            <a href="{{ route('events.public.ticket.download', ['uuid' => $payment->payable->uuid]) }}" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
                                <x-icon name="file-pdf" style="duotone" class="w-5 h-5 mr-2" />
                                Baixar ingresso
                            </a>
                        @endif
                    @else
                        <a href="{{ route('events.public.registration.confirmed', $payment->payable->id) }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                            <x-icon name="circle-check" style="duotone" class="w-5 h-5 mr-2" />
                            Ver confirmação da inscrição
                        </a>
                    @endif
                @endif
                <a href="{{ route('homepage.index') }}" class="px-8 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Voltar ao início</a>
            </div>
        </div>
    </div>
</div>

<script>
    function copyPixCode() {
        const input = document.getElementById('pix-code-input');
        input.select();
        document.execCommand('copy');
        alert('Código PIX copiado para a área de transferência!');
    }

    @if($payment->status === 'pending' || $payment->status === 'processing')
    document.addEventListener('DOMContentLoaded', function() {
        let pollInterval;

        function checkPaymentStatus() {
            fetch(window.location.href, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.status === 'completed' || data.status === 'failed' || data.status === 'cancelled') {
                    clearInterval(pollInterval);
                    window.location.reload();
                }
            })
            .catch(error => console.log('Polling inactive or error:', error));
        }

        // Iniciar polling
        pollInterval = setInterval(checkPaymentStatus, 5000);
    });
    @endif
</script>
@endsection
