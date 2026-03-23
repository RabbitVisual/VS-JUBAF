@extends('memberpanel::components.layouts.master')

@section('title', $plan->title)

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    <!-- Back Link -->
    <a href="{{ route('member.bible.plans.catalog') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-purple-600 mb-6 transition-colors group">
        <x-icon name="chevron-left" style="duotone" class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform" />
        Voltar ao Catálogo
    </a>

    <!-- Hero Section -->
    <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-gray-900 border border-gray-800">
        @if($plan->cover_image)
            <img src="{{ Storage::url($plan->cover_image) }}" alt="{{ $plan->title }}" class="w-full h-80 object-cover opacity-60">
        @else
            <div class="w-full h-80 bg-linear-to-br from-indigo-900 to-purple-900"></div>
        @endif

        <div class="absolute inset-0 bg-linear-to-t from-gray-900 via-gray-900/40 to-transparent"></div>

        <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full">
            <div class="flex items-center gap-3 mb-4">
                 <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white uppercase tracking-wider border border-white/10">
                    {{ ucfirst($plan->type) }}
                </span>
                <span class="px-3 py-1 bg-purple-500/20 backdrop-blur-md rounded-full text-xs font-bold text-purple-200 border border-purple-500/30 flex items-center gap-1">
                    <x-icon name="clock" class="w-3 h-3" /> {{ $plan->duration_days }} Dias
                </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4 shadow-sm">{{ $plan->title }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-10">
        <!-- Left Content: Description & Syllabus -->
        <div class="lg:col-span-2 space-y-10">

            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sobre este Plano</h2>
                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                    {!! nl2br(e($plan->description)) !!}
                </div>
            </section>

            <section>
                 <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded text-blue-600">
                        <x-icon name="book-open" class="w-5 h-5" />
                    </div>
                    O que você vai ler (Amostra)
                </h2>

                <div class="space-y-4">
                    @forelse($sampleDays as $day)
                        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                             <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400 text-sm">
                                {{ $day->day_number }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">Dia {{ $day->day_number }}</h4>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($day->contents as $content)
                                        @if($content->type === 'scripture')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $content->book->name }} {{ $content->chapter_start }}
                                                @if($content->chapter_end > $content->chapter_start)-{{ $content->chapter_end }}@endif
                                            </span>
                                        @elseif($content->type === 'devotional')
                                             <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                Devocional
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Cronograma ainda não gerado.</p>
                    @endforelse

                    @if($plan->days_count > 5)
                        <div class="text-center pt-2">
                            <span class="text-sm text-gray-500">e mais {{ $plan->days_count - 5 }} dias de conteúdo transformador...</span>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Right Content: CTA Card -->
        <div class="lg:col-span-1">
            <div class="sticky top-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden p-8 text-center space-y-6">
                    <div>
                         <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <x-icon name="check" class="w-8 h-8" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Pronto para começar?</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Junte-se a outros leitores e transforme sua vida espiritual.</p>
                    </div>

                    <form action="{{ route('member.bible.plans.subscribe', $plan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-4 text-lg bg-linear-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                            Inscrever-se Agora
                            <x-icon name="arrow-right" class="w-5 h-5" />
                        </button>
                    </form>

                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Você pode cancelar ou pausar a qualquer momento.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

