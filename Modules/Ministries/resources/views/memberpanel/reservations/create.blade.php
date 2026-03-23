@extends('memberpanel::components.layouts.master')

@section('page-title', 'Solicitar equipamento – ' . $ministry->name)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-2xl mx-auto px-6 pt-8 space-y-8">
            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm font-medium text-rose-800 dark:text-rose-200 flex items-center gap-2">
                    <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('error') }}
                </div>
            @endif
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-slate-400 font-medium">
                <a href="{{ route('memberpanel.ministries.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 uppercase tracking-widest text-[10px] font-black">Ministérios</a>
                <x-icon name="chevron-right" class="w-3 h-3 opacity-50" />
                <a href="{{ route('memberpanel.ministries.show', $ministry) }}" class="hover:text-blue-600 dark:hover:text-blue-400 font-bold">{{ $ministry->name }}</a>
                <x-icon name="chevron-right" class="w-3 h-3 opacity-50" />
                <span class="text-gray-900 dark:text-white font-bold">Solicitar equipamento</span>
            </nav>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="box-open" class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Solicitar equipamento</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Preencha os dados para reservar um recurso do patrimônio.</p>
                    </div>
                </div>

                <form action="{{ route('memberpanel.ministries.reservations.store', $ministry) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="asset_id" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Equipamento *</label>
                        <select name="asset_id" id="asset_id" required class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="">Selecione...</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->name }}{{ $asset->code ? ' (' . $asset->code . ')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('asset_id')<p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_at" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Início *</label>
                            <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at') }}" required class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            @error('start_at')<p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="end_at" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Fim *</label>
                            <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at') }}" required class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            @error('end_at')<p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    @if(count($events) > 0)
                        <div>
                            <label for="event_id" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Vincular a evento (opcional)</label>
                            <select name="event_id" id="event_id" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="">Nenhum</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }} ({{ $event->starts_at->format('d/m/Y H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label for="notes" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Observações</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="Ex.: Culto de domingo, ensaio...">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                            <x-icon name="paper-plane" class="w-4 h-4" /> Enviar solicitação
                        </button>
                        <a href="{{ route('memberpanel.ministries.show', $ministry) }}" class="px-6 py-3 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
