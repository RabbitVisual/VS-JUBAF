@extends('homepage::components.layouts.master')

@section('title', ($image->title ?: 'Imagem da Galeria') . ' - Igreja Batista Avenida')

@section('meta')
    <meta property="og:title" content="{{ $image->title ?: 'Imagem da Galeria' }}">
    <meta property="og:description" content="{{ $image->description ?: 'Imagem da galeria da Igreja Batista Avenida' }}">
    <meta property="og:image" content="{{ asset($image->image_url) }}">
    <meta property="og:type" content="article">
@endsection

@section('content')
    <!-- Immersive Hero Background -->
    <div class="fixed inset-0 bg-gray-900 -z-10">
        <div class="absolute inset-0 bg-linear-to-br from-teal-900/50 to-emerald-900/50"></div>
        <img src="{{ asset($image->image_url) }}" class="w-full h-full object-cover opacity-20 blur-3xl scale-110">
    </div>

    <!-- Image Detail Section -->
    <section class="py-24 min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('gallery.index') }}"
                    class="inline-flex items-center text-teal-200 hover:text-white transition-colors bg-black/20 hover:bg-black/40 px-4 py-2 rounded-full backdrop-blur-sm">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                    Voltar à Galeria
                </a>
            </div>

            <div class="grid lg:grid-cols-4 gap-8 items-start">
                <!-- Main Image (Theater Mode) -->
                <div class="lg:col-span-3">
                    <div class="group relative bg-black rounded-2xl shadow-2xl overflow-hidden border border-white/10">
                        <img src="{{ asset($image->image_url) }}"
                            alt="{{ $image->title ?: 'Imagem da galeria' }}"
                            class="w-full h-auto max-h-[80vh] object-contain mx-auto">

                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-between p-6">
                            <button onclick="downloadImage()" class="text-white hover:text-emerald-400 flex items-center gap-2 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg backdrop-blur-md transition-all">
                                <x-icon name="download" style="duotone" class="w-5 h-5" />
                                <span>Baixar Original</span>
                            </button>

                             <button onclick="toggleFullScreen()" class="text-white hover:text-emerald-400 p-2 bg-white/10 hover:bg-white/20 rounded-lg backdrop-blur-md transition-all">
                                <x-icon name="maximize" style="duotone" class="w-6 h-6" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Details Card -->
                    <div class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20 dark:border-gray-700">
                        @if($image->category)
                            <span class="inline-block px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider rounded-full mb-4">
                                {{ ucfirst($image->category) }}
                            </span>
                        @endif

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
                            {{ $image->title ?: 'Sem título' }}
                        </h1>

                        @if($image->description)
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm mb-6">
                                {{ $image->description }}
                            </p>
                        @else
                            <p class="text-gray-500 italic text-sm mb-6">Sem descrição.</p>
                        @endif

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Compartilhar</h3>
                            <div class="flex gap-2">
                                <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                    Facebook
                                </button>
                                <button onclick="shareOnWhatsApp()" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                    WhatsApp
                                </button>
                            </div>
                            <button onclick="copyImageLink()" class="w-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 py-2 rounded-lg text-sm font-medium transition-colors">
                                Copiar Link
                            </button>
                        </div>
                    </div>

                    <!-- Mini Navigation -->
                     <div class="grid grid-cols-2 gap-4">
                        <!-- Placeholder for prev/next logic if implemented later -->
                        <a href="{{ route('gallery.index') }}" class="col-span-2 bg-black/40 hover:bg-black/60 text-white py-3 rounded-xl text-center backdrop-blur-sm transition-colors text-sm font-medium border border-white/10">
                            Explorar Mais Fotos
                        </a>
                     </div>
                </div>
            </div>

            <!-- Related Images -->
            @if($relatedImages->count() > 0)
                <div class="mt-20">
                    <h3 class="text-2xl font-bold text-white mb-8">Veja Também</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($relatedImages as $related)
                            <a href="{{ route('gallery.show', $related) }}"
                                class="group relative aspect-square overflow-hidden rounded-xl shadow-lg border border-white/10">
                                <img src="{{ $related->image_url }}"
                                    alt="{{ $related->title ?: 'Imagem da galeria' }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    loading="lazy">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-300"></div>
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
            const title = encodeURIComponent("{{ $image->title ?: 'Imagem da Galeria' }} - Igreja Batista Avenida");
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent("{{ $image->title ?: 'Imagem da Galeria' }} - Igreja Batista Avenida\n\n{{ route('gallery.show', $image) }}");
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function downloadImage() {
            const link = document.createElement('a');
            link.href = '{{ asset($image->image_url) }}';
            link.download = '{{ $image->title ?: 'imagem-galeria' }}.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function copyImageLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = 'Copiado!';
                button.classList.add('text-green-600', 'dark:text-green-400');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('text-green-600', 'dark:text-green-400');
                }, 2000);
            });
        }

        function toggleFullScreen() {
            const img = document.querySelector('.group.relative.bg-black img');
            if (img.requestFullscreen) {
                img.requestFullscreen();
            } else if (img.webkitRequestFullscreen) { /* Safari */
                img.webkitRequestFullscreen();
            } else if (img.msRequestFullscreen) { /* IE11 */
                img.msRequestFullscreen();
            }
        }
    </script>
@endsection

