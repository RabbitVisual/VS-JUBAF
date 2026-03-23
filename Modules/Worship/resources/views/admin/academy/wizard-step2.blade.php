@extends('admin::components.layouts.master')

@section('title', 'Módulos do Curso | Worship Academy')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-[10px] font-black text-indigo-600 dark:text-indigo-500 uppercase tracking-widest">
        <a href="{{ route('worship.admin.academy.courses.index') }}" class="hover:underline">Academy</a>
        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
        <span class="text-gray-400 dark:text-gray-500">Novo Curso – Módulos</span>
    </nav>
    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Passo 2: Organização de Módulos</h1>
    <p class="text-gray-500 dark:text-gray-400">Defina os módulos do curso <strong class="text-gray-900 dark:text-white">{{ $course->title }}</strong>. Depois você poderá adicionar as lições em cada módulo.</p>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 px-4 py-3 flex items-center gap-2">
            <x-icon name="circle-check" class="w-5 h-5 shrink-0" />
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('worship.admin.academy.courses.wizard.store', $course->id) }}" method="POST" x-data="{ modules: @json($course->modules->map(fn($m) => ['id' => $m->id, 'title' => $m->title])->values()->all()) }" x-init="if (modules.length === 0) modules = [{ id: null, title: '' }]">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <div class="space-y-4" x-ref="modulesContainer">
                <template x-for="(mod, index) in modules" :key="index">
                    <div class="flex items-center gap-3">
                        <input type="hidden" :name="'modules[' + index + '][id]'" :value="mod.id || ''">
                        <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-8" x-text="index + 1"></span>
                        <input type="text" :name="'modules[' + index + '][title]'" x-model="mod.title" required
                            placeholder="Título do módulo"
                            class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                        <button type="button" @@click="if (modules.length > 1) modules.splice(index, 1)"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            x-show="modules.length > 1">
                            <x-icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                </template>
            </div>
            <div class="mt-6">
                <button type="button" @@click="modules.push({ id: null, title: '' })"
                    class="inline-flex items-center px-4 py-2 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-bold hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Adicionar módulo
                </button>
            </div>

            <div class="mt-10 flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('worship.admin.academy.courses.show', $course->id) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                    Pular e editar depois
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    Continuar para Lições
                    <x-icon name="arrow-right" class="h-5 w-5 ml-2" />
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
