@props(['name', 'value' => '', 'label' => null])

@php
    $initialContent = $value ?? '';
@endphp

<div class="w-full" x-data="richEditor()" x-init="init()">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Conteúdo inicial em script para o JS ler (evita escape e garante valor na edição) --}}
    <script type="text/template" x-ref="initialContent" data-rich-editor="{{ $name }}">{!! $initialContent !!}</script>

    <div class="relative rounded-md shadow-sm rich-editor-root">
        <div x-ref="editor" class="prose dark:prose-invert max-w-none bg-white dark:bg-gray-800 rounded-md min-h-[400px]">
            {!! $initialContent !!}
        </div>

        <input type="hidden" name="{{ $name }}" :value="content">
    </div>

    <!-- Styles for Quill Dark Mode -->
    <style>
        .ql-toolbar {
            background-color: var(--color-surface);
            border-color: var(--color-border) !important;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        .dark .ql-toolbar {
            background-color: var(--color-surface); /* Uses dark variant automatically via CSS var */
            border-color: var(--color-border) !important;
        }
        .dark .ql-toolbar .ql-stroke {
            stroke: var(--color-text-muted);
        }
        .dark .ql-toolbar .ql-fill {
            fill: var(--color-text-muted);
        }
        .dark .ql-toolbar .ql-picker {
            color: var(--color-text-muted);
        }
        .ql-container {
            border-color: var(--color-border) !important;
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            font-family: inherit;
            font-size: 1rem;
        }
        .dark .ql-container {
            border-color: var(--color-border) !important;
            background-color: var(--color-background);
            color: var(--color-text);
        }
        .ql-editor {
            min-height: 400px;
        }
        .ql-editor blockquote {
            border-left: 4px solid var(--color-warning);
            padding-left: 1rem;
            font-style: italic;
            color: var(--color-text-muted);
        }
        .dark .ql-editor blockquote {
            color: var(--color-text-muted);
        }
    </style>
</div>
