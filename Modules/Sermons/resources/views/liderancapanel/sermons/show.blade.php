@extends('liderancapanel::components.layouts.master')

@section('title', $sermon->title)

@push('styles')
<style>
    /* Typography Customization for "Medium" feel */
    .prose-sermon {
        font-family: 'Merriweather', 'Georgia', serif;
        font-size: 1.125rem;
        line-height: 1.8;
        color: #292929;
    }
    .dark .prose-sermon {
        color: #d1d5db;
    }
    .prose-sermon h2, .prose-sermon h3 {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        margin-top: 2em;
        margin-bottom: 0.5em;
        color: #111827;
    }
    .dark .prose-sermon h2, .dark .prose-sermon h3 {
        color: #f3f4f6;
    }
    .prose-sermon blockquote {
        border-left: 4px solid #f59e0b;
        padding-left: 1.5rem;
        font-style: italic;
        background: #fffbeb;
        padding: 1rem;
        border-radius: 0.5rem;
        margin: 2rem 0;
    }
    .dark .prose-sermon blockquote {
        background: rgba(245, 158, 11, 0.1);
        color: #d1d5db;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white dark:bg-slate-950 pb-32 rounded-3xl lg:border lg:border-gray-200 lg:dark:border-slate-800">

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <!-- Header -->
        <header class="mb-10 text-center">
            <div class="mb-6 flex items-center justify-center gap-2">
                @if($sermon->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500">
                        {{ $sermon->category->name }}
                    </span>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-gray-100 text-gray-800 dark:bg-slate-800 dark:text-gray-300">
                    {{ $sermon->visibility_display }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-4">
                {{ $sermon->title }}
            </h1>

            @if($sermon->subtitle)
                <p class="text-xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                    {{ $sermon->subtitle }}
                </p>
            @endif

            <div class="mt-8 flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        @if($sermon->user->profile_photo_url)
                            <img src="{{ $sermon->user->profile_photo_url }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="user" class="w-6 h-6 text-gray-400 m-2" />
                        @endif
                    </div>
                    <div class="ml-3 text-left">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $sermon->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sermon->published_at?->format('d M, Y') }} • {{ ceil(str_word_count(strip_tags($sermon->full_content ?? $sermon->development)) / 200) }} min leitura</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Cover Image -->
        @if($sermon->cover_image)
            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
                <img src="{{ asset('storage/' . $sermon->cover_image) }}" alt="{{ $sermon->title }}" class="w-full h-auto object-cover max-h-[500px]">
            </div>
        @endif

        <!-- Content -->
        <article class="prose-sermon relative text-base min-w-0 overflow-x-hidden sermon-content-with-refs" id="sermon-content">
            @if($sermon->full_content)
                {!! $sermon->full_content !!}
            @else
                @if($sermon->introduction)
                    <h3>Introdução</h3>
                    {!! nl2br(e($sermon->introduction)) !!}
                @endif

                @if($sermon->development)
                    <h3>Desenvolvimento</h3>
                    {!! nl2br(e($sermon->development)) !!}
                @endif

                @if($sermon->conclusion)
                    <h3>Conclusão</h3>
                    {!! nl2br(e($sermon->conclusion)) !!}
                @endif

                @if($sermon->application)
                    <h3>Aplicação</h3>
                    {!! nl2br(e($sermon->application)) !!}
                @endif
            @endif
        </article>

        <!-- Tags & Actions -->
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($sermon->tags as $tag)
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400">#{{ $tag->name }}</span>
                @endforeach
            </div>

            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex space-x-2 flex-wrap gap-y-2">
                    <a href="{{ route('lideranca.sermoes.sermons.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-bold rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('lideranca.sermoes.sermons.edit', $sermon) }}" class="inline-flex items-center min-h-[44px] px-4 py-2 text-sm font-bold bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-500 rounded-lg hover:bg-amber-100 transition-colors">
                        <x-icon name="pen-to-square" class="w-4 h-4 mr-2" />
                        Editar
                    </a>
                    
                    <div x-data="{ exportModalOpen: false, format: 'full', size: 'a5' }" class="contents">
                        <button type="button" @click="exportModalOpen = true" class="inline-flex items-center min-h-[44px] px-4 py-2 text-sm font-bold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                            <x-icon name="file-pdf" class="w-4 h-4 mr-2" />
                            PDF
                        </button>
                        <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                            <div @click.outside="exportModalOpen = false" class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-icon name="file-pdf" class="w-5 h-5 text-amber-500" />
                                    Exportar para o púlpito
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Formato</label>
                                        <div class="flex gap-3">
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'full' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="full" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Esboço completo</span>
                                            </label>
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'topics' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="topics" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Apenas tópicos</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tamanho do papel</label>
                                        <div class="flex gap-4">
                                            <label class="flex-1 cursor-pointer" @click="size = 'a4'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a4' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 42px; height: 59px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A4</span>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer" @click="size = 'a5'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a5' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 30px; height: 42px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A5</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 flex gap-3 justify-end">
                                    <button type="button" @click="exportModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</button>
                                    <a :href="'{{ route('lideranca.sermoes.sermons.export-pdf', $sermon) }}?format=' + format + '&size=' + size" target="_blank" @click="exportModalOpen = false"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg">
                                        <x-icon name="download" class="w-4 h-4 mr-2" />
                                        Gerar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($sermon->attachments))
            <div class="mt-12 bg-gray-50 dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="paper-clip" class="w-5 h-5 mr-2" />
                    Materiais de Apoio
                </h4>
                <div class="grid gap-3">
                    @foreach($sermon->attachments as $file)
                        <a href="{{ Storage::url($file['path']) }}" target="_blank" class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-amber-500 dark:hover:border-amber-500 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold uppercase">{{ pathinfo($file['name'], PATHINFO_EXTENSION) }}</span>
                            </div>
                            <div class="ml-4 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-amber-500 transition-colors">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                            </div>
                            <x-icon name="download" class="w-5 h-5 ml-auto text-gray-400 group-hover:text-amber-500" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </main>
</div>
@endsection
