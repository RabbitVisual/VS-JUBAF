@extends('memberpanel::components.layouts.master')

@section('title', __('events::messages.registration_confirmed') . ' - ' . __('memberpanel::messages.member_panel'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12 flex items-center justify-center p-4">
    <div class="max-w-7xl mx-auto w-full">
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-6 justify-center">
            <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('memberpanel::messages.panel') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('events::messages.events') }}</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium">{{ __('events::messages.registration_confirmed_title') }}</span>
        </nav>
    <div class="max-w-xl w-full mx-auto bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 p-6 sm:p-8 md:p-12 text-center relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-t-2xl"></div>
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 w-20 h-20 sm:w-24 sm:h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8">
            <x-icon name="check" class="w-10 h-10 sm:w-12 sm:h-12 text-emerald-600 dark:text-emerald-400" />
        </div>

        <h2 class="relative z-10 text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-3 sm:mb-4 tracking-tight">
            {{ __('events::messages.registration_confirmed_title') }}
        </h2>

        <p class="relative z-10 text-base sm:text-lg text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 leading-relaxed px-2">
            {!! __('events::messages.registration_success_msg', ['event' => '<strong>' . $registration->event->title . '</strong>']) !!}
        </p>

        <div class="relative z-10 bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-slate-700 mb-6 sm:mb-8 text-left">
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('events::messages.registration') }}</span>
                <span class="font-black text-gray-900 dark:text-white">#{{ $registration->id }}</span>
            </div>
            <div class="flex justify-between items-center">
                 <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('events::messages.value') }}</span>
                 <span class="font-bold text-green-600 dark:text-green-400">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="{{ route('memberpanel.events.show-registration', $registration) }}"
               class="px-6 sm:px-8 py-3 sm:py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl active:scale-[0.98] touch-manipulation">
                {{ __('events::messages.view_details') }}
            </a>
            <a href="{{ route('memberpanel.events.index') }}"
               class="px-6 sm:px-8 py-3 sm:py-4 bg-white dark:bg-slate-800 text-gray-700 dark:text-white border border-gray-200 dark:border-slate-700 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all touch-manipulation active:scale-[0.98]">
                {{ __('events::messages.back_to_events') }}
            </a>
        </div>
    </div>
    </div>
</div>
@endsection

