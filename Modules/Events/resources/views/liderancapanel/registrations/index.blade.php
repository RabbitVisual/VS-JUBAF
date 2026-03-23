@extends('liderancapanel::components.layouts.master')

@section('title', __('events::messages.registrations') . ' - ' . $event->title)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.eventos.index') }}"
                        class="hover:text-white transition-colors">{{ __('events::messages.events') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('lideranca.eventos.show', $event) }}"
                        class="hover:text-white transition-colors">{{ $event->title }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ __('events::messages.registrations') }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('events::messages.registrations') }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('events::messages.event') }}: {{ $event->title }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if (\Illuminate\Support\Facades\Gate::allows('export', $event))
                    <a href="{{ route('lideranca.eventos.registrations.export-pdf', $event) }}?{{ http_build_query(request()->query()) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        <x-icon name="file-pdf" class="w-5 h-5 text-red-500" />
                        {{ __('events::messages.export_pdf') ?? 'PDF' }}
                    </a>
                    <a href="{{ route('lideranca.eventos.registrations.export-badges', $event) }}?{{ http_build_query(request()->query()) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        <x-icon name="id-card" class="w-5 h-5" /> {{ __('events::messages.print_badges') ?? 'Crachás' }}
                    </a>
                    <a href="{{ route('lideranca.eventos.registrations.export-excel', $event) }}?{{ http_build_query(request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        <x-icon name="file-excel" class="w-5 h-5 text-green-600" />
                        {{ __('events::messages.export_excel') ?? 'Excel' }}
                    </a>
                @endif
                <a href="{{ route('lideranca.eventos.show', $event) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('events::messages.back') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
            <form method="GET" action="{{ route('lideranca.eventos.registrations.index', $event) }}"
                class="flex flex-wrap items-center gap-4">
                <select name="status"
                    class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                    <option value="">{{ __('events::messages.all_statuses') ?? 'Todos' }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                        {{ __('events::messages.pending') }}</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                        {{ __('events::messages.registration_confirmed') ?? 'Confirmado' }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                        {{ __('events::messages.registration_cancelled') ?? 'Cancelado' }}</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium">{{ __('events::messages.filter') ?? 'Filtrar' }}</button>
                @if (request()->has('status'))
                    <a href="{{ route('lideranca.eventos.registrations.index', $event) }}"
                        class="px-4 py-2 bg-slate-200 dark:bg-slate-600 text-gray-800 dark:text-white rounded-xl font-medium">Limpar</a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.enrolled') ?? 'Inscrito' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.participants') ?? 'Participantes' }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.total') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                {{ __('events::messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($registrations as $registration)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $registration->user->name ?? __('events::messages.visitor') }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $registration->user->email ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $registration->participants->count() }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">R$
                                    {{ number_format($registration->total_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full
                                @if ($registration->status === 'confirmed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($registration->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                                        {{ $registration->status_display }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('lideranca.eventos.registrations.show', [$event, $registration]) }}"
                                        class="text-amber-600 dark:text-amber-400 hover:underline font-medium">{{ __('events::messages.view_details') ?? 'Ver' }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon name="clipboard-list" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                    <p>{{ __('events::messages.no_registrations_found_admin') ?? 'Nenhuma inscrição encontrada.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registrations->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
