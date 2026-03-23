{{-- Partial: _form_badge.blade.php — Badge/Credencial config (edit only) --}}
@php $ev = $event ?? null; @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
        <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="id-card-clip" style="duotone" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Credencial (Badge)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure o modelo de credencial para impressão</p>
        </div>
    </div>

    @php $badge = $ev?->badges()?->first(); @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label for="badge_orientation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Orientação</label>
            <select name="badge_orientation" id="badge_orientation"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="portrait"  {{ old('badge_orientation', $badge?->orientation ?? 'portrait') === 'portrait'  ? 'selected' : '' }}>Retrato</option>
                <option value="landscape" {{ old('badge_orientation', $badge?->orientation ?? 'portrait') === 'landscape' ? 'selected' : '' }}>Paisagem</option>
            </select>
        </div>
        <div>
            <label for="badge_paper_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tamanho do Papel</label>
            <select name="badge_paper_size" id="badge_paper_size"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach(['A4' => 'A4', 'Letter' => 'Letter'] as $v => $l)
                    <option value="{{ $v }}" {{ old('badge_paper_size', $badge?->paper_size ?? 'A4') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="badge_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Credenciais por Página</label>
            <select name="badge_per_page" id="badge_per_page"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach([4, 6, 8, 10] as $n)
                    <option value="{{ $n }}" {{ old('badge_per_page', $badge?->per_page ?? 6) == $n ? 'selected' : '' }}>{{ $n }} por página</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3">
            <label for="badge_template_html" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Template HTML da Credencial
                <span class="text-xs text-gray-400 font-normal ml-1">— use @{{ name }}, @{{ event }}, @{{ qr_code }}</span>
            </label>
            <textarea name="badge_template_html" id="badge_template_html" rows="5"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm font-mono text-xs">{{ old('badge_template_html', $badge?->template_html) }}</textarea>
        </div>
    </div>
</div>
