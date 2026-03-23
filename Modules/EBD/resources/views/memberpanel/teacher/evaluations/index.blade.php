@extends('memberpanel::components.layouts.master')

@section('title', 'Avaliações - EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-5xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal do Professor</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium">Avaliações</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Gestão de Desempenho</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Avalie e direcione o progresso dos seus alunos.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Painel
            </a>
        </div>

        <!-- Hero -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-amber-400 dark:bg-amber-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-orange-400 dark:bg-orange-600 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-xl shadow-amber-500/20 shrink-0">
                    <x-icon name="file-certificate" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 mb-2">
                        <x-icon name="graduation-cap" class="w-3 h-3 text-amber-600 dark:text-amber-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Centro de Avaliação</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Avaliações para corrigir</h2>
                    <p class="text-gray-500 dark:text-slate-300 text-sm mt-1">Pendentes e já corrigidas.</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap justify-center sm:justify-start">
            <div class="inline-flex p-1.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm gap-1">
                <a href="?status=completed" class="px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all {{ request('status') == 'completed' ? 'bg-amber-500 text-white shadow' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    Pendentes ({{ $evaluations->where('status', 'completed')->count() }})
                </a>
                <a href="?status=graded" class="px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all {{ request('status') == 'graded' ? 'bg-emerald-500 text-white shadow' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    Corrigidas ({{ $evaluations->where('status', 'graded')->count() }})
                </a>
                <a href="?" class="px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    Todas
                </a>
            </div>
        </div>

        <!-- List -->
        @if($evaluations->count() > 0)
            <div class="space-y-4">
                @foreach($evaluations as $evaluation)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-4 sm:p-6 hover:shadow-md transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="relative shrink-0">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700">
                                        <img src="{{ $evaluation->student->user->avatar_url }}" alt="{{ $evaluation->student->user->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center border border-gray-200 dark:border-slate-700">
                                        @if($evaluation->status === 'graded')
                                            <x-icon name="circle-check" class="text-emerald-500 w-3 h-3" />
                                        @else
                                            <x-icon name="clock" class="text-amber-500 w-3 h-3" />
                                        @endif
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $evaluation->student->user->name }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-500 dark:text-slate-400">
                                        <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $evaluation->lesson->title }}</span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-slate-600"></span>
                                        <span>{{ $evaluation->lesson->ebdClass->name }}</span>
                                    </div>
                                    @if($evaluation->completed_at)
                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-1">Entregue em {{ $evaluation->completed_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                @if($evaluation->status === 'graded')
                                    <div class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 text-center">
                                        <span class="text-lg font-black {{ $evaluation->score >= 70 ? 'text-emerald-500' : ($evaluation->score >= 50 ? 'text-amber-500' : 'text-rose-500') }}">{{ $evaluation->score }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('memberpanel.ebd.teacher.evaluations.grade', $evaluation) }}"
                                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition-all {{ $evaluation->status === 'completed' ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/20' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700' }} active:scale-[0.98]">
                                    {{ $evaluation->status === 'completed' ? 'Avaliar Agora' : 'Revisar' }}
                                    <x-icon name="chevron-right" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $evaluations->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                    <x-icon name="file-slash" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 dark:text-slate-500" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma avaliação</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">Nenhuma atividade pendente de correção.</p>
            </div>
        @endif
    </div>
</div>
@endsection
