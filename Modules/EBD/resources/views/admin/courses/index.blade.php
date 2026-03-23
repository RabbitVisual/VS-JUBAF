@extends('admin::components.layouts.master')

@section('title', 'Cursos EBD | Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Cursos <span class="text-blue-600">EBD</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Currículos e revistas (ex.: Panorama do Antigo Testamento). Homologados pelo conselho antes de uso em turmas.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.courses.create') }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="graduation-cap" style="duotone" class="mr-2 h-4 w-4" />
                Novo Curso
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-2">
        <form method="GET" action="{{ route('admin.ebd.courses.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="homologation_status" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                <option value="">Homologação (Todas)</option>
                <option value="draft" {{ request('homologation_status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                <option value="pending_approval" {{ request('homologation_status') === 'pending_approval' ? 'selected' : '' }}>Aguardando</option>
                <option value="approved" {{ request('homologation_status') === 'approved' ? 'selected' : '' }}>Aprovados</option>
            </select>
            <select name="is_active" class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                <option value="">Status (Todos)</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
            </select>
            <button type="submit" class="p-3 bg-gray-950 dark:bg-white text-white dark:text-gray-950 rounded-xl hover:scale-105 transition-all">
                <x-icon name="filter" style="duotone" class="w-4 h-4" />
            </button>
            @if (request()->hasAny(['homologation_status', 'is_active']))
                <a href="{{ route('admin.ebd.courses.index') }}" class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl hover:text-red-500 transition-all">
                    <x-icon name="xmark" class="w-4 h-4" />
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead class="bg-gray-50/50 dark:bg-gray-800/30">
                    <tr>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Curso</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Lições</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Turmas</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Homologação</th>
                        <th class="px-6 py-5 text-right text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($courses as $course)
                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                                    <x-icon name="graduation-cap" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $course->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">{{ $course->slug }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">{{ $course->lessons_count }}</td>
                        <td class="px-6 py-5">{{ $course->classes_count }}</td>
                        <td class="px-6 py-5">
                            @if($course->homologation_status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400">Aprovado</span>
                            @elseif($course->homologation_status === 'pending_approval')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">Aguardando</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400">Rascunho</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.ebd.courses.show', $course) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Ver">
                                    <x-icon name="eye" style="duotone" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('admin.ebd.courses.edit', $course) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors" title="Editar">
                                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.ebd.courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Excluir este curso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Excluir">
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
                                    <x-icon name="graduation-cap" style="duotone" class="w-8 h-8 text-gray-300" />
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nenhum curso</h3>
                                <p class="text-xs text-gray-500 mt-2 font-medium">Crie um currículo para vincular a turmas e lições.</p>
                                <a href="{{ route('admin.ebd.courses.create') }}" class="mt-6 px-6 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:scale-105 transition-all">Criar Curso</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())
            <div class="bg-gray-50/50 dark:bg-gray-800/30 px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
