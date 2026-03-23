@extends('memberpanel::components.layouts.master')

@section('title', 'Presença - ' . $lesson->title)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="attendanceManager()">
    <div class="max-w-5xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Lições</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium truncate max-w-[140px] sm:max-w-none">Presença</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Registrar Presença</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ $lesson->title }} • {{ $lesson->ebdClass->name }}</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.lessons.show', $lesson) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Voltar à Lição
            </a>
        </div>

        <!-- Hero -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-teal-400 dark:bg-teal-600 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-xl shadow-emerald-500/20 shrink-0">
                    <x-icon name="user-check" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 mb-2">
                        <x-icon name="clipboard-list" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Lista de Chamada</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $lesson->lesson_date->format('d/m/Y') }}</h2>
                    <p class="text-gray-500 dark:text-slate-300 text-sm mt-1">Marque presença, atraso, justificado ou falta.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('memberpanel.ebd.teacher.attendance.store', $lesson) }}" method="POST" class="space-y-8" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando chamada...' } }))">
            @csrf

            <!-- Quick actions: mobile-first large touch targets -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Alunos</h3>
                <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-row">
                    <button type="button" @click="markAll('present')" class="min-h-[52px] sm:min-h-0 px-5 py-4 sm:py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl text-sm font-bold transition-all active:scale-[0.98] touch-manipulation flex items-center justify-center gap-2">
                        <x-icon name="check-circle" style="duotone" class="w-5 h-5 shrink-0" />
                        Todos Presentes
                    </button>
                    <button type="button" @click="markAll('absent')" class="min-h-[52px] sm:min-h-0 px-5 py-4 sm:py-3 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl text-sm font-bold transition-all active:scale-[0.98] touch-manipulation flex items-center justify-center gap-2">
                        <x-icon name="xmark-circle" style="duotone" class="w-5 h-5 shrink-0" />
                        Todos Ausentes
                    </button>
                </div>
            </div>

            <!-- Students list: mobile-first with large Presente/Ausente buttons -->
            <div class="space-y-5">
                @forelse($lesson->ebdClass->activeStudents as $student)
                    @php $attendance = $existingAttendance->get($student->id); @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
                        <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">

                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden border-2 border-gray-200 dark:border-slate-700 shrink-0">
                                    <img src="{{ $student->user->avatar_url }}" alt="{{ $student->user->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-lg text-gray-900 dark:text-white truncate">{{ $student->user->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $student->user->email ?? '—' }}</p>
                                </div>
                            </div>

                            <!-- Primary: Presente | Ausente — large mobile buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer touch-manipulation min-h-[56px] flex">
                                    <input type="radio" name="attendance[{{ $loop->index }}][status]" value="present"
                                        {{ old('attendance.' . $loop->index . '.status', $attendance?->status ?? 'present') === 'present' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div class="w-full flex items-center justify-center gap-2 rounded-2xl border-2 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white text-emerald-700 dark:text-emerald-300 py-4 px-4 transition-all min-h-[56px]">
                                        <x-icon name="check-circle" style="duotone" class="w-6 h-6 shrink-0" />
                                        <span class="font-bold text-sm">Presente</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer touch-manipulation min-h-[56px] flex">
                                    <input type="radio" name="attendance[{{ $loop->index }}][status]" value="absent"
                                        {{ old('attendance.' . $loop->index . '.status', $attendance?->status) === 'absent' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div class="w-full flex items-center justify-center gap-2 rounded-2xl border-2 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white text-rose-700 dark:text-rose-300 py-4 px-4 transition-all min-h-[56px]">
                                        <x-icon name="xmark-circle" style="duotone" class="w-6 h-6 shrink-0" />
                                        <span class="font-bold text-sm">Ausente</span>
                                    </div>
                                </label>
                            </div>
                            <!-- Secondary: Atrasado | Justificado -->
                            <div class="grid grid-cols-2 gap-2">
                                <label class="relative cursor-pointer touch-manipulation min-h-[48px] flex">
                                    <input type="radio" name="attendance[{{ $loop->index }}][status]" value="late"
                                        {{ old('attendance.' . $loop->index . '.status', $attendance?->status) === 'late' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div class="w-full flex items-center justify-center gap-1.5 rounded-xl border-2 border-gray-100 dark:border-slate-700 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 text-center py-3 px-3 transition-all min-h-[48px]">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400">Atrasado</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer touch-manipulation min-h-[48px] flex">
                                    <input type="radio" name="attendance[{{ $loop->index }}][status]" value="excused"
                                        {{ old('attendance.' . $loop->index . '.status', $attendance?->status) === 'excused' ? 'checked' : '' }}
                                        class="peer sr-only">
                                    <div class="w-full flex items-center justify-center gap-1.5 rounded-xl border-2 border-gray-100 dark:border-slate-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center py-3 px-3 transition-all min-h-[48px]">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400 peer-checked:text-blue-600 dark:peer-checked:text-blue-400">Justificado</span>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2 pt-2 border-t border-gray-100 dark:border-slate-800">
                                <input type="time" name="attendance[{{ $loop->index }}][arrival_time]"
                                    value="{{ old('attendance.' . $loop->index . '.arrival_time', $attendance ? \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i') : '') }}"
                                    class="min-h-[44px] flex-1 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-gray-700 dark:text-slate-300 focus:ring-2 focus:ring-emerald-500/20 touch-manipulation">
                                <input type="text" name="attendance[{{ $loop->index }}][notes]"
                                    value="{{ old('attendance.' . $loop->index . '.notes', $attendance?->notes) }}"
                                    placeholder="Observações"
                                    class="min-h-[44px] flex-1 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 dark:text-slate-300 placeholder-gray-400 focus:ring-2 focus:ring-emerald-500/20 touch-manipulation">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-12 text-center">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                            <x-icon name="users-slash" style="duotone" class="w-7 h-7 text-gray-400 dark:text-slate-500" />
                        </div>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Nenhum aluno matriculado nesta turma.</p>
                    </div>
                @endforelse
            </div>

            @if($lesson->ebdClass->activeStudents->count() > 0)
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-slate-800">
                    <a href="{{ route('memberpanel.ebd.teacher.lessons.show', $lesson) }}" class="text-sm font-bold text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Descartar
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-600/20 active:scale-[0.98]">
                        <x-icon name="check-double" class="w-4 h-4" />
                        Salvar Chamada
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
function attendanceManager() {
    return {
        markAll(status) {
            document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(radio => { radio.checked = true; });
        }
    };
}
</script>
@endpush
@endsection
