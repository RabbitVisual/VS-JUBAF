@extends('admin::components.layouts.master')

@section('title', $event->title . ' - Administração')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                @if($event->banner_path)
                    <div class="mb-4">
                        <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}" class="h-48 w-full object-cover rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    </div>
                @else
                    <div class="mb-4 h-48 w-full bg-linear-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center border border-indigo-200 dark:border-indigo-900 shadow-sm overflow-hidden relative">
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                        <x-icon name="calendar-days" style="duotone" class="w-20 h-20 text-white/20 relative z-10" />
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $event->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('events::messages.created_by') }} {{ $event->creator->name ?? 'Sistema' }} {{ __('events::messages.on_date') }} {{ $event->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($event->status === 'published')
                    <a href="{{ route('events.public.show', $event->slug) }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center px-4 py-2 border border-indigo-300 dark:border-indigo-600 text-sm font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">
                        <x-icon name="eye" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                        {{ __('events::messages.view_public_page') ?? 'Ver página pública' }}
                    </a>
                    <a href="{{ route('events.public.landing', $event->slug) }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center px-4 py-2 border border-amber-300 dark:border-amber-600 text-sm font-medium rounded-md text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40">
                        <x-icon name="window-maximize" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                        {{ __('events::messages.view_landing') ?? 'Ver página de divulgação (landing)' }}
                    </a>
                @endif
                <a href="{{ route('admin.events.events.edit', $event) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <x-icon name="pen-to-square" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                    {{ __('events::messages.edit') }}
                </a>
                <a href="{{ route('admin.events.events.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <x-icon name="arrow-left" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                    {{ __('events::messages.back') }}
                </a>
            </div>
        </div>

        @if($event->status === 'published')
        <!-- Página de divulgação (landing) -->
        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <x-icon name="window-maximize" style="duotone" class="w-4 h-4" />
                        {{ __('events::messages.landing_page_options') ?? 'Página de divulgação do evento (landing)' }}
                    </h3>
                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                        {{ __('events::messages.landing_page_options_desc') ?? 'A landing é a página exclusiva do evento (navbar do evento, sem menu do site). O que exibir (capa, sobre, programação, local, mapa, vagas, etc.) é configurado em' }}
                        <a href="{{ route('admin.events.events.edit', $event) }}#page-options" class="font-semibold underline">{{ __('events::messages.edit') ?? 'Editar evento' }}</a>
                        {{ __('events::messages.landing_in_section') ?? ', seção "Página do evento".' }}
                    </p>
                </div>
                <a href="{{ route('events.public.landing', $event->slug) }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-200/50 dark:bg-amber-800/30 text-amber-800 dark:text-amber-200 text-sm font-medium hover:bg-amber-200 dark:hover:bg-amber-800/50">
                    <x-icon name="arrow-up-right-from-square" style="duotone" class="w-4 h-4" />
                    {{ __('events::messages.view_landing') ?? 'Abrir landing' }}
                </a>
            </div>
        </div>
        @endif

        <!-- Event Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.status') }}</p>
                <p class="mt-1 text-lg font-semibold">
                    <span
                        class="px-2 py-1 text-xs font-medium rounded-full
                    @if ($event->status === 'published') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                    @elseif($event->status === 'draft') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                    @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                        {{ $event->status_display }}
                    </span>
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.registered') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $event->total_participants ?? 0 }} / {{ $event->capacity ?? '∞' }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.registrations_count') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $event->registrations->count() }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.visibility') }}</p>
                <p class="mt-1 text-lg font-semibold">
                    <span
                        class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        {{ $event->visibility_display }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Event Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                @if ($event->description)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.description') }}</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $event->description }}</p>
                    </div>
                @endif

                <!-- Date and Location -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.date_and_location') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-icon name="calendar-days" style="duotone" class="w-5 h-5 mr-3" />
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ __('events::messages.start') }}</div>
                                <div>{{ $event->start_date->format('d/m/Y H:i') }}</div>
                                @if ($event->end_date)
                                    <div class="mt-1">{{ __('events::messages.end') }}: {{ $event->end_date->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                        @if ($event->location)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-icon name="location-dot" style="duotone" class="w-5 h-5 mr-3" />
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ __('events::messages.location') }}</div>
                                    <div>{{ $event->location }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price Rules -->
                @if ($event->priceRules->count() > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.active_price_rules') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            {{ __('events::messages.rule') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            {{ __('events::messages.category') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            {{ __('events::messages.conditions') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            {{ __('events::messages.price_discount') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            {{ __('events::messages.priority') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($event->priceRules->sortByDesc('priority') as $rule)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($rule->effective_rule_type === 'age_range') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                                    @elseif($rule->effective_rule_type === 'member_status') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                                    @elseif($rule->effective_rule_type === 'participant_type') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300
                                                    @elseif($rule->effective_rule_type === 'discount_code') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                                    {{ $rule->rule_type_display }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $rule->label }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                @switch($rule->effective_rule_type)
                                                    @case('age_range')
                                                        @if ($rule->min_age !== null && $rule->max_age !== null)
                                                            {{ $rule->min_age }}-{{ $rule->max_age }} {{ __('events::messages.condition_years') }}
                                                        @elseif($rule->min_age !== null)
                                                            {{ $rule->min_age }} + {{ __('events::messages.condition_years') }}
                                                        @elseif($rule->max_age !== null)
                                                            {{ __('events::messages.condition_until') }} {{ $rule->max_age }} {{ __('events::messages.condition_years') }}
                                                        @else
                                                            {{ __('events::messages.any_age') }}
                                                        @endif
                                                        @break
                                                    @case('member_status')
                                                        {{ __('events::messages.status') }}: {{ ucfirst($rule->member_status ?? __('events::messages.any')) }}
                                                        @break
                                                    @case('participant_type')
                                                        {{ __('events::messages.label') }}: {{ ucfirst($rule->participant_type ?? __('events::messages.any')) }}
                                                        @break
                                                    @case('discount_code')
                                                        {{ __('events::messages.code') }}: {{ $rule->discount_code ?? __('events::messages.no_code') }}
                                                        @break
                                                    @case('registration_date')
                                                        @if($rule->date_from && $rule->date_to)
                                                            {{ $rule->date_from->format('d/m') }} - {{ $rule->date_to->format('d/m') }}
                                                        @elseif($rule->date_from)
                                                            {{ __('events::messages.condition_from') }} {{ $rule->date_from->format('d/m') }}
                                                        @elseif($rule->date_to)
                                                            {{ __('events::messages.condition_until') }} {{ $rule->date_to->format('d/m') }}
                                                        @endif
                                                        @break
                                                    @case('group_size')
                                                        @if($rule->min_participants && $rule->max_participants)
                                                            {{ $rule->min_participants }}-{{ $rule->max_participants }} {{ __('events::messages.participants') }}
                                                        @elseif($rule->min_participants)
                                                            {{ $rule->min_participants }} + {{ __('events::messages.participants') }}
                                                        @elseif($rule->max_participants)
                                                            {{ __('events::messages.condition_until') }} {{ $rule->max_participants }} {{ __('events::messages.participants') }}
                                                        @endif
                                                        @break
                                                    @case('location')
                                                        {{ __('events::messages.location') }}: {{ $rule->location ?? __('events::messages.not_informed') }}
                                                        @break
                                                    @case('standard')
                                                        {{ __('events::messages.event_standard') }}
                                                        @break
                                                    @default
                                                        -
                                                @endswitch
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                @if($rule->price > 0)
                                                    R$ {{ number_format($rule->price, 2, ',', '.') }}
                                                @elseif($rule->discount_percentage > 0)
                                                    -{{ number_format($rule->discount_percentage, 1) }}%
                                                @elseif($rule->discount_fixed > 0)
                                                    -R$ {{ number_format($rule->discount_fixed, 2, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $rule->priority }}
                                                @if(!$rule->is_active)
                                                    <span class="text-red-600 dark:text-red-400 text-xs">({{ __('events::messages.inactive') }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.quick_actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.events.events.registrations.index', $event) }}"
                            class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            {{ __('events::messages.view_registration') }} ({{ $event->registrations->count() }})
                        </a>
                        <a href="{{ route('admin.events.events.edit', $event) }}"
                            class="block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            {{ __('events::messages.edit_event') }}
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.statistics') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('events::messages.total_registrations') }}</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->registrations->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('events::messages.registration_confirmed') }}</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                {{ $event->registrations->where('status', 'confirmed')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('events::messages.pending') }}</span>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                {{ $event->registrations->where('status', 'pending')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('events::messages.registration_cancelled') }}</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $event->registrations->where('status', 'cancelled')->count() }}
                            </span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('events::messages.total_collected') }}</span>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                    R$
                                    {{ number_format($totalArrecadado ?? $event->registrations->where('status', 'confirmed')->sum('total_amount'), 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                @if (isset($revenueData) && count($revenueData) > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.revenue_30_days') }}
                        </h3>
                        <div class="h-64">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                @endif

                <!-- Age Distribution -->
                @if (isset($ageDistribution) && count($ageDistribution) > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.age_distribution') }}</h3>
                        <div class="space-y-3">
                            @foreach ($ageDistribution as $group => $count)
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $group }}</span>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full"
                                            style="width: {{ ($count / array_sum($ageDistribution)) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        @if (isset($revenueData) && count($revenueData) > 0)
            <script src="{{ asset('vendor/chart.js/chart.umd.min.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Destroy existing charts if they exist
                    try {
                        if (window.revenueChart && typeof window.revenueChart.destroy === 'function') {
                            window.revenueChart.destroy();
                            window.revenueChart = null;
                        }
                    } catch (e) {
                        console.warn('Error destroying revenue chart:', e);
                    }

                    try {
                        if (window.ageChart && typeof window.ageChart.destroy === 'function') {
                            window.ageChart.destroy();
                            window.ageChart = null;
                        }
                    } catch (e) {
                        console.warn('Error destroying age chart:', e);
                    }

                    const ctx = document.getElementById('revenueChart');
                    if (ctx) {
                        const revenueData = @json($revenueData);
                        window.revenueChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: revenueData.map(d => d.date),
                                datasets: [{
                                    label: '{{ __('events::messages.revenue_label') }}',
                                    data: revenueData.map(d => d.revenue),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'R$ ' + value.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
        @endif
    @endpush
@endsection

