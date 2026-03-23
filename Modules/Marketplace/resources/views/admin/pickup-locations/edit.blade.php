@extends('admin::components.layouts.master')

@section('title', 'Editar ponto de retirada')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar ponto de retirada</h1>
        <a href="{{ route('admin.marketplace.pickup-locations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" /> Voltar
        </a>
    </div>

    <form action="{{ route('admin.marketplace.pickup-locations.update', $pickupLocation) }}" method="POST" class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
            <input type="text" name="name" value="{{ old('name', $pickupLocation->name) }}" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
            <input type="text" name="address" value="{{ old('address', $pickupLocation->address) }}" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
            @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instruções (para o cliente)</label>
            <textarea name="instructions" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">{{ old('instructions', $pickupLocation->instructions) }}</textarea>
            @error('instructions')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pickupLocation->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600" id="is_active">
            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Ativo</label>
        </div>
        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm">
                <x-icon name="check" style="duotone" class="w-4 h-4 mr-2" /> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
