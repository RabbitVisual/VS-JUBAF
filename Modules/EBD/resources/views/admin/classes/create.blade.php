@extends('admin::components.layouts.master')

@section('title', 'Criar Classe EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Configuração Acadêmica</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Nova <span class="text-blue-600">Classe</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Estabeleça uma nova turma, definindo público-alvo, localização e logística das aulas.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.classes.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <form action="{{ route('admin.ebd.classes.store') }}" method="POST" class="p-8 space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando turma...' } }))">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Name -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome da Classe</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Adultos, Jovens 'Radicais', Crianças I..."
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all @error('name') ring-2 ring-red-500/20 @enderror">
                    @error('name') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Age Group -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Grupo Etário</label>
                    <select name="age_group" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                        <option value="">Selecione o grupo</option>
                        <option value="adult" {{ old('age_group') === 'adult' ? 'selected' : '' }}>Adultos</option>
                        <option value="youth" {{ old('age_group') === 'youth' ? 'selected' : '' }}>Jovens</option>
                        <option value="teen" {{ old('age_group') === 'teen' ? 'selected' : '' }}>Adolescentes</option>
                        <option value="children" {{ old('age_group') === 'children' ? 'selected' : '' }}>Crianças</option>
                    </select>
                </div>

                <!-- Course -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Curso (currículo)</label>
                    <select name="course_id" class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                        <option value="">Selecione um curso homologado</option>
                        @foreach($courses ?? [] as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Room -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sala / Localização</label>
                    <div class="relative group">
                        <x-icon name="door-open" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <input type="text" name="room" value="{{ old('room') }}" placeholder="Ex: Sala 01, Templo..."
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                </div>

                <!-- Time -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Horário das Aulas</label>
                    <div class="relative group">
                        <x-icon name="clock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <input type="time" name="schedule_time" value="{{ old('schedule_time', $defaultLessonTime ?? '09:00') }}" required
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                </div>

                <!-- Capacity -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Capacidade Máxima</label>
                    <input type="number" name="max_students" value="{{ old('max_students') }}" placeholder="Ilimitado"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>

                <!-- Order -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ordem Listagem</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>

                <!-- Active Status -->
                <div class="md:col-span-1">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50 w-fit pr-12">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Classe Aberta</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">Habilitar matrículas e visualização</span>
                        </div>
                    </label>
                </div>

                <!-- Description -->
                <div class="md:col-span-3 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição e Objetivos</label>
                    <textarea name="description" rows="5" placeholder="Qual o propósito pedagógico desta turma?"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20 transition-all resize-none">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-8 border-t border-gray-50 dark:border-gray-800">
                <a href="{{ route('admin.ebd.classes.index') }}" class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">Cancelar</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl shadow-blue-600/20 active:scale-95">
                    Criar Classe EBD
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

