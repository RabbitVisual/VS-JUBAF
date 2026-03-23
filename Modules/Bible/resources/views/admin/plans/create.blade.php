@extends('admin::components.layouts.master')

@section('title', 'Novo Plano | Bíblia')

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Estúdio de Criação</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Crie planos de leitura engajadores para sua comunidade</p>
            </div>
            <a href="{{ route('admin.bible.plans.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Voltar
            </a>
        </div>

        <form action="{{ route('admin.bible.plans.store') }}" method="POST" enctype="multipart/form-data"
              x-data="{
                  days: 7,
                  type: 'sequential',
                  get estimate() {
                      if (this.days <= 0) return '---';
                      if (this.type === 'manual') return 'Definido manualmente etapa por etapa.';
                      if (this.type === 'sequential') return 'Calculado com base nos livros que você selecionar na próxima etapa.';

                      // Chronological (Full Bible approx 1189 chapters)
                      let total = 1189;
                      let perDay = (total / this.days).toFixed(1);
                      return `Isso exigirá ler aprox. ${perDay} capítulos por dia (Baseado em 1189 caps).`;
                  }
              }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Core Setup -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Step 1: Basic Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm">1</span>
                            Informações Básicas
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do Plano</label>
                                <input type="text" name="title" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-lg placeholder-gray-400"
                                    placeholder="Ex: Jornada da Esperança">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição Inspiradora</label>
                                <textarea name="description" rows="4"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm leading-relaxed"
                                    placeholder="Descreva o objetivo deste plano e o que os leitores aprenderão..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Strategy (Plan Type) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                         <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-sm">2</span>
                            Estratégia de Leitura
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Type: Manual -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="type" value="manual" class="peer sr-only" x-model="type">
                                <div class="h-full p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:border-purple-300 dark:hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/10 transition-all flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-3">
                                            <x-icon name="pencil" class="w-6 h-6" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">Manual / Devocional</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Você tem controle total. Ideal para séries de devocionais temáticos ou planos curtos criados por você.</p>
                                    </div>
                                    <div class="text-purple-600 dark:text-purple-400 opacity-0 peer-checked:opacity-100 transition-opacity flex justify-end">
                                        <x-icon name="check-circle" class="w-6 h-6" />
                                    </div>
                                </div>
                            </label>

                            <!-- Type: Sequential -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="type" value="sequential" class="peer sr-only" x-model="type">
                                <div class="h-full p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:border-purple-300 dark:hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/10 transition-all flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3">
                                            <x-icon name="book-open" class="w-6 h-6" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">Livros Selecionados</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Escolha livros específicos (ex: Gênesis e Êxodo) e o sistema os dividirá inteligentemente.</p>
                                    </div>
                                    <div class="text-purple-600 dark:text-purple-400 opacity-0 peer-checked:opacity-100 transition-opacity flex justify-end">
                                        <x-icon name="check-circle" class="w-6 h-6" />
                                    </div>
                                </div>
                            </label>

                            <!-- Type: Chronological -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="type" value="chronological" class="peer sr-only" x-model="type">
                                <div class="h-full p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:border-purple-300 dark:hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/10 transition-all flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-3">
                                            <x-icon name="globe" class="w-6 h-6" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">Bíblia Completa</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Gera automaticamente um plano cronológico cobrindo de Gênesis a Apocalipse na ordem padrão.</p>
                                    </div>
                                    <div class="text-purple-600 dark:text-purple-400 opacity-0 peer-checked:opacity-100 transition-opacity flex justify-end">
                                        <x-icon name="check-circle" class="w-6 h-6" />
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                    <!-- Step 3: Reading Mode -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                         <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm">3</span>
                            Modo de Leitura
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Digital Mode -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="reading_mode" value="digital" class="peer sr-only" checked>
                                <div class="h-full p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:border-indigo-300 dark:hover:border-indigo-500 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/10 transition-all flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3">
                                            <x-icon name="mobile-screen-button" class="w-6 h-6" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">Leitura Digital (App)</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">O texto bíblico aparece completo na tela para leitura direta no dispositivo.</p>
                                    </div>
                                    <div class="text-indigo-600 dark:text-indigo-400 opacity-0 peer-checked:opacity-100 transition-opacity flex justify-end">
                                        <x-icon name="circle-check" style="duotone" class="w-6 h-6" />
                                    </div>
                                </div>
                            </label>

                            <!-- Physical Timer Mode -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="reading_mode" value="physical_timer" class="peer sr-only">
                                <div class="h-full p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 hover:border-indigo-300 dark:hover:border-indigo-500 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/10 transition-all flex flex-col justify-between">
                                    <div class="mb-4">
                                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-3">
                                            <x-icon name="clock" style="duotone" class="w-6 h-6" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">Bíblia Física (Checklist + Timer)</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Ideal para quem lê no livro físico. Mostra apenas o alvo, um cronômetro e checklist de conclusão.</p>
                                    </div>
                                    <div class="text-indigo-600 dark:text-indigo-400 opacity-0 peer-checked:opacity-100 transition-opacity flex justify-end">
                                        <x-icon name="circle-check" style="duotone" class="w-6 h-6" />
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Media -->
                <div class="space-y-8">
                     <!-- Step 4: Duration -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-sm">4</span>
                            Duração
                        </h2>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dias Totais</label>
                            <div class="relative">
                                <input type="number" name="duration_days" required min="1" x-model="days"
                                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-2xl">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Dias</span>
                            </div>
                            <!-- Live Estimation Feedback -->
                            <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30 flex items-start gap-3">
                                <x-icon name="calculator" class="w-5 h-5 text-blue-500 mt-0.5" />
                                <div>
                                    <p class="text-sm font-bold text-blue-700 dark:text-blue-300" x-text="estimate"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4.5: Navigation Settings -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Navegação</h2>

                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-5">
                                <input id="allow_back_tracking" name="allow_back_tracking" type="checkbox" checked
                                    class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                            </div>
                            <div class="ml-1 text-sm">
                                <label for="allow_back_tracking" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Permitir voltar para dias concluídos</label>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">Se desmarcado, o usuário não poderá acessar o conteúdo de dias que já marcou como lido.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Cover Image -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Capa do Plano</h2>

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-700/50 group">
                            <input type="file" name="cover_image" class="hidden" id="coverUpload">
                            <div class="space-y-2 pointer-events-none">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <x-icon name="image" class="w-6 h-6" />
                                </div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="text-blue-600">Clique para enviar</span> ou arraste
                                </p>
                                <p class="text-xs text-gray-400">PNG, JPG até 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                     <button type="submit" class="w-full py-4 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center group">
                        Continuar para Configuração
                        <x-icon name="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Simple Script for File Upload Interaction (Visual only for now) -->
    <script>
        const uploadBox = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('coverUpload');

        uploadBox.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            if(e.target.files.length > 0) {
                // Show simple feedback
                uploadBox.querySelector('p.text-sm').innerHTML = `<span class="text-green-600 font-bold">${e.target.files[0].name}</span> selecionado`;
            }
        });
    </script>
@endsection

