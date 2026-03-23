{{-- Partial: _form_certificate.blade.php — Certificado config (edit only) --}}
@php $ev = $event ?? null; $cert = $ev?->certificates()?->first(); @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
        <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="award" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Certificado de Participação</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Template e liberação do certificado</p>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <label for="certificate_release_after" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Liberar certificado após
            </label>
            <input type="datetime-local" name="certificate_release_after" id="certificate_release_after"
                value="{{ old('certificate_release_after', $cert?->release_after?->format('Y-m-d\TH:i')) }}"
                class="block w-full max-w-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe vazio para liberar imediatamente após o evento</p>
        </div>
        <div>
            <label for="certificate_template_html" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Template HTML do Certificado
                <span class="text-xs text-gray-400 font-normal ml-1">— use @{{ name }}, @{{ event }}, @{{ date }}, @{{ hours }}</span>
            </label>
            <textarea name="certificate_template_html" id="certificate_template_html" rows="6"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm font-mono text-xs">{{ old('certificate_template_html', $cert?->template_html) }}</textarea>
        </div>
    </div>
</div>
