@extends('admin::components.layouts.master')

@section('title', 'Academy: Cursos | Worship Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest mb-1.5">
                <a href="{{ route('worship.admin.dashboard') }}" class="hover:underline">Louvor</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <span class="text-gray-400 dark:text-gray-500">Academy</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Worship Academy</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie os cursos de formação para músicos e equipe.</p>
        </div>
        <a href="{{ route('worship.admin.academy.students') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
            <x-icon name="users" class="w-5 h-5 mr-2" />
            Alunos
        </a>
        <a href="{{ route('worship.admin.academy.courses.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            Novo Curso
        </a>
    </div>

    @if($courses->isEmpty())
        <div class="py-20 text-center bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-4 text-indigo-600 dark:text-indigo-400">
                <x-icon name="graduation-cap" class="w-8 h-8" />
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum curso cadastrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Comece criando trilhas de aprendizado para seus voluntários.</p>
            <a href="{{ route('worship.admin.academy.courses.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/20 transition-all">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Criar Primeiro Curso
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full hover:shadow-lg transition-all duration-300">
                    <!-- Cover / Header -->
                    <div class="h-32 bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                        @if($course->cover_image)
                            <img src="{{ $course->cover_image }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-indigo-50 dark:bg-gray-700">
                                <x-icon name="book" class="h-12 w-12 text-indigo-200 dark:text-gray-600" />
                            </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white dark:bg-gray-800 rounded-lg px-2 py-1 text-xs font-bold text-gray-900 dark:text-white shadow-sm">
                            {{ ucfirst($course->level) }}
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                             @if($course->instrument)
                                <span class="bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $course->instrument->name }}
                                </span>
                             @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">Geral</span>
                             @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $course->title }}
                        </h3>

                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-6 line-clamp-2 prose prose-sm dark:prose-invert max-w-none prose-p:my-0.5 prose-ul:my-1 prose-li:my-0">
                            @if($course->description)
                                {!! strip_tags($course->description, '<p><strong><em><b><i><ul><ol><li><br><a><span>') !!}
                            @else
                                <span>Sem descrição.</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            @if($course->category)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $course->category->label() }}</span>
                            @endif
                        </div>

                        <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                             <div class="text-sm text-gray-500 dark:text-gray-400">
                                 <span class="font-bold text-gray-900 dark:text-white">{{ $course->lessons->count() }}</span> lições
                             </div>
                             <a href="{{ route('worship.admin.academy.courses.show', $course->id) }}" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 transition-colors">
                                Gerenciar
                                <x-icon name="arrow-right" class="w-4 h-4 ml-1" />
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection

