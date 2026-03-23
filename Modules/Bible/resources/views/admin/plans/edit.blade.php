@extends('admin::components.layouts.master')

@section('title', 'Editar Plano | ' . $plan->title)

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar Plano</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie as configurações e metadados</p>
            </div>
            <a href="{{ route('admin.bible.plans.show', $plan->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Voltar
            </a>
        </div>

        <form action="{{ route('admin.bible.plans.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                <input type="text" name="title" required value="{{ old('title', $plan->title) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-lg placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição Inspiradora</label>
                                <textarea name="description" rows="4"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm leading-relaxed">{{ old('description', $plan->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Settings (Duration & Active) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-sm">2</span>
                            Configurações Gerais
                        </h2>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dias Totais</label>
                                <div class="relative">
                                    <input type="number" name="duration_days" required min="1" value="{{ old('duration_days', $plan->duration_days) }}"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-2xl">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Dias</span>
                                </div>
                            </div>

                            <!-- Visibility Switch -->
                            <div class="flex items-center h-full pt-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" class="sr-only peer" {{ $plan->is_active ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Plano Publicado (Visível)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Navigation & Media -->
                <div class="space-y-8">
                    <!-- Step 3: Navigation Rule -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Navegação</h2>

                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-5">
                                <input id="allow_back_tracking" name="allow_back_tracking" type="checkbox" {{ $plan->allow_back_tracking ? 'checked' : '' }}
                                    class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                            </div>
                            <div class="ml-1 text-sm">
                                <label for="allow_back_tracking" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Permitir voltar para dias concluídos</label>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">Se desmarcado, o usuário não poderá acessar o conteúdo de dias que já marcou como lido.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Cover Image -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Capa do Plano</h2>

                        @if($plan->cover_image)
                            <div class="mb-4 rounded-xl overflow-hidden shadow-md">
                                <img src="{{ asset('storage/' . $plan->cover_image) }}" alt="Current Cover" class="w-full h-auto object-cover">
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-700/50 group">
                            <input type="file" name="cover_image" class="hidden" id="coverUpload">
                            <div class="space-y-2 pointer-events-none">
                                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <x-icon name="photograph" class="w-6 h-6" />
                                </div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="text-blue-600">Alterar Capa</span> ou arraste
                                </p>
                                <p class="text-xs text-gray-400">PNG, JPG até 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                     <button type="submit" class="w-full py-4 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center group">
                        Salvar Alterações
                        <x-icon name="check" class="w-5 h-5 ml-2" />
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Simple Script for File Upload Interaction -->
    <script>
        const uploadBox = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('coverUpload');

        uploadBox.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            if(e.target.files.length > 0) {
                uploadBox.querySelector('p.text-sm').innerHTML = `<span class="text-green-600 font-bold">${e.target.files[0].name}</span> selecionado`;
            }
        });
    </script>
@endsection

