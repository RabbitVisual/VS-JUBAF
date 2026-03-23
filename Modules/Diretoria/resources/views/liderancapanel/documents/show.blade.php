@extends('liderancapanel::components.layouts.master')

@section('title', $document->title)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                    <a href="{{ route('lideranca.conselho.index') }}"
                        class="hover:text-white transition-colors">{{ __('diretoria::messages.diretoria') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <a href="{{ route('lideranca.conselho.documents.index') }}"
                        class="hover:text-white transition-colors">{{ __('diretoria::messages.documents') }}</a>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    <span class="text-white font-bold">{{ $document->title }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $document->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ \Modules\Diretoria\App\Models\AtaDocumento::getDocumentTypeLabel($document->document_type) }}
                    · {{ $document->document_date ? $document->document_date->format('d/m/Y') : '' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('lideranca.conselho.documents.download', $document) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <x-icon name="arrow-down-to-bracket" class="w-5 h-5" /> {{ __('diretoria::messages.download') }}
                </a>
                <a href="{{ route('lideranca.conselho.documents.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" /> {{ __('diretoria::messages.back') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">
                {{ __('diretoria::messages.description') }}</h2>
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                {{ $document->description ?? '—' }}
            </div>
            @if ($document->meeting)
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('diretoria::messages.meeting') }}: <a
                        href="{{ route('lideranca.conselho.meetings.show', $document->meeting) }}"
                        class="text-amber-600 dark:text-amber-400 hover:underline">{{ $document->meeting->title }}</a>
                </p>
            @endif
        </div>
    </div>
@endsection
