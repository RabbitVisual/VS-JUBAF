@extends('memberpanel::components.layouts.master')

@section('title', 'Lições EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm mb-2">
                    <a href="{{ route('memberpanel.ebd.student.index') }}" class="text-gray-400 dark:text-slate-500 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        <x-icon name="house" class="w-4 h-4" />
                    </a>
                    <x-icon name="chevron-right" class="w-3 h-3 text-gray-300 dark:text-slate-600" />
                    <span class="text-gray-600 dark:text-slate-300 font-medium">Lições</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Lições Sagradas</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Aprofunde-se na Palavra de Deus através de lições estruturadas.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.student.index') }}" class="inline-flex items-center px-5 py-3 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar ao Painel
            </a>
        </div>

        <!-- Hero Info Card -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-80 h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px] opacity-50"></div>
            </div>

            <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-8 z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-purple-500/20">
                    <x-icon name="book-open-reader" style="duotone" class="w-8 h-8 text-white" />
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800 mb-2">
                        <x-icon name="scroll" class="w-3 h-3 text-purple-600 dark:text-purple-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">Biblioteca de Conhecimento</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        Explore o <span class="text-purple-600 dark:text-purple-400">conteúdo</span>
                    </h2>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-sm mt-1">
                        Materiais exclusivos e avaliações práticas para sua edificação espiritual.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="GET" action="{{ route('memberpanel.ebd.student.lessons') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1 space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-2 px-1">
                        <x-icon name="filter" class="w-3 h-3 text-purple-500" /> Minha classe
                    </label>
                    <div class="relative">
                        <select name="class_id" class="w-full appearance-none px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all cursor-pointer">
                            <option value="">Todas as minhas classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (isset($selectedClassId) ? $selectedClassId : request('class_id')) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-icon name="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" />
                    </div>
                </div>

                <div class="flex-1 space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-2 px-1">
                        <x-icon name="shield-check" class="w-3 h-3 text-emerald-500" /> Status
                    </label>
                    <div class="relative">
                        <select name="status" class="w-full appearance-none px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all cursor-pointer">
                            <option value="">Todos os Status</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Agendada</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluída</option>
                        </select>
                        <x-icon name="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" />
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm transition-all shadow-lg hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white active:scale-[0.98] flex items-center gap-2">
                        <x-icon name="magnifying-glass" class="w-4 h-4" />
                        Filtrar
                    </button>
                    @if(request()->hasAny(['class_id', 'status']))
                        <a href="{{ route('memberpanel.ebd.student.lessons') }}" class="px-4 py-3 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl font-bold text-sm hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lessons List -->
        @if($lessons->count() > 0)
        <div class="space-y-4">
            @foreach($lessons as $lesson)
            <div class="group bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 hover:shadow-xl hover:border-purple-200 dark:hover:border-purple-800 transition-all duration-300">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    <!-- Date Box -->
                    <div class="shrink-0 flex lg:flex-col items-center justify-center w-full lg:w-20 h-16 lg:h-24 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 group-hover:border-purple-300 dark:group-hover:border-purple-700 transition-colors">
                        <span class="text-[10px] font-bold uppercase text-purple-600 dark:text-purple-400 tracking-wider mr-2 lg:mr-0 lg:mb-1">{{ $lesson->lesson_date->format('M') }}</span>
                        <span class="text-2xl lg:text-3xl font-black text-gray-900 dark:text-white leading-none">{{ $lesson->lesson_date->format('d') }}</span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                {{ $lesson->ebdClass->name }}
                            </span>
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg border
                                @if($lesson->status === 'scheduled') bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800
                                @elseif($lesson->status === 'in_progress') bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800
                                @elseif($lesson->status === 'completed') bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800
                                @else bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border-gray-200 dark:border-slate-700 @endif">
                                {{ $lesson->status_display }}
                            </span>
                        </div>

                        @php
                            $lessonTime = \Carbon\Carbon::parse($lesson->lesson_time);
                            $lessonDateTime = $lesson->lesson_date->copy()->setTime($lessonTime->hour, $lessonTime->minute);
                            $state = $lessonStates[$lesson->id] ?? [];
                            $canAccess = $state['can_access'] ?? false;
                            $availableByDate = $state['available_by_date'] ?? $lessonDateTime->lte(now());
                            $unlockedByProgression = $state['unlocked_by_progression'] ?? true;
                            $isCompleted = $state['is_completed'] ?? $lesson->isCompletedByUser(auth()->id());
                            $isPending = $state['is_pending'] ?? false;
                        @endphp

                        <div class="mb-1 flex flex-wrap items-center gap-2">
                            @if($canAccess)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Disponível agora
                                </span>
                            @elseif($availableByDate && !$unlockedByProgression)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-[10px] font-bold uppercase tracking-wider">
                                    <x-icon name="lock" class="w-3 h-3" />
                                    Conclua a aula anterior
                                </span>
                            @elseif($isPending)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-[10px] font-bold uppercase tracking-wider">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    Pendente
                                </span>
                            @elseif(!$availableByDate)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    Em breve • {{ $lessonDateTime->format('d/m H:i') }}
                                </span>
                            @endif

                            @if($isCompleted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                                    <x-icon name="circle-check" class="w-3 h-3" />
                                    Concluída
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                            {{ $lesson->title }}
                        </h3>

                        @if($lesson->description)
                            <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 mb-3 max-w-3xl">
                                {{ $lesson->description }}
                            </p>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 dark:text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="clock" class="w-4 h-4 text-purple-500" />
                                {{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}
                            </span>
                            @if($lesson->bible_reference)
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="book-bible" class="w-4 h-4 text-amber-500" />
                                    {{ $lesson->bible_reference }}
                                </span>
                            @endif
                            @if($lesson->materials->count() > 0)
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="paperclip" class="w-4 h-4 text-emerald-500" />
                                    {{ $lesson->materials->count() }} Arquivo(s)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="shrink-0">
                        @if($canAccess)
                            <a href="{{ route('memberpanel.ebd.student.classroom.player', $lesson->id) }}" class="inline-flex items-center justify-center w-full lg:w-auto px-6 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm transition-all shadow-lg hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white active:scale-[0.98]">
                                {{ $isPending ? 'Continuar' : 'Acessar' }}
                                <x-icon name="arrow-right" class="w-4 h-4 ml-2" />
                            </a>
                        @elseif($availableByDate && !$unlockedByProgression)
                            <div class="flex flex-col items-center justify-center px-6 py-4 bg-amber-50 dark:bg-amber-900/20 border-2 border-dashed border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400">
                                <x-icon name="lock" class="w-5 h-5 mb-1" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Conclua a aula anterior</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center px-6 py-4 bg-gray-100 dark:bg-slate-800 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-xl text-gray-400 dark:text-slate-500">
                                <x-icon name="lock" class="w-5 h-5 mb-1" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Indisponível</span>
                                <span class="text-[9px] mt-1 opacity-70">Libera: {{ $lessonDateTime->format('d M') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-gray-200 dark:border-slate-800 p-16 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <x-icon name="scroll" style="duotone" class="w-10 h-10 text-gray-300 dark:text-slate-600" />
            </div>
            <h3 class="text-xl font-black text-gray-400 dark:text-slate-500 mb-2">Nenhuma Lição Encontrada</h3>
            <p class="text-sm text-gray-400 dark:text-slate-500 max-w-md mx-auto">
                @if(request()->hasAny(['class_id', 'status']))
                    Nenhuma lição corresponde ao filtro selecionado. Tente uma nova busca!
                @else
                    Sua jornada está sendo preparada. Fique atento para novas lições!
                @endif
            </p>
        </div>
        @endif

        <!-- Pagination -->
        @if($lessons->hasPages())
        <div class="flex justify-center">
            {{ $lessons->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
