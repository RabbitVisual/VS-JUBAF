@extends('memberpanel::components.layouts.master')

@section('title', 'Parabéns!')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center -mt-10">
    <div class="max-w-xl w-full mx-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden text-center relative p-8 md:p-12">

            <!-- Confetti/Celebration Background Effect -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 left-0 w-full h-2 bg-linear-to-r from-purple-500 via-pink-500 to-red-500"></div>
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-100 dark:bg-purple-900/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-100 dark:bg-blue-900/20 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <div class="w-24 h-24 mx-auto bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mb-6 animate-bounce shadow-lg">
                    <x-icon name="circle-check" style="duotone" class="w-12 h-12" />
                </div>

                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">Leitura Concluída!</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400 font-medium mb-8">Dia {{ $day->day_number }} • {{ $subscription->plan->title }}</p>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-2xl p-6 mb-10 transform rotate-1 hover:rotate-0 transition-transform duration-300">
                    <x-icon name="quote-left" class="w-8 h-8 text-purple-400 mx-auto mb-3" />
                    <p class="text-gray-800 dark:text-purple-100 italic font-serif text-lg leading-relaxed">
                        "Lâmpada para os meus pés é a tua palavra, e luz para o meu caminho."
                    </p>
                    <p class="text-xs text-purple-500 font-bold uppercase tracking-wider mt-3">— Salmos 119:105</p>
                </div>

                <div class="space-y-4">
                    @if($nextDay)
                        <a href="{{ route('member.bible.reader', ['subscriptionId' => $subscription->id, 'day' => $nextDay]) }}" class="w-full inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-purple-600 rounded-xl hover:bg-purple-700 hover:shadow-lg hover:-translate-y-1 transform group">
                            Próxima Leitura
                            <x-icon name="arrow-right" style="duotone" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                        </a>
                    @else
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl text-green-700 dark:text-green-300 font-bold mb-4">
                            🎉 Plano Finalizado com Sucesso!
                        </div>
                    @endif

                    <a href="{{ route('member.bible.plans.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 text-base font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Voltar para Meus Planos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

