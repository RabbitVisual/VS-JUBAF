@extends('admin::components.layouts.master')

@section('title', 'Chamada | ' . $lesson->title)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-emerald-600 text-white rounded">Registro de Presença</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Realizar <span class="text-emerald-600">Chamada</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">
                Sessão pedagógica: <span class="text-gray-900 dark:text-white font-bold text-sm uppercase tracking-tight">{{ $lesson->title }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.attendance.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Lesson Context Card -->
    <div class="bg-emerald-600 rounded-3xl p-8 shadow-xl shadow-emerald-600/20 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="absolute -right-4 -top-4 opacity-10">
            <x-icon name="clipboard-user" style="duotone" class="w-48 h-48 text-white" />
        </div>
        <div class="z-10 flex gap-8">
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Classe</span>
                <span class="text-lg font-black text-white uppercase">{{ $lesson->ebdClass->name }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Data</span>
                <span class="text-lg font-black text-white uppercase">{{ $lesson->lesson_date->format('d/m/Y') }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Alunos Ativos</span>
                <span class="text-lg font-black text-white uppercase">{{ $lesson->ebdClass->activeStudents->count() }}</span>
            </div>
        </div>
        <div class="z-10">
            <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1 block">Horário Previsto</span>
            <div class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-xl inline-flex items-center gap-2">
                <x-icon name="clock" class="w-4 h-4 text-white" />
                <span class="text-sm font-black text-white font-mono">{{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.ebd.attendance.store') }}" method="POST" class="space-y-6 pb-20">
        @csrf
        <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">

        <div class="grid grid-cols-1 gap-4">
            @forelse ($lesson->ebdClass->activeStudents as $index => $student)
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col md:flex-row md:items-center gap-6 group hover:shadow-md transition-all">
                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">

                    <div class="flex items-center gap-4 min-w-[300px]">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform">
                            <span class="text-lg font-black uppercase">{{ substr($student->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $student->user->name }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $student->user->email }}</p>
                        </div>
                    </div>

                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status de Presença</label>
                            <select name="attendance[{{ $index }}][status]" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-xs font-black focus:ring-2 focus:ring-emerald-500/20 transition-all cursor-pointer">
                                <option value="present" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id] === 'present' ? 'selected' : '' }}>Presente</option>
                                <option value="absent" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id] === 'absent' ? 'selected' : '' }}>Ausente</option>
                                <option value="late" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id] === 'late' ? 'selected' : '' }}>Atrasado</option>
                                <option value="excused" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id] === 'excused' ? 'selected' : '' }}>Justificado</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Horário de Chegada</label>
                            <input type="time" name="attendance[{{ $index }}][arrival_time]"
                                value="{{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-xs font-black focus:ring-2 focus:ring-emerald-500/20 transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Observações</label>
                            <input type="text" name="attendance[{{ $index }}][notes]" placeholder="..."
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-xs font-medium focus:ring-2 focus:ring-emerald-500/20 transition-all">
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl p-16 text-center">
                    <x-icon name="users-slash" style="duotone" class="w-16 h-16 text-gray-200 mx-auto mb-4" />
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Nenhum aluno ativo nesta classe</h3>
                </div>
            @endforelse
        </div>

        @if ($lesson->ebdClass->activeStudents->count() > 0)
            <div class="fixed bottom-8 right-8 z-50">
                <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl hover:scale-105 active:scale-95 transition-all">
                    <x-icon name="check-double" class="w-4 h-4" />
                    Salvar Chamada
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

