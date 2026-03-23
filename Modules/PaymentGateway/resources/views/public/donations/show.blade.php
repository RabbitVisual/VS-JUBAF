@extends('homepage::components.layouts.master')

@section('content')
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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Doação recebida!</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-1">
                        Obrigado pela sua contribuição. Deus abençoe!
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        Sua doação foi registrada e será utilizada conforme o destino escolhido.
                    </p>
                </div>
            @elseif($payment->status === 'pending')
                <!-- Pending -->
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="rotate" style="duotone" class="w-8 h-8 text-yellow-600 dark:text-yellow-400 animate-spin" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Aguardando Pagamento</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Seu pagamento está sendo processado. Aguarde a confirmação.
                    </p>
                </div>

                <!-- PIX QR Code Section -->
                @php
                    $gatewayResponse = is_array($payment->gateway_response) ? $payment->gateway_response : json_decode($payment->gateway_response, true);

                    // Flatten MP structure
                    if (isset($gatewayResponse['point_of_interaction']['transaction_data'])) {
                        $data = $gatewayResponse['point_of_interaction']['transaction_data'];
                        $pixCode = $data['qr_code'] ?? null;
                        $qrCodeBase64 = $data['qr_code_base64'] ?? null;
                    } else {
                        $pixCode = $gatewayResponse['pix_code'] ?? null;
                        $qrCodeBase64 = $gatewayResponse['qr_code_base64'] ?? null;
                    }

                    // Stripe fallback
                    if (!$pixCode && isset($gatewayResponse['next_action']['pix_display_qr_code'])) {
                        $pixCode = $gatewayResponse['next_action']['pix_display_qr_code']['data'] ?? null;
                    }

                    // Final URL
                    $qrCodeUrl = $qrCodeBase64 ?? $gatewayResponse['qr_code'] ?? null;
                    $expiresAt = isset($gatewayResponse['date_of_expiration'])
                        ? \Carbon\Carbon::parse($gatewayResponse['date_of_expiration'])
                        : (isset($gatewayResponse['expires_at']) ? \Carbon\Carbon::parse($gatewayResponse['expires_at']) : null);

                    $hasPixData = $pixCode || $qrCodeUrl || $qrCodeBase64;
                @endphp

                @if($hasPixData && $payment->gateway_response)

                    @if($pixCode || $qrCodeUrl || $qrCodeBase64)
                        <div class="bg-linear-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-8 mb-6 border-2 border-green-200 dark:border-green-800">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                                    <x-icon name="mobile-screen-button" style="duotone" class="w-8 h-8 text-green-600 dark:text-green-400" />
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Escaneie o QR Code</h2>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Use o aplicativo do seu banco para escanear e realizar o pagamento
                                </p>
                                @if($expiresAt)
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                                        <x-icon name="clock" style="duotone" class="w-4 h-4 inline mr-1" />
                                        Válido até: {{ $expiresAt->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>

                            <!-- QR Code Display -->
                            <div class="flex flex-col items-center justify-center mb-6">
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl border-4 border-green-500 dark:border-green-600">
                                    @php
                                        $qrCodeToShow = $qrCodeUrl ?? $qrCodeBase64;
                                    @endphp
                                    @if($qrCodeToShow)
                                        @if(str_starts_with($qrCodeToShow, 'data:image'))
                                            <img src="{{ $qrCodeToShow }}" alt="QR Code PIX"
                                                class="w-64 h-64 object-contain mx-auto"
                                                id="pix-qr-code">
                                        @elseif(str_starts_with($qrCodeToShow, 'http'))
                                            <img src="{{ $qrCodeToShow }}" alt="QR Code PIX"
                                                class="w-64 h-64 object-contain mx-auto"
                                                id="pix-qr-code"
                                                onerror="this.onerror=null; this.src='{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode ?? ''), ['+' => '-', '/' => '_']), 'size' => 300]) }}';">
                                        @else
                                            <img src="data:image/png;base64,{{ $qrCodeToShow }}" alt="QR Code PIX"
                                                class="w-64 h-64 object-contain mx-auto"
                                                id="pix-qr-code">
                                        @endif
                                    @elseif($pixCode)
                                        <img src="{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']), 'size' => 300]) }}"
                                            alt="QR Code PIX"
                                            class="w-64 h-64 object-contain mx-auto"
                                            id="pix-qr-code">
                                    @else
                                        <div class="w-64 h-64 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                            <p class="text-gray-500 dark:text-gray-400">QR Code não disponível</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- PIX Code (Copy to Clipboard) -->
                            @if($pixCode)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border border-gray-200 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Código PIX (Copie e cole no app do banco)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="text"
                                            id="pix-code-input"
                                            value="{{ $pixCode }}"
                                            readonly
                                            class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-mono text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <button type="button"
                                            onclick="copyPixCode()"
                                            class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                                            <x-icon name="copy" style="duotone" class="w-5 h-5" />
                                            <span id="copy-button-text">Copiar</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Instructions -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-2 flex items-center">
                                    <x-icon name="circle-info" style="duotone" class="w-5 h-5 mr-2" />
                                    Como pagar com PIX
                                </h3>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-300">
                                    <li>Abra o aplicativo do seu banco</li>
                                    <li>Escaneie o QR Code ou copie o código PIX</li>
                                    <li>Confirme o pagamento no app</li>
                                    <li>Aguarde a confirmação automática</li>
                                </ol>
                            </div>

                            <!-- Auto-refresh notice -->
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <x-icon name="rotate" style="duotone" class="w-4 h-4 inline mr-1 animate-spin" />
                                    Esta página será atualizada automaticamente quando o pagamento for confirmado
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <!-- Failed -->
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="xmark" style="duotone" class="w-8 h-8 text-red-600 dark:text-red-400" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Pagamento Não Processado</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Ocorreu um erro ao processar seu pagamento. Tente novamente.
                    </p>
                </div>
            @endif

            <!-- Payment Details -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6 text-left">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detalhes da Doação</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">ID da Transação:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->transaction_id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Valor:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            R$ {{ number_format($payment->amount, 2, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Gateway:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $payment->gateway->display_name ?? 'N/A' }}
                        </dd>
                    </div>
                    @if($payment->payable)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">
                                @if($payment->payment_type === 'campaign')
                                    Campanha:
                                @elseif($payment->payment_type === 'ministry_donation')
                                    Ministério:
                                @else
                                    Destino:
                                @endif
                            </dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $payment->payable->name ?? 'N/A' }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Status:</dt>
                        <dd class="text-sm font-medium">
                            @if($payment->status === 'completed')
                                <span class="text-green-600 dark:text-green-400">Concluído</span>
                            @elseif($payment->status === 'pending')
                                <span class="text-yellow-600 dark:text-yellow-400">Pendente</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Falhou</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Data:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $payment->created_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                    @if($payment->description)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                            <dt class="text-sm text-gray-600 dark:text-gray-400 mb-1">Descrição:</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('donation.create') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    <x-icon name="hand-holding-heart" style="duotone" class="w-5 h-5 mr-2" />
                    Fazer nova doação
                </a>
                <a href="{{ route('homepage.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition-colors">
                    <x-icon name="house" style="duotone" class="w-5 h-5 mr-2" />
                    Voltar ao início
                </a>
            </div>
        </div>
    </div>
</div>

@if($payment->status === 'pending' && $payment->payment_method === 'pix')
<script>
    function copyPixCode() {
        const pixCodeInput = document.getElementById('pix-code-input');
        const copyButtonText = document.getElementById('copy-button-text');

        if (pixCodeInput) {
            pixCodeInput.select();
            pixCodeInput.setSelectionRange(0, 99999); // Para mobile

            try {
                document.execCommand('copy');
                copyButtonText.textContent = 'Copiado!';
                copyButtonText.parentElement.classList.add('bg-green-700');

                setTimeout(() => {
                    copyButtonText.textContent = 'Copiar';
                    copyButtonText.parentElement.classList.remove('bg-green-700');
                }, 2000);
            } catch (err) {
                console.error('Erro ao copiar:', err);
                copyButtonText.textContent = 'Erro';
            }
        }
    }

    // Auto-refresh para verificar status do pagamento PIX
    let refreshInterval;
    let refreshCount = 0;
    const maxRefreshAttempts = 120; // 10 minutos (5 segundos * 120)

    function checkPaymentStatus() {
        if (refreshCount >= maxRefreshAttempts) {
            clearInterval(refreshInterval);
            const notice = document.createElement('div');
            notice.className = 'mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg';
            notice.innerHTML = '<p class="text-sm text-yellow-800 dark:text-yellow-200">Tempo de espera esgotado. Por favor, verifique o status do pagamento manualmente.</p>';
            document.querySelector('.bg-white.dark\\:bg-gray-800').appendChild(notice);
            return;
        }

        fetch('{{ route("donation.show", $payment->transaction_id) }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json();
            }
            // Se não for JSON, recarrega a página
            window.location.reload();
            return null;
        })
        .then(data => {
            if (data && data.status === 'completed') {
                clearInterval(refreshInterval);
                window.location.reload();
            }
            refreshCount++;
        })
        .catch(error => {
            console.error('Erro ao verificar status:', error);
            refreshCount++;
        });
    }

    // Inicia polling a cada 5 segundos
    refreshInterval = setInterval(checkPaymentStatus, 5000);
</script>
@endif
@endsection
