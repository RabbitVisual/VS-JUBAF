@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Turmas | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Turmas <span class="text-blue-600">EBD</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gestão de salas, faixas etárias e distribuição de professores por classe.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.classes.create') }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="plus" style="duotone" class="mr-2 h-4 w-4" />
                Nova Turma
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="screen-users" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ativas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $classes->where('is_active', true)->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Classes em operação</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <x-icon name="user-graduate" style="duotone" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alunos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $classes->sum(fn($c) => $c->activeStudents->count()) }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Matrículas ativas</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                    <x-icon name="chalkboard-user" style="duotone" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professores</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $classes->sum(fn($c) => $c->activeTeachers->count()) }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Corpo docente</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <x-icon name="door-open" style="duotone" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Salas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $classes->pluck('room')->filter()->unique()->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Espaços físicos</div>
        </div>
    </div>

    <!-- Premium Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-2">
        <form method="GET" action="{{ route('admin.ebd.classes.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="age_group" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Faixa Etária (Todas)</option>
                <option value="adult" {{ request('age_group') === 'adult' ? 'selected' : '' }}>Adultos</option>
                <option value="youth" {{ request('age_group') === 'youth' ? 'selected' : '' }}>Jovens</option>
                <option value="teen" {{ request('age_group') === 'teen' ? 'selected' : '' }}>Adolescentes</option>
                <option value="children" {{ request('age_group') === 'children' ? 'selected' : '' }}>Crianças</option>
            </select>

            <select name="is_active" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Status (Todos)</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativas</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativas</option>
            </select>

            <button type="submit" class="p-3 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg shadow-gray-950/10">
                <x-icon name="filter" style="duotone" class="w-4 h-4" />
            </button>

            @if (request()->hasAny(['age_group', 'is_active']))
                <a href="{{ route('admin.ebd.classes.index') }}" class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl hover:text-red-500 transition-all">
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
                            Informações da Turma
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Faixa Etária
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Logística & Lotação
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-5 text-right text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($classes as $class)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center shrink-0">
                                    <x-icon name="screen-users" style="duotone" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 transition-colors">
                                        {{ $class->name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5 line-clamp-1">
                                        {{ $class->description ?? 'Sem descrição da turma.' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @php
                                $ageGroupStyles = [
                                    'adult' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                    'youth' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                    'teen' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
                                    'children' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                ];
                                $groupStyle = $ageGroupStyles[$class->age_group] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
                            @endphp
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full {{ $groupStyle }}">
                                {{ $class->age_group_display }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <x-icon name="door-closed" class="w-3.5 h-3.5 text-gray-400" />
                                    <span class="text-xs font-black text-gray-700 dark:text-gray-300">Sala: {{ $class->room ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-icon name="users" class="w-3.5 h-3.5 text-gray-400" />
                                    <span class="text-[10px] font-bold text-gray-500 uppercase">
                                        {{ $class->activeStudents->count() }} @if($class->max_students) / {{ $class->max_students }} @endif Alunos
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @if($class->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-700 border border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                                    <span class="w-1 h-1 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                    Ativa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
                                    Inativa
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.ebd.classes.edit', $class) }}"
                                   class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tooltip" title="Editar">
                                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.ebd.classes.destroy', $class) }}" class="inline"
                                      onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir esta turma? Todos os vínculos de alunos e professores serão removidos.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors tooltip" title="Excluir">
                                        <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                                    <x-icon name="users-slash" style="duotone" class="w-8 h-8 text-gray-300" />
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Sem turmas encontradas</h3>
                                <p class="text-xs text-gray-500 mt-2 font-medium">
                                    Não existem turmas cadastradas ou os filtros aplicados não retornaram resultados.
                                </p>
                                @if(!request()->hasAny(['age_group', 'is_active']))
                                <a href="{{ route('admin.ebd.classes.create') }}" class="mt-6 px-6 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:scale-105 transition-all">
                                    Criar Primeira Turma
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
        <div class="bg-gray-50/50 dark:bg-gray-800/30 px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $classes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

