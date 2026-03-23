@extends('memberpanel::components.layouts.master')

@section('page-title', 'Fazer Doação')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="space-y-8 pb-12" data-tour="donations-area">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
        <!-- Decorative Mesh Gradient Background -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-600 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-10 w-80 h-80 bg-pink-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-1/2 w-64 h-64 bg-indigo-500 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-8 z-10">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-purple-200/80 font-bold uppercase tracking-widest text-xs">Contribua</p>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Fazer Doação
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    "Cada um dê conforme determinou em seu coração, não com pesar ou por obrigação, pois Deus ama quem dá com alegria." — 2 Coríntios 9:7
                </p>
            </div>
        </div>
    </div>

    <!-- Donation Form -->
    @if($gateways->isEmpty())
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-3xl shadow-xl p-12 text-center backdrop-blur-sm">
            <div class="mb-6">
                <div class="w-20 h-20 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center mx-auto shadow-inner">
                    <x-icon name="exclamation-circle" style="duotone" class="w-10 h-10 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
            <h2 class="text-2xl font-black text-amber-900 dark:text-amber-200 mb-2">Sistema Indisponível</h2>
            <p class="text-amber-800 dark:text-amber-300 mb-8 max-w-md mx-auto leading-relaxed">
                No momento, não há gateways de pagamento configurados. Por favor, entre em contato com a administração.
            </p>
            <a href="{{ route('memberpanel.dashboard') }}"
               class="inline-flex items-center px-8 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <x-icon name="arrow-left" style="duotone" class="w-5 h-5 mr-2" />
                Voltar ao Dashboard
            </a>
        </div>
    @else
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8">
            <form action="{{ route('memberpanel.donations.store') }}" method="POST" id="donation-form" class="space-y-8" data-tour="donations-create-form">
                @csrf

                <!-- Donation Type -->
                <div class="space-y-4">
                    <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                        Tipo de Contribuição
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Donation -->
                        <label class="group relative flex flex-col p-6 rounded-2xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-lg hover:-translate-y-1 outline-none focus-within:ring-4 ring-blue-500/20
                            {{ old('payment_type', $campaign ? 'campaign' : 'donation') === 'donation' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700' }}">
                            <input type="radio" name="payment_type" value="donation"
                                   {{ old('payment_type', $campaign ? 'campaign' : 'donation') === 'donation' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-4 text-blue-600 dark:text-blue-400 peer-checked:bg-blue-500 peer-checked:text-white transition-colors shadow-sm">
                                <x-icon name="heart" style="duotone" class="w-6 h-6" />
                            </div>
                            <span class="block text-lg font-black text-gray-900 dark:text-white mb-1">Doação</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 font-medium group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Contribuição geral para igreja</span>
                            <div class="absolute top-6 right-6 w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-colors flex items-center justify-center">
                                <x-icon name="check" style="solid" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" />
                            </div>
                        </label>

                        <!-- Offering -->
                        <label class="group relative flex flex-col p-6 rounded-2xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-lg hover:-translate-y-1 outline-none focus-within:ring-4 ring-emerald-500/20
                            {{ old('payment_type') === 'offering' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700' }}">
                            <input type="radio" name="payment_type" value="offering"
                                   {{ old('payment_type') === 'offering' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-4 text-emerald-600 dark:text-emerald-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-colors shadow-sm">
                                <x-icon name="money-bill-wave" style="duotone" class="w-6 h-6" />
                            </div>
                            <span class="block text-lg font-black text-gray-900 dark:text-white mb-1">Oferta</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 font-medium group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Sua oferta regular de culto</span>
                            <div class="absolute top-6 right-6 w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 transition-colors flex items-center justify-center">
                                <x-icon name="check" style="solid" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" />
                            </div>
                        </label>

                        <!-- Ministry -->
                        <label class="group relative flex flex-col p-6 rounded-2xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-lg hover:-translate-y-1 outline-none focus-within:ring-4 ring-purple-500/20
                            {{ old('payment_type') === 'ministry_donation' ? 'border-purple-500 bg-purple-50/50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700' }}">
                            <input type="radio" name="payment_type" value="ministry_donation"
                                   {{ old('payment_type') === 'ministry_donation' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center mb-4 text-purple-600 dark:text-purple-400 peer-checked:bg-purple-500 peer-checked:text-white transition-colors shadow-sm">
                                <x-icon name="users" style="duotone" class="w-6 h-6" />
                            </div>
                            <span class="block text-lg font-black text-gray-900 dark:text-white mb-1">Ministério</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 font-medium group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Apoie um ministério específico</span>
                            <div class="absolute top-6 right-6 w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-500 transition-colors flex items-center justify-center">
                                <x-icon name="check" style="solid" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" />
                            </div>
                        </label>

                        <!-- Campaign -->
                        <label class="group relative flex flex-col p-6 rounded-2xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-lg hover:-translate-y-1 outline-none focus-within:ring-4 ring-orange-500/20
                            {{ old('payment_type', $campaign ? 'campaign' : '') === 'campaign' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-700' }}">
                            <input type="radio" name="payment_type" value="campaign"
                                   {{ old('payment_type', $campaign ? 'campaign' : '') === 'campaign' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center mb-4 text-orange-600 dark:text-orange-400 peer-checked:bg-orange-500 peer-checked:text-white transition-colors shadow-sm">
                                <x-icon name="bullhorn" style="duotone" class="w-6 h-6" />
                            </div>
                            <span class="block text-lg font-black text-gray-900 dark:text-white mb-1">Campanha</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400 font-medium group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Doe para projetos especiais</span>
                            <div class="absolute top-6 right-6 w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 peer-checked:border-orange-500 peer-checked:bg-orange-500 transition-colors flex items-center justify-center">
                                <x-icon name="check" style="solid" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" />
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Conditional Fields Container -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Ministry Selection -->
                    <div id="ministry-select-container" class="space-y-2 hidden">
                        <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                            Selecione o Ministério
                        </label>
                        <select name="payable_id_ministry" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500/20 transition-all font-medium py-3">
                            <option value="" class="dark:bg-gray-800">Selecione um ministério...</option>
                            @foreach($ministries as $ministry)
                                <option value="{{ $ministry->id }}" class="dark:bg-gray-800" {{ old('payable_id_ministry') == $ministry->id ? 'selected' : '' }}>
                                    {{ $ministry->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Campaign Selection -->
                    <div id="campaign-select-container" class="space-y-2 {{ old('payment_type', $campaign ? 'campaign' : '') === 'campaign' ? '' : 'hidden' }}">
                        <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                            Selecione a Campanha
                        </label>
                        <select name="payable_id_campaign" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-orange-500 focus:ring-orange-500/20 transition-all font-medium py-3">
                            <option value="" class="dark:bg-gray-800">Selecione uma campanha...</option>
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}" class="dark:bg-gray-800"
                                    {{ old('payable_id_campaign', $campaign ? $campaign->id : '') == $camp->id ? 'selected' : '' }}>
                                    {{ $camp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Value and Description -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                            Valor da Doação (R$)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 font-bold">R$</span>
                            </div>
                            <input type="number" name="amount" step="0.01" min="1" placeholder="0,00" required
                                   value="{{ old('amount', request('amount')) }}"
                                   class="pl-12 w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 transition-all font-bold text-lg py-3">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                            Descrição (Opcional)
                        </label>
                        <input type="text" name="description" placeholder="Ex: Oferta de gratidão"
                               value="{{ old('description', request('description')) }}"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 transition-all font-medium py-3">
                    </div>
                </div>

                <!-- Manual Document (CPF/CNPJ) Field -->
                <div class="space-y-2">
                    <label for="payer_document" class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                         CPF/CNPJ <span class="text-xs font-normal normal-case text-gray-400">(Preenchido automaticamente)</span>
                    </label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                             <x-icon name="id-card" style="duotone" class="w-5 h-5 text-gray-400" />
                         </div>
                        <input type="text" name="payer_document" id="payer_document"
                            value="{{ old('payer_document', optional(auth()->user())->cpf ?? '') }}"
                            class="pl-12 w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 focus:border-blue-500 focus:ring-blue-500/20 transition-all font-bold text-lg py-3 cursor-not-allowed"
                            placeholder="000.000.000-00"
                            readonly
                            required>
                    </div>
                    @error('payer_document')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Payment Gateway Selection -->
                <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <label class="block text-sm font-black text-gray-400 uppercase tracking-widest">
                        Forma de Pagamento
                    </label>

                    <div class="bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($gateways as $gateway)
                            <label class="group relative flex items-center p-4 rounded-xl cursor-pointer bg-white dark:bg-gray-800 border-2 transition-all duration-200 hover:shadow-md outline-none focus-within:ring-4 ring-indigo-500/20
                                {{ old('payment_gateway_id') == $gateway->id ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">

                                <input type="radio" name="payment_gateway_id"
                                       value="{{ $gateway->id }}"
                                       data-name="{{ $gateway->name }}"
                                       {{ old('payment_gateway_id') == $gateway->id ? 'checked' : '' }}
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
                        class="w-full py-5 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3"
                        data-tour="donations-submit">
                        <x-icon name="lock" style="duotone" class="w-6 h-6" />
                        Confirmar Doação
                    </button>
                    <p class="text-center text-xs font-bold text-gray-400 mt-4 uppercase tracking-widest flex items-center justify-center gap-2">
                        <x-icon name="shield-check" style="duotone" class="w-4 h-4 text-green-500" />
                        Ambiente Seguro
                    </p>
                </div>
            </form>
        </div>
    @endif
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const donationTypes = document.querySelectorAll('input[name="payment_type"]');
        const ministrySelect = document.getElementById('ministry-select-container');
        const campaignSelect = document.getElementById('campaign-select-container');

        function updateFields() {
            const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;

            if(ministrySelect) ministrySelect.classList.add('hidden');
            if(campaignSelect) campaignSelect.classList.add('hidden');

            if (selectedType === 'ministry_donation' && ministrySelect) {
                ministrySelect.classList.remove('hidden');
            } else if (selectedType === 'campaign' && campaignSelect) {
                campaignSelect.classList.remove('hidden');
            }
        }

        if (donationTypes.length > 0) {
            donationTypes.forEach(radio => {
                radio.addEventListener('change', updateFields);
            });
            updateFields();
        }

        // Brick Variables
        const gatewayInputs = document.querySelectorAll('.payment-gateway-input');
        const amountInput = document.querySelector('input[name="amount"]');
        const brickPayloadInput = document.getElementById('brick_payload');
        const form = document.getElementById('donation-form');
        const paymentBrickContainer = document.getElementById('paymentBrick_container');
        const submitButtonContainer = document.querySelector('.submit-button-container');
        const payerEmailInput = { value: '{{ Auth::user()->email }}' }; // Use auth user email directly

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

            const amount = parseFloat(amountInput.value) || 0;
            if (amount <= 0) {
                paymentBrickContainer.style.display = 'none';
                return;
            }

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

            if (paymentBrickController) {
                await paymentBrickController.unmount();
                paymentBrickController = null;
            }

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
            } finally {
                paymentBrickContainer.dataset.loading = 'false';
            }
        }

        function updateInterface() {
            const selectedGateway = document.querySelector('.payment-gateway-input:checked');

            // Default
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

        if(amountInput) {
            amountInput.addEventListener('change', () => {
                const selectedGateway = document.querySelector('.payment-gateway-input:checked');
                if (selectedGateway && selectedGateway.dataset.name === 'mercado_pago') {
                    initPaymentBrick();
                }
            });
        }

        // Initialize
        updateInterface();
    });
</script>
@endpush
@endsection
