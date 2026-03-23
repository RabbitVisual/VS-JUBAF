@extends('memberpanel::components.layouts.master')

@section('page-title', 'Worship Academy')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-4xl bg-white dark:bg-gray-950 border border-gray-200 dark:border-white/5 p-8 sm:p-12 shadow-sm dark:shadow-none mb-12">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-600/5 dark:bg-blue-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-purple-600/5 dark:bg-purple-600/10 rounded-full blur-[100px]"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <nav class="flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-4">
                    <span>Vertex Academy</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400 dark:text-gray-500">Cursos & Treinamentos</span>
                </nav>
                <h1 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1] mb-3">Worship <br><span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Academy</span></h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md leading-relaxed">Capacitação técnica e espiritual para levitas. Desenvolva seus dons para a glória de Deus.</p>
            </div>

            <div class="bg-gray-50 dark:bg-white/5 backdrop-blur-xl rounded-3xl p-6 border border-gray-200 dark:border-white/10 ring-1 ring-black/5 dark:ring-white/5 shadow-xl">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                        <x-icon name="graduation-cap" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1">Cursos Disponíveis</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $courses->count() }} Módulos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-tour="worship-academy-list">
        @forelse($courses as $course)
        <a href="{{ route('worship.member.academy.course', $course) }}" class="group relative block bg-white dark:bg-gray-950 border border-gray-200 dark:border-white/5 rounded-4xl overflow-hidden transition-all duration-500 hover:border-blue-500/30 hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-1">
            <!-- Cover -->
            <div class="aspect-video w-full bg-gray-100 dark:bg-gray-900/50 relative overflow-hidden">
                @if($course->cover_image)
                    <img src="{{ $course->cover_image }}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                @else
                    <div class="flex items-center justify-center h-full text-gray-300 dark:text-gray-700">
                        <x-icon name="music" class="w-12 h-12" />
                    </div>
                @endif

                <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent opacity-60"></div>

                <!-- Level Badge -->
                <div class="absolute top-4 right-4 px-3 py-1 bg-white/90 dark:bg-black/60 backdrop-blur-md rounded-full border border-gray-200 dark:border-white/10 shadow-sm">
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">{{ $course->level }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/10 dark:bg-blue-500/10 flex items-center justify-center border border-blue-600/20 dark:border-blue-500/20">
                        <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase">{{ substr($course->instrument->name ?? 'G', 0, 2) }}</span>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $course->instrument->name ?? 'Geral' }}</span>
                </div>

                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-tight">{{ $course->title }}</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed mb-6 prose prose-sm dark:prose-invert max-w-none prose-p:my-0 prose-ul:my-0 prose-li:my-0">
                    @if($course->description)
                        {!! strip_tags($course->description, '<p><strong><em><ul><ol><li><br>') !!}
                    @else
                        <span>Sem descrição.</span>
                    @endif
                </div>

                <!-- Progress Bar -->
                <div class="mt-auto pt-6 border-t border-gray-100 dark:border-white/5">
                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                        <span>Progresso</span>
                        <span class="text-blue-600 dark:text-blue-400">{{ $course->progress_percent }}%</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-linear-to-r from-blue-500 to-purple-500 transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: {{ $course->progress_percent }}%"></div>
                    </div>
                </div>
            </div>
        </a>
        @empty
            <div class="col-span-full py-20 text-center bg-white dark:bg-gray-900/50 rounded-[3rem] border border-dashed border-gray-200 dark:border-white/10">
                <div class="w-20 h-20 rounded-3xl bg-gray-50 dark:bg-black/50 flex items-center justify-center mx-auto mb-6 text-gray-300 dark:text-gray-700">
                     <x-icon name="graduation-cap" class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nenhum curso disponível</h3>
                <p class="text-gray-500 mt-2">Fique atento, novas aulas serão adicionadas em breve.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

