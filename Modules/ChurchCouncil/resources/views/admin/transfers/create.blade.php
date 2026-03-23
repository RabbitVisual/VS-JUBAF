@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Registrar carta de transferência</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Crie um registro de saída ou entrada por carta de transferência.
            </p>
        </div>
        <a href="{{ route('admin.churchcouncil.transfers.index') }}"
           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form id="createTransferLetterForm" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Membro <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                            required>
                        <option value="">Selecione o membro</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Direção da carta <span class="text-red-500">*</span>
                    </label>
                    <select name="direction" id="direction"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                            required>
                        <option value="outgoing">Saída (para outra igreja)</option>
                        <option value="incoming">Entrada (vindo de outra igreja)</option>
                    </select>
                </div>

                <div>
                    <label for="from_church" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Igreja de origem
                    </label>
                    <input type="text" name="from_church" id="from_church"
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm"
                           placeholder="Ex.: Primeira Igreja Batista em ...">
                </div>

                <div>
                    <label for="to_church" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Igreja de destino
                    </label>
                    <input type="text" name="to_church" id="to_church"
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm"
                           placeholder="Ex.: Segunda Igreja Batista em ...">
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-emerald-500/30 flex items-center justify-center">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Salvar carta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createTransferLetterForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    window.dispatchEvent(new CustomEvent('loading-overlay:show'));

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('{{ route("admin.churchcouncil.transfers.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector(\'meta[name="csrf-token"]\').content,
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();
        window.dispatchEvent(new CustomEvent('stop-loading'));

        if (result.success) {
            window.location.href = result.redirect || '{{ route("admin.churchcouncil.transfers.index") }}';
        } else {
            alert(result.message || 'Erro ao registrar carta.');
        }
    } catch (error) {
        window.dispatchEvent(new CustomEvent('stop-loading'));
        console.error(error);
        alert('Erro ao processar solicitação.');
    }
});
</script>
@endsection

