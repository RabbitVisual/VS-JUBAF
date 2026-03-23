<footer class="bg-gray-900 border-t border-gray-800 text-gray-400 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
            <div class="space-y-4">
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider">{{ __('marketplace::messages.name') }}</h3>
                <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <li><a href="{{ route('marketplace.storefront.policy', 'politica-entrega') }}" class="text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                        <x-icon name="truck-ramp-box" style="duotone" class="w-4 h-4 text-gray-500" /> Política de Entrega
                    </a></li>
                    <li><a href="{{ route('marketplace.storefront.policy', 'trocas') }}" class="text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                        <x-icon name="rotate-left" style="duotone" class="w-4 h-4 text-gray-500" /> Trocas e Devoluções
                    </a></li>
                    <li><a href="{{ route('marketplace.storefront.policy', 'termos') }}" class="text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                        <x-icon name="file-lines" style="duotone" class="w-4 h-4 text-gray-500" /> Termos e Privacidade
                    </a></li>
                </ul>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                    <x-icon name="shield-halved" style="duotone" class="w-5 h-5 text-green-500" />
                    <span>Compra Segura</span>
                </div>
                <div class="inline-flex items-center gap-3 text-sm text-gray-400">
                    <span>Formas de pagamento:</span>
                    <span class="font-medium text-white">PIX</span>
                    <span class="font-medium text-white">Cartão</span>
                </div>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-gray-800 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Loja Missionária.
        </div>
    </div>
</footer>
