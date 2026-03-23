@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Casos Disciplinares</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Acompanhamento pastoral de disciplina e restauração, conforme Mateus 18 e princípios batistas.
            </p>
        </div>
        <a href="{{ route('admin.churchcouncil.discipline.create') }}"
           class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors shadow-sm flex items-center justify-center sm:w-auto w-full">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Abrir novo caso
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="scale-balanced" class="w-5 h-5 text-red-500" />
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Casos em acompanhamento</h2>
            </div>
        </div>

        @if($cases->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($cases as $case)
                    <a href="{{ route('admin.churchcouncil.discipline.show', $case) }}"
                       class="flex flex-col md:flex-row md:items-center gap-3 md:gap-6 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $case->member->name ?? 'Membro removido' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Tipo: {{ ucfirst(str_replace('_', ' ', $case->case_type)) }} • Aberto em {{ $case->created_at->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ $case->summary }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                @if($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_OPENED)
                                    bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_UNDER_CARE)
                                    bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_RECOMMENDED_TO_ASSEMBLY)
                                    bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                                @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_DECIDED_BY_ASSEMBLY)
                                    bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                @else
                                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                            </span>
                            @if($case->current_stage)
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                    Etapa: {{ $case->current_stage }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $cases->links() }}
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                    <x-icon name="check-circle" class="w-8 h-8" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Nenhum caso disciplinar aberto</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Use esta área para registrar processos de cuidado, disciplina e restauração de membros.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

