@php $template = $template ?? null; @endphp
<div>
    <label for="key" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Chave (única) *</label>
    <input type="text" name="key" id="key" value="{{ old('key', $template?->key) }}" required maxlength="128"
        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono" {{ $template ? 'readonly' : '' }}>
    @error('key')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
<div>
    <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
    <input type="text" name="name" id="name" value="{{ old('name', $template?->name) }}" required maxlength="255"
        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    @error('name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
<div>
    <label for="subject" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Assunto (e-mail)</label>
    <input type="text" name="subject" id="subject" value="{{ old('subject', $template?->subject) }}" maxlength="255"
        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    @error('subject')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
<div>
    <label for="body" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Corpo (use &#123;&#123; title &#125;&#125;, &#123;&#123; message &#125;&#125;, &#123;&#123; action_url &#125;&#125;) *</label>
    <textarea name="body" id="body" rows="8" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono">{{ old('body', $template?->body) }}</textarea>
    @error('body')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template?->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Template ativo</span>
    </label>
</div>
