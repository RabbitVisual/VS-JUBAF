@extends('pastoralpanel::components.layouts.master')

@section('title', $course->name . ' - EBD')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.ebd.courses.index') }}" class="hover:text-white">Cursos</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">{{ $course->name }}</span>
                    </nav>
                    @if($course->homologation_status === 'approved')
                        <span class="inline-flex px-2 py-0.5 rounded bg-green-500/20 text-green-300 text-xs font-medium mb-2">Homologado</span>
                    @elseif($course->homologation_status === 'pending_approval')
                        <span class="inline-flex px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 text-xs font-medium mb-2">Aguardando homologação</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-500/20 text-gray-300 text-xs font-medium mb-2">Rascunho</span>
                    @endif
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">{{ $course->name }}</h1>
                    <p class="text-slate-300 text-sm">{{ $course->description ?? 'Sem descrição.' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pastor.ebd.lessons.index') }}?course_id={{ $course->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="book-open-reader" class="w-5 h-5" /> Lições
                    </a>
                    <a href="{{ route('pastor.ebd.courses.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="arrow-left" class="w-5 h-5" /> Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Lições do curso ({{ $course->lessons->count() }})</h3>
            <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($course->lessons as $lesson)
                    <li class="py-3 flex items-center justify-between">
                        <a href="{{ route('pastor.ebd.lessons.show', $lesson) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">{{ $lesson->title }}</a>
                        <span class="text-xs text-gray-500">Ordem {{ $lesson->order + 1 }}</span>
                    </li>
                @empty
                    <li class="py-6 text-sm text-gray-500 dark:text-gray-400">Nenhuma lição neste curso.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
