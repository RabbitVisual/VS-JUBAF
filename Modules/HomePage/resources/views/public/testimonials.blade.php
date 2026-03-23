@extends('homepage::components.layouts.master')

@section('title', 'Testemunhos - Igreja Batista Avenida')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[40vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-br from-purple-900 to-pink-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2 z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <span class="inline-block py-1 px-3 rounded-full bg-pink-500/20 border border-pink-400/30 text-pink-200 text-sm font-semibold tracking-wider uppercase mb-4 backdrop-blur-sm">
                Histórias Reais
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">
                Vidas <span class="text-transparent bg-clip-text bg-linear-to-r from-pink-400 to-purple-400">Transformadas</span>
            </h1>
            <p class="text-xl md:text-2xl text-pink-100 max-w-3xl mx-auto font-light leading-relaxed mb-10">
                Conheça as histórias de fé e transformação da nossa comunidade. Deus continua escrevendo lindas histórias!
            </p>

            <!-- Modern Search Form -->
            <div class="max-w-2xl mx-auto">
                <form method="GET" action="{{ route('testimonials.index') }}" class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-r from-pink-600 to-purple-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                    <div class="relative flex items-center bg-white/10 dark:bg-gray-900/50 backdrop-blur-md border border-white/20 dark:border-gray-700/50 rounded-xl p-2 shadow-2xl">
                        <x-icon name="magnifying-glass" style="duotone" class="w-6 h-6 text-gray-400 ml-3" />
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar testemunhos por nome..."
                            class="w-full bg-transparent border-none text-white placeholder-gray-400 focus:ring-0 px-4 py-2 text-lg">
                        <button type="submit"
                            class="px-6 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg shadow-pink-500/30">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @if($testimonials->count() > 0)
                <!-- Results Info -->
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Depoimentos Recentes</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 px-4 py-2 rounded-full shadow-sm border border-gray-100 dark:border-gray-800">
                        @if(request('search'))
                            Encontrados <span class="font-bold text-pink-600 dark:text-pink-400">{{ $testimonials->total() }}</span> resultado(s)
                        @else
                            Mostrando <span class="font-bold text-pink-600 dark:text-pink-400">{{ $testimonials->count() }}</span> de {{ $testimonials->total() }} testemunhos
                        @endif
                    </p>
                </div>

                <!-- Testimonials Grid -->
                <!-- Masonry-like Layout for Quotes -->
                <div class="columns-1 md:columns-2 lg:columns-3 gap-8 space-y-8">
                    @foreach($testimonials as $testimonial)
                        <div class="break-inside-avoid group relative bg-white dark:bg-gray-900 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 p-8 border border-gray-100 dark:border-gray-800 hover:-translate-y-2 transition-all duration-300">
                            <!-- Large Quote Icon Watermark -->
                            <div class="absolute top-4 right-6 text-9xl font-serif text-gray-100 dark:text-gray-800 opacity-50 z-0 pointer-events-none select-none">"</div>

                            <div class="relative z-10">
                                <!-- User Info Header -->
                                <div class="flex items-center mb-6">
                                    <div class="relative">
                                        @if($testimonial->photo)
                                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-pink-100 dark:border-pink-900/50 shadow-sm">
                                                <img src="{{ Storage::url($testimonial->photo) }}"
                                                    alt="{{ $testimonial->name }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-14 h-14 rounded-full bg-linear-to-br from-pink-500 to-purple-600 flex items-center justify-center shadow-lg shadow-pink-500/30">
                                                <span class="text-white font-bold text-xl">{{ substr($testimonial->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-900 rounded-full p-1">
                                            <div class="bg-green-500 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900"></div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-bold text-gray-900 dark:text-white text-lg leading-tight">{{ $testimonial->name }}</h4>
                                        @if($testimonial->position)
                                            <p class="text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wide">{{ $testimonial->position }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quote Content -->
                                <blockquote class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 font-light italic relative">
                                    "{{ Str::limit($testimonial->testimonial, 250) }}"
                                </blockquote>

                                <!-- Action -->
                                <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-800/50">
                                    <a href="{{ route('testimonials.show', $testimonial) }}" class="inline-flex items-center text-sm font-bold text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 transition-colors group-hover:translate-x-1 duration-200">
                                        Ler História Completa
                                        <x-icon name="arrow-right" style="duotone" class="w-4 h-4 ml-1" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-16">
                    {{ $testimonials->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Testimonials -->
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-pink-50 dark:bg-pink-900/20 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="message-dots" style="duotone" class="w-12 h-12 text-pink-300 dark:text-pink-700" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Nenhum testemunho encontrado</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md">
                        @if(request('search'))
                            Não encontramos histórias para "<span class="font-semibold text-pink-600">{{ request('search') }}</span>".
                        @else
                            Ainda não temos histórias publicadas. Seja o primeiro a compartilhar!
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('testimonials.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-xl shadow-lg shadow-pink-500/30 font-medium transition-all hover:-translate-y-1">
                            Ver Todos os Testemunhos
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection

