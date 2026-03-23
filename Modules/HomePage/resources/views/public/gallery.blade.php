@extends('homepage::components.layouts.master')

@section('title', 'Galeria - Igreja Batista Avenida')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[40vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-br from-teal-900 to-emerald-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2 z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <span class="inline-block py-1 px-3 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-sm font-semibold tracking-wider uppercase mb-4 backdrop-blur-sm">
                Nossa Memória
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">
                Galeria de <span class="text-transparent bg-clip-text bg-linear-to-r from-emerald-400 to-teal-400">Momentos</span>
            </h1>
            <p class="text-xl md:text-2xl text-emerald-100 max-w-3xl mx-auto font-light leading-relaxed mb-10">
                Momentos especiais, celebrações e a vida da nossa comunidade registrada em imagens.
            </p>

            <!-- Modern Search & Filter Form -->
            <div class="max-w-4xl mx-auto">
                <form method="GET" action="{{ route('gallery.index') }}" class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-r from-teal-600 to-emerald-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                    <div class="relative flex flex-col md:flex-row items-center gap-2 bg-white/10 dark:bg-gray-900/50 backdrop-blur-md border border-white/20 dark:border-gray-700/50 rounded-xl p-2 shadow-2xl">

                        <!-- Search Input -->
                        <div class="flex-1 w-full flex items-center px-2">
                            <x-icon name="magnifying-glass" style="duotone" class="w-6 h-6 text-gray-400 ml-2" />
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar imagens..."
                                class="w-full bg-transparent border-none text-white placeholder-gray-400 focus:ring-0 px-4 py-2 text-lg">
                        </div>

                        <!-- Divider -->
                        <div class="hidden md:block w-px h-8 bg-white/20 mx-2"></div>

                        <!-- Category Select -->
                        <div class="w-full md:w-64 relative">
                            <select name="category" class="w-full bg-transparent border-none text-white focus:ring-0 px-4 py-2 text-lg appearance-none cursor-pointer">
                                <option value="" class="text-gray-900">Todas as categorias</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }} class="text-gray-900">
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" style="duotone" class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>

                        <button type="submit"
                            class="w-full md:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg shadow-emerald-500/30 whitespace-nowrap">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @if($images->count() > 0)
                <!-- Results Info -->
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Galeria Recente</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 px-4 py-2 rounded-full shadow-sm border border-gray-100 dark:border-gray-800">
                        @if(request('search') || request('category'))
                            Encontrados <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $images->total() }}</span> resultado(s)
                        @else
                            Mostrando <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $images->count() }}</span> de {{ $images->total() }} imagens
                        @endif
                    </p>
                </div>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 auto-rows-[200px]">
                    @foreach($images as $image)
                        @php
                            // Randomize span for masonry feel (simulated)
                            $rowSpan = $loop->iteration % 5 == 0 || $loop->iteration % 7 == 0 ? 'row-span-2' : 'row-span-1';
                            $colSpan = $loop->iteration % 7 == 0 ? 'sm:col-span-2' : 'col-span-1';
                        @endphp

                        <div class="group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 {{ $rowSpan }} {{ $colSpan }}">
                            <div class="absolute inset-0 bg-gray-200 dark:bg-gray-800 animate-pulse"></div>
                            <img src="{{ $image->image_url }}"
                                alt="{{ $image->title ?: 'Imagem da galeria' }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 relative z-10"
                                loading="lazy">

                            <!-- Premium Overlay -->
                            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20 flex flex-col justify-end p-6">
                                <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                    @if($image->category)
                                        <span class="inline-block px-2 py-1 mb-2 bg-emerald-500/80 text-white text-xs font-bold rounded-md backdrop-blur-sm">
                                            {{ ucfirst($image->category) }}
                                        </span>
                                    @endif

                                    @if($image->title)
                                        <h4 class="font-bold text-white text-lg leading-tight mb-1">{{ $image->title }}</h4>
                                    @endif

                                    <div class="mt-3 flex items-center justify-between">
                                        <a href="{{ route('gallery.show', $image) }}"
                                            class="text-sm font-medium text-emerald-300 hover:text-white flex items-center transition-colors">
                                            Ver Imagem
                                            <x-icon name="arrow-right" style="duotone" class="w-4 h-4 ml-1" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-16">
                    {{ $images->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Images -->
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="image" style="duotone" class="w-12 h-12 text-emerald-300 dark:text-emerald-700" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Nenhuma imagem encontrada</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md">
                        @if(request('search') || request('category'))
                            Não encontramos resultados para sua busca. Tente outros termos.
                        @else
                            Nenhuma imagem foi adicionada à galeria ainda.
                        @endif
                    </p>
                    @if(request('search') || request('category'))
                        <a href="{{ route('gallery.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg shadow-emerald-500/30 font-medium transition-all hover:-translate-y-1">
                            Limpar Filtros
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection

