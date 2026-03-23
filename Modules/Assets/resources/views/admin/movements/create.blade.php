@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Nova Movimentação</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Nova Movimentação</h1>
                    <p class="text-gray-300 max-w-xl">Registre transferência, empréstimo, devolução ou envio para manutenção.</p>
                </div>
                <a href="{{ route('assets.admin.movements.history') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Histórico
                </a>
            </div>
        </div>

        <form action="{{ route('assets.admin.movements.store') }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Registrando movimentação...' } }))">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative space-y-6">
            <div>
                <label for="asset_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item de Patrimônio</label>
                <select name="asset_id" id="asset_id" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione o item...</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->code }} - {{ $asset->name }} (Atual: {{ $asset->location->name }})</option>
                    @endforeach
                </select>
                @error('asset_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Movimento</label>
                    <select name="type" id="type" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="transfer">Transferência Definitiva</option>
                        <option value="loan">Empréstimo Temporário</option>
                        <option value="return">Devolução</option>
                        <option value="maintenance_out">Envio para Manutenção</option>
                        <option value="maintenance_return">Retorno de Manutenção</option>
                        <option value="disposal">Descarte/Baixa</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data/Hora</label>
                    <input type="datetime-local" name="date" id="date" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="new_location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Local de Destino</label>
                <select name="new_location_id" id="new_location_id" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione o destino...</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('new_location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="responsible_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Responsável no Destino (Opcional)</label>
                <select name="responsible_id" id="responsible_id" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quem receberá ou ficará responsável pelo item.</p>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all">
                    Registrar Movimentação
                </button>
            </div>
                </div>
            </div>
        </form>
    </div>
@endsection

