@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-purple-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Etiquetas</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Imprimir Etiquetas</h1>
                    <p class="text-gray-300 max-w-xl">Selecione os itens e gere etiquetas com QR Code para impressão.</p>
                </div>
                <a href="{{ route('assets.admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('assets.admin.labels.print') }}" method="POST" target="_blank" class="space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Gerando etiquetas...' } }))">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex items-start gap-3 mb-6">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <p class="text-sm text-blue-800 dark:text-blue-200">Selecione os itens para gerar etiquetas. A impressão abrirá em uma nova aba com QR Code e código de cada item.</p>
                </div>

                <div class="relative overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 w-10">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Código</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach ($assets as $asset)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="assets[]" value="{{ $asset->id }}" class="item-checkbox rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $asset->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $asset->code }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>

                <div class="relative flex justify-between items-center pt-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400" id="selectedCount">0 itens selecionados</span>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all">
                        Gerar Etiquetas
                    </button>
                </div>
            </div>
        </form>

        <script>
            document.getElementById('selectAll').addEventListener('change', function(e) {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
                updateCount();
            });

            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateCount);
            });

            function updateCount() {
                const count = document.querySelectorAll('.item-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = count + ' itens selecionados';
            }
        </script>
    </div>
@endsection

