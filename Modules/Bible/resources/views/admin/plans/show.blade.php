@extends('admin::components.layouts.master')

@section('title', $plan->title)

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $plan->title }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $plan->is_active ? 'Publicado' : 'Rascunho' }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $plan->reading_mode === 'physical_timer' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $plan->reading_mode === 'physical_timer' ? 'Físico (Timer)' : 'Digital' }}
                </span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-4">
                <span class="flex items-center gap-1"><x-icon name="clock" style="duotone" class="w-4 h-4" /> {{ $plan->duration_days }} dias</span>
                <span class="flex items-center gap-1"><x-icon name="clipboard-list" class="w-4 h-4" /> {{ $plan->days_count }} dias gerados</span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.bible.plans.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Voltar</a>
            <a href="{{ route('admin.bible.plans.edit', $plan->id) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center">
                <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                Editar Configurações
            </a>
            <a href="{{ route('admin.bible.plans.generate', $plan->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition shadow-sm flex items-center" onclick="return {{ $plan->days_count > 0 ? 'confirm(\'Isso irá apagar todo o conteúdo atual e permitir gerar novamente. Continuar?\')' : 'true' }}">
                <x-icon name="arrows-rotate" class="w-4 h-4 mr-2" />
                {{ $plan->days_count > 0 ? 'Regenerar (Apagar & Criar)' : 'Gerar Estrutura' }}
            </a>
        </div>
    </div>

    <!-- Calendar Grid Visualization -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($plan->days as $day)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">{{ $day->day_number }}</span>
                        Dia {{ $day->day_number }}
                    </span>
                    <a href="{{ route('admin.bible.plans.days.edit', ['planId' => $plan->id, 'dayId' => $day->id]) }}" class="text-xs font-medium text-gray-400 group-hover:text-blue-500 transition-colors">Editar</a>
                </div>
                <div class="p-4 space-y-2 max-h-48 overflow-y-auto">
                    @forelse($day->contents as $content)
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/30 px-3 py-1.5 rounded-lg justify-between">
                            <div class="flex items-center gap-2">
                                @if($content->type === 'scripture')
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    {{ $content->book->name }} {{ $content->chapter_start }}
                                    @if($content->chapter_end > $content->chapter_start)-{{ $content->chapter_end }}@endif
                                @elseif($content->type === 'video')
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span>Vídeo: {{ $content->title ?: 'Conteúdo de Vídeo' }}</span>
                                @elseif($content->type === 'devotional')
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                    <span>Devocional: {{ $content->title ?: 'Texto' }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] uppercase font-bold text-gray-400">{{ substr($content->type, 0, 3) }}</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-red-400 italic bg-red-50 dark:bg-red-900/10 rounded-lg">
                            Sem conteúdo definido
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                <x-icon name="plus" class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum dia gerado</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gere o cronograma automaticamente ou adicione dias manualmente.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.bible.plans.generate', $plan->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Gerar Agora
                    </a>
                </div>
            </div>
        @endforelse
    </div>
    </div>

@endsection

