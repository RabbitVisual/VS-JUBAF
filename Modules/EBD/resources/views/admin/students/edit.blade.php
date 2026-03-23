@extends('admin::components.layouts.master')

@section('title', 'Editar Matrícula | ' . $student->user->name)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Estudante</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Editar <span class="text-blue-600">Matrícula</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gerencie o registro e o vínculo acadêmico de {{ $student->user->name }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.students.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Presenças</span>
            <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $student->attendance->where('status', 'present')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Avaliações</span>
            <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $student->evaluations->count() }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Média Score</span>
            <div class="text-2xl font-black text-blue-600">{{ $student->evaluations->where('status', 'graded')->avg('score') ? number_format($student->evaluations->where('status', 'graded')->avg('score'), 1) : '--' }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-gray-800">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Tempo EBD</span>
            <div class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ $student->enrollment_date->diffForHumans() }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <form action="{{ route('admin.ebd.students.update', $student) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- User Association -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Usuário do Sistema</label>
                    <div class="relative group">
                        <x-icon name="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <select name="user_id" required class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer @error('user_id') ring-2 ring-red-500/20 @enderror">
                            <option value="">Selecione o usuário</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $student->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('user_id') <p class="text-[10px] font-bold text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Class Association -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Classe Designada</label>
                    <div class="relative group">
                        <x-icon name="graduation-cap" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                        <select name="class_id" required class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer @error('class_id') ring-2 ring-red-500/20 @enderror">
                            <option value="">Selecione a classe</option>
                            @foreach ($classes as $class)
                                @php
                                    $currentCount = \Modules\EBD\App\Models\EBDStudent::where('class_id', $class->id)
                                        ->where('is_active', true)
                                        ->where('id', '!=', $student->id)
                                        ->count();
                                    $isFull = $class->max_students && $currentCount >= $class->max_students && $class->id != $student->class_id;
                                @endphp
                                <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                    {{ $class->name }} ({{ $class->age_group_display }})
                                    @if ($isFull) - LOTADA @elseif($class->max_students) - {{ $currentCount }}/{{ $class->max_students }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Enrollment Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data de Matrícula</label>
                    <input type="date" name="enrollment_date" value="{{ old('enrollment_date', $student->enrollment_date->format('Y-m-d')) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all @error('enrollment_date') ring-2 ring-red-500/20 @enderror">
                </div>

                <!-- Graduation/Exit Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data de Saída/Graduação</label>
                    <input type="date" name="graduation_date" value="{{ old('graduation_date', $student->graduation_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>

                <!-- Status & Active -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50 w-fit pr-12">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $student->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Matrícula Ativa</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">Habilitar acesso do aluno ao painel EBD</span>
                        </div>
                    </label>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Observações Acadêmicas</label>
                    <textarea name="notes" rows="4" placeholder="Algum detalhe relevante sobre este aluno..."
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20 transition-all resize-none">{{ old('notes', $student->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-8 border-t border-gray-50 dark:border-gray-800">
                <a href="{{ route('admin.ebd.students.index') }}" class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">Cancelar</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl shadow-blue-600/20 active:scale-95">
                    Atualizar Registro
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

