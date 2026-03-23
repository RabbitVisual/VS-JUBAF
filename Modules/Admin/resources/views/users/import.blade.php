@extends('admin::components.layouts.master')

@section('title', 'Importar Membros')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Membros</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Importação</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Importar Membros</h1>
                    <p class="text-gray-300 max-w-xl">Adicione múltiplos membros de uma só vez via arquivo Excel ou CSV. Use o modelo para garantir o formato correto das colunas.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Instructions -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                    <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Instruções</h3>
                    </div>
                    <div class="relative p-6 space-y-4">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">1</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Baixe o modelo de importação abaixo para garantir que os dados estejam no formato correto.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">2</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Preencha as colunas necessárias. <strong>Nome</strong> e <strong>Email</strong> são obrigatórios. Datas no formato DD/MM/AAAA ou AAAA-MM-DD.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">3</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Se um email já estiver cadastrado no sistema, a linha será ignorada para evitar duplicatas.</p>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('admin.users.import.template') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-sm font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group">
                                <x-icon name="document-download" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" />
                                Baixar Modelo (CSV)
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-600/20">
                    <div class="flex items-center gap-3 mb-4">
                        <x-icon name="information-circle" class="w-6 h-6" />
                        <h4 class="font-bold">Dica Importante</h4>
                    </div>
                    <p class="text-sm text-blue-100 leading-relaxed">
                        Para o campo <strong>Sexo</strong>, usamos "Masculino" ou "Feminino". Para <strong>Estado Civil</strong>, use "Solteiro", "Casado", etc.
                    </p>
                </div>
            </div>

            <!-- Import Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
                    <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Enviar Arquivo</h3>
                    </div>

                    <form action="{{ route('admin.users.import.post') }}" method="POST" enctype="multipart/form-data" class="p-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando importação...' } }))">
                        @csrf

                        <div class="relative group">
                            <input type="file" name="file" id="file" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="updateFileName(this)">

                            <div id="dropzone" class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-3xl p-12 text-center group-hover:border-blue-500 transition-colors bg-gray-50/50 dark:bg-gray-800/50">
                                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                    <x-icon name="cloud-upload" class="w-8 h-8" />
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1" id="file-name">Clique ou arraste seu arquivo aqui</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Suporta XLSX, XLS ou CSV (Máx 2MB)</p>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 text-sm font-black uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                                <x-icon name="check" class="w-5 h-5 mr-2" />
                                Processar Importação
                            </button>
                        </div>
                    </form>
                </div>

                @if(session('importErrors'))
                    <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-3xl p-6">
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-3 flex items-center gap-2">
                            <x-icon name="circle-exclamation" class="w-5 h-5" />
                            Erros por linha
                        </h4>
                        <ul class="space-y-2 text-sm text-red-700 dark:text-red-400">
                            @foreach(session('importErrors') as $failure)
                                <li class="flex items-start gap-2">
                                    <span class="font-bold shrink-0">Linha {{ $failure['row'] }}:</span>
                                    <span>{{ implode('; ', $failure['errors'] ?? []) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/30 rounded-xl p-4">
                    <div class="flex gap-3 text-yellow-800 dark:text-yellow-400">
                        <x-icon name="exclamation" class="w-5 h-5 flex-shrink-0" />
                        <p class="text-xs font-medium">
                            <strong>Aviso:</strong> A senha padrão para todos os novos membros importados será <code>mudar123</code>. Eles serão solicitados a trocar no primeiro acesso ou via perfil.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const label = document.getElementById('file-name');
            const dropzone = document.getElementById('dropzone');
            if (input.files && input.files[0]) {
                label.innerText = 'Arquivo selecionado: ' + input.files[0].name;
                dropzone.classList.remove('border-gray-300', 'dark:border-gray-700');
                dropzone.classList.add('border-green-500', 'bg-green-50/30', 'dark:bg-green-900/10');
            }
        }
    </script>
@endsection

