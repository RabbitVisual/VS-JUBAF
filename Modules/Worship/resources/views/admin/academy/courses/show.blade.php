@extends('admin::components.layouts.master')

@section('title', 'Curriculum: ' . $course->title . ' | Worship Academy')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('worship.admin.academy.courses.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest">
                    <a href="{{ route('worship.admin.academy.courses.index') }}" class="hover:underline">Academy</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500 truncate max-w-[200px]">{{ $course->title }}</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $course->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciamento do currículo e lições.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('worship.admin.academy.courses.edit', $course->id) }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                <x-icon name="pen-to-square" class="w-5 h-5 mr-2" />
                Editar Dados
            </a>
            <a href="{{ route('worship.admin.academy.builder', $course->id) }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-500/20 transition-all">
                <x-icon name="code" class="w-5 h-5 mr-2" />
                Editor de Conteúdo
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 overflow-hidden relative">
                @if($course->cover_image)
                    <div class="h-40 -mx-6 -mt-6 mb-6">
                        <img src="{{ $course->cover_image }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Detalhes</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Instrumento</span>
                        <div class="flex items-center mt-1">
                             <div class="px-2 py-1 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold">
                                {{ $course->instrument ? $course->instrument->name : 'Geral' }}
                             </div>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Nível</span>
                        <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($course->level) }}</div>
                    </div>
                    @if($course->category)
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase">Categoria</span>
                            <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $course->category->label() }}</div>
                        </div>
                    @endif
                     <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Total de Lições</span>
                        <div class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $course->lessons->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            @if($course->description || $course->biblical_reflection)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sobre o Curso</h2>
                    @if($course->description)
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 mb-6">
                            {!! strip_tags($course->description, '<p><strong><em><b><i><ul><ol><li><br><a><span><h2><h3><h4>') !!}
                        </div>
                    @endif
                    @if($course->biblical_reflection)
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">Reflexão Bíblica</h3>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                                {!! strip_tags($course->biblical_reflection, '<p><strong><em><br><a>') !!}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Conteúdo do Curso</h2>
                    <button onclick="openAddLessonModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-all">
                        <x-icon name="plus" class="h-4 w-4 mr-2" />
                        Adicionar Lição
                    </button>
                </div>

                @if($course->modules->flatMap->lessons->isEmpty())
                    <div class="py-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl bg-gray-50/50 dark:bg-gray-700/20">
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhuma lição adicionada ainda.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($course->modules as $module)
                            <div>
                                <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ $module->title }}</h4>
                                <div class="space-y-2">
                        @foreach($module->lessons as $lesson)
                            <div class="group flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-gray-500 shadow-sm border border-gray-100 dark:border-gray-500">
                                        {{ $lesson->order }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $lesson->title }}</h4>
                                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            @if($lesson->video_url)
                                                <span class="flex items-center">
                                                    <x-icon name="play" class="h-3 w-3 mr-1" />
                                                    Vídeo
                                                </span>
                                            @endif
                                            @if($lesson->duration_minutes)
                                                <span>{{ $lesson->duration_minutes }} min</span>
                                            @endif
                                            @if($lesson->requirement_song_id)
                                                <span class="text-orange-500 flex items-center" title="Música Obrigatória">
                                                    <x-icon name="clipboard-list" class="h-3 w-3 mr-1" />
                                                    Requer Prática
                                                </span>
                                            @endif
                                            @if($lesson->content && strlen(strip_tags($lesson->content)) > 0)
                                                <span class="flex items-center text-emerald-600 dark:text-emerald-400" title="Tem descrição">
                                                    <x-icon name="file-lines" class="h-3 w-3 mr-1" />
                                                    Descrição
                                                </span>
                                            @endif
                                            @if($lesson->teacher_tips && strlen(strip_tags($lesson->teacher_tips)) > 0)
                                                <span class="flex items-center text-blue-600 dark:text-blue-400" title="Tem dicas do professor">
                                                    <x-icon name="chalkboard-user" class="h-3 w-3 mr-1" />
                                                    Dicas
                                                </span>
                                            @endif
                                            @if($lesson->pdf_path || $lesson->sheet_music_pdf)
                                                <span class="flex items-center text-purple-600 dark:text-purple-400" title="Tem materiais para download">
                                                    <x-icon name="file-pdf" class="h-3 w-3 mr-1" />
                                                    PDF
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" data-lesson='@json($lesson)' onclick="openEditLessonModal(this)" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Editar">
                                        <x-icon name="pen-to-square" style="duotone" class="h-5 w-5" />
                                    </button>
                                    <form action="{{ route('worship.admin.academy.courses.destroyLesson', ['id' => $course->id, 'lessonId' => $lesson->id]) }}" method="POST" onsubmit="return confirm('Excluir esta lição?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Excluir">
                                            <x-icon name="trash-can" class="h-5 w-5" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Lesson Modal (centered, fields by type) -->
    <dialog id="addLessonModal" class="academy-lesson-dialog rounded-2xl shadow-2xl p-0 w-full max-w-2xl max-h-[90vh] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 backdrop:bg-black/50">
        <div class="p-6 md:p-8 overflow-y-auto max-h-[85vh]">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Nova Lição</h3>
            <form action="{{ route('worship.admin.academy.courses.storeLesson', $course->id) }}" method="POST" id="addLessonForm">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título da Lição <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                        <select name="type" id="add_lesson_type" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" onchange="toggleAddLessonSections()">
                            <option value="video">Vídeo</option>
                            <option value="chordpro">ChordPro / Cifra</option>
                            <option value="material">Material para Download</option>
                            <option value="devotional">Devocional</option>
                        </select>
                    </div>

                    <div id="add_section_chordpro" class="add-type-section border-t border-gray-200 dark:border-gray-600 pt-4" style="display: none;">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Cifra completa (ChordPro / Cifra Club)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Cole a cifra completa copiada do Cifra Club ou em formato ChordPro. Inclua letra, acordes e tablaturas — a exibição ficará alinhada como no site.</p>
                        <textarea name="content_chordpro" rows="14" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 font-mono text-sm" placeholder="Ex: [Intro] Dm7  Bb9(11+)&#10;E|------------------------------------------|&#10;B|------------5---6-----6----X--------------|&#10;..."></textarea>
                    </div>

                    <div id="add_section_video" class="add-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Vídeo</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">URL do Vídeo (YouTube/Vimeo)</label>
                                <input type="url" name="video_url" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">URL Vídeo Multicâmera</label>
                                <input type="url" name="multicam_video_url" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Duração (min)</label>
                                <input type="number" name="duration_minutes" min="0" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                        </div>
                    </div>

                    <div id="add_section_desc_dicas" class="add-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Descrição e Dicas</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição da aula</label>
                                <textarea name="content" rows="4" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Texto ou HTML exibido na aba Descrição na sala de aula."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dicas do Professor</label>
                                <textarea name="teacher_tips" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Dicas exibidas na aba Dicas do Professor."></textarea>
                            </div>
                            <div id="add_section_bible_ref">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Referência bíblica</label>
                                @include('worship::admin.academy.partials.bible-reference-picker', ['inputId' => 'add_bible_reference'])
                                <input type="text" name="bible_reference" id="add_bible_reference" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 mt-2" placeholder="Ex: João 3:16 ou use o seletor acima">
                            </div>
                        </div>
                    </div>

                    <div id="add_section_materiais" class="add-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Materiais (Downloads)</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Guia de estudo (caminho ou URL do PDF)</label>
                                <input type="text" name="pdf_path" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Ex: storage/materials/guia.pdf">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Partitura (caminho ou URL do PDF)</label>
                                <input type="text" name="sheet_music_pdf" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Ex: storage/materials/partitura.pdf">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addLessonModal').close()" class="px-5 py-2.5 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-colors">Adicionar</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Edit Lesson Modal (centered, fields by type) -->
    <dialog id="editLessonModal" class="academy-lesson-dialog rounded-2xl shadow-2xl p-0 w-full max-w-2xl max-h-[90vh] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 backdrop:bg-black/50">
        <div class="p-6 md:p-8 overflow-y-auto max-h-[85vh]">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Editar Lição</h3>
            <form id="editLessonForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título da Lição <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="edit_title" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                        <select name="type" id="edit_type" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" onchange="toggleEditLessonSections()">
                            <option value="video">Vídeo</option>
                            <option value="chordpro">ChordPro / Cifra</option>
                            <option value="material">Material para Download</option>
                            <option value="devotional">Devocional</option>
                        </select>
                    </div>

                    <div id="edit_section_chordpro" class="edit-type-section border-t border-gray-200 dark:border-gray-600 pt-4" style="display: none;">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Cifra completa (ChordPro / Cifra Club)</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Cole a cifra completa copiada do Cifra Club ou em formato ChordPro. Inclua letra, acordes e tablaturas.</p>
                        <textarea name="content_chordpro" id="edit_content_chordpro" rows="14" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 font-mono text-sm" placeholder="[Intro] Dm7  Bb9(11+)..."></textarea>
                    </div>

                    <div id="edit_section_video" class="edit-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Vídeo</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">URL do Vídeo</label>
                                <input type="url" name="video_url" id="edit_video_url" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">URL Vídeo Multicâmera</label>
                                <input type="url" name="multicam_video_url" id="edit_multicam_video_url" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Duração (min)</label>
                                <input type="number" name="duration_minutes" id="edit_duration_minutes" min="0" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                        </div>
                    </div>

                    <div id="edit_section_desc_dicas" class="edit-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Descrição e Dicas</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição da aula</label>
                                <textarea name="content" id="edit_content" rows="4" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dicas do Professor</label>
                                <textarea name="teacher_tips" id="edit_teacher_tips" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"></textarea>
                            </div>
                            <div id="edit_section_bible_ref">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Referência bíblica</label>
                                @include('worship::admin.academy.partials.bible-reference-picker', ['inputId' => 'edit_bible_reference'])
                                <input type="text" name="bible_reference" id="edit_bible_reference" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 mt-2" placeholder="Ex: Salmos 22:1">
                            </div>
                        </div>
                    </div>

                    <div id="edit_section_materiais" class="edit-type-section border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Materiais (Downloads)</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Guia de estudo (caminho ou URL do PDF)</label>
                                <input type="text" name="pdf_path" id="edit_pdf_path" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Partitura (caminho ou URL do PDF)</label>
                                <input type="text" name="sheet_music_pdf" id="edit_sheet_music_pdf" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editLessonModal').close()" class="px-5 py-2.5 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-colors">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </dialog>

    <style>
        .academy-lesson-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
        }
        .academy-lesson-dialog::backdrop {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
    <script>
        function toggleAddLessonSections() {
            var type = (document.getElementById('add_lesson_type') || {}).value || 'video';
            var showVideo = type === 'video';
            var showChordpro = type === 'chordpro';
            var showDescDicas = ['video', 'chordpro', 'devotional'].indexOf(type) >= 0;
            var showBibleRef = (type === 'devotional' || type === 'video') && showDescDicas;
            var showMateriais = type === 'video' || type === 'material';

            var el = document.getElementById('add_section_video');
            if (el) el.style.display = showVideo ? 'block' : 'none';
            el = document.getElementById('add_section_chordpro');
            if (el) el.style.display = showChordpro ? 'block' : 'none';
            el = document.getElementById('add_section_desc_dicas');
            if (el) el.style.display = showDescDicas ? 'block' : 'none';
            el = document.getElementById('add_section_bible_ref');
            if (el) el.style.display = showBibleRef ? 'block' : 'none';
            el = document.getElementById('add_section_materiais');
            if (el) el.style.display = showMateriais ? 'block' : 'none';
        }
        function toggleEditLessonSections() {
            var type = (document.getElementById('edit_type') || {}).value || 'video';
            var showVideo = type === 'video';
            var showChordpro = type === 'chordpro';
            var showDescDicas = ['video', 'chordpro', 'devotional'].indexOf(type) >= 0;
            var showBibleRef = (type === 'devotional' || type === 'video') && showDescDicas;
            var showMateriais = type === 'video' || type === 'material';

            var el = document.getElementById('edit_section_video');
            if (el) el.style.display = showVideo ? 'block' : 'none';
            el = document.getElementById('edit_section_chordpro');
            if (el) el.style.display = showChordpro ? 'block' : 'none';
            el = document.getElementById('edit_section_desc_dicas');
            if (el) el.style.display = showDescDicas ? 'block' : 'none';
            el = document.getElementById('edit_section_bible_ref');
            if (el) el.style.display = showBibleRef ? 'block' : 'none';
            el = document.getElementById('edit_section_materiais');
            if (el) el.style.display = showMateriais ? 'block' : 'none';
        }
        function openAddLessonModal() {
            document.getElementById('add_lesson_type').value = 'video';
            toggleAddLessonSections();
            document.getElementById('addLessonModal').showModal();
        }

        function openEditLessonModal(btn) {
            var lesson = typeof btn === 'object' && btn.dataset && btn.dataset.lesson
                ? JSON.parse(btn.dataset.lesson)
                : btn;
            document.getElementById('edit_title').value = lesson.title || '';
            document.getElementById('edit_type').value = lesson.type || 'video';
            document.getElementById('edit_video_url').value = lesson.video_url || '';
            document.getElementById('edit_multicam_video_url').value = lesson.multicam_video_url || '';
            document.getElementById('edit_duration_minutes').value = lesson.duration_minutes ?? '';
            if (lesson.type === 'chordpro') {
                var chordproEl = document.getElementById('edit_content_chordpro');
                if (chordproEl) chordproEl.value = lesson.content || '';
                document.getElementById('edit_content').value = '';
            } else {
                document.getElementById('edit_content').value = lesson.content || '';
                var chordproEl = document.getElementById('edit_content_chordpro');
                if (chordproEl) chordproEl.value = '';
            }
            document.getElementById('edit_teacher_tips').value = lesson.teacher_tips || '';
            document.getElementById('edit_bible_reference').value = lesson.bible_reference || '';
            document.getElementById('edit_pdf_path').value = lesson.pdf_path || '';
            document.getElementById('edit_sheet_music_pdf').value = lesson.sheet_music_pdf || '';

            var form = document.getElementById('editLessonForm');
            form.action = "{{ route('worship.admin.academy.courses.updateLesson', ['id' => $course->id, 'lessonId' => 0]) }}".replace('/lessons/0', '/lessons/' + lesson.id);

            toggleEditLessonSections();
            document.getElementById('editLessonModal').showModal();
        }
    </script>
</div>
@endsection

