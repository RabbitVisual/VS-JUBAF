@extends('admin::components.layouts.master')

@section('title', __('events::messages.registration_details') . ' - Administração')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('events::messages.registration_details') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('events::messages.event') }}: {{ $event->title }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.events.events.registrations.index', $event) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="-ml-1 mr-2 h-5 w-5" />
                {{ __('events::messages.back') }}
            </a>
        </div>
    </div>

    <!-- Registration Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Registration Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.registration_info') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.enrolled') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $registration->user->name ?? __('events::messages.visitor') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $registration->user->email ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.status') }}</p>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if ($registration->status === 'confirmed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($registration->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                                {{ $registration->status_display }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.total_value') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            R$ {{ number_format($registration->total_amount, 2, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.registration_date') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $registration->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @if($registration->payment_method)
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.payment_method') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $registration->payment_method }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Participants -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.participants') }} ({{ $registration->participants->count() }})</h3>
                <div class="space-y-4">
                    @foreach($registration->participants as $participant)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.name') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $participant->name }}</p>
                            </div>
                            @if($participant->email)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.email') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $participant->email }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.age') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($participant->birth_date)
                                        {{ \Carbon\Carbon::parse($participant->birth_date)->age }} {{ __('events::messages.condition_years') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            @if($participant->document)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.document') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $participant->document }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.actions') }}</h3>
                <div class="space-y-3">
                    @if($registration->status === 'pending')
                        <form action="{{ route('admin.events.events.registrations.confirm', [$event, $registration]) }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                {{ __('events::messages.confirm_registration') }}
                            </button>
                        </form>
                    @endif
                    @if($registration->status !== 'cancelled')
                        <form action="{{ route('admin.events.events.registrations.cancel', [$event, $registration]) }}" method="POST" onsubmit="return confirm('{{ __('events::messages.cancel_confirm') }}');">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                {{ __('events::messages.cancel_registration') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Event Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('events::messages.event_info') }}</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.event') }}</p>
                        <p class="text-gray-900 dark:text-white">{{ $event->title }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.date') }}</p>
                        <p class="text-gray-900 dark:text-white">{{ $event->start_date->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($event->location)
                    <div>
                        <p class="font-medium text-gray-500 dark:text-gray-400">{{ __('events::messages.location') }}</p>
                        <p class="text-gray-900 dark:text-white">{{ $event->location }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

