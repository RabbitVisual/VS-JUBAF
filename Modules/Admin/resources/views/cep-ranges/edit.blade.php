@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero (padrão Configurações) -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-indigo-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">Edição</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Faixa de CEP</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Editar Faixa de CEP</h1>
                    <p class="text-gray-300 max-w-xl">Atualize as informações de cobertura. As alterações afetam imediatamente a validação de endereços no sistema.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.cep-ranges.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5 text-indigo-300" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar (card estilo Configurações) -->
            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider mb-4 px-1">Valores Atuais</p>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">CEP Inicial</span>
                                <span class="font-mono font-bold text-gray-900 dark:text-white">{{ \App\Services\CepService::formatar($cepRange->cep_de) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">CEP Final</span>
                                <span class="font-mono font-bold text-gray-900 dark:text-white">{{ \App\Services\CepService::formatar($cepRange->cep_ate) }}</span>
                            </div>
                        </div>
                        <p class="mt-6 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            Verifique os intervalos com atenção para não sobrepor outras faixas.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div class="flex-1 min-w-0">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <form action="{{ route('admin.cep-ranges.update', $cepRange) }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando alterações...' } }))">
                            @csrf
                            @method('PUT')

                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="map-location-dot" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Localização</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">UF e cidade da faixa.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="uf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF <span class="text-red-500">*</span></label>
                                    <select name="uf" id="uf" required
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                                        <option value="">Selecione...</option>
                                        @foreach($ufs as $uf)
                                            <option value="{{ $uf }}" {{ old('uf', $cepRange->uf) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                    @error('uf') <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1"><x-icon name="circle-exclamation" style="solid" class="w-3 h-3" /> {{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                        <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="city" class="w-4 h-4" /></span>
                                        <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $cepRange->cidade) }}" required placeholder="Ex: São Paulo"
                                            class="flex-1 min-w-0 py-2.5 pr-4 pl-0 bg-transparent border-0 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-0 placeholder:text-gray-400">
                                    </div>
                                    @error('cidade') <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1"><x-icon name="circle-exclamation" style="solid" class="w-3 h-3" /> {{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                        <x-icon name="flag-checkered" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Intervalo de Cobertura</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">CEP inicial e final da faixa.</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="cep_de" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP Inicial <span class="text-red-500">*</span></label>
                                        <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                            <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="flag-checkered" class="w-4 h-4" /></span>
                                            <input type="text" name="cep_de" id="cep_de" value="{{ old('cep_de', \App\Services\CepService::formatar($cepRange->cep_de)) }}" required data-mask="cep" placeholder="00000-000"
                                                class="flex-1 min-w-0 py-2.5 pr-4 pl-0 bg-transparent border-0 text-gray-900 dark:text-white text-sm font-mono focus:outline-none focus:ring-0 placeholder:text-gray-400">
                                        </div>
                                        @error('cep_de') <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1"><x-icon name="circle-exclamation" style="solid" class="w-3 h-3" /> {{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="cep_ate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP Final <span class="text-red-500">*</span></label>
                                        <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                            <span class="pl-3 flex items-center justify-center text-gray-400 shrink-0"><x-icon name="flag-pennant" class="w-4 h-4" /></span>
                                            <input type="text" name="cep_ate" id="cep_ate" value="{{ old('cep_ate', \App\Services\CepService::formatar($cepRange->cep_ate)) }}" required data-mask="cep" placeholder="00000-000"
                                                class="flex-1 min-w-0 py-2.5 pr-4 pl-0 bg-transparent border-0 text-gray-900 dark:text-white text-sm font-mono focus:outline-none focus:ring-0 placeholder:text-gray-400">
                                        </div>
                                        @error('cep_ate') <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1"><x-icon name="circle-exclamation" style="solid" class="w-3 h-3" /> {{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Classificação da Área</label>
                                    <select name="tipo" id="tipo"
                                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                                        <option value="">Não especificado</option>
                                        <option value="urbano" {{ old('tipo', $cepRange->tipo) == 'urbano' ? 'selected' : '' }}>Urbano</option>
                                        <option value="rural" {{ old('tipo', $cepRange->tipo) == 'rural' ? 'selected' : '' }}>Rural</option>
                                        <option value="total" {{ old('tipo', $cepRange->tipo) == 'total' ? 'selected' : '' }}>Total</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.cep-ranges.index') }}" class="px-5 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                                    <x-icon name="check" class="w-5 h-5" />
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
