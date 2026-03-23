@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.my_registrations') . ' - ' . __('memberpanel::messages.member_panel'))
@section('page-title', __('events::messages.my_registrations'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6 sm:space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
                    <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                    <a href="{{ route('memberpanel.events.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.events') }}</a>
                    <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                    <span class="text-gray-900 dark:text-white font-medium">{{ __('events::messages.my_registrations') }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('events::messages.my_registrations') }}</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm max-w-xl">{{ __('events::messages.track_your_registrations') }}</p>
            </div>
            <a href="{{ route('memberpanel.events.index') }}"
               class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shrink-0 touch-manipulation active:scale-[0.98]">
                <x-icon name="arrow-left" class="w-4 h-4" />
                {{ __('events::messages.view_events') }}
            </a>
        </div>

        <div class="space-y-4">
        @forelse($registrations as $registration)
        <div class="group bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Info -->
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs font-black rounded-full uppercase tracking-wider
                            @if ($registration->status === 'confirmed') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                            @elseif($registration->status === 'pending') bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400
                            @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 @endif">
                            {{ $registration->status_display }}
                        </span>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                             #{{ $registration->id }}
                        </span>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $registration->event->title }}
                    </h3>

                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400 font-medium">
                        <div class="flex items-center">
                            <x-icon name="calendar" class="w-4 h-4 mr-1.5 text-blue-500" />
                            {{ $registration->event->start_date->format('d/m/Y H:i') }}
                        </div>
                        <div class="flex items-center">
                            <x-icon name="users" class="w-4 h-4 mr-1.5 text-purple-500" />
                            {{ __('events::messages.participant_count', ['count' => $registration->participants->count()]) }}
                        </div>
                        <div class="flex items-center">
                            <x-icon name="currency-dollar" class="w-4 h-4 mr-1.5 text-emerald-500" />
                            R$ {{ number_format($registration->total_amount, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3 border-t md:border-t-0 md:border-l border-gray-100 dark:border-slate-800 pt-4 md:pt-0 md:pl-6">
                    <a href="{{ route('memberpanel.events.show-registration', $registration) }}"
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-4 sm:px-5 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold transition-colors touch-manipulation active:scale-[0.98]">
                        {{ __('events::messages.details') }}
                    </a>
                    @if($registration->status === 'pending')
                         <div class="flex flex-col sm:flex-row gap-2 flex-1 md:flex-none">
                            @if($registration->latestPayment)
                                <a href="{{ route('checkout.show', $registration->latestPayment->transaction_id) }}"
                                   class="inline-flex justify-center items-center px-4 sm:px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-indigo-500/20 touch-manipulation active:scale-[0.98]">
                                    {{ __('events::messages.pay_now') }}
                                </a>
                            @else
                                <a href="{{ route('memberpanel.events.show', $registration->event) }}"
                                   class="inline-flex justify-center items-center px-4 sm:px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-colors shadow-lg shadow-amber-500/20 touch-manipulation active:scale-[0.98]">
                                    {{ __('events::messages.pay_now') }}
                                </a>
                            @endif
                            <a href="{{ route('memberpanel.events.registration.retry', $registration) }}"
                               class="inline-flex justify-center items-center px-4 sm:px-5 py-2.5 bg-amber-100 dark:bg-amber-900/20 hover:bg-amber-200 dark:hover:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-xl font-bold transition-colors touch-manipulation active:scale-[0.98]">
                                {{ __('events::messages.change_method') }}
                            </a>
                         </div>
                    @else
                        <a href="{{ route('memberpanel.events.show', $registration->event) }}"
                           class="flex-1 md:flex-none inline-flex justify-center items-center px-4 sm:px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-indigo-500/20 touch-manipulation active:scale-[0.98]">
                            {{ __('events::messages.view_event') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 sm:p-12 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <x-icon name="ticket" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 dark:text-slate-500" />
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('events::messages.no_registrations_found') }}</h3>
            <p class="text-gray-500 dark:text-slate-400 text-sm max-w-md mx-auto mb-6 sm:mb-8">
                {{ __('events::messages.no_registrations_msg') }}
            </p>
            <a href="{{ route('memberpanel.events.index') }}"
               class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl active:scale-[0.98] touch-manipulation">
                <x-icon name="search" class="w-5 h-5" />
                {{ __('events::messages.view_available_events') }}
            </a>
        </div>
        @endforelse
        </div>

        @if($registrations->hasPages())
            <div class="mt-6 sm:mt-8">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
