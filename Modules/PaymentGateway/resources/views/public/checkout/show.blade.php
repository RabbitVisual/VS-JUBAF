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
                        <div class="bg-linear-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-8 mb-6 border-2 border-green-200 dark:border-green-800">
                            <div class="text-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Pague com PIX</h2>
                                <p class="text-gray-600 dark:text-gray-400">Escaneie o QR Code no app do seu banco ou copie o código “Copia e Cola” abaixo.</p>
                            </div>

                            <div class="flex flex-col items-center justify-center mb-6">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border-2 border-green-500">
                                    @if($qrCodeUrl)
                                        @if(str_starts_with($qrCodeUrl, 'data:image') || str_starts_with($qrCodeUrl, 'http'))
                                            <img src="{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto">
                                        @else
                                            <img src="data:image/png;base64,{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto">
                                        @endif
                                    @elseif($pixCode)
                                        <img src="{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']), 'size' => 300]) }}" alt="QR Code PIX" class="w-64 h-64 object-contain mx-auto">
                                    @endif
                                </div>
                            </div>

                            @if($pixCode)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border border-gray-200 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código PIX "Copia e Cola"</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="pix-code-input" value="{{ $pixCode }}" readonly class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-mono">
                                        <button type="button" onclick="copyPixCode()" class="px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">Copiar</button>
                                    </div>
                                </div>
                            @endif

                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 animate-pulse">
                                Esta página será atualizada automaticamente assim que o pagamento for confirmado.
                            </p>
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
