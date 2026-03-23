@extends('homepage::components.layouts.master')

@section('title', 'Testemunho de ' . $testimonial->name . ' - Igreja Batista Avenida')

@section('meta')
    <meta property="og:title" content="Testemunho de {{ $testimonial->name }}">
    <meta property="og:description" content="{{ Str::limit($testimonial->testimonial, 160) }}">
    <meta property="og:type" content="article">
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[50vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-br from-pink-900 to-purple-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-pink-500/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2 z-0"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
            <a href="{{ route('testimonials.index') }}" class="inline-flex items-center text-pink-300 hover:text-white transition-colors mb-8 font-medium tracking-wide bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm hover:bg-white/20">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar aos Testemunhos
            </a>

            <div class="relative inline-block mb-8">
                @if($testimonial->photo)
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-white/20 shadow-2xl relative z-10 mx-auto">
                        <img src="{{ Storage::url($testimonial->photo) }}"
                            alt="{{ $testimonial->name }}"
                            class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-linear-to-br from-pink-500 to-purple-600 flex items-center justify-center border-4 border-white/20 shadow-2xl relative z-10 mx-auto">
                        <span class="text-white font-bold text-5xl">{{ substr($testimonial->name, 0, 1) }}</span>
                    </div>
                @endif
                <!-- Decorative Ring -->
                <div class="absolute inset-0 -m-3 border border-white/10 rounded-full animate-pulse z-0"></div>
            </div>

            <h1 class="text-4xl md:text-6xl font-black text-white mb-4 tracking-tight leading-tight">
                {{ $testimonial->name }}
            </h1>
            @if($testimonial->position)
                <p class="text-xl md:text-2xl text-pink-200 font-light tracking-wide uppercase">{{ $testimonial->position }}</p>
            @endif
        </div>

        <!-- Decorative Bottom Curve -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
            <svg class="relative block w-full h-12 md:h-24 text-white dark:text-gray-950 fill-current" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
            </svg>
        </div>
    </section>

    <!-- Testimonial Content -->
    <section class="pb-20 pt-10 bg-white dark:bg-gray-950 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-20 z-20">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-8 md:p-16 border border-gray-100 dark:border-gray-800">
                    <!-- Giant Quote Icon -->
                    <div class="absolute top-10 left-10 text-8xl text-pink-100 dark:text-gray-800 font-serif opacity-50 z-0 select-none">"</div>

                    <div class="relative z-10">
                        <blockquote class="text-xl md:text-2xl text-gray-700 dark:text-gray-200 leading-relaxed font-light italic text-center mb-12">
                            "{{ $testimonial->testimonial }}"
                        </blockquote>

                        <div class="flex items-center justify-center mb-12">
                            <div class="w-16 h-1 bg-linear-to-r from-pink-500 to-purple-500 rounded-full"></div>
                        </div>

                        <!-- Share Section -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-8 text-center">
                            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-6">Compartilhar esta História</h3>
                            <div class="flex justify-center flex-wrap gap-4">
                                <button onclick="shareOnFacebook()"
                                    class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-all hover:-translate-y-1 shadow-md hover:shadow-lg">
                                    <x-icon name="facebook" style="brands" class="w-6 h-6" />
                                </button>

                                <button onclick="shareOnWhatsApp()"
                                    class="w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-all hover:-translate-y-1 shadow-md hover:shadow-lg">
                                    <x-icon name="whatsapp" style="brands" class="w-6 h-6" />
                                </button>

                                <button onclick="shareOnTwitter()"
                                    class="w-12 h-12 bg-sky-500 hover:bg-sky-600 text-white rounded-full flex items-center justify-center transition-all hover:-translate-y-1 shadow-md hover:shadow-lg">
                                    <x-icon name="x-twitter" style="brands" class="w-6 h-6" />
                                </button>

                                <button onclick="copyTestimonialLink()"
                                    class="w-12 h-12 bg-gray-600 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition-all hover:-translate-y-1 shadow-md hover:shadow-lg">
                                    <x-icon name="link" style="duotone" class="w-6 h-6" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Testimonials -->
            @php
                $relatedTestimonials = \Modules\HomePage\App\Models\Testimonial::active()
                    ->where('id', '!=', $testimonial->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
            @endphp

            @if($relatedTestimonials->count() > 0)
                <div class="mt-24">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-10 text-center tracking-tight">Outras histórias inspiradoras</h3>
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($relatedTestimonials as $related)
                            <a href="{{ route('testimonials.show', $related) }}" class="group block bg-white dark:bg-gray-900 rounded-3xl p-8 border border-gray-100 dark:border-gray-800 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold">
                                        {{ substr($related->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-bold text-gray-900 dark:text-white group-hover:text-pink-600 transition-colors">{{ $related->name }}</p>
                                    </div>
                                </div>
                                <blockquote class="text-gray-600 dark:text-gray-400 text-sm italic leading-relaxed line-clamp-3 mb-4">
                                    "{{ Str::limit($related->testimonial, 100) }}"
                                </blockquote>
                                <span class="text-pink-600 dark:text-pink-400 text-sm font-semibold flex items-center">
                                    Ler mais
                                    <x-icon name="arrow-right" style="duotone" class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" />
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script>
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent("Testemunho de {{ $testimonial->name }} - Igreja Batista Avenida");
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent("Testemunho de {{ $testimonial->name }} - Igreja Batista Avenida\n\n{{ route('testimonials.show', $testimonial) }}");
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent("Testemunho de {{ $testimonial->name }} - Igreja Batista Avenida");
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }

        function copyTestimonialLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;

                button.innerHTML = `
                    <x-icon name="check" style="duotone" class="w-6 h-6 text-green-500" />
                `;
                button.classList.add('bg-green-100', 'hover:bg-green-200');

                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('bg-green-100', 'hover:bg-green-200');
                }, 2000);
            });
        }
    </script>
@endsection

