@extends('memberpanel::components.layouts.master')

@section('title', 'Catálogo de Planos | Bíblia')

@section('content')
    <div class="max-w-7xl mx-auto space-y-10 pb-12">

        <!-- Search & Filter Header -->
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 border-b border-gray-200 dark:border-gray-700 pb-6">
            <div>
                <a href="{{ route('member.bible.plans.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-purple-600 mb-2 transition-colors">
                    <x-icon name="chevron-left" style="duotone" class="h-4 w-4 mr-1" />
                    Voltar aos meus planos
                </a>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Explorar Planos</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Descubra novos roteiros para aprofundar seu conhecimento bíblico.</p>
            </div>

            <!-- Search Bar -->
            <form action="{{ route('member.bible.plans.catalog') }}" method="GET" class="w-full md:w-auto relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar planos..." class="w-full md:w-64 pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all dark:text-white">
                <div class="absolute left-3 top-2.5 text-gray-400 pointer-events-none">
                    <x-icon name="magnifying-glass" style="duotone" class="h-5 w-5" />
                </div>
            </form>
        </div>

        @if($featuredPlans->isNotEmpty())
            <!-- Featured Section -->
            <section>
                <div class="flex items-center gap-2 mb-6">
                    <div class="p-1.5 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <x-icon name="star" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Destaques da Semana</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @foreach($featuredPlans as $plan)
                        <div class="relative group bg-slate-900 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 border border-slate-800 h-96 flex flex-col justify-end">
                            <!-- Background Image (simulated) -->
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900 via-slate-900/50 to-transparent z-10 transition-opacity duration-500"></div>
                            @if(isset($plan->cover_image))
                                <img src="{{ Storage::url($plan->cover_image) }}" class="absolute inset-0 w-full h-full object-cover z-0 group-hover:scale-105 transition-transform duration-700 opacity-60">
                            @else
                                <div class="absolute inset-0 bg-linear-to-br from-indigo-600 to-purple-800 z-0"></div>
                            @endif

                            <div class="relative z-20 p-8 space-y-4 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white uppercase tracking-wider border border-white/10">
                                    {{ ucfirst($plan->type) }}
                                </span>
                                <h3 class="text-3xl font-black text-white leading-tight shadow-sm">{{ $plan->title }}</h3>
                                <p class="text-slate-300 line-clamp-2 text-sm">{{ $plan->description }}</p>

                                <div class="pt-4 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
                                    <span class="text-white font-medium flex items-center gap-1">
                                        <x-icon name="clock" class="w-4 h-4 text-slate-400" />
                                        {{ $plan->duration_days }} dias
                                    </span>
                                    <a href="{{ route('member.bible.plans.preview', $plan->id) }}" class="px-5 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm hover:bg-slate-100 transition-colors shadow-lg">
                                        Começar Agora
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- All Plans Grid -->
        <section>
            <div class="flex items-center gap-2 mb-6">
                <div class="p-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <x-icon name="view-grid" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Todos os Planos</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($allPlans as $plan)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col group overflow-hidden">
                    @if($plan->cover_image)
                        <div class="h-48 overflow-hidden relative bg-gray-100 dark:bg-gray-700">
                            <img src="{{ Storage::url($plan->cover_image) }}" alt="{{ $plan->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 right-3 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-sm text-gray-700 dark:text-gray-200 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                {{ $plan->duration_days }} Dias
                            </div>
                        </div>
                        <div class="p-6 flex-1">
                             <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                {{ $plan->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-5 leading-relaxed">
                                {{ $plan->description }}
                            </p>
                        </div>
                    @else
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                                    <x-icon name="book-open" class="h-6 w-6" />
                                </div>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                    {{ $plan->duration_days }} Dias
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                {{ $plan->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-5 leading-relaxed">
                                {{ $plan->description }}
                            </p>
                        </div>
                    @endif

                        <div class="px-6 pb-6 pt-2">
                            <a href="{{ route('member.bible.plans.preview', $plan->id) }}" class="block w-full text-center py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 font-bold text-sm text-gray-600 dark:text-gray-300 hover:bg-purple-600 hover:border-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:border-purple-600 dark:hover:text-white transition-all">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $allPlans->links() }}
            </div>
        </section>
    </div>
@endsection

