@extends('memberpanel::components.layouts.master')

@section('page-title', 'Status da Doação')

@section('content')
    <div class="space-y-8 pb-12">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-4 font-medium">
            <a href="{{ route('memberpanel.donations.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors uppercase tracking-wider text-xs font-bold">Minhas Doações</a>
            <x-icon name="chevron-right" style="solid" class="w-4 h-4" />
            <span class="text-gray-900 dark:text-white font-bold">Status</span>
        </nav>

        <!-- Status Card -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-8 md:p-12 text-center relative overflow-hidden">
                <!-- Decorative Background -->
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute top-0 left-0 w-64 h-64 bg-blue-500 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-purple-500 rounded-full blur-[100px]"></div>
                </div>

                <div class="relative z-10">
                    @if($payment->status === 'completed')
                        <!-- Success -->
                        <div class="mb-8">
                            <div class="mx-auto w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-6 shadow-inner ring-4 ring-emerald-50 dark:ring-emerald-900/10">
                                <x-icon name="check-circle" style="duotone" class="w-12 h-12 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">Doação Confirmada!</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                                Sua generosidade faz a diferença. Deus abençoe sua vida grandemente!
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg mx-auto mb-8">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Valor</span>
                                <span class="block text-xl font-black text-gray-900 dark:text-white">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Data</span>
                                <span class="block text-xl font-black text-gray-900 dark:text-white">{{ $payment->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                    @elseif($payment->status === 'pending')
                        <!-- Pending -->
                        <div class="mb-8">
                            <div class="mx-auto w-24 h-24 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mb-6 shadow-inner ring-4 ring-amber-50 dark:ring-amber-900/10 relative">
                                <x-icon name="clock" style="duotone" class="w-12 h-12 text-amber-600 dark:text-amber-400 animate-pulse" />
                                <div class="absolute inset-0 rounded-full border-4 border-amber-200 dark:border-amber-800 border-t-amber-500 animate-spin"></div>
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">Aguardando Pagamento</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                                Estamos aguardando a confirmação da sua doação.
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
                                <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-emerald-500/20 p-8 max-w-md mx-auto shadow-lg relative overflow-hidden">
                                     <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-emerald-500 to-green-500"></div>

                                    <div class="text-center mb-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pague com PIX</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Escaneie o QR Code abaixo</p>
                                    </div>

                                    <!-- QR Code Display -->
                                    <div class="flex flex-col items-center justify-center mb-6">
                                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                            @php
                                                $qrCodeToShow = $qrCodeUrl ?? $qrCodeBase64;
                                            @endphp
                                            @if($qrCodeToShow)
                                                @if(str_starts_with($qrCodeToShow, 'data:image'))
                                                    <img src="{{ $qrCodeToShow }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                                @elseif(str_starts_with($qrCodeToShow, 'http'))
                                                    <img src="{{ $qrCodeToShow }}" alt="QR Code PIX"
                                                         class="w-48 h-48 object-contain"
                                                         onerror="this.onerror=null; this.src='{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode ?? ''), ['+' => '-', '/' => '_']), 'size' => 300]) }}';">
                                                @else
                                                     <img src="data:image/png;base64,{{ $qrCodeToShow }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                                @endif
                                            @elseif($pixCode)
                                                <img src="{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']), 'size' => 300]) }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                            @endif
                                        </div>
                                    </div>

                                    @if($pixCode)
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Copia e Cola</label>
                                            <div class="flex items-center gap-2">
                                                <input type="text" value="{{ $pixCode }}" readonly class="flex-1 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-mono text-gray-600 dark:text-gray-400 focus:outline-none">
                                                <button onclick="navigator.clipboard.writeText('{{ $pixCode }}')" class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors" title="Copiar">
                                                    <x-icon name="copy" style="duotone" class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if($expiresAt)
                                        <div class="mt-4 flex items-center justify-center gap-2 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 py-2 rounded-lg">
                                            <x-icon name="clock" style="duotone" class="w-3 h-3" />
                                            Expira em: {{ $expiresAt->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    @else
                        <!-- Failed/Cancelled -->
                        <div class="mb-8">
                             <div class="mx-auto w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-900/10">
                                <x-icon name="circle-xmark" style="duotone" class="w-12 h-12 text-red-600 dark:text-red-400" />
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">Doação Cancelada</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                                O pagamento não foi concluído.
                            </p>
                        </div>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('memberpanel.donations.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors uppercase tracking-widest">
                            Voltar para o Histórico
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($payment->status === 'pending' || $payment->status === 'processing')
            let pollInterval;

            function checkPaymentStatus() {
                // Use the dedicated check-status route defined in web.php
                fetch('{{ route("memberpanel.donations.check-status", $payment->transaction_id) }}', {
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

            // Iniciar polling a cada 5 segundos
            pollInterval = setInterval(checkPaymentStatus, 5000);
        @endif
    });
</script>
@endpush
