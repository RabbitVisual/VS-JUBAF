@extends('pastoralpanel::components.layouts.master')

@section('title', 'Educação')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-slate-800 dark:bg-slate-900 border border-amber-900/30 text-white p-6 md:p-8">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <x-icon name="graduation-cap" class="w-7 h-7 text-amber-400" />
            Educação
        </h1>
        <p class="text-slate-300 mt-1">Visão macro da EBD e Worship Academy.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">EBD · Cursos</h2>
            </div>
            <div class="p-6">
                @forelse($ebdCourses ?? [] as $course)
                    <div class="py-2 border-b border-gray-100 dark:border-slate-700 last:border-0">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $course->name ?? $course->title ?? 'Curso' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum curso EBD cadastrado.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Worship Academy</h2>
            </div>
            <div class="p-6">
                @forelse($academyCourses ?? [] as $course)
                    <div class="py-2 border-b border-gray-100 dark:border-slate-700 last:border-0">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $course->title ?? $course->name ?? 'Curso' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum curso da Academy.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
