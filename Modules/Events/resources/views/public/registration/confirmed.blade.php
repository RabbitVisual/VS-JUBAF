@extends('homepage::components.layouts.master')

@section('title', __('events::messages.registration_confirmed'))

@section('content')
<!-- Hero / Status Section -->
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 dark:bg-gray-950 py-20 pt-40 relative overflow-hidden">
    <!-- Ambient Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl translate-y-1/2"></div>
    </div>

    <div class="max-w-3xl w-full mx-auto px-4 relative z-10">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-green-100 dark:border-green-900/30 overflow-hidden">
            <!-- Success Header -->
            <div class="bg-linear-to-br from-green-500 to-emerald-600 p-10 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner ring-4 ring-white/10">
                    <x-icon name="check" style="duotone" class="h-12 w-12 text-white" />
                </div>

                <h2 class="text-3xl md:text-4xl font-black text-white mb-4 tracking-tight leading-tight">
                    {{ __('events::messages.registration_confirmed_title') ?? 'Inscrição Confirmada!' }}
                </h2>
                <p class="text-green-50 font-medium text-lg leading-relaxed max-w-lg mx-auto">
                    {{ __('events::messages.registration_success_public_msg') ?? 'Sua inscrição foi confirmada com sucesso. Você receberá um email com mais detalhes em breve.' }}
                </p>
            </div>

            <!-- Details Section -->
            <div class="p-10">
                <h3 class="flex items-center text-xs font-black text-gray-400 uppercase tracking-widest mb-8">
                    <span class="flex-grow border-t border-gray-100 dark:border-gray-800 mr-4"></span>
                    {{ __('events::messages.registration_details') ?? 'Detalhes da Inscrição' }}
                    <span class="flex-grow border-t border-gray-100 dark:border-gray-800 ml-4"></span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="group p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.event') }}</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 transition-colors">{{ $registration->event->title }}</p>
                    </div>

                    <div class="group p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.date') }}</p>
                        <div class="flex items-center">
                            <x-icon name="calendar-days" style="duotone" class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-500 transition-colors" />
                            <p class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $registration->event->start_date->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="group p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.registration_number') ?? 'Número da Inscrição' }}</p>
                        <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">#{{ $registration->id }}</p>
                    </div>

                    <div class="group p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.participants') }}</p>
                         <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2 text-gray-600 dark:text-gray-300 font-bold text-sm">{{ $registration->participants->count() }}</div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">Participante(s)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-4 pt-2">
                    @if($registration->event->hasTicketEnabled() && !empty($registration->uuid))
                    <a href="{{ route('events.public.ticket.download', $registration->uuid) }}" class="group inline-flex items-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl transition-all">
                        <x-icon name="file-arrow-down" style="duotone" class="w-5 h-5 mr-2" />
                        {{ __('events::messages.download_ticket') ?? 'Baixar ingresso' }}
                    </a>
                    @endif
                    @if($registration->event->hasCertificateEnabled() && ($certificate_available ?? false))
                    <a href="{{ route('events.public.certificate.download', $registration->uuid) }}" class="group inline-flex items-center px-10 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-xl transition-all">
                        <x-icon name="certificate" style="duotone" class="w-5 h-5 mr-2" />
                        {{ __('events::messages.download_certificate') }}
                    </a>
                    @endif
                    <a href="{{ route('homepage.index') }}" class="group inline-flex items-center px-10 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-black rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                        <x-icon name="house" style="duotone" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" />
                        {{ __('events::messages.back_to_home') ?? 'Voltar ao Início' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

