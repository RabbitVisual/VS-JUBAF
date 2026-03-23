@extends('admin::components.layouts.master')

@section('title', 'Detalhes da Chamada | ' . $lesson->title)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-emerald-600 text-white rounded">Relatório de Frequência</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Registro de <span class="text-emerald-600">Presença</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">
                Conferência da aula: <span class="text-gray-900 dark:text-white font-bold text-sm uppercase tracking-tight">{{ $lesson->title }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.attendance.create', ['lesson_id' => $lesson->id]) }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="pen-to-square" style="duotone" class="mr-2 h-4 w-4" />
                Editar Chamada
            </a>
            <a href="{{ route('admin.ebd.attendance.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                Voltar
            </a>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Matriculados</span>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $lesson->ebdClass->activeStudents()->count() }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest block mb-1">Confirmados</span>
            <div class="text-3xl font-black text-emerald-600">{{ $attendance->where('status', 'present')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <span class="text-[10px] font-black text-red-500 uppercase tracking-widest block mb-1">Ausentes</span>
            <div class="text-3xl font-black text-red-600">{{ $attendance->where('status', 'absent')->count() }}</div>
        </div>
        <div class="bg-emerald-600 rounded-3xl p-6 shadow-xl shadow-emerald-600/20 text-white relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 opacity-10">
                <x-icon name="chart-pie" class="w-20 h-20 text-white" />
            </div>
            <span class="text-[10px] font-black text-emerald-100 uppercase tracking-widest block mb-1">Taxa de Presença</span>
            <div class="text-3xl font-black">
                {{ $attendance->count() > 0 ? round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between">
            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Lista de Participação</h3>
            <div class="flex items-center gap-2">
                <span class="text-[9px] font-black text-gray-400 uppercase">Aula realizada em:</span>
                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ $lesson->lesson_date->format('d M, Y') }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estudante</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Atual</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Check-in</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Notas Acadêmicas</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Responsável</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach ($lesson->ebdClass->activeStudents()->with('user')->get() as $student)
                        @php $record = $attendance->get($student->id); @endphp
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 font-black text-sm group-hover:scale-110 transition-transform">
                                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $student->user->name }}</div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-xs font-black text-gray-500 uppercase tracking-widest">
                                @if ($record)
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border
                                        @if ($record->status === 'present') bg-emerald-50 border-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400
                                        @elseif($record->status === 'late') bg-amber-50 border-amber-100 text-amber-700 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400
                                        @elseif($record->status === 'excused') bg-blue-50 border-blue-100 text-blue-700 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400
                                        @else bg-red-50 border-red-100 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 @endif">
                                        {{ $record->status_display }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-gray-50 border border-gray-100 text-gray-400 dark:bg-gray-800 dark:border-gray-700">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-[11px] font-black text-gray-500 font-mono">
                                {{ $record && $record->arrival_time ? \Carbon\Carbon::parse($record->arrival_time)->format('H:i') : '--:--' }}
                            </td>
                            <td class="px-8 py-6 text-[10px] font-bold text-gray-400 max-w-xs truncate italic">
                                {{ $record->notes ?? 'Nenhuma observação' }}
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                {{ $record->registeredBy->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

