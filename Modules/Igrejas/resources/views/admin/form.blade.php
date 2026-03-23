@php
    $isEdit = $igreja->exists;
    $pastorValue = old('pastor_titular', $igreja->pastor_titular ?? $igreja->lideranca_titular);
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-8"
      onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: '{{ $isEdit ? 'Atualizando igreja...' : 'Cadastrando igreja...' }}' } }))">
    @csrf
    @if ($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <x-icon name="church" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dados da Igreja</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Preencha os dados institucionais da congregação.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Igreja *</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome', $igreja->nome) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="pastor_titular" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pastor Titular</label>
                <input type="text" name="pastor_titular" id="pastor_titular" value="{{ $pastorValue }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="lider_jovens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Líder de Jovens</label>
                <input type="text" name="lider_jovens" id="lider_jovens" value="{{ old('lider_jovens', $igreja->lider_jovens) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $igreja->cidade) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado (UF)</label>
                <input type="text" name="estado" id="estado" maxlength="2" value="{{ old('estado', $igreja->estado) }}"
                       class="w-full px-4 py-2.5 uppercase rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <x-icon name="image" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Logo da Igreja</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Use imagem quadrada para melhor resultado visual.</p>
            </div>
        </div>

        <div class="flex items-center gap-5">
            <img id="logo-preview"
                 src="{{ old('logo_path') ? asset('storage/' . old('logo_path')) : ($igreja->logo_path ? Storage::url($igreja->logo_path) : asset('logo_icon.svg')) }}"
                 alt="Preview da logo"
                 class="h-20 w-20 rounded-2xl object-cover border border-gray-200 dark:border-gray-600 shadow-sm bg-white">

            <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <x-icon name="upload" class="w-4 h-4" />
                Selecionar logo
                <input id="logo_path" type="file" name="logo_path" accept="image/*" class="hidden">
            </label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.igrejas.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-colors">
            {{ $isEdit ? 'Salvar alterações' : 'Cadastrar igreja' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('logo_path');
        const preview = document.getElementById('logo-preview');
        if (!input || !preview) return;

        input.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush

