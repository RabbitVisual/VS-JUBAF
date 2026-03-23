@extends('admin::components.layouts.master')

@section('title', $course->name . ' | Cursos EBD')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Curso</span>
                @if($course->homologation_status === 'approved')
                    <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-green-600 text-white rounded">Homologado</span>
                @elseif($course->homologation_status === 'pending_approval')
                    <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white rounded">Aguardando homologação</span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-gray-500 text-white rounded">Rascunho</span>
                @endif
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $course->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">{{ $course->description ?? 'Sem descrição.' }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($course->homologation_status === 'draft')
                <form action="{{ route('admin.ebd.courses.submit-homologation', $course) }}" method="POST" class="inline" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Enviando para homologação...' } }))">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-3 rounded-xl bg-amber-600 text-white text-sm font-bold hover:bg-amber-700 transition-all">
                        <x-icon name="paper-plane" style="duotone" class="mr-2 h-4 w-4" />
                        Enviar para homologação
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.ebd.lessons.index', ['course_id' => $course->id]) }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-all">
                <x-icon name="book-open" style="duotone" class="mr-2 h-4 w-4" />
                Lições
            </a>
            <a href="{{ route('admin.ebd.courses.edit', $course) }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="pen-to-square" style="duotone" class="mr-2 h-4 w-4" />
                Editar
            </a>
            <a href="{{ route('admin.ebd.courses.index') }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden" x-data="{ tab: 'info' }">
        <div class="flex gap-1 p-2 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
            <button type="button" @click="tab = 'info'" :class="tab === 'info' ? 'bg-white dark:bg-slate-900 shadow-sm text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                class="rounded-2xl px-4 py-2.5 text-sm transition-all flex items-center gap-2">
                <x-icon name="circle-info" style="duotone" class="w-4 h-4" />
                Informações
            </button>
            <button type="button" @click="tab = 'lessons'" :class="tab === 'lessons' ? 'bg-white dark:bg-slate-900 shadow-sm text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                class="rounded-2xl px-4 py-2.5 text-sm transition-all flex items-center gap-2">
                <x-icon name="book-open" style="duotone" class="w-4 h-4" />
                Lições ({{ $course->lessons->count() }})
            </button>
        </div>
        <div class="p-6">
            <div x-show="tab === 'info'" x-cloak class="space-y-4">
                <p class="text-gray-600 dark:text-gray-300 font-medium">{{ $course->description ?? 'Sem descrição.' }}</p>
                @if($course->approved_at)
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Homologado em {{ $course->approved_at->format('d/m/Y') }}</p>
                @endif
            </div>
            <div x-show="tab === 'lessons'" x-cloak class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($course->lessons as $lesson)
                <div class="py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/20 -mx-2 px-2 rounded-xl transition-colors first:pt-0">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase w-8">{{ $lesson->order + 1 }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $lesson->title }}</span>
                    </div>
                    <a href="{{ route('admin.ebd.lessons.edit', $lesson) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                        <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                    </a>
                </div>
                @empty
                <div class="py-12 text-center text-gray-500 dark:text-gray-400 font-medium">
                    Nenhuma lição neste curso. Adicione lições em Lições (filtro por curso).
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
