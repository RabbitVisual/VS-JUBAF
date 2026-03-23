@extends('admin::components.layouts.master')

@section('title', 'Configurar Gateway')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Configurar {{ $gateway->display_name }}</h2>
            <p class="text-slate-600 dark:text-slate-400">Ajuste as credenciais e parâmetros de segurança.</p>
        </div>
        <a href="{{ route('admin.payment-gateways.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form action="{{ route('admin.payment-gateways.update', $gateway->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Status</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $gateway->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-slate-300">Habilitar Driver</span>
                    </label>
                </div>

                <div>
                        <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Ambiente</label>
                        <select name="mode" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                        <option value="sandbox" {{ $gateway->is_test_mode ? 'selected' : '' }}>Sandbox (Teste)</option>
                        <option value="production" {{ !$gateway->is_test_mode ? 'selected' : '' }}>Produção</option>
                        </select>
                </div>
            </div>

            <hr class="h-px my-6 bg-slate-200 border-0 dark:bg-slate-700">

            @php
                $credentials = $gateway->getDecryptedCredentials();
            @endphp

            <!-- Dynamic Fields based on Driver -->
            @if($gateway->name === 'stripe')
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Public Key</label>
                    <input type="text" name="credentials[public_key]" value="{{ $credentials['public_key'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Secret Key</label>
                    <input type="password" name="credentials[secret_key]" value="{{ $credentials['secret_key'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Webhook Secret</label>
                    <input type="password" name="credentials[webhook_secret]" value="{{ $credentials['webhook_secret'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500">Obrigatório para confirmação segura de pagamentos via Checkout.</p>
                </div>
                <div class="mb-6 p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">URL do Webhook (configure no Stripe)</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Copie e use esta URL no painel do Stripe &gt; Webhooks.</p>
                    <code class="block text-sm text-slate-800 dark:text-slate-200 break-all select-all">{{ url()->route('api.gateway.webhook', ['driver' => 'stripe']) }}</code>
                </div>

            @elseif($gateway->name === 'mercado_pago')
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Public Key</label>
                    <input type="text" name="credentials[public_key]" value="{{ $credentials['public_key'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Access Token</label>
                    <input type="password" name="credentials[access_token]" value="{{ $credentials['access_token'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Webhook Secret</label>
                    <input type="password" name="credentials[webhook_secret]" value="{{ $credentials['webhook_secret'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500">Obrigatório para confirmação automática de pagamentos.</p>
                </div>
                <div class="mb-6 p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">URL do Webhook (configure em Suas integrações)</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Copie e use esta URL no painel Mercado Pago &gt; Webhooks.</p>
                    <code class="block text-sm text-slate-800 dark:text-slate-200 break-all select-all">{{ url()->route('api.gateway.webhook', ['driver' => 'mercado_pago']) }}</code>
                </div>

            @elseif($gateway->name === 'pix_mtls')
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Client ID / Chave Pix</label>
                    <input type="text" name="credentials[pix_key]" value="{{ $credentials['pix_key'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Client Secret</label>
                    <input type="password" name="credentials[client_secret]" value="{{ $credentials['client_secret'] ?? '' }}" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white">
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Certificado (.pem / .p12)</label>
                    @if(isset($credentials['certificate_path']))
                        <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-slate-800 dark:text-green-400 dark:border-green-800">
                            <i class="fas fa-certificate mr-3"></i>
                            <div>
                                <span class="font-medium">Ativo:</span> {{ $credentials['certificate_path'] }}
                            </div>
                        </div>
                    @endif
                    <input class="block w-full text-sm text-slate-900 border border-slate-300 rounded-lg cursor-pointer bg-slate-50 dark:text-slate-400 focus:outline-none dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400" type="file" name="certificate">
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">Certificado mTLS obrigatório para comunicação bancária.</p>
                </div>
            @endif

            <hr class="h-px my-6 bg-slate-200 border-0 dark:bg-slate-700">

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Métodos de Pagamento Suportados</label>
                <div class="space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="supported_methods[]" value="pix" class="form-checkbox rounded text-indigo-600 dark:bg-slate-700 dark:border-slate-600"
                            {{ in_array('pix', $gateway->supported_methods ?? []) ? 'checked' : '' }}>
                        <span class="ml-2 text-slate-700 dark:text-slate-300">Pix</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="checkbox" name="supported_methods[]" value="credit_card" class="form-checkbox rounded text-indigo-600 dark:bg-slate-700 dark:border-slate-600"
                            {{ in_array('credit_card', $gateway->supported_methods ?? []) ? 'checked' : '' }}>
                        <span class="ml-2 text-slate-700 dark:text-slate-300">Cartão de Crédito</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="checkbox" name="supported_methods[]" value="debit_card" class="form-checkbox rounded text-indigo-600 dark:bg-slate-700 dark:border-slate-600"
                            {{ in_array('debit_card', $gateway->supported_methods ?? []) ? 'checked' : '' }}>
                        <span class="ml-2 text-slate-700 dark:text-slate-300">Cartão de Débito</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="checkbox" name="supported_methods[]" value="boleto" class="form-checkbox rounded text-indigo-600 dark:bg-slate-700 dark:border-slate-600"
                            {{ in_array('boleto', $gateway->supported_methods ?? []) ? 'checked' : '' }}>
                        <span class="ml-2 text-slate-700 dark:text-slate-300">Boleto</span>
                    </label>
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Selecione quais métodos este gateway deve processar.</p>
            </div>

            <div class="flex items-center justify-end space-x-2 border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
                <a href="{{ route('admin.payment-gateways.index') }}" class="text-slate-800 hover:text-white border border-slate-300 hover:bg-slate-900 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-slate-600 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-600 dark:focus:ring-slate-800">Cancelar</a>
                <button type="submit" class="text-white bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
