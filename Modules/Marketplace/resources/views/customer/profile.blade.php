@extends('marketplace::customer.layout')

@section('customer-content')
    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
            {{ __('marketplace::messages.profile') }}
        </h2>

        <form action="{{ route('marketplace.customer.profile.update') }}" method="POST"
              onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: '{{ __('marketplace::messages.saving_profile') }}' } }))">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('marketplace::messages.name') }}
                    </label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('marketplace::messages.email') }}
                    </label>
                    <input type="email" value="{{ $customer->email }}" disabled
                           class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 px-3 py-2 text-sm cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('marketplace::messages.phone') }}
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('marketplace::messages.document') }}
                    </label>
                    <input type="text" name="document" value="{{ old('document', $customer->document) }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    @error('document')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @php
                $addr = $customer->address_default ?? [];
            @endphp

            <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-6 space-y-4">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('marketplace::messages.default_address') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CEP</label>
                        <input type="text" name="address_default[cep]" value="{{ old('address_default.cep', $addr['cep'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Rua</label>
                        <input type="text" name="address_default[street]" value="{{ old('address_default.street', $addr['street'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Número</label>
                        <input type="text" name="address_default[number]" value="{{ old('address_default.number', $addr['number'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Complemento</label>
                        <input type="text" name="address_default[complement]" value="{{ old('address_default.complement', $addr['complement'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bairro</label>
                        <input type="text" name="address_default[district]" value="{{ old('address_default.district', $addr['district'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cidade</label>
                        <input type="text" name="address_default[city]" value="{{ old('address_default.city', $addr['city'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">UF</label>
                        <input type="text" name="address_default[state]" value="{{ old('address_default.state', $addr['state'] ?? '') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm" maxlength="2">
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
                    {{ __('marketplace::messages.save_changes') }}
                </button>
            </div>
        </form>
    </div>
@endsection

