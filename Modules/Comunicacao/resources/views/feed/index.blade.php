@extends('memberpanel::components.layouts.master')

@section('title', 'Mural Oficial')
@section('page-title', 'Mural Oficial')

@section('content')
    <div class="min-h-[calc(100vh-8rem)]">
        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-3xl mb-10 border border-gray-200/80 dark:border-slate-700/80 bg-linear-to-br from-slate-900 via-blue-950 to-slate-900 text-white shadow-2xl shadow-blue-900/20">
            <div class="absolute inset-0 opacity-30 bg-[radial-gradient(ellipse_at_top_right,var(--tw-gradient-stops))] from-blue-400/20 via-transparent to-transparent"></div>
            <div class="relative px-6 py-10 md:px-12 md:py-14 max-w-4xl">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 backdrop-blur border border-white/20">
                        <x-icon name="bullhorn" class="w-6 h-6 text-amber-300" />
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200/90">Transparência</p>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight">Mural Oficial</h1>
                    </div>
                </div>
                <p class="text-blue-100/90 text-lg max-w-2xl leading-relaxed">
                    Comunicados, editais e notícias da diretoria. Acompanhe em um só lugar o que importa para a JUBAF.
                </p>
            </div>
        </div>

        <div class="max-w-3xl mx-auto space-y-8 pb-12">
            @forelse ($postagens as $postagem)
                @php
                    $badge = match ($postagem->tipo) {
                        'edital' => ['label' => 'Edital', 'class' => 'bg-red-100 text-red-800 ring-red-600/20 dark:bg-red-900/50 dark:text-red-100 dark:ring-red-500/30'],
                        'ata' => ['label' => 'Ata', 'class' => 'bg-slate-100 text-slate-800 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-500/30'],
                        'aviso' => ['label' => 'Aviso', 'class' => 'bg-amber-100 text-amber-900 ring-amber-500/25 dark:bg-amber-900/40 dark:text-amber-100 dark:ring-amber-500/30'],
                        'noticia' => ['label' => 'Notícia', 'class' => 'bg-blue-100 text-blue-800 ring-blue-600/20 dark:bg-blue-900/50 dark:text-blue-100 dark:ring-blue-500/30'],
                        default => ['label' => $postagem->tipo, 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'],
                    };
                @endphp
                <article
                    class="group relative rounded-3xl border border-gray-200/90 dark:border-slate-700/90 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl shadow-xl shadow-gray-900/5 dark:shadow-black/40 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:border-blue-500/20">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-linear-to-b from-blue-500 to-indigo-600 opacity-90"></div>
                    <div class="p-6 md:p-8 pl-7 md:pl-10">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            <time class="text-sm text-gray-500 dark:text-gray-400 font-medium"
                                datetime="{{ $postagem->created_at?->toIso8601String() }}">{{ $postagem->created_at?->format('d/m/Y \à\s H:i') }}</time>
                            @if ($postagem->autor)
                                <span class="text-sm text-gray-400 dark:text-gray-500">· {{ $postagem->autor->name }}</span>
                            @endif
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white tracking-tight mb-4 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                            {{ $postagem->titulo }}
                        </h2>
                        <div class="prose prose-slate dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed text-[15px] md:text-base">
                            {!! nl2br(e($postagem->conteudo)) !!}
                        </div>
                        @if ($postagem->anexo_path)
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-800">
                                <a href="{{ asset($postagem->anexo_path) }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-3 rounded-2xl px-5 py-3 bg-linear-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg shadow-blue-600/30 hover:from-blue-500 hover:to-indigo-500 transition-all">
                                    <x-icon name="download" class="w-5 h-5" />
                                    Baixar anexo
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-gray-300 dark:border-slate-700 p-12 text-center">
                    <x-icon name="folder-open" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-600 dark:text-gray-400">Ainda não há publicações no mural.</p>
                </div>
            @endforelse

            @if ($postagens->hasPages())
                <div class="flex justify-center pt-4">
                    {{ $postagens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

