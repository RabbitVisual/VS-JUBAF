@extends('homepage::components.layouts.master')

@section('title', __('events::messages.registration_pending'))

@section('content')
<!-- Hero / Status Section -->
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 dark:bg-gray-950 py-20 pt-40 relative overflow-hidden">
    <!-- Ambient Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl translate-y-1/2"></div>
    </div>

    <div class="max-w-3xl w-full mx-auto px-4 relative z-10">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-amber-100 dark:border-amber-900/30 overflow-hidden">
            <!-- Pending Header -->
            <div class="bg-linear-to-br from-amber-400 to-orange-500 p-10 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner ring-4 ring-white/10">
                    <x-icon name="clock" style="duotone" class="h-12 w-12 text-white animate-pulse" />
                </div>

                <h2 class="text-3xl md:text-4xl font-black text-white mb-4 tracking-tight leading-tight">
                    {{ __('events::messages.waiting_payment') ?? 'Aguardando Pagamento' }}
                </h2>
                <p class="text-amber-50 font-medium text-lg leading-relaxed max-w-lg mx-auto">
                    {{ __('events::messages.registration_pending_public_msg') ?? 'Sua inscrição foi criada com sucesso, mas está aguardando confirmação de pagamento.' }}
                </p>
            </div>

            <!-- Details Section -->
            <div class="p-10">
                <h3 class="flex items-center text-xs font-black text-gray-400 uppercase tracking-widest mb-8">
                    <span class="flex-grow border-t border-gray-100 dark:border-gray-800 mr-4"></span>
                    {{ __('events::messages.registration_details') ?? 'Detalhes da Inscrição' }}
                    <span class="flex-grow border-t border-gray-100 dark:border-gray-800 ml-4"></span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="group p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.registration_number') ?? 'Número da Inscrição' }}</p>
                        <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">#{{ $registration->id }}</p>
                    </div>

                    <div class="group p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('events::messages.total_value') ?? 'Valor Total' }}</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white">
                            R$ {{ number_format($registration->total_amount, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center space-y-4 mb-10">
                     <span class="px-6 py-2 rounded-full uppercase tracking-wider bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-black text-sm">
                        {{ $registration->status_display }}
                    </span>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic max-w-md text-center">
                        {{ __('events::messages.payment_instructions_msg') ?? 'Você receberá um email com as instruções de pagamento em breve. Após a confirmação do pagamento, sua inscrição será confirmada automaticamente.' }}
                    </p>
                </div>

                <div class="flex justify-center pt-2">
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

