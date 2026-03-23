@extends('admin::components.layouts.master')

@section('title', __('events::messages.registrations') . ' - ' . $event->title)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('events::messages.registrations') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('events::messages.event') }}: {{ $event->title }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.events.events.show', $event) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                {{ __('events::messages.back_to_event') }}
            </a>
        </div>
    </div>

    <!-- Badges & Export Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <x-icon name="id-card" style="duotone" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('events::messages.badges') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('events::messages.badge_template_desc') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.events.events.edit', $event) }}#badge-section"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                    {{ __('events::messages.configure_badges') ?? 'Configurar Crachás' }}
                </a>
                <a href="{{ route('admin.events.events.registrations.export-badges', $event) }}?{{ http_build_query(request()->query()) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <x-icon name="print" style="duotone" class="w-4 h-4 mr-2" />
                    {{ __('events::messages.print_badges') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.events.events.registrations.export-excel', $event) }}?{{ http_build_query(request()->query()) }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="file-excel" style="duotone" class="w-4 h-4 mr-2 text-green-600" />
            {{ __('events::messages.export_excel') }}
        </a>
        <a href="{{ route('admin.events.events.registrations.export-pdf', $event) }}?{{ http_build_query(request()->query()) }}"
            target="_blank"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="file-pdf" style="duotone" class="w-4 h-4 mr-2 text-red-600" />
            {{ __('events::messages.export_pdf') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.events.events.registrations.index', $event) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">{{ __('events::messages.all_statuses') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('events::messages.pending') }}</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('events::messages.registration_confirmed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('events::messages.registration_cancelled') }}</option>
            </select>
            <select name="age_group" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">{{ __('events::messages.all_ages') }}</option>
                <option value="0-12" {{ request('age_group') === '0-12' ? 'selected' : '' }}>{{ __('events::messages.children') }}</option>
                <option value="13-17" {{ request('age_group') === '13-17' ? 'selected' : '' }}>{{ __('events::messages.teenagers') }}</option>
                <option value="18-29" {{ request('age_group') === '18-29' ? 'selected' : '' }}>{{ __('events::messages.young_adults') }}</option>
                <option value="30-49" {{ request('age_group') === '30-49' ? 'selected' : '' }}>{{ __('events::messages.adults') }}</option>
                <option value="50-64" {{ request('age_group') === '50-64' ? 'selected' : '' }}>{{ __('events::messages.middle_aged') }}</option>
                <option value="65+" {{ request('age_group') === '65+' ? 'selected' : '' }}>{{ __('events::messages.seniors') }}</option>
            </select>
            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600">
                    {{ __('events::messages.filter') }}
                </button>
                @if (request()->has('status') || request()->has('age_group'))
                    <a href="{{ route('admin.events.events.registrations.index', $event) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        {{ __('events::messages.clear') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.total') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $registrations->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.registration_confirmed') }}</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                {{ $registrations->where('status', 'confirmed')->count() }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.pending') }}</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                {{ $registrations->where('status', 'pending')->count() }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.total_collected') }}</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                R$ {{ number_format($registrations->where('status', 'confirmed')->sum('total_amount'), 2, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.enrolled') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.participants') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('events::messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($registrations as $registration)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $registration->user->name ?? __('events::messages.visitor') }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $registration->user->email ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ __('events::messages.participant_qty', ['count' => $registration->participants->count()]) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            R$ {{ number_format($registration->total_amount, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if ($registration->status === 'confirmed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($registration->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                                {{ $registration->status_display }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $registration->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.events.events.registrations.show', [$event, $registration]) }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                {{ __('events::messages.view_details') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                                    <x-icon name="file-circle-xmark" style="duotone" class="w-12 h-12 text-gray-400 mb-4" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('events::messages.no_registrations_found_admin') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('events::messages.no_registrations_desc_admin') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

