@extends('admin::components.layouts.master')

@section('title', isset($course) ? 'Editor do Curso: ' . $course->title : 'Academy Dashboard')

@section('content')
    @if(isset($course))
        <div class="space-y-8">
            <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest">
                <a href="{{ route('worship.admin.academy.courses.index') }}" class="hover:underline">Academy</a>
                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                <span class="text-gray-400 dark:text-gray-500">Editor</span>
            </nav>
            <div id="worship-academy-admin"
                 data-component="CourseBuilder"
                 data-course-id="{{ $course->id }}"
                 class="h-[calc(100vh-120px)]">
            </div>
        </div>
        @vite(['Modules/Worship/resources/assets/js/app.js'])
    @else
        <div class="space-y-8">
            <!-- Header (Admin pattern) -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="space-y-1">
                    <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest mb-1.5">
                        <span>Módulo de Louvor</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                        <span class="text-gray-400 dark:text-gray-500">Academy</span>
                    </nav>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Worship Academy</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Gestão de Cursos, Alunos e Progresso.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('worship.admin.academy.courses.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <x-icon name="graduation-cap" class="w-5 h-5 mr-2" />
                        Gerenciar Cursos
                    </a>
                    <a href="{{ route('worship.admin.academy.students') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <x-icon name="users" class="w-5 h-5 mr-2" />
                        Alunos
                    </a>
                    <a href="{{ route('worship.admin.academy.courses.create') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                        <x-icon name="plus" class="w-5 h-5 mr-2" />
                        Novo Curso
                    </a>
                </div>
            </div>

            <!-- Stats Grid (Admin pattern) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <x-icon name="users" class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Alunos Ativos</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $totalStudents }}</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <x-icon name="file-lines" class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Cursos / Lições</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $totalCourses }}</h3>
                        <p class="text-sm text-gray-400 mt-2">{{ $totalLessons }} lições</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                <x-icon name="circle-check" class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Conclusões</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $totalCompletions }}</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <x-icon name="chart-line" class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Engajamento</p>
                        <p class="text-sm text-gray-400 mt-2">{{ $totalCompletions > 0 ? 'Em alta este mês' : 'Sem dados suficientes' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-icon name="clock" class="w-5 h-5 text-gray-400" />
                                Atividade Recente
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Músico</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Lição</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($recentProgress as $progress)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                                        {{ substr($progress->user->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $progress->user->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $progress->lesson->title }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $progress->lesson->module->course->title }}</div>
                                            </td>
                                            <td class="px-8 py-6 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $progress->completed_at->format('d/m H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-8 py-8 text-center text-gray-500 dark:text-gray-400">
                                                Nenhuma atividade recente.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-icon name="trophy" class="w-5 h-5 text-amber-500" />
                            Destaques
                        </h2>
                        <div class="space-y-4">
                            @foreach($leaderboard as $index => $user)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="font-black text-gray-300 dark:text-gray-600 text-lg w-4">{{ $index + 1 }}</div>
                                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-400">
                                            @if(isset($user->photo) && $user->photo)
                                                <img src="{{ Storage::url($user->photo) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(mb_substr($user->name ?? '?', 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                    <div class="px-2 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg flex items-center gap-1">
                                        {{ $user->lessons_completed }} <x-icon name="star" class="w-3 h-3" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div id="courses-section" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Gerenciar Cursos</h2>

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Curso</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Nível</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($courses as $c)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0">
                                            @if($c->cover_image)
                                                <img src="{{ $c->cover_image }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center h-full">
                                                    <x-icon name="music" class="h-5 w-5 text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $c->title }}</div>
                                            <div class="text-xs text-gray-500">{{ $c->modules_count ?? 0 }} Módulos</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold uppercase bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $c->level }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($c->status === 'published')
                                        <span class="flex items-center gap-1.5 text-green-600 dark:text-green-400 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Publicado
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-orange-600 dark:text-orange-400 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Rascunho
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('worship.admin.academy.builder', $c->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors mr-2">
                                        <x-icon name="pen-to-square" class="h-3 w-3 mr-1.5" />
                                        Editar Conteúdo
                                    </a>
                                    <!-- <button class="text-gray-400 hover:text-red-500 transition-colors">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($courses->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-icon name="graduation-cap" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum curso encontrado</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Comece criando o primeiro curso da academia.</p>
                            <a href="{{ route('worship.admin.academy.courses.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/20 transition-all">
                                <x-icon name="plus" class="w-5 h-5 mr-2" />
                                Criar Curso Agora
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

