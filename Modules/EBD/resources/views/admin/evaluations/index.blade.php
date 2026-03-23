@extends('admin::components.layouts.master')

@section('title', 'Avaliações | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Avaliações <span class="text-blue-600">& Rendimento</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Monitoramento de desempenho, correção de exercícios e feedback pedagógico.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="file-circle-check" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Concluídas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $evaluations->where('status', 'completed')->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Aguardando Revisão</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-icon name="clipboard-check" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Avaliadas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $evaluations->where('status', 'graded')->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Histórico de Notas</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <x-icon name="chart-pie" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Média Geral</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">
                {{ number_format($evaluations->whereNotNull('score')->avg('score') ?? 0, 1) }}
            </div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Desempenho Médio</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <x-icon name="clock-rotate-left" style="duotone" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pendentes</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $evaluations->where('status', 'pending')->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Em Andamento</div>
        </div>
    </div>

    <!-- Premium Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-2">
        <form method="GET" action="{{ route('admin.ebd.evaluations.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="lesson_id" class="flex-1 min-w-[300px] px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Todas as Lições</option>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                        {{ $lesson->title }} ({{ $lesson->lesson_date->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Status (Todos)</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluída</option>
                <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Avaliada</option>
            </select>

            <button type="submit" class="p-3 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg shadow-gray-950/10">
                <x-icon name="filter" style="duotone" class="w-4 h-4" />
            </button>

            @if (request()->hasAny(['lesson_id', 'status']))
                <a href="{{ route('admin.ebd.evaluations.index') }}" class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl hover:text-red-500 transition-all">
                    <x-icon name="xmark" class="w-4 h-4" />
                </a>
            @endif
        </form>
    </div>

    <!-- Premium Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead class="bg-gray-50/50 dark:bg-gray-800/30">
                    <tr>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Aluno & Classe
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Lição Associada
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-5 text-center text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Rendimento
                        </th>
                        <th scope="col" class="px-6 py-5 text-right text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($evaluations as $evaluation)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-black text-gray-400">
                                        {{ strtoupper(substr($evaluation->student->user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 transition-colors">
                                        {{ $evaluation->student->user->name }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                        {{ $evaluation->student->ebdClass->name }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-gray-700 dark:text-gray-300">
                                    {{ $evaluation->lesson->title }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">
                                    Aula: {{ $evaluation->lesson->lesson_date->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @php
                                $statusStyles = [
                                    'completed' => 'bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
                                    'graded' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800'
                                ];
                                $currentStyle = $statusStyles[$evaluation->status] ?? 'bg-gray-50 text-gray-500 border-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $currentStyle }}">
                                <span class="w-1 h-1 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                {{ $evaluation->status_display }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($evaluation->score !== null)
                                @php
                                    $scoreColor = $evaluation->score >= 7 ? 'text-green-600' : ($evaluation->score >= 5 ? 'text-amber-600' : 'text-red-600');
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-black {{ $scoreColor }}">{{ number_format($evaluation->score, 1) }}</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Nota Final</span>
                                </div>
                            @else
                                <span class="text-xs font-bold text-gray-300 dark:text-gray-600">Aguardando</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.ebd.evaluations.show', $evaluation) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-[10px] font-black uppercase tracking-widest rounded-lg hover:scale-105 transition-all shadow-sm">
                                    <x-icon name="eye" style="duotone" class="w-3.5 h-3.5 mr-2" />
                                    Ver Detalhes
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                                    <x-icon name="clipboard-question" style="duotone" class="w-8 h-8 text-gray-300" />
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nenhuma avaliação encontrada</h3>
                                <p class="text-xs text-gray-500 mt-2 font-medium">
                                    Ainda não há atividades concluídas por alunos para serem listadas aqui.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($evaluations->hasPages())
        <div class="bg-gray-50/50 dark:bg-gray-800/30 px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $evaluations->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

