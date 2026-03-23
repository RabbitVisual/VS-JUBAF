@extends('homepage::components.layouts.master')

@section('content')
@php
    $gateways = $gateways ?? collect();
    $pendingPayment = $pendingPayment ?? null;
    $paymentUrl = $pendingPayment ? route('checkout.show', $pendingPayment->transaction_id) : null;
    $gatewayResponse = $pendingPayment && $pendingPayment->gateway_response
        ? (is_array($pendingPayment->gateway_response) ? $pendingPayment->gateway_response : json_decode($pendingPayment->gateway_response, true))
        : null;
    $pixCode = null;
    $qrCodeBase64 = null;
    if ($gatewayResponse) {
        if (isset($gatewayResponse['point_of_interaction']['transaction_data'])) {
            $data = $gatewayResponse['point_of_interaction']['transaction_data'];
            $pixCode = $data['qr_code'] ?? null;
            $qrCodeBase64 = $data['qr_code_base64'] ?? null;
        } else {
            $pixCode = $gatewayResponse['pix_code'] ?? null;
            $qrCodeBase64 = $gatewayResponse['qr_code_base64'] ?? null;
        }
        if (!$pixCode && isset($gatewayResponse['next_action']['pix_display_qr_code'])) {
            $pixCode = $gatewayResponse['next_action']['pix_display_qr_code']['data'] ?? null;
        }
    }
    $qrCodeUrl = $qrCodeBase64 ?? ($gatewayResponse['qr_code'] ?? null);
    $hasPixData = $pixCode || $qrCodeUrl || $qrCodeBase64;
@endphp
<div class="min-h-screen bg-slate-950 text-white font-sans flex items-center justify-center p-4"
     x-data="checkoutTimer('{{ $expiration->toIso8601String() }}')">

    <div class="max-w-2xl w-full bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden relative">

        <!-- Timer Bar -->
        <div class="bg-slate-800 p-4 flex justify-between items-center border-b border-slate-700">
            <div class="flex items-center gap-2 text-amber-500">
                <x-icon name="clock" style="duotone" class="w-5 h-5 animate-pulse" />
                <span class="font-bold uppercase text-sm tracking-wider">{{ __('events::messages.time_to_complete') ?? 'Tempo para concluir' }}</span>
            </div>
            <div class="text-2xl font-mono font-bold text-white" x-text="timeLeft"></div>
        </div>

        <!-- Main Content -->
        <div class="p-8">
            @if(session('error'))
                <div class="mb-6 rounded-xl border border-red-500/50 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-6 rounded-xl border border-amber-500/50 bg-amber-500/10 text-amber-200 px-4 py-3 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="check" style="duotone" class="w-8 h-8 text-emerald-500" />
                </div>
                <h2 class="text-2xl font-bold text-white">{{ __('events::messages.ingresso_reservado') ?? 'Ingresso Reservado!' }}</h2>
                <p class="text-slate-400 mt-2">{{ __('events::messages.garantido_por_minutos') ?? 'Seu lugar está garantido por' }} <span class="text-amber-500 font-bold">15 {{ __('events::messages.minutes') ?? 'minutos' }}</span>.</p>
            </div>

            <div class="bg-slate-950 rounded-xl p-6 border border-slate-800 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-slate-400">{{ __('events::messages.event') ?? 'Evento' }}</span>
                    <span class="font-medium text-white text-right">{{ $registration->event->title }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-slate-400">{{ __('events::messages.registration') ?? 'Inscrição' }}</span>
                    <span class="font-medium text-white">{{ $registration->batch?->name ?? __('events::messages.registration') }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-slate-800">
                    <span class="text-lg font-bold text-white">{{ __('events::messages.total') ?? 'Total' }}</span>
                    <span class="text-2xl font-bold text-amber-500">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>

            @if($pendingPayment)
                {{-- Já existe pagamento pendente: mostrar PIX/QR se houver e link para checkout --}}
                @if($hasPixData)
                    <div class="mb-8 p-6 bg-slate-950 border border-emerald-500/30 rounded-xl">
                        <h3 class="font-bold text-white mb-4">{{ __('events::messages.pay_with_pix') ?? 'Pague com PIX' }}</h3>
                        <p class="text-sm text-slate-400 mb-4">{{ __('events::messages.pix_instructions') ?? 'Escaneie o QR Code no app do seu banco ou copie o código abaixo.' }}</p>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl">
                                @if($qrCodeUrl)
                                    @if(str_starts_with($qrCodeUrl, 'data:image') || str_starts_with($qrCodeUrl, 'http'))
                                    <img src="{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                    @else
                                    <img src="data:image/png;base64,{{ $qrCodeUrl }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                    @endif
                                @elseif($pixCode)
                                    <img src="{{ route('checkout.qr', ['d' => strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']), 'size' => 300]) }}" alt="QR Code PIX" class="w-48 h-48 object-contain">
                                @endif
                            </div>
                        </div>
                        @if($pixCode)
                            <div class="flex items-center gap-2">
                                <input type="text" id="pix-code-input" value="{{ $pixCode }}" readonly class="flex-1 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-amber-500 text-xs font-mono">
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('pix-code-input').value); this.textContent='{{ __('events::messages.copied') ?? 'Copiado!' }}'; setTimeout(() => this.textContent='{{ __('events::messages.copy') ?? 'Copiar' }}', 2000)" class="px-4 py-2 bg-amber-500 text-slate-950 font-semibold rounded-lg hover:bg-amber-400 transition-colors">{{ __('events::messages.copy') ?? 'Copiar' }}</button>
                            </div>
                        @endif
                        <p class="text-xs text-slate-500 mt-4 animate-pulse">{{ __('events::messages.payment_auto_update') ?? 'Esta página será atualizada quando o pagamento for confirmado.' }}</p>
                    </div>
                @endif
                <a href="{{ $paymentUrl }}"
                   class="block w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-center rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                    {{ $hasPixData ? (__('events::messages.see_payment_page') ?? 'Ver página de pagamento') : (__('events::messages.go_to_payment') ?? 'Ir para o pagamento') }}
                </a>
            @elseif($gateways->isNotEmpty())
                {{-- Escolher gateway e iniciar pagamento --}}
                <form action="{{ route('events.public.payment.start', ['uuid' => $registration->uuid]) }}" method="POST" id="pagar-form" class="space-y-6">
                    @csrf
                    <h3 class="font-bold text-white mb-4">{{ __('events::messages.payment_method') ?? 'Forma de pagamento' }}</h3>
                    <div class="space-y-3">
                        @foreach($gateways as $index => $gateway)
                            @if($gateway->isConfigured())
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-slate-700 hover:border-amber-500 cursor-pointer transition-colors">
                                    <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}" data-name="{{ $gateway->name }}" class="sr-only peer pagar-gateway-input" required {{ $index === 0 ? 'checked' : '' }}>
                                    @if($gateway->logo_url)
                                        <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->display_name }}" class="w-10 h-10 object-contain">
                                    @else
                                        <x-icon name="credit-card" style="duotone" class="w-8 h-8 text-slate-400" />
                                    @endif
                                    <span class="font-medium text-white">{{ $gateway->display_name ?? $gateway->name }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    <input type="hidden" name="payment_method" id="pagar_method_input" value="">
                    <input type="hidden" name="brick_payload" id="pagar_brick_payload" value="">
                    <div id="pagar_brick_wrapper" class="pt-4 border-t border-slate-700" style="display: none;">
                        <div id="pagar_brick_container" class="w-full" style="min-height: 360px;"></div>
                    </div>
                    <button type="submit" id="pagar_submit_btn" class="block w-full py-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-center rounded-xl transition-all"
                            @click="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: '{{ __('events::messages.processing_registration') ?? 'Processando...' }}' }))">
                        {{ __('events::messages.finish_and_pay') ?? 'Finalizar e pagar' }}
                    </button>
                </form>
            @else
                <p class="text-slate-400 text-center">{{ __('events::messages.no_gateways_configured') ?? 'Nenhuma forma de pagamento disponível no momento. Entre em contato com o organizador.' }}</p>
                <a href="{{ route('events.public.show', $registration->event->slug) }}" class="block w-full py-4 mt-6 bg-slate-700 hover:bg-slate-600 text-white font-bold text-center rounded-xl transition-colors">
                    {{ __('events::messages.back_to_event') ?? 'Voltar ao evento' }}
                </a>
            @endif
        </div>

        <!-- Timeout Overlay -->
        <div x-show="expired" x-transition.opacity
             class="absolute inset-0 bg-slate-950/95 backdrop-blur-sm z-50 flex items-center justify-center text-center p-8"
             style="display: none;">
            <div>
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="circle-exclamation" style="duotone" class="w-10 h-10 text-red-500" />
                </div>
                <h3 class="text-3xl font-bold text-white mb-2">{{ __('events::messages.time_expired') ?? 'Tempo Esgotado' }}</h3>
                <p class="text-slate-400 mb-8 max-w-md mx-auto">{{ __('events::messages.time_expired_msg') ?? 'O tempo para concluir a compra acabou. Tente novamente.' }}</p>
                <a href="{{ route('events.public.show', $registration->event->slug) }}" class="inline-block px-8 py-3 bg-amber-500 text-slate-950 font-bold rounded-lg hover:bg-amber-400 transition-colors">
                    {{ __('events::messages.back_to_event') ?? 'Voltar para o Evento' }}
                </a>
            </div>
        </div>
    </div>
</div>

@if(!$pendingPayment && $gateways->isNotEmpty())
@php
    $mpGateway = $gateways->firstWhere('name', 'mercado_pago');
    $mpPublicKey = $mpGateway ? ($mpGateway->getDecryptedCredentials()['public_key'] ?? null) : null;
@endphp
@if($mpPublicKey)
@push('scripts')
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pagar-form');
    const gatewayInputs = document.querySelectorAll('.pagar-gateway-input');
    const brickWrapper = document.getElementById('pagar_brick_wrapper');
    const brickContainer = document.getElementById('pagar_brick_container');
    const submitBtn = document.getElementById('pagar_submit_btn');
    const brickPayloadInput = document.getElementById('pagar_brick_payload');
    const payerEmail = '{{ $registration->participants->first()?->email ?? '' }}';

    let paymentBrickController = null;
    let mp = null;
    let bricksBuilder = null;
    try {
        mp = new MercadoPago('{{ $mpPublicKey }}', { locale: 'pt-BR' });
        bricksBuilder = mp.bricks();
    } catch (e) { console.error('Mercado Pago init error:', e); }

    async function initBrick() {
        if (!bricksBuilder || !brickContainer) return;
        if (paymentBrickController) {
            await paymentBrickController.unmount();
            paymentBrickController = null;
        }
        brickWrapper.style.display = 'block';
        brickContainer.innerHTML = '<div class="flex items-center justify-center p-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div></div>';
        await new Promise(r => setTimeout(r, 400));
        brickContainer.innerHTML = '';
        const amount = {{ (float) $registration->total_amount }};
        const settings = {
            initialization: { amount: amount, payer: { email: payerEmail || 'visitante@email.com', entityType: 'individual' } },
            customization: { paymentMethods: { bankTransfer: 'all', ticket: 'all', creditCard: 'all', debitCard: 'all' }, visual: { style: { theme: 'default' } } },
            callbacks: {
                onReady: () => {},
                onSubmit: ({ formData }) => {
                    brickPayloadInput.value = JSON.stringify({ ...formData });
                    form.submit();
                    return Promise.resolve();
                },
                onError: (err) => console.error('Brick error:', err),
            },
        };
        try {
            paymentBrickController = await bricksBuilder.create('payment', 'pagar_brick_container', settings);
        } catch (e) {
            brickContainer.innerHTML = '<p class="text-red-400 text-sm">' + (e.message || 'Erro ao carregar pagamento.') + '</p>';
        }
    }

    function updatePagarInterface() {
        const selected = document.querySelector('.pagar-gateway-input:checked');
        if (!selected) {
            if (brickWrapper) brickWrapper.style.display = 'none';
            if (submitBtn) submitBtn.style.display = 'block';
            return;
        }
        if (selected.dataset.name === 'mercado_pago') {
            if (submitBtn) submitBtn.style.display = 'none';
            initBrick();
        } else {
            if (brickWrapper) brickWrapper.style.display = 'none';
            if (submitBtn) submitBtn.style.display = 'block';
            if (paymentBrickController) {
                paymentBrickController.unmount().then(() => { paymentBrickController = null; }).catch(() => {});
            }
        }
    }
    gatewayInputs.forEach(el => el.addEventListener('change', updatePagarInterface));
    updatePagarInterface();
});
</script>
@endpush
@endif
@endif

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutTimer', (isoExpiration) => ({
            expiration: new Date(isoExpiration).getTime(),
            now: new Date().getTime(),
            timeLeft: '15:00',
            expired: false,
            timer: null,
            init() {
                this.updateTimer();
                this.timer = setInterval(() => this.updateTimer(), 1000);
            },
            updateTimer() {
                this.now = new Date().getTime();
                const distance = this.expiration - this.now;
                if (distance < 0) {
                    this.expired = true;
                    this.timeLeft = '00:00';
                    clearInterval(this.timer);
                    return;
                }
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                this.timeLeft = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }
        }));
    });
</script>
@endpush
@endsection
