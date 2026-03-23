@extends('memberpanel::components.layouts.master')

@section('title', 'Lições - EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" data-tour="ebd-teacher-lessons">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal do Professor</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium">Lições</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Cânon de Lições</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Cronograma e materiais das suas turmas.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Painel
            </a>
        </div>

        <!-- Hero -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl shadow-indigo-500/20 shrink-0">
                    <x-icon name="book-open-reader" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 mb-2">
                        <x-icon name="book-open" class="w-3 h-3 text-indigo-600 dark:text-indigo-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Biblioteca de Conteúdo</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Suas lições</h2>
                    <p class="text-gray-500 dark:text-slate-300 text-sm mt-1">Filtre por turma e status.</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-4 sm:p-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <div class="flex-1 min-w-0">
                    <select name="class_id" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Todas as turmas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Todos os status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendadas</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluídas</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98]">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['class_id', 'status']))
                        <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="px-5 py-3 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 rounded-xl text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-slate-700">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($lessons->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($lessons as $lesson)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col">
                        <div class="p-5 sm:p-6 flex-1">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <span class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-bold uppercase">
                                    {{ $lesson->ebdClass->name }}
                                </span>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase
                                        @if($lesson->status === 'scheduled') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                        @elseif($lesson->status === 'in_progress') bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400
                                        @elseif($lesson->status === 'completed') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                                        @else bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 @endif">
                                        {{ $lesson->status_display }}
                                    </span>

                                    @php
                                        $presentCount = $lesson->attendance->where('status', 'present')->count();
                                        $hasAttendance = $presentCount > 0 || $lesson->attendance->count() > 0;
                                        $pendingEvaluationsCount = $lesson->evaluations->where('status', 'completed')->count();

                                        $stats = $completionStats[$lesson->id] ?? ['completed' => 0, 'total' => $lesson->ebdClass->activeStudents->count()];
                                        $totalStudents = $stats['total'];
                                        $completedStudents = $stats['completed'];
                                    @endphp

                                    @if($hasAttendance)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold uppercase tracking-wider">
                                            <x-icon name="clipboard-check" class="w-3 h-3" />
                                            Presença {{ $presentCount }}/{{ $totalStudents }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[9px] font-bold uppercase tracking-wider">
                                            <x-icon name="clipboard-question" class="w-3 h-3" />
                                            Presença pendente
                                        </span>
                                    @endif

                                    @if($totalStudents > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50/60 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[9px] font-bold uppercase tracking-wider">
                                            <x-icon name="circle-check" class="w-3 h-3" />
                                            Conclusão {{ $completedStudents }}/{{ $totalStudents }}
                                        </span>
                                    @endif

                                    @if($pendingEvaluationsCount > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[9px] font-bold uppercase tracking-wider">
                                            <x-icon name="file-pen" class="w-3 h-3" />
                                            {{ $pendingEvaluationsCount }} avaliação(ões)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-2 min-h-[2.5rem] mb-4">{{ $lesson->title }}</h3>
                            <div class="space-y-2 text-xs text-gray-500 dark:text-slate-400">
                                <div class="flex items-center gap-2">
                                    <x-icon name="calendar-days" class="w-4 h-4 text-indigo-500" />
                                    <span>{{ $lesson->lesson_date->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}</span>
                                </div>
                                @if($lesson->bible_reference)
                                    <div class="flex items-center gap-2 truncate">
                                        <x-icon name="book-bible" class="w-4 h-4 text-indigo-500 shrink-0" />
                                        <span class="truncate">{{ $lesson->bible_reference }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <x-icon name="paperclip" class="w-4 h-4 text-indigo-500" />
                                    <span>{{ $lesson->materials->count() }} {{ $lesson->materials->count() === 1 ? 'material' : 'materiais' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('memberpanel.ebd.teacher.lessons.show', $lesson) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl font-bold text-sm transition-all hover:bg-indigo-600 hover:text-white active:scale-[0.98]">
                                    <x-icon name="book-open-reader" class="w-4 h-4" />
                                    Detalhes
                                </a>
                                <a href="{{ route('memberpanel.ebd.teacher.attendance.manage', $lesson) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-xl font-bold text-sm transition-all hover:bg-emerald-600 hover:text-white active:scale-[0.98]">
                                    <x-icon name="clipboard-user" class="w-4 h-4" />
                                    Presença
                                </a>
                                <a href="{{ route('memberpanel.ebd.teacher.evaluations', ['lesson_id' => $lesson->id]) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-xl font-bold text-sm transition-all hover:bg-amber-500 hover:text-white active:scale-[0.98]">
                                    <x-icon name="file-pen" class="w-4 h-4" />
                                    Avaliações
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $lessons->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                    <x-icon name="book-slash" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 dark:text-slate-500" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma lição encontrada</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">Ajuste os filtros ou aguarde novas lições.</p>
            </div>
        @endif
    </div>
</div>
@endsection
