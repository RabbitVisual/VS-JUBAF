@extends('admin::components.layouts.master')

@section('title', 'Editar Jogo | EBD Arcade')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500" x-data="{ activeTab: 'settings' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ebd.games.index') }}" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                <x-icon name="arrow-left" style="solid" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $game->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Configurações e Banco de Questões</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
            <button @click="activeTab = 'settings'"
                :class="activeTab === 'settings' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all">Configurações</button>
            <button @click="activeTab = 'questions'"
                :class="activeTab === 'questions' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all">Perguntas ({{ $game->questions->count() }})</button>
        </div>
    </div>

    <!-- Tab: Settings -->
    <div x-show="activeTab === 'settings'" class="max-w-3xl">
        <form action="{{ route('admin.ebd.games.update', $game->id) }}" method="POST" class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div class="col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $game->is_active ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-amber-600 transition-colors">Jogo Ativo</span>
                    </label>
                </div>

                <!-- Icon -->
                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Ícone (FontAwesome)</label>
                    <div class="flex gap-2">
                        <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-gray-500">
                            <x-icon :name="$game->icon" style="duotone" class="w-6 h-6" />
                        </div>
                        <input type="text" name="icon" value="{{ old('icon', $game->icon) }}" class="flex-1 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Descrição</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500">{{ old('description', $game->description) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-900/20 transition-all active:scale-95">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Tab: Questions -->
    <div x-show="activeTab === 'questions'" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg overflow-hidden">
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/30">
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Banco de Perguntas</h3>
                <button onclick="document.getElementById('addQuestionModal').showModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs uppercase tracking-widest shadow transition-all flex items-center gap-2">
                    <x-icon name="plus" class="w-3 h-3" /> Adicionar Pergunta
                </button>
            </div>

            <!-- List -->
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($game->questions as $question)
                <div class="p-6 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest
                                    {{ $question->difficulty === 'easy' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                      ($question->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                      'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ $question->difficulty }}
                                </span>
                            </div>
                            <h4 class="text-gray-900 dark:text-white font-bold text-lg mb-2">{{ $question->question_text }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-500">
                                @foreach($question->answers as $answer)
                                <div class="flex items-center gap-2 {{ $answer->is_correct ? 'text-green-600 dark:text-green-400 font-bold' : '' }}">
                                    <x-icon name="{{ $answer->is_correct ? 'check-circle' : 'circle' }}" class="w-4 h-4" />
                                    {{ $answer->answer_text }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditModal({{ $question->id }}, {{ json_encode($question) }}, {{ json_encode($question->answers) }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Editar">
                                <x-icon name="pen" class="w-4 h-4" />
                            </button>
                            <form action="{{ route('admin.ebd.games.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Remover esta pergunta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Excluir">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-400">
                    <x-icon name="scroll" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p class="text-sm font-bold uppercase tracking-widest">Nenhuma pergunta cadastrada.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <dialog id="addQuestionModal" class="bg-transparent p-0 w-full max-w-2xl backdrop:bg-slate-950/80 backdrop:backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 p-8 w-full">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Nova Pergunta</h3>

            <form action="{{ route('admin.ebd.games.questions.store', $game->id) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Enunciado</label>
                    <textarea name="question_text" rows="2" class="w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-amber-500 focus:border-amber-500" required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Dificuldade</label>
                    <select name="difficulty" class="w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-amber-500 focus:border-amber-500">
                        <option value="easy">Fácil</option>
                        <option value="medium" selected>Média</option>
                        <option value="hard">Difícil</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-4">Respostas (Marque a correta)</label>
                    <div class="space-y-3">
                        @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-3">
                            <input type="radio" name="correct_answer_index" value="{{ $i }}" class="w-5 h-5 text-green-600 focus:ring-green-500" {{ $i === 0 ? 'checked' : '' }}>
                            <input type="text" name="answers[{{ $i }}][text]" placeholder="Opção {{ $i + 1 }}" class="flex-1 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500" required>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="document.getElementById('addQuestionModal').close()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">Adicionar</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Edit Question Modal (Simplified JS implementation) -->
    <dialog id="editQuestionModal" class="bg-transparent p-0 w-full max-w-2xl backdrop:bg-slate-950/80 backdrop:backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 p-8 w-full">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Editar Pergunta</h3>

            <form id="editQuestionForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Enunciado</label>
                    <textarea id="edit_question_text" name="question_text" rows="2" class="w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-amber-500 focus:border-amber-500" required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Dificuldade</label>
                    <select id="edit_difficulty" name="difficulty" class="w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-amber-500 focus:border-amber-500">
                        <option value="easy">Fácil</option>
                        <option value="medium">Média</option>
                        <option value="hard">Difícil</option>
                    </select>
                </div>

                <div id="edit_answers_container" class="space-y-3">
                    <!-- Populated via JS -->
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="document.getElementById('editQuestionModal').close()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">Salvar</button>
                </div>
            </form>
        </div>
    </dialog>

</div>

<script>
    function openEditModal(id, question, answers) {
        const form = document.getElementById('editQuestionForm');
        form.action = `/admin/ebd/games/questions/${id}`;

        document.getElementById('edit_question_text').value = question.question_text;
        document.getElementById('edit_difficulty').value = question.difficulty;

        const container = document.getElementById('edit_answers_container');
        container.innerHTML = '<label class="block text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-4">Respostas</label>';

        answers.forEach((answer, index) => {
            container.innerHTML += `
                <div class="flex items-center gap-3">
                    <input type="radio" name="correct_answer_index" value="${index}" class="w-5 h-5 text-green-600 focus:ring-green-500" ${answer.is_correct ? 'checked' : ''}>
                    <input type="hidden" name="answers[${index}][id]" value="${answer.id}">
                    <input type="text" name="answers[${index}][text]" value="${answer.answer_text.replace(/"/g, '&quot;')}" class="flex-1 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500" required>
                </div>
            `;
        });

        document.getElementById('editQuestionModal').showModal();
    }
</script>
@endsection

