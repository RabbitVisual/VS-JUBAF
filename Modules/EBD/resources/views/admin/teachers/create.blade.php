@extends('admin::components.layouts.master')

@section('title', 'Adicionar Professor EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-cyan-600 text-white rounded">Corpo Docente</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Novo <span class="text-cyan-600">Educador</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Credencie um especialista para conduzir o ensino bíblico em nossas turmas.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.teachers.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.ebd.teachers.store') }}" method="POST" class="space-y-8 max-w-5xl pb-20">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Identification & Role -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                    <x-icon name="user-tie" style="duotone" class="w-5 h-5 text-cyan-600" />
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Perfil & Atribuição</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Membro / Usuário</label>
                        <select name="user_id" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-cyan-500/20 transition-all cursor-pointer">
                            <option value="">Selecione o professor</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Função Docente</label>
                        <select name="role" id="role" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-cyan-500/20 transition-all cursor-pointer">
                            <option value="">Selecione o cargo</option>
                            <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Professor Titular</option>
                            <option value="assistant" {{ old('role') === 'assistant' ? 'selected' : '' }}>Monitor / Auxiliar</option>
                            <option value="substitute" {{ old('role') === 'substitute' ? 'selected' : '' }}>Professor Substituto</option>
                        </select>
                        @error('role') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Allocation & Cycle -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                    <x-icon name="calendar-range" style="duotone" class="w-5 h-5 text-cyan-600" />
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Ciclo & Alocação</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Classe de Atuação</label>
                        <select name="class_id" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-cyan-500/20 transition-all cursor-pointer">
                            <option value="">Selecione a classe</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->age_group_display }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data de Início</label>
                            <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-xs font-black focus:ring-2 focus:ring-cyan-500/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Previsão Término</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-xs font-black focus:ring-2 focus:ring-cyan-500/20 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width: Footer Actions & Notes -->
            <div class="md:col-span-2 space-y-8">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Status de Atividade</h4>
                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-tighter mt-1">Define se o professor tem permissão de acesso ao diário de classe.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-cyan-600 shadow-inner"></div>
                        </label>
                    </div>
                    <div class="p-8">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">Histórico ou Observações</label>
                        <textarea name="notes" rows="3" placeholder="Qualificações, especialidades teológicas ou observações administrativas..."
                            class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-3xl text-sm font-medium focus:ring-2 focus:ring-cyan-500/20 transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-center pt-4">
            <button type="submit" class="flex items-center gap-3 px-14 py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-2xl font-black text-xs uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-2xl">
                <x-icon name="user-plus" class="w-4 h-4" />
                Credenciar Educador
            </button>
        </div>
    </form>
</div>
@endsection

