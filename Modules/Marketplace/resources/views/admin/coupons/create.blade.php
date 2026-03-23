@extends('admin::components.layouts.master')

@section('title', 'Novo cupom')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo cupom</h1>
        <a href="{{ route('admin.marketplace.coupons.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" /> Voltar
        </a>
    </div>

    <form action="{{ route('admin.marketplace.coupons.store') }}" method="POST" class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando cupom...' } }))">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código *</label>
            <input type="text" name="code" value="{{ old('code') }}" required placeholder="Ex: MISSÕES10" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 uppercase">
            @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Valor fixo (R$)</option>
                    <option value="percent" {{ old('type', 'percent') === 'percent' ? 'selected' : '' }}>Percentual (%)</option>
                </select>
                @error('type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor *</label>
                <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2" placeholder="{{ old('type') === 'fixed' ? '0.00' : '10' }}">
                @error('value')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Compra mínima (R$)</label>
            <input type="number" name="min_purchase" value="{{ old('min_purchase') }}" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2" placeholder="Opcional">
            @error('min_purchase')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Limite de usos</label>
            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2" placeholder="Ilimitado">
            @error('max_uses')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Válido de</label>
                <input type="date" name="valid_from" value="{{ old('valid_from') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
                @error('valid_from')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Válido até</label>
                <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
                @error('valid_until')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600" id="is_active">
            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Ativo</label>
        </div>
        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm">
                <x-icon name="check" style="duotone" class="w-4 h-4 mr-2" /> Criar cupom
            </button>
        </div>
    </form>
</div>
@endsection
