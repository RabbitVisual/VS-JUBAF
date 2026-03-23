@extends('admin::components.layouts.master')

@section('title', 'Editar Curso | Worship Academy')

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
                    <span class="text-gray-400 dark:text-gray-500">Editar</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Editar Curso</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $course->title }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('worship.admin.academy.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do Curso <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-colors">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instrument -->
                    <div>
                        <label for="instrument_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Instrumento / Área <span class="text-red-500">*</span></label>
                        <select name="instrument_id" id="instrument_id" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-colors">
                            <option value="">Selecione...</option>
                            @foreach($instruments as $instrument)
                                <option value="{{ $instrument->id }}" {{ old('instrument_id', $course->instrument_id) == $instrument->id ? 'selected' : '' }}>
                                    {{ $instrument->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('instrument_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nível <span class="text-red-500">*</span></label>
                        <select name="level" id="level" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-colors">
                            <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Iniciante</option>
                            <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediário</option>
                            <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Avançado</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Categoria</label>
                        <select name="category" id="category"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-colors">
                            <option value="">—</option>
                            @foreach(\Modules\Worship\App\Enums\AcademyCourseCategory::cases() as $cat)
                                <option value="{{ $cat->value }}" {{ old('category', $course->category) == $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-4 transition-colors resize-none">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biblical reflection (integração Módulo Bible) -->
                    <div class="col-span-2">
                        <label for="biblical_reflection" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reflexão bíblica</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Use o seletor abaixo para buscar um versículo no Módulo Bible; o texto será inserido aqui. Ex.: Salmos 22:1 — [texto]. Você pode acrescentar sua reflexão.</p>
                        @include('worship::admin.academy.partials.bible-reflection-picker', ['textareaId' => 'biblical_reflection'])
                        <textarea name="biblical_reflection" id="biblical_reflection" rows="4"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-4 transition-colors resize-none mt-3">{{ old('biblical_reflection', $course->biblical_reflection) }}</textarea>
                        @error('biblical_reflection')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cover Image URL -->
                    <div class="col-span-2">
                         <label for="cover_image" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">URL da Imagem de Capa</label>
                         <input type="url" name="cover_image" id="cover_image" value="{{ old('cover_image', $course->cover_image) }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-colors">
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                    <form action="{{ route('worship.admin.academy.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este curso e todas as lições?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-bold hover:underline">
                            Excluir Curso
                        </button>
                    </form>

                    <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <x-icon name="check" class="h-5 w-5 mr-2" />
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

