@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero (padrão Configurações) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Cobertura</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">{{ ucfirst($cepRange->tipo ?? 'Geral') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ $cepRange->cidade }} — {{ $cepRange->uf }}</h1>
                    <p class="text-gray-300 max-w-xl">Detalhes da faixa de CEP e validador para verificar se um código postal pertence a esta região.</p>
                </div>
                <div class="flex flex-shrink-0 gap-3">
                    <a href="{{ route('admin.cep-ranges.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5 text-blue-300" />
                        Voltar
                    </a>
                    <a href="{{ route('admin.cep-ranges.edit', $cepRange) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="pen-to-square" class="w-5 h-5 text-blue-600" />
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Conteúdo principal -->
            <div class="flex-1 min-w-0 space-y-8">
                <!-- Card: Dados da faixa -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <x-icon name="map-pin" class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Localização</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cepRange->cidade }}, {{ $cepRange->uf }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início da Faixa</span>
                                    <x-icon name="flag-checkered" class="w-4 h-4 text-gray-400" />
                                </div>
                                <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white">{{ \App\Services\CepService::formatar($cepRange->cep_de) }}</p>
                            </div>
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fim da Faixa</span>
                                    <x-icon name="flag-pennant" class="w-4 h-4 text-gray-400" />
                                </div>
                                <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white">{{ \App\Services\CepService::formatar($cepRange->cep_ate) }}</p>
                            </div>
                        </div>
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <x-icon name="calculator" class="w-6 h-6" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abrangência Total</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">
                                    {{ number_format((int) $cepRange->cep_ate - (int) $cepRange->cep_de + 1, 0, ',', '.') }} <span class="text-sm font-medium text-gray-500 dark:text-gray-400">códigos postais</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Validador de CEP -->
                <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
                    <div class="absolute inset-0 dash-pattern opacity-10"></div>
                    <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-blue-300">
                                <x-icon name="magnifying-glass-location" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Validador de CEP</h3>
                                <p class="text-gray-400 text-sm">Verifique se um código postal pertence a esta faixa.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 flex items-center gap-2 rounded-xl bg-white/10 border border-white/20 focus-within:bg-white/15 focus-within:border-white/30 transition-all">
                                <span class="pl-4 flex items-center justify-center text-gray-500 shrink-0"><x-icon name="hashtag" class="w-5 h-5" /></span>
                                <input type="text" id="test_cep" data-mask="cep" placeholder="00000-000"
                                    class="flex-1 min-w-0 py-3 pr-4 pl-0 bg-transparent border-0 text-white placeholder:text-gray-500 font-mono font-bold outline-none focus:ring-0">
                            </div>
                            <button type="button" onclick="testCep()"
                                class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-colors shadow-lg flex items-center justify-center gap-2">
                                <x-icon name="magnifying-glass" class="w-5 h-5" />
                                Testar
                            </button>
                        </div>
                        <div id="test_result" class="mt-6 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-72 flex-shrink-0 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider mb-4 px-1">Metadados</p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <x-icon name="clock" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Criado em</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cepRange->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <x-icon name="clock-rotate-left" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Atualizado em</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cepRange->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <x-icon name="tag" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Classificação</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ ucfirst($cepRange->tipo ?? 'Geral') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-3xl border border-blue-100 dark:border-blue-800/30 p-6 text-center">
                    <x-icon name="circle-info" class="w-10 h-10 text-blue-600 dark:text-blue-400 mx-auto mb-3" />
                    <p class="text-xs text-blue-800 dark:text-blue-300 font-medium leading-relaxed">
                        Esta faixa é usada para cálculos de frete e restrições de entrega na logística.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testCep() {
            var cepInput = document.getElementById('test_cep');
            var resultDiv = document.getElementById('test_result');
            var cep = cepInput.value.replace(/\D/g, '');

            if (cep.length !== 8) {
                resultDiv.innerHTML = '<div class="p-4 bg-red-500/20 border border-red-500/30 rounded-2xl flex items-center gap-3"><span class="text-red-300 font-bold text-sm">Formato inválido (informe 8 dígitos)</span></div>';
                resultDiv.classList.remove('hidden');
                return;
            }

            var cepDe = parseInt('{{ $cepRange->cep_de }}');
            var cepAte = parseInt('{{ $cepRange->cep_ate }}');
            var cepInt = parseInt(cep);

            if (cepInt >= cepDe && cepInt <= cepAte) {
                resultDiv.innerHTML = '<div class="p-5 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl flex items-center gap-4"><div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-white"></i></div><div><p class="text-emerald-200 font-bold">CEP válido!</p><p class="text-emerald-300/80 text-sm">O código ' + cepInput.value + ' está nesta faixa.</p></div></div>';
            } else {
                resultDiv.innerHTML = '<div class="p-5 bg-red-500/20 border border-red-500/30 rounded-2xl flex items-center gap-4"><div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-xmark text-white"></i></div><div><p class="text-red-200 font-bold">Fora da faixa</p><p class="text-red-300/80 text-sm">O código ' + cepInput.value + ' não pertence a esta região.</p></div></div>';
            }
            resultDiv.classList.remove('hidden');
        }
    </script>
@endsection
