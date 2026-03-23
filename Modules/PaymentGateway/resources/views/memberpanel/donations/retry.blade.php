@extends('memberpanel::components.layouts.master')

@section('page-title', 'Alterar Forma de Pagamento')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="space-y-8 pb-12">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
        <!-- Decorative Mesh Gradient Background -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-amber-600 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-10 w-80 h-80 bg-orange-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-yellow-500 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-8 z-10">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-amber-200/80 font-bold uppercase tracking-widest text-xs">Pendente</p>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Alterar Forma de Pagamento
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    Sua doação de <strong>R$ {{ number_format($payment->amount, 2, ',', '.') }}</strong> ainda está aguardando pagamento. Escolha uma nova forma de pagamento abaixo.
                </p>
            </div>
        </div>
    </div>

    <!-- Donation Form -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8">
        <form action="{{ route('memberpanel.donations.update-gateway', $payment->transaction_id) }}" method="POST" id="donation-form" class="space-y-8">
            @csrf

            <!-- Payment Info Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Valor</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Descrição</p>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $payment->description ?: 'Doação' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Data Inicial</p>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <input type="hidden" name="amount" value="{{ $payment->amount }}">

            <!-- Payment Gateway Selection -->
            <div class="space-y-4">
                <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                    Nova Forma de Pagamento
                </label>

                <div class="bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($gateways as $gateway)
                        <label class="group relative flex items-center p-4 rounded-xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-md outline-none focus-within:ring-4 ring-indigo-500/20
                            {{ (old('payment_gateway_id') ?? $payment->payment_gateway_id) == $gateway->id ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">

                            <input type="radio" name="payment_gateway_id"
                                   value="{{ $gateway->id }}"
                                   data-name="{{ $gateway->name }}"
                                   {{ (old('payment_gateway_id') ?? $payment->payment_gateway_id) == $gateway->id ? 'checked' : '' }}
                                   class="sr-only peer payment-gateway-input">

                            <div class="w-12 h-12 rounded-lg bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center mr-4 p-2">
                                @if($gateway->logo_url)
                                    <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->display_name ?? $gateway->name }}" class="h-full w-auto object-contain">
                                @else
                                    <x-icon name="{{ $gateway->icon ?? 'credit-card' }}" style="duotone" class="w-6 h-6 text-gray-400" />
                                @endif
                            </div>

                            <div class="flex-1">
                                <span class="block font-bold text-gray-900 dark:text-white text-lg">
                                    {{ $gateway->display_name ?? $gateway->name }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $gateway->description ?? 'Pagamento seguro' }}
                                </span>
                            </div>

                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 transition-colors flex items-center justify-center">
                                <x-icon name="check" style="solid" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" />
                            </div>
                        </label>
                    @endforeach
                    </div>
                </div>
            </div>

            <input type="hidden" name="brick_payload" id="brick_payload">

            @error('payment_gateway_id')
                <div class="p-4 mb-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm font-bold flex items-center gap-2">
                    <x-icon name="exclamation-circle" style="duotone" class="w-5 h-5 flex-shrink-0" />
                    {{ $message }}
                </div>
            @enderror

            <!-- Payment Brick Wrapper -->
            <div id="paymentBrick_wrapper" class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-6" style="display: none;">
                <div id="paymentBrick_container" class="w-full max-w-full overflow-hidden" style="min-height: 480px;"></div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 submit-button-container">
                <button type="submit"
                    class="w-full py-5 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3">
                    <x-icon name="lock" style="duotone" class="w-6 h-6" />
                    Atualizar e Pagar
                </button>
                <div class="flex items-center justify-center gap-6 mt-6">
                    <a href="{{ route('memberpanel.donations.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancelar</a>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <x-icon name="shield-check" style="duotone" class="w-4 h-4 text-green-500" />
                        Ambiente Seguro
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gatewayInputs = document.querySelectorAll('.payment-gateway-input');
        const brickPayloadInput = document.getElementById('brick_payload');
        const form = document.getElementById('donation-form');
        const paymentBrickContainer = document.getElementById('paymentBrick_container');
        const submitButtonContainer = document.querySelector('.submit-button-container');
        const payerEmailInput = { value: '{{ Auth::user()->email }}' };

        let mp = null;
        let bricksBuilder = null;
        let paymentBrickController = null;

        // Initialize MP if configured
        @php
            $mpGateway = $gateways->firstWhere('name', 'mercado_pago');
            $mpPublicKey = $mpGateway ? $mpGateway->getDecryptedCredentials()['public_key'] ?? null : null;
            $supportedMethods = $mpGateway ? $mpGateway->supported_methods : [];

            // Bricks Config
            $mpBrickConfig = ['maxInstallments' => 12];
            if (!empty($supportedMethods)) {
                if (in_array('pix', $supportedMethods)) $mpBrickConfig['bankTransfer'] = 'all';
                if (in_array('boleto', $supportedMethods)) $mpBrickConfig['ticket'] = 'all';
                if (in_array('credit_card', $supportedMethods)) {
                    $mpBrickConfig['creditCard'] = 'all';
                    $mpBrickConfig['debitCard'] = 'all';
                }
            } else {
                 $mpBrickConfig['bankTransfer'] = 'all';
                 $mpBrickConfig['ticket'] = 'all';
                 $mpBrickConfig['creditCard'] = 'all';
                 $mpBrickConfig['debitCard'] = 'all';
            }
        @endphp

        @if($mpPublicKey)
            try {
                mp = new MercadoPago('{{ $mpPublicKey }}', {
                    locale: 'pt-BR'
                });
                bricksBuilder = mp.bricks();
            } catch (e) {
                console.error('Erro ao inicializar Mercado Pago:', e);
            }
        @endif

        async function initPaymentBrick() {
            if (!bricksBuilder || !mp) return;

            const amount = {{ $payment->amount }};

            if (paymentBrickController) {
                await paymentBrickController.unmount();
                paymentBrickController = null;
            }

            // Mostrar container e spinner
            const wrapper = document.getElementById('paymentBrick_wrapper');
            if (wrapper) wrapper.style.display = 'block';

            paymentBrickContainer.style.display = 'block';
            paymentBrickContainer.innerHTML = '<div class="flex items-center justify-center p-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';

            // Delay e render lock
            await new Promise(resolve => setTimeout(resolve, 500));
            await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));

            paymentBrickContainer.innerHTML = '';

            const settings = {
                initialization: {
                    amount: amount,
                    payer: {
                        email: payerEmailInput.value,
                        entityType: 'individual',
                    },
                },
                customization: {
                    paymentMethods: {!! json_encode($mpBrickConfig) !!},
                    visual: {
                        style: {
                            theme: 'default',
                        }
                    }
                },
                callbacks: {
                    onReady: () => {},
                    onSubmit: ({ selectedPaymentMethod, formData }) => {
                        return new Promise((resolve, reject) => {
                            const payload = { ...formData, payment_method_id: selectedPaymentMethod };
                            brickPayloadInput.value = JSON.stringify(payload);
                            form.submit();
                            resolve();
                        });
                    },
                    onError: (error) => {
                        console.error('Payment Brick error:', error);
                    },
                },
            };

            try {
                paymentBrickController = await bricksBuilder.create('payment', 'paymentBrick_container', settings);
                console.log('Brick Rendered');
            } catch (e) {
                console.error('Failed to create Payment Brick:', e);
                paymentBrickContainer.innerHTML = `
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Erro ao carregar pagamento!</span>
                        <br>Não foi possível inicializar o módulo do Mercado Pago.
                    </div>
                `;
            }
        }

        function updateInterface() {
            const selectedGateway = document.querySelector('.payment-gateway-input:checked');

            if (paymentBrickContainer) paymentBrickContainer.style.display = 'none';
            if (submitButtonContainer) submitButtonContainer.style.display = 'block';

            if (!selectedGateway) return;

            const gatewayName = selectedGateway.dataset.name;

            if (gatewayName === 'mercado_pago') {
                if (submitButtonContainer) submitButtonContainer.style.display = 'none';
                initPaymentBrick();
            } else {
                if (submitButtonContainer) submitButtonContainer.style.display = 'block';
                const wrapper = document.getElementById('paymentBrick_wrapper');
                if (wrapper) wrapper.style.display = 'none';
                if (paymentBrickController) {
                    paymentBrickController.unmount()
                        .then(() => { paymentBrickController = null; })
                        .catch((err) => console.error('Error unmounting brick', err));
                }
            }
        }

        gatewayInputs.forEach(input => {
            input.addEventListener('change', updateInterface);
        });

        // Initialize
        updateInterface();
    });
</script>
@endpush
@endsection
