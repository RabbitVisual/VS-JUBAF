@extends('homepage::components.layouts.master')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full blur-[100px] opacity-20"></div>
            <div class="absolute top-1/2 -left-40 w-80 h-80 bg-purple-500 rounded-full blur-[100px] opacity-20"></div>
        </div>

        <div class="max-w-3xl mx-auto relative z-10">
            <!-- Header -->
            <div class="text-center mb-10">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-widest mb-4">
                    Contribua com a Obra
                </span>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Doe com segurança</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-2">
                    "Cada um contribua segundo propôs no seu coração... porque Deus ama ao que dá com alegria."
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 max-w-xl mx-auto">
                    Doação rápida e segura. Não é necessário criar conta ou fazer login.
                </p>
            </div>

            <!-- Donation Form -->
            @if($gateways->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <x-icon name="triangle-exclamation" style="duotone" class="w-16 h-16 text-yellow-600 dark:text-yellow-400 mx-auto" />
                    </div>
                    <h2 class="text-2xl font-bold text-yellow-900 dark:text-yellow-200 mb-2">Sistema de Doações Indisponível</h2>
                    <p class="text-yellow-800 dark:text-yellow-300 mb-4">
                        No momento, não há gateways de pagamento configurados. Por favor, entre em contato com a administração.
                    </p>
                    <a href="{{ route('homepage.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        <x-icon name="house" style="duotone" class="w-5 h-5 mr-2" />
                        Voltar ao Início
                    </a>
                </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <form action="{{ route('donation.store') }}" method="POST" id="donation-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="brick_payload" id="brick_payload">
                    <script src="https://sdk.mercadopago.com/js/v2"></script>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Tipo de Doação
                        </label>


                            <!-- Donation Types with Icons -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex items-center p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 donation-type-option group">
                                    <input type="radio" name="donation_type" value="general"
                                        {{ old('donation_type', $campaign ? 'campaign' : 'general') === 'general' ? 'checked' : '' }}
                                        class="sr-only peer donation-type-input">
                                    <div class="mr-4 w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <x-icon name="hand-holding-heart" style="duotone" class="w-6 h-6" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-900 dark:text-white">Doação Geral</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            Contribua para a igreja em geral
                                        </div>
                                    </div>
                                    <div class="ml-4 w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center transition-colors">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full hidden peer-checked:block"></div>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl border-2 border-indigo-600 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 donation-type-option group">
                                    <input type="radio" name="donation_type" value="ministry"
                                        {{ old('donation_type') === 'ministry' ? 'checked' : '' }}
                                        class="sr-only peer donation-type-input">
                                    <div class="mr-4 w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <x-icon name="church" style="duotone" class="w-6 h-6" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-900 dark:text-white">Ministério</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            Oferte para uma área específica
                                        </div>
                                    </div>
                                    <div class="ml-4 w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center transition-colors">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full hidden peer-checked:block"></div>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl border-2 border-indigo-600 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 donation-type-option group">
                                    <input type="radio" name="donation_type" value="campaign"
                                        {{ old('donation_type', $campaign ? 'campaign' : '') === 'campaign' ? 'checked' : '' }}
                                        class="sr-only peer donation-type-input">
                                    <div class="mr-4 w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <x-icon name="bullhorn" style="duotone" class="w-6 h-6" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-900 dark:text-white">Campanha</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            Apoie nossos projetos especiais
                                        </div>
                                    </div>
                                    <div class="ml-4 w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center transition-colors">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full hidden peer-checked:block"></div>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl border-2 border-indigo-600 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>
                            </div>

                    <!-- Ministry Selection (hidden by default) -->
                    <div id="ministry-selection" class="hidden mt-6">
                        <label for="ministry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selecione o Ministério <span class="text-red-500">*</span>
                        </label>
                        <select name="ministry_id" id="ministry_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Selecione um ministério...</option>
                            @foreach ($ministries as $ministry)
                                <option value="{{ $ministry->id }}"
                                    {{ old('ministry_id') == $ministry->id ? 'selected' : '' }}>
                                    {{ $ministry->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ministry_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Selection (hidden by default) -->
                    <div id="campaign-selection" class="hidden mt-6">
                        <label for="campaign_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selecione a Campanha <span class="text-red-500">*</span>
                        </label>
                        <select name="campaign_id" id="campaign_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Selecione uma campanha...</option>
                            @foreach ($campaigns as $camp)
                                <option value="{{ $camp->id }}"
                                    {{ old('campaign_id', $campaign && $campaign->id == $camp->id ? $camp->id : '') == $camp->id ? 'selected' : '' }}>
                                    {{ $camp->name }}
                                    @if ($camp->target_amount)
                                        - Meta: R$ {{ number_format($camp->target_amount, 2, ',', '.') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('campaign_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @if ($campaign)
                            <!-- Campaign Info Card if pre-selected via URL -->
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start">
                                    <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 shrink-0" />
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">
                                            {{ $campaign->name }}</h4>
                                        @if ($campaign->description)
                                            <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">
                                                {{ Str::limit($campaign->description, 150) }}</p>
                                        @endif
                                        @if ($campaign->target_amount)
                                            <div class="flex items-center justify-between text-xs text-blue-600 dark:text-blue-400 mb-2">
                                                <span>Arrecadado: R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                                                <span>Meta: R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</span>
                                            </div>
                                            <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                                <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all"
                                                    style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Valor da Doação (R$) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">R$</span>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                                value="{{ old('amount') }}"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0,00">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button type="button" class="quick-amount px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-amount="10">R$ 10</button>
                            <button type="button" class="quick-amount px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-amount="25">R$ 25</button>
                            <button type="button" class="quick-amount px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-amount="50">R$ 50</button>
                            <button type="button" class="quick-amount px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-amount="100">R$ 100</button>
                            <button type="button" class="quick-amount px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-amount="500">R$ 500</button>
                        </div>
                    </div>

                    <!-- Payer Information -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-bold">
                                <x-icon name="user" style="duotone" class="w-4 h-4" />
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Seus Dados</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="payer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome Completo
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="user" style="solid" class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <input type="text" name="payer_name" id="payer_name"
                                        value="{{ old('payer_name', auth()->user()->name ?? '') }}"
                                        class="pl-10 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Seu nome (opcional)">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="payer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="envelope" style="solid" class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <input type="email" name="payer_email" id="payer_email" required
                                        value="{{ old('payer_email', auth()->user()->email ?? '') }}"
                                        class="pl-10 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="seu@email.com">
                                </div>
                            </div>

                            <!-- CPF -->
                            <div>
                                <label for="payer_document" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CPF <span class="text-xs text-gray-500 font-normal">(Necessário para Pix)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="id-card" style="solid" class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <input type="text" name="payer_document" id="payer_document"
                                        value="{{ old('payer_document', auth()->user()->cpf ?? '') }}"
                                        class="pl-10 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="000.000.000-00"
                                        x-mask="999.999.999-99">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Gateway -->
                    <div class="mt-8">
                        <label for="payment_gateway_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Forma de Pagamento <span class="text-red-500">*</span>
                        </label>
                        @if($gateways->isEmpty())
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Nenhum gateway de pagamento configurado.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($gateways as $gateway)
                                    <label class="group relative flex items-center p-4 border-2 border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-300 gateway-option {{ $gateway->isConfigured() ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}"
                                            data-name="{{ $gateway->name }}"
                                            {{ old('payment_gateway_id') == $gateway->id ? 'checked' : ($loop->first ? 'checked' : '') }}
                                            {{ $gateway->isConfigured() ? '' : 'disabled' }}
                                            class="sr-only peer gateway-input" required>

                                        <!-- Gateway Icon -->
                                        <div class="shrink-0 mr-4 w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-600 shadow-sm flex items-center justify-center p-2 transition-transform duration-300 group-hover:scale-105">
                                            @if($gateway->logo_url)
                                                <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="max-w-full max-h-full object-contain">
                                            @elseif($gateway->name == 'mercado_pago')
                                                <img src="https://logospng.org/wp-content/uploads/mercado-pago.png" alt="Mercado Pago" class="max-w-full max-h-full">
                                            @elseif($gateway->name == 'stripe')
                                                <x-icon name="stripe" style="brands" class="w-6 h-6 text-indigo-600" />
                                            @else
                                                <x-icon name="credit-card" style="duotone" class="w-6 h-6 text-slate-500" />
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2 whitespace-nowrap">
                                                <span class="truncate">{{ $gateway->display_name }}</span>
                                                @if(!$gateway->isConfigured())
                                                    <span class="text-[10px] uppercase bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded font-bold tracking-wider">Configurar</span>
                                                @endif
                                            </div>
                                            @if ($gateway->description)
                                                <div class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-tight">
                                                    {{ $gateway->description }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="shrink-0 ml-4 w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center transition-colors">
                                            <div class="w-2.5 h-2.5 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                        <div class="absolute inset-0 rounded-2xl border-2 border-indigo-600 opacity-0 peer-checked:opacity-100 pointer-events-none transition-all duration-300"></div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('payment_gateway_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>




                    <!-- Payment Brick Wrapper -->
                    <div id="paymentBrick_wrapper" class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800" style="display: none;">
                        <div id="paymentBrick_container" class="w-full max-w-full overflow-hidden" style="min-height: 480px;"></div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrição (opcional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Deixe uma mensagem ou observação sobre sua doação...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Error Messages -->
                    @if ($errors->has('error'))
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <p class="text-sm text-red-800 dark:text-red-200">{{ $errors->first('error') }}</p>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-4 submit-button-container">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Ir para o pagamento
                        </button>
                        <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">Pagamento processado de forma segura. Valor mínimo R$ 0,01.</p>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle ministry and campaign selection
            const donationTypeInputs = document.querySelectorAll('.donation-type-input');
            const ministrySelection = document.getElementById('ministry-selection');
            const ministrySelect = document.getElementById('ministry_id');
            const campaignSelection = document.getElementById('campaign-selection');
            const campaignSelect = document.getElementById('campaign_id');

            function updateSelectionVisibility() {
                const selectedType = document.querySelector('.donation-type-input:checked')?.value;

                // Ministry selection
                if (selectedType === 'ministry') {
                    ministrySelection.classList.remove('hidden');
                    ministrySelect.setAttribute('required', 'required');
                } else {
                    ministrySelection.classList.add('hidden');
                    ministrySelect.removeAttribute('required');
                    ministrySelect.value = '';
                }

                // Campaign selection
                if (selectedType === 'campaign') {
                    campaignSelection.classList.remove('hidden');
                    campaignSelect.setAttribute('required', 'required');
                } else {
                    campaignSelection.classList.add('hidden');
                    campaignSelect.removeAttribute('required');
                    if (selectedType !== 'campaign') {
                        campaignSelect.value = '';
                    }
                }
            }

            donationTypeInputs.forEach(input => {
                input.addEventListener('change', updateSelectionVisibility);
            });

            // Initial state
            updateSelectionVisibility();

            // Quick amount buttons
            document.querySelectorAll('.quick-amount').forEach(button => {
                button.addEventListener('click', function() {
                    const amount = this.dataset.amount;
                    document.getElementById('amount').value = amount;
                    // Trigger change event for amount input to update MP Brick if active
                    const event = new Event('change');
                    document.getElementById('amount').dispatchEvent(event);
                });
            });

            // Inicializar variáveis para Mercado Pago Bricks
            const gatewayInputs = document.querySelectorAll('.gateway-input');
            const amountInput = document.getElementById('amount');
            const brickPayloadInput = document.getElementById('brick_payload');
            const form = document.getElementById('donation-form');
            const paymentBrickContainer = document.getElementById('paymentBrick_container');
            const submitButtonContainer = document.querySelector('.submit-button-container');

            // Mercado Pago Brick Controller
            let mp = null;
            let bricksBuilder = null;
            let paymentBrickController = null;

            // Initialize MP if configured
            @php
                $mpGateway = $gateways->firstWhere('name', 'mercado_pago');
                $mpPublicKey = $mpGateway ? $mpGateway->getDecryptedCredentials()['public_key'] ?? null : null;
                $supportedMethods = $mpGateway ? $mpGateway->supported_methods : [];

                // Mapeia os métodos do banco para a configuração do Brick
                $mpBrickConfig = ['maxInstallments' => 12];

                // Se houver métodos configurados, habilitamos apenas os selecionados.
                // Caso contrário (null), habilitamos o padrão (fallback).
                if (!empty($supportedMethods)) {
                    if (in_array('pix', $supportedMethods)) $mpBrickConfig['bankTransfer'] = 'all';
                    if (in_array('boleto', $supportedMethods)) $mpBrickConfig['ticket'] = 'all';
                    if (in_array('credit_card', $supportedMethods)) {
                        $mpBrickConfig['creditCard'] = 'all';
                        $mpBrickConfig['debitCard'] = 'all';
                    }
                } else {
                     // Fallback padrão se absolutamente nada estiver configurado no gateway
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
                            email: '{{ auth()->user()->email ?? "nao-informado@doacao.com" }}',
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
                        onReady: () => {
                            console.log('Mercado Pago Brick Ready');
                        },
                        onSubmit: ({ selectedPaymentMethod, formData }) => {
                            return new Promise((resolve) => {
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
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                            <p class="text-sm text-red-600 dark:text-red-400 font-medium">Erro ao carregar o módulo de pagamento.</p>
                        </div>
                    `;
                } finally {
                    paymentBrickContainer.dataset.loading = 'false';
                }
            }

            // Main interface update function
            function updateInterface() {
                const selectedGateway = document.querySelector('.gateway-input:checked');

                // Default state: show submit button, hide brick
                if (paymentBrickContainer) paymentBrickContainer.style.display = 'none';
                if (submitButtonContainer) submitButtonContainer.style.display = 'block';

                if (!selectedGateway) return;

                const gatewayName = selectedGateway.dataset.name;

                if (gatewayName === 'mercado_pago') {
                    // Show Brick, Hide Default Submit
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

            // Listeners
            gatewayInputs.forEach(input => {
                input.addEventListener('change', updateInterface);
            });

            // Listener for amount changes
            amountInput.addEventListener('change', () => {
                const selectedGateway = document.querySelector('.gateway-input:checked');
                if (selectedGateway && selectedGateway.dataset.name === 'mercado_pago') {
                    initPaymentBrick();
                }
            });

            // Initial update
            updateInterface();
        });
    </script>
@endsection
