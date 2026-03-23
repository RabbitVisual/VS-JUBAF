@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.change_payment_title') . ' - ' . __('memberpanel::messages.member_panel'))
@section('page-title', __('events::messages.change_payment_page_title'))

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6 sm:space-y-8">
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.my-registrations') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.my_registrations') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium">{{ __('events::messages.change_payment_breadcrumb') }}</span>
        </nav>

        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 p-4 sm:p-6 md:p-8">
        <form action="{{ route('memberpanel.events.registration.update-gateway', $registration) }}" method="POST" id="donation-form" class="space-y-8">
            @csrf

            <!-- Payment Info Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('events::messages.total_value') }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('events::messages.participants') }}</p>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('events::messages.person_count', ['count' => $registration->participants->count()]) }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('events::messages.registration_date') }}</p>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $registration->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Payment Gateway Selection -->
            <div class="space-y-4">
                <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                    {{ __('events::messages.new_payment_method') }}
                </label>

                <div class="bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($gateways as $gateway)
                        <label class="group relative flex items-center p-4 rounded-xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-md outline-none focus-within:ring-4 ring-indigo-500/20
                            {{ (old('payment_gateway_id') ?? ($registration->latestPayment->payment_gateway_id ?? '')) == $gateway->id ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">

                            <input type="radio" name="payment_gateway_id"
                                   value="{{ $gateway->id }}"
                                   data-name="{{ $gateway->name }}"
                                   {{ (old('payment_gateway_id') ?? ($registration->latestPayment->payment_gateway_id ?? '')) == $gateway->id ? 'checked' : '' }}
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
                                    {{ $gateway->description ?? __('events::messages.secure_payment') }}
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
                    class="w-full py-4 sm:py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-base sm:text-lg shadow-xl shadow-indigo-500/20 transition-all duration-200 flex items-center justify-center gap-3 touch-manipulation active:scale-[0.98]">
                    <x-icon name="lock" style="duotone" class="w-6 h-6" />
                    {{ __('events::messages.update_and_pay') }}
                </button>
                <div class="flex items-center justify-center gap-6 mt-6">
                    <a href="{{ route('memberpanel.events.my-registrations') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">{{ __('memberpanel::messages.cancel') }}</a>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <x-icon name="shield-check" style="duotone" class="w-4 h-4 text-green-500" />
                        {{ __('events::messages.secure_environment') }}
                    </p>
                </div>
            </div>
        </form>
        </div>
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

            const amount = {{ $registration->total_amount }};

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
                        <span class="font-medium">@lang('events::messages.payment_load_error')</span>
                        <br>@lang('events::messages.payment_load_error_detail')
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
