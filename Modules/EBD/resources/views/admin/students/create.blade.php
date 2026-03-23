@extends('admin::components.layouts.master')

@section('title', 'Matricular Aluno EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded">Gestão de Alunos</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Nova <span class="text-indigo-600">Matrícula</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Cadastre um novo estudante em uma jornada acadêmica e espiritual.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.students.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.ebd.students.store') }}" method="POST" class="space-y-8 max-w-5xl pb-20">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Side: Identification -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                    <x-icon name="user-large" style="duotone" class="w-5 h-5 text-indigo-600" />
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Identificação do Membro</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Usuário do Sistema</label>
                        <select name="user_id" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                            <option value="">Selecione o usuário</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data de Matrícula</label>
                        <input type="date" name="enrollment_date" value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required
                            class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                </div>
            </div>

            <!-- Right Side: Allocation -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                    <x-icon name="school" style="duotone" class="w-5 h-5 text-indigo-600" />
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Alocação de Turma</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Classe EBD</label>
                        <select name="class_id" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                            <option value="">Selecione a classe</option>
                            @foreach ($classes as $class)
                                @php
                                    $currentCount = \Modules\EBD\App\Models\EBDStudent::where('class_id', $class->id)->where('is_active', true)->count();
                                    $isFull = $class->max_students && $currentCount >= $class->max_students;
                                @endphp
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                    {{ $class->name }} ({{ $class->age_group_display }})
                                    @if ($isFull) - LOTADA @elseif($class->max_students) - {{ $currentCount }}/{{ $class->max_students }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-[9px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl border border-transparent group hover:border-indigo-500/20 transition-all">
                        <div>
                            <h4 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Matrícula Ativa</h4>
                            <p class="text-[9px] font-medium text-gray-500 uppercase tracking-tighter">O aluno será listado nas chamadas.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Full Width: Notes -->
            <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest text-center">Observações Adicionais</h3>
                </div>
                <div class="p-8">
                    <textarea name="notes" rows="3" placeholder="Restrições, necessidades especiais ou histórico acadêmico..."
                        class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center">
            <button type="submit" class="flex items-center gap-3 px-12 py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-2xl font-black text-xs uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-2xl">
                <x-icon name="check-to-slot" style="duotone" class="w-4 h-4" />
                Confirmar Matrícula
            </button>
        </div>
    </form>
</div>
@endsection

