@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Alunos | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Alunos <span class="text-blue-600">EBD</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gestão de matrículas, acompanhamento de progresso e enturmação de alunos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.students.create') }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="plus" style="duotone" class="mr-2 h-4 w-4" />
                Matricular Aluno
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="user-graduate" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ativos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $students->where('is_active', true)->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Matrículas Vigentes</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <x-icon name="star" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">XP Global</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($students->sum('xp'), 0, ',', '.') }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Experiência Acumulada</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-icon name="calendar-check" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Novos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $students->where('created_at', '>=', now()->subDays(30))->count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Últimos 30 dias</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <x-icon name="award" style="duotone" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Medalhas</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ \Modules\EBD\App\Models\EbdUserBadge::count() }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Conquistas Totais</div>
        </div>
    </div>

    <!-- Premium Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-2">
        <form method="GET" action="{{ route('admin.ebd.students.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="flex-1 min-w-[300px]">
                <div class="relative group">
                    <x-icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-600 transition-colors" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
                        placeholder="Buscar por nome, email ou matrícula...">
                </div>
            </div>

            <select name="class_id" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Todas as Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>

            <select name="is_active" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                <option value="">Status (Todos)</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="p-3 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg shadow-gray-950/10">
                <x-icon name="filter" style="duotone" class="w-4 h-4" />
            </button>

            @if (request()->hasAny(['search', 'class_id', 'is_active']))
                <a href="{{ route('admin.ebd.students.index') }}" class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl hover:text-red-500 transition-all">
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
                            Aluno & Perfil
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            Turma / Matrícula
                        </th>
                        <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                            XP & Nível
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
                    @forelse($students as $student)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-2 border-white dark:border-slate-900 shadow-sm group-hover:shadow-md transition-all">
                                        @if($student->user->photo)
                                            <img src="{{ $student->user->photo }}" alt="{{ $student->user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-black text-gray-400">
                                                {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($student->is_active)
                                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-slate-950 rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 transition-colors">
                                        {{ $student->user->name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">
                                        {{ $student->user->email }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800 w-fit">
                                    {{ $student->ebdClass->name }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">
                                    Matrícula: {{ $student->enrollment_date->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($student->xp, 0, ',', '.') }} <span class="text-[10px] text-gray-400 uppercase ml-1">xp</span></span>
                                    <div class="w-24 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ min(100, ($student->xp % 1000) / 10) }}%"></div>
                                    </div>
                                </div>
                                @if($student->badges_count > 0)
                                    <div class="flex -space-x-1.5 overflow-hidden">
                                        @foreach($student->badges->take(3) as $badge)
                                            <div class="w-6 h-6 rounded-full border border-white dark:border-slate-900 bg-slate-800 flex items-center justify-center tooltip" title="{{ $badge->name }}">
                                                <x-icon name="{{ $badge->icon ?? 'award' }}" class="w-3 h-3 text-amber-400" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @if($student->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-700 border border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                                    <span class="w-1 h-1 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
                                    Inativo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.ebd.students.edit', $student) }}"
                                   class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tooltip" title="Editar Matrícula">
                                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.ebd.students.destroy', $student) }}" class="inline"
                                      onsubmit="return confirm('ATENÇÃO: Deseja realmente remover este aluno? O histórico de XP e medalhas não será excluído, mas ele perderá o acesso à classe.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors tooltip" title="Remover Aluno">
                                        <x-icon name="user-minus" style="duotone" class="w-4 h-4" />
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
                                    <x-icon name="user-graduate" style="duotone" class="w-8 h-8 text-gray-300" />
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nenhum aluno encontrado</h3>
                                <p class="text-xs text-gray-500 mt-2 font-medium">
                                    Não existem matrículas cadastradas ou os filtros aplicados não retornaram resultados.
                                </p>
                                @if(!request()->hasAny(['search', 'class_id']))
                                <a href="{{ route('admin.ebd.students.create') }}" class="mt-6 px-6 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:scale-105 transition-all">
                                    Matricular Primeiro Aluno
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
        @if($students->hasPages())
        <div class="bg-gray-50/50 dark:bg-gray-800/30 px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $students->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

