@extends('memberpanel::components.layouts.master')

@section('title', 'Minha Jornada Bíblica')

@push('styles')
<style>[x-cloak]{display:none!important}</style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto space-y-12">

        <!-- Hero Section -->
        <div class="relative rounded-3xl overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-linear-to-br from-indigo-900 via-purple-900 to-slate-900 opacity-95 z-10"></div>
            <!-- Decorative Background -->
            <div class="absolute inset-0 z-0 opacity-20">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid-pattern" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid-pattern)" />
                </svg>
            </div>

            <div class="relative z-20 px-8 py-12 md:px-12 md:py-16 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-2xl text-center md:text-left">
                    <span class="inline-block py-1 px-3 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-200 text-xs font-semibold uppercase tracking-wider mb-4">
                        Discipulado Digital
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4 tracking-tight">
                        Sua Jornada <br>
                        <span class="text-transparent bg-clip-text bg-linear-to-r from-purple-300 to-indigo-300">Pelas Escrituras</span>
                    </h1>
                    <p class="text-lg text-indigo-100 leading-relaxed max-w-xl mx-auto md:mx-0">
                        "Lâmpada para os meus pés é a tua palavra e luz para o meu caminho." <br>
                        <span class="text-sm opacity-70">- Salmos 119:105</span>
                    </p>
                </div>

                <!-- Action Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl w-full md:w-auto min-w-[300px] text-center md:text-left">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-indigo-500/30 rounded-xl text-white">
                            <x-icon name="plus" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold text-white">Começar Novo Plano</h3>
                            <p class="text-xs text-indigo-200">Explore novos temas e livros</p>
                        </div>
                    </div>
                    <a href="{{ route('member.bible.plans.catalog') }}" class="block w-full py-3 bg-white text-indigo-900 rounded-xl font-bold text-center hover:bg-indigo-50 transition-colors shadow-lg">
                        Explorar Catálogo
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <span class="w-1.5 h-8 bg-purple-600 rounded-full"></span>
                    Planos em Andamento
                </h2>
            </div>

            @if($subscriptions->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border-2 border-dashed border-gray-200 dark:border-gray-700 p-12 text-center group hover:border-purple-500/50 transition-colors">
                    <div class="w-20 h-20 bg-purple-50 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="book-open" class="w-10 h-10 text-purple-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sua estante está vazia</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Ainda não há planos ativos. Escolha um plano de leitura para começar a acompanhar seu progresso diário.
                    </p>
                    <a href="{{ route('member.bible.plans.catalog') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:-translate-y-1">
                        Ver Catálogo de Planos
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($subscriptions as $sub)
                        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full relative">
                            <!-- Card Header / Cover -->
                            <div class="h-32 bg-linear-to-r from-indigo-500 to-purple-600 relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

                                <div class="relative z-10 p-6 h-full flex flex-col justify-center">
                                    <h3 class="text-xl font-black text-white leading-tight drop-shadow-md line-clamp-2">
                                        {{ $sub->plan->title }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Progress Stripe (dynamic) -->
                            <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $sub->percent }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="h-full bg-linear-to-r from-purple-400 to-indigo-500 transition-all duration-500 rounded-full" style="width: {{ $sub->percent }}%"></div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Status da leitura</p>
                                    <p class="text-xl font-black text-gray-900 dark:text-white mb-4">
                                        Dia <span class="text-purple-600 dark:text-purple-400">{{ $sub->current_day_number }}</span> de <span class="text-gray-600 dark:text-gray-300">{{ $sub->total_days ?? $sub->plan->duration_days }}</span>
                                    </p>
                                    <div class="flex justify-between items-end mb-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Progresso</p>
                                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $sub->percent }}%</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dia Atual</p>
                                            <p class="text-sm font-bold text-purple-600 dark:text-purple-400">
                                                {{ $sub->current_day_number }} <span class="text-gray-400 font-normal">/ {{ $sub->total_days ?? $sub->plan->duration_days }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Mini Calendar Visual (Optional) or Description -->
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-6">
                                        {{ $sub->plan->description ?? 'Continue sua leitura diária para fortalecer sua fé.' }}
                                    </p>
                                </div>

                                <div class="space-y-3">
                                    <a href="{{ route('member.bible.reader', ['subscriptionId' => $sub->id, 'day' => $sub->current_day_number]) }}" class="flex items-center justify-center w-full py-3 bg-gray-50 dark:bg-gray-700 hover:bg-purple-600 dark:hover:bg-purple-600 text-gray-700 dark:text-gray-200 hover:text-white font-bold rounded-xl transition-all group-hover:shadow-lg gap-2 group/btn">
                                        <span>Continuar Leitura</span>
                                        <x-icon name="arrow-right" style="duotone" class="h-4 w-4 transition-transform group-hover/btn:translate-x-1" />
                                    </a>
                                    @if(!empty($sub->offer_recalculate))
                                        <div x-data="{ open: false }" class="w-full">
                                            <button type="button" @click="open = true" class="w-full py-2.5 text-sm font-bold text-amber-700 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 border border-amber-400/50 dark:border-amber-500/50 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all flex items-center justify-center gap-2">
                                                <x-icon name="arrows-rotate" class="w-4 h-4" />
                                                Recalcular Rotas
                                            </button>
                                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
                                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-700" @click.outside="open = false">
                                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Recalcular rotas de leitura</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                                        A leitura restante será redistribuída até a data final original do plano, evitando acúmulo de atrasos. Você continuará de onde parou, com os dias seguintes rebalanceados.
                                                    </p>
                                                    <div class="flex gap-3">
                                                        <button type="button" @click="open = false" class="flex-1 py-2.5 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                            Cancelar
                                                        </button>
                                                        <form method="POST" action="{{ route('member.bible.plans.recalculate', $sub->id) }}" class="flex-1">
                                                            @csrf
                                                            <button type="submit" class="w-full py-2.5 font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-colors">
                                                                Confirmar recálculo
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

