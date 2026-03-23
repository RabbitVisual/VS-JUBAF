@extends('admin::components.layouts.master')

@section('title', 'Adicionar Material - ' . $lesson->title)

@section('content')
<div class="space-y-8 pb-20">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-emerald-600 text-white rounded">Recursos Educativos</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Novo <span class="text-emerald-600">Material</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Contexto da Lição: <span class="text-gray-900 dark:text-white font-bold text-sm uppercase tracking-tight">{{ $lesson->title }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.lessons.show', $lesson) }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Main Form Grid -->
    <form action="{{ route('admin.ebd.lessons.materials.store', $lesson) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Primary Data -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                            <x-icon name="file-circle-plus" style="duotone" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informações Principais</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Defina o título e o tipo do conteúdo</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">
                                Título do Material <span class="text-emerald-600">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-gray-800 rounded-2xl text-gray-900 dark:text-white font-bold transition-all outline-hidden @error('title') border-red-500/50 @enderror"
                                placeholder="Ex: PDF de Apoio - Aula 01">
                            @error('title')
                                <p class="mt-2 text-xs font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="md:col-span-2">
                            <label for="type" class="block text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">
                                Tipo de Conteúdo <span class="text-emerald-600">*</span>
                            </label>
                            <div class="relative group">
                                <select name="type" id="type" required
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-gray-800 rounded-2xl text-gray-900 dark:text-white font-bold transition-all outline-hidden appearance-none @error('type') border-red-500/50 @enderror">
                                    <option value="" disabled selected>Selecione o tipo...</option>
                                    <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Vídeo</option>
                                    <option value="pdf" {{ old('type') === 'pdf' ? 'selected' : '' }}>Arquivo PDF</option>
                                    <option value="document" {{ old('type') === 'document' ? 'selected' : '' }}>Documento</option>
                                    <option value="presentation" {{ old('type') === 'presentation' ? 'selected' : '' }}>Apresentação (PPT)</option>
                                    <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Imagem / Infográfico</option>
                                    <option value="link" {{ old('type') === 'link' ? 'selected' : '' }}>Link Externo</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <x-icon name="chevron-down" style="duotone" class="w-5 h-5" />
                                </div>
                            </div>
                            @error('type')
                                <p class="mt-2 text-xs font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Content/Upload Area -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                            <x-icon name="upload" style="duotone" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Arquivo ou Fonte</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Escolha como o material será disponibilizado</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Method Selector -->
                        <div class="flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-2xl w-fit">
                            <button type="button" onclick="setUploadMethod('file')" id="btn-file"
                                class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-black uppercase tracking-widest transition-all bg-white dark:bg-gray-900 text-emerald-600 shadow-sm">
                                <x-icon name="file-upload" class="w-4 h-4" />
                                Arquivo Local
                            </button>
                            <button type="button" onclick="setUploadMethod('url')" id="btn-url"
                                class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-black uppercase tracking-widest transition-all text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <x-icon name="link" class="w-4 h-4" />
                                Link / URL
                            </button>
                            <input type="hidden" name="upload_method" id="upload_method" value="file">
                        </div>

                        <!-- File Section -->
                        <div id="file-section" class="transition-all duration-300">
                            <div class="relative group border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-3xl p-10 text-center transition-all bg-gray-50/30 dark:bg-gray-800/10">
                                <input type="file" name="file" id="file_input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="relative z-0">
                                    <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:scale-110 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 group-hover:text-emerald-600 transition-all">
                                        <x-icon name="cloud-upload" style="duotone" class="w-8 h-8" />
                                    </div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg">Selecione o arquivo</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-xs mx-auto">
                                        Clique ou arraste o arquivo aqui. Tamanho máximo permitido: <span class="text-emerald-600 font-bold">100MB</span>
                                    </p>
                                    <div id="file-name" class="mt-4 hidden animate-in fade-in slide-in-from-bottom-2">
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-sm font-bold border border-emerald-100 dark:border-emerald-900/50">
                                            <x-icon name="check-circle" class="mr-2 h-4 w-4" />
                                            <span id="selected-name"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-4 text-xs font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL Section -->
                        <div id="url-section" class="hidden transition-all duration-300">
                            <label for="url" class="block text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">
                                Endereço URL
                            </label>
                            <input type="url" name="url" id="url" value="{{ old('url') }}"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-gray-800 rounded-2xl text-gray-900 dark:text-white font-bold transition-all outline-hidden"
                                placeholder="https://youtube.com/watch?v=... ou link direto">
                            <p class="mt-3 text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                                Use para vídeos do YouTube, Vimeo ou arquivos hospedados externamente.
                            </p>
                            @error('url')
                                <p class="mt-2 text-xs font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600">
                            <x-icon name="align-left" style="duotone" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Descrição</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Breve explicação do conteúdo para o aluno</p>
                        </div>
                    </div>

                    <textarea name="description" id="description" rows="4"
                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent focus:border-amber-500 focus:bg-white dark:focus:bg-gray-800 rounded-2xl text-gray-900 dark:text-white font-medium transition-all outline-hidden resize-none placeholder-gray-400"
                        placeholder="Descreva o que este material contém e como ele ajuda no estudo...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Right Column: Settings & Actions -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Configuration Card -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                        <x-icon name="sliders-h" class="w-4 h-4" />
                        Configurações
                    </h3>

                    <div class="space-y-4">
                        <!-- Is Required -->
                        <label class="group flex items-center justify-between p-4 rounded-2xl border-2 border-gray-50 dark:border-gray-800 hover:border-emerald-500 transition-all cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 group-hover:text-emerald-600 transition-all">
                                    <x-icon name="circle-exclamation" style="duotone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Obrigatório</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Trava progresso</p>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-600"></div>
                            </div>
                        </label>

                        <!-- Is Public -->
                        <label class="group flex items-center justify-between p-4 rounded-2xl border-2 border-emerald-500/10 dark:border-emerald-500/5 bg-emerald-50/10 dark:bg-emerald-950/5 hover:border-emerald-500 transition-all cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center">
                                    <x-icon name="eye" style="duotone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Visível</p>
                                    <p class="text-[10px] text-emerald-600/70 font-bold uppercase tracking-tighter">Publicado no LMS</p>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-600"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="bg-gray-900 dark:bg-black rounded-3xl p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full"></div>
                    <div class="relative z-10">
                        <h3 class="text-white font-bold text-lg mb-2">Pronto para publicar?</h3>
                        <p class="text-gray-400 text-sm mb-6">O material será anexado à lição '{{ Str::limit($lesson->title, 20) }}' imediatamente.</p>

                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase tracking-widest text-xs py-5 rounded-2xl shadow-lg shadow-emerald-600/30 transition-all active:scale-95 group">
                            <x-icon name="check-circle" style="duotone" class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                            Salvar Material
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setUploadMethod(method) {
        const btnFile = document.getElementById('btn-file');
        const btnUrl = document.getElementById('btn-url');
        const fileSection = document.getElementById('file-section');
        const urlSection = document.getElementById('url-section');
        const uploadMethod = document.getElementById('upload_method');
        const fileInput = document.getElementById('file_input');
        const urlInput = document.getElementById('url');

        uploadMethod.value = method;

        if (method === 'file') {
            // Activate File
            btnFile.classList.add('bg-white', 'dark:bg-gray-900', 'text-emerald-600', 'shadow-sm');
            btnFile.classList.remove('text-gray-400');
            // Deactivate URL
            btnUrl.classList.remove('bg-white', 'dark:bg-gray-900', 'text-emerald-600', 'shadow-sm');
            btnUrl.classList.add('text-gray-400');

            fileSection.classList.remove('hidden');
            urlSection.classList.add('hidden');
            fileInput.removeAttribute('disabled');
            urlInput.setAttribute('disabled', 'disabled');
        } else {
            // Activate URL
            btnUrl.classList.add('bg-white', 'dark:bg-gray-900', 'text-emerald-600', 'shadow-sm');
            btnUrl.classList.remove('text-gray-400');
            // Deactivate File
            btnFile.classList.remove('bg-white', 'dark:bg-gray-900', 'text-emerald-600', 'shadow-sm');
            btnFile.classList.add('text-gray-400');

            urlSection.classList.remove('hidden');
            fileSection.classList.add('hidden');
            urlInput.removeAttribute('disabled');
            fileInput.setAttribute('disabled', 'disabled');
        }
    }

    // File name display
    document.getElementById('file_input').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : '';
        const nameContainer = document.getElementById('file-name');
        const nameText = document.getElementById('selected-name');

        if (fileName) {
            nameText.textContent = fileName;
            nameContainer.classList.remove('hidden');
        } else {
            nameContainer.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection

