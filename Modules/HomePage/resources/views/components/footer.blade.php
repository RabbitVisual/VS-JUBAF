<!-- Footer -->
<footer class="bg-gray-900 border-t border-gray-800 text-gray-400 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8">
            <!-- About -->
            <div class="lg:col-span-5 space-y-6">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset(\App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png')) }}" alt="Logo" class="h-10 w-auto opacity-90">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Igreja Batista Avenida</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Coração de Maria - BA</p>
                    </div>
                </div>
                <p class="text-gray-400 leading-relaxed max-w-sm">
                    {{ $homepageSettings['hero_subtitle'] ?? 'Uma comunidade de fé comprometida com o Evangelho de Jesus Cristo e o serviço ao próximo.' }}
                </p>

                <div class="flex space-x-5 pt-2">
                    @if(!empty($homepageSettings['social_facebook']))
                    <a href="{{ $homepageSettings['social_facebook'] }}" target="_blank" class="text-gray-500 hover:text-blue-500 transition-colors duration-300 transform hover:-translate-y-1">
                        <span class="sr-only">Facebook</span>
                        <x-icon name="facebook" style="brands" class="h-6 w-6" />
                    </a>
                    @endif

                    @if(!empty($homepageSettings['social_instagram']))
                    <a href="{{ $homepageSettings['social_instagram'] }}" target="_blank" class="text-gray-500 hover:text-pink-500 transition-colors duration-300 transform hover:-translate-y-1">
                        <span class="sr-only">Instagram</span>
                        <x-icon name="instagram" style="brands" class="h-6 w-6" />
                    </a>
                    @endif

                    @if(!empty($homepageSettings['social_youtube']))
                    <a href="{{ $homepageSettings['social_youtube'] }}" target="_blank" class="text-gray-500 hover:text-red-600 transition-colors duration-300 transform hover:-translate-y-1">
                        <span class="sr-only">YouTube</span>
                        <x-icon name="youtube" style="brands" class="h-6 w-6" />
                    </a>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div class="lg:col-span-3">
                <h3 class="text-white font-semibold text-lg mb-6">Navegação</h3>
                <ul class="space-y-4">
                    <li><a href="#sobre" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Sobre Nós</a></li>
                    <li><a href="#ministerios" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Ministérios</a></li>
                    <li><a href="#eventos" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Próximos Eventos</a></li>
                    <li><a href="#contato" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Fale Conosco</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="lg:col-span-4">
                <h3 class="text-white font-semibold text-lg mb-6">Informações de Contato</h3>
                <ul class="space-y-4">
                    <li class="flex items-start group">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center mr-4 group-hover:bg-blue-600 transition-colors duration-300">
                             <x-icon name="location-dot" style="duotone" class="w-5 h-5 text-gray-400 group-hover:text-white" />
                        </div>
                        <span class="text-gray-400 mt-2">{{ $homepageSettings['contact_address'] ?? 'Avenida, Coração de Maria - BA' }}</span>
                    </li>
                    <li class="flex items-start group">
                         <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center mr-4 group-hover:bg-blue-600 transition-colors duration-300">
                             <x-icon name="phone" style="duotone" class="w-5 h-5 text-gray-400 group-hover:text-white" />
                        </div>
                        <span class="text-gray-400 mt-2">{{ $homepageSettings['contact_phone'] ?? '(75) 0000-0000' }}</span>
                    </li>
                    <li class="flex items-start group">
                         <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center mr-4 group-hover:bg-blue-600 transition-colors duration-300">
                             <x-icon name="envelope" style="duotone" class="w-5 h-5 text-gray-400 group-hover:text-white" />
                        </div>
                        <span class="text-gray-400 mt-2 break-all">{{ $homepageSettings['contact_email'] ?? 'contato@igrejabatistaavenida.com.br' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Igreja Batista Avenida. Todos os direitos reservados.</p>
            <p class="mt-4 md:mt-0 flex items-center">
                <span>Desenvolvido por</span>
                <a href="https://vertexsolutions.com.br" target="_blank" class="ml-2 text-white hover:text-blue-500 font-bold transition-colors">
                    Vertex Solutions
                </a>
            </p>
        </div>
    </div>
</footer>

