@extends('admin::components.layouts.master')

@section('title', 'Editar Questão | ' . $lesson->title)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Matriz de Avaliação</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Editar <span class="text-amber-600">Questão</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Lição contextual: <span class="text-gray-900 dark:text-white font-bold text-sm uppercase tracking-tight">{{ $lesson->title }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.lessons.show', $lesson) }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.ebd.lessons.questions.update', [$lesson, $question]) }}" method="POST" class="space-y-8 max-w-4xl pb-20">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <x-icon name="seal-question" style="duotone" class="w-5 h-5 text-amber-600" />
                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Configuração da Pergunta</h3>
            </div>
            <div class="p-8 space-y-8">
                <!-- Question Text -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Texto da Questão</label>
                    <textarea name="question" rows="4" required placeholder="Elabore a pergunta..."
                        class="w-full px-6 py-6 bg-gray-50 dark:bg-gray-800/50 border-none rounded-3xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 transition-all resize-none">{{ old('question', $question->question) }}</textarea>
                    @error('question') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Type -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Formato da Resposta</label>
                        <select name="type" id="type" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all cursor-pointer">
                            <option value="short_answer" {{ old('type', $question->type) === 'short_answer' ? 'selected' : '' }}>Resposta Curta (Texto)</option>
                            <option value="essay" {{ old('type', $question->type) === 'essay' ? 'selected' : '' }}>Dissertativa (Parágrafo)</option>
                            <option value="multiple_choice" {{ old('type', $question->type) === 'multiple_choice' ? 'selected' : '' }}>Múltipla Escolha</option>
                            <option value="true_false" {{ old('type', $question->type) === 'true_false' ? 'selected' : '' }}>Verdadeiro ou Falso</option>
                        </select>
                    </div>

                    <!-- Points -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Peso / Pontuação</label>
                        <div class="relative">
                            <input type="number" name="points" min="1" max="100" value="{{ old('points', $question->points) }}" required
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase">Pontos</span>
                        </div>
                    </div>
                </div>

                <!-- Multiple Choice Options -->
                <div id="options-container" style="display: none;" class="space-y-4 pt-4 border-t border-gray-50 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Alternativas</label>
                        <button type="button" id="add-option" class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <x-icon name="plus" class="w-3 h-3" />
                            Nova Opção
                        </button>
                    </div>
                    <div id="options-list" class="space-y-3">
                        @foreach(old('options', $question->options ?? []) as $index => $option)
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <input type="text" name="options[]" value="{{ $option }}" placeholder="Opção..."
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-amber-500/20 transition-all">
                                </div>
                                @if($index >= 2)
                                    <button type="button" class="remove-option p-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Required Toggle -->
                <div class="flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800/30 rounded-3xl border border-transparent group hover:border-amber-500/20 transition-all">
                    <div>
                        <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Obrigatoriedade</h4>
                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-tighter mt-1">O aluno não poderá concluir a avaliação sem responder esta questão.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <button type="submit" class="flex items-center gap-3 px-10 py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-2xl font-black text-xs uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-2xl">
                <x-icon name="floppy-disk" class="w-4 h-4" />
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const addOptionBtn = document.getElementById('add-option');

        function toggleOptions() {
            optionsContainer.style.display = typeSelect.value === 'multiple_choice' ? 'block' : 'none';
        }

        typeSelect.addEventListener('change', toggleOptions);
        toggleOptions();

        addOptionBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 animate-in slide-in-from-left-2 duration-300';
            div.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="options[]" placeholder="Nova opção..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-amber-500/20 transition-all">
                </div>
                <button type="button" class="remove-option p-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                    <x-icon name="trash" class="w-4 h-4" />
                </button>
            `;
            optionsList.appendChild(div);
        });

        optionsList.addEventListener('click', (e) => {
            if (e.target.closest('.remove-option')) e.target.closest('.flex').remove();
        });
    });
</script>
@endpush
@endsection

