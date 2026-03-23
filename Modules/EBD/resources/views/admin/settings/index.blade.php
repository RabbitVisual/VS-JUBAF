@extends('admin::components.layouts.master')

@section('title', 'Configurações EBD | EBD Academy')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Configurações <span class="text-blue-600">Globais</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Parâmetros de funcionamento, automações e regras de negócio da academia.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-2xl flex items-center gap-3 animate-in slide-in-from-top-2">
            <x-icon name="circle-check" style="duotone" class="w-5 h-5 text-green-600" />
            <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.ebd.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Engine -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-xl">
                        <x-icon name="gears" style="duotone" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Motor de Conteúdo</h3>
                        <p class="text-xs text-gray-500 font-medium">Padrões de exibição e referências bíblicas.</p>
                    </div>
                </div>
            </div>
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Horário Padrão das Lições</label>
                    <div class="relative group">
                        <x-icon name="clock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <input type="time" name="default_lesson_time"
                            value="{{ old('default_lesson_time', $settings['default_lesson_time']) }}"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                    @error('default_lesson_time') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Versão Bíblica Padrão</label>
                    <div class="relative group">
                        <x-icon name="book-bible" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <select name="default_bible_version"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none cursor-pointer">
                            @foreach ($bibleVersions as $version)
                                <option value="{{ $version['value'] }}"
                                    {{ old('default_bible_version', $settings['default_bible_version']) === $version['value'] ? 'selected' : '' }}>
                                    {{ $version['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('default_bible_version') <p class="text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Automation & Rules -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Attendance Rules -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center gap-3">
                    <div class="p-2 bg-green-600 rounded-xl">
                        <x-icon name="user-check" style="duotone" class="w-4 h-4 text-white" />
                    </div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Regras de Presença</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Janela de Registro (Horas)</label>
                        <input type="number" name="attendance_deadline_hours"
                            value="{{ old('attendance_deadline_hours', $settings['attendance_deadline_hours']) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-green-500/20 transition-all">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter italic">Tempo limite após a lição para registrar presença.</p>
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_create_attendance" value="1"
                                {{ old('auto_create_attendance', $settings['auto_create_attendance']) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Auto-Presença</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">Gerar registros ao criar lição</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Evaluation Rules -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center gap-3">
                    <div class="p-2 bg-amber-500 rounded-xl">
                        <x-icon name="pen-ruler" style="duotone" class="w-4 h-4 text-white" />
                    </div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Avaliações</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Prazo de Resolução (Dias)</label>
                        <input type="number" name="evaluation_deadline_days"
                            value="{{ old('evaluation_deadline_days', $settings['evaluation_deadline_days']) }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-amber-500/20 transition-all">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter italic">Dias disponíveis para o aluno completar sua tarefa.</p>
                    </div>

                    <div class="p-4 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl border border-amber-100/50 dark:border-amber-800/30">
                        <div class="flex items-center gap-3 text-amber-700 dark:text-amber-400">
                            <x-icon name="circle-info" style="duotone" class="w-4 h-4 shrink-0" />
                            <p class="text-[10px] font-black uppercase tracking-tight leading-normal">
                                Após este prazo, as lições podem ser marcadas como expiradas automaticamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-600 rounded-xl">
                        <x-icon name="bell-ring" style="duotone" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Engajamento & Notificações</h3>
                        <p class="text-xs text-gray-500 font-medium">Configure como os alunos são avisados.</p>
                    </div>
                </div>
            </div>
            <div class="p-8 flex flex-col md:flex-row gap-8 items-center">
                <label class="flex-1 flex items-center gap-4 p-6 bg-gray-50 dark:bg-gray-800/30 rounded-3xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50 border border-transparent hover:border-purple-500/20">
                    <div class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="send_lesson_reminders" value="1"
                            {{ old('send_lesson_reminders', $settings['send_lesson_reminders']) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600 rounded-full"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">Lembretes de Aula</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Notificar alunos sobre novas lições</span>
                    </div>
                </label>

                <div class="w-full md:w-72 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Antecedência (Dias)</label>
                    <div class="relative group">
                        <x-icon name="calendar-day" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-purple-600 transition-colors" />
                        <input type="number" name="reminder_days_before"
                            value="{{ old('reminder_days_before', $settings['reminder_days_before']) }}"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-4 p-8 bg-gray-50 dark:bg-gray-950/20 rounded-3xl border border-gray-100 dark:border-gray-800">
            <button type="submit"
                class="inline-flex items-center px-10 py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-black uppercase tracking-widest rounded-2xl transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-gray-950/20">
                <x-icon name="floppy-disk" style="duotone" class="w-4 h-4 mr-3" />
                Salvar Configurações
            </button>
        </div>
    </form>
</div>
@endsection

