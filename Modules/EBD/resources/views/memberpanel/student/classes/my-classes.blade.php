@extends('memberpanel::components.layouts.master')

@section('title', 'Minhas Classes EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm mb-2">
                    <a href="{{ route('memberpanel.ebd.student.index') }}" class="text-gray-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <x-icon name="house" class="w-4 h-4" />
                    </a>
                    <x-icon name="chevron-right" class="w-3 h-3 text-gray-300 dark:text-slate-600" />
                    <span class="text-gray-600 dark:text-slate-300 font-medium">Minhas Classes</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Minhas Classes</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Acompanhe sua jornada em todas as classes matriculadas.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.student.index') }}" class="inline-flex items-center px-5 py-3 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar ao Painel
            </a>
        </div>

        <!-- Hero Info Card -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px] opacity-50"></div>
            </div>

            <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-8 z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-blue-500/20">
                    <x-icon name="users" style="duotone" class="w-8 h-8 text-white" />
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 mb-2">
                        <x-icon name="graduation-cap" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">Meus Estudos</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        Onde você <span class="text-blue-600 dark:text-blue-400">pertence</span>
                    </h2>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-sm mt-1">
                        A Palavra de Deus é viva e eficaz! Explore suas turmas e cresça na fé.
                    </p>
                </div>
                <div class="shrink-0 bg-gray-50 dark:bg-slate-800 rounded-2xl px-6 py-4 border border-gray-100 dark:border-slate-700 text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Total de Classes</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $studentEnrollments->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Classes Grid -->
        @if($studentEnrollments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-tour="ebd-student-classes">
            @foreach($studentEnrollments as $enrollment)
            <div class="group bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 hover:shadow-xl hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300 flex flex-col h-full">
                <!-- Header with Status -->
                <div class="flex items-start justify-between mb-5">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110
                        @if($enrollment->ebdClass->age_group === 'adult') bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800
                        @elseif($enrollment->ebdClass->age_group === 'youth') bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800
                        @elseif($enrollment->ebdClass->age_group === 'teen') bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-800
                        @else bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800 @endif">
                        @if($enrollment->ebdClass->age_group === 'adult') <x-icon name="user-tie" style="duotone" class="w-7 h-7" />
                        @elseif($enrollment->ebdClass->age_group === 'youth') <x-icon name="users" style="duotone" class="w-7 h-7" />
                        @elseif($enrollment->ebdClass->age_group === 'teen') <x-icon name="headphones" style="duotone" class="w-7 h-7" />
                        @else <x-icon name="baby-carriage" style="duotone" class="w-7 h-7" /> @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($enrollment->ebdClass->is_active)
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <span class="text-[9px] font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">Ativa</span>
                        @else
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                            <span class="text-[9px] font-bold uppercase text-gray-400 tracking-wider">Inativa</span>
                        @endif
                    </div>
                </div>

                <!-- Info -->
                <div class="space-y-2 mb-4">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $enrollment->ebdClass->name }}
                    </h3>
                    <span class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-slate-800 rounded-lg text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                        {{ $enrollment->ebdClass->age_group_display }}
                    </span>
                </div>

                <!-- Description -->
                <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed flex-1 mb-5">
                    {{ Str::limit($enrollment->ebdClass->description ?? 'Nenhuma descrição disponível para esta classe.', 100) }}
                </p>

                <!-- Schedule & Room -->
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="block text-[9px] font-bold uppercase text-gray-400 dark:text-slate-500 tracking-wider mb-1">Horário</span>
                        <div class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                            <x-icon name="clock" class="text-blue-500 w-4 h-4" />
                            {{ \Carbon\Carbon::parse($enrollment->ebdClass->schedule_time)->format('H:i') }}
                        </div>
                    </div>
                    @if($enrollment->ebdClass->room)
                    <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-xl border border-gray-100 dark:border-slate-700">
                        <span class="block text-[9px] font-bold uppercase text-gray-400 dark:text-slate-500 tracking-wider mb-1">Sala</span>
                        <div class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                            <x-icon name="door-open" class="text-purple-500 w-4 h-4" />
                            {{ $enrollment->ebdClass->room }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Teachers -->
                <div class="mb-5">
                    <span class="text-[9px] font-bold uppercase text-gray-400 dark:text-slate-500 tracking-wider block mb-2">Professores</span>
                    <div class="flex -space-x-2 overflow-hidden">
                        @foreach($enrollment->ebdClass->activeTeachers->take(4) as $teacher)
                        <div class="inline-block h-9 w-9 rounded-full border-2 border-white dark:border-slate-900 overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 shadow-sm" title="{{ $teacher->user->name }}">
                            @if($teacher->user->avatar_url)
                                <img src="{{ $teacher->user->avatar_url }}" alt="{{ $teacher->user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        @endforeach
                        @if($enrollment->ebdClass->activeTeachers->isEmpty())
                            <span class="text-xs font-medium text-gray-400 dark:text-slate-500 italic">A definir</span>
                        @endif
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ route('memberpanel.ebd.student.lessons', ['class_id' => $enrollment->ebdClass->id]) }}" class="mt-auto w-full inline-flex justify-center items-center px-5 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm transition-all shadow-lg hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white active:scale-[0.98]">
                    Explorar Conteúdo
                    <x-icon name="arrow-right" class="w-4 h-4 ml-2" />
                </a>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-gray-200 dark:border-slate-800 p-16 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <x-icon name="book-bible" style="duotone" class="w-10 h-10 text-gray-300 dark:text-slate-600" />
            </div>
            <h3 class="text-xl font-black text-gray-400 dark:text-slate-500 mb-2">Nenhuma Matrícula</h3>
            <p class="text-sm text-gray-400 dark:text-slate-500 max-w-md mx-auto">
                Você ainda não foi matriculado em nenhuma classe da EBD. Procure a secretaria para ingressar em uma jornada de aprendizado!
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
