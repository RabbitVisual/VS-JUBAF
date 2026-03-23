@extends('memberpanel::components.layouts.master')

@section('title', 'Solicitar carta de transferência')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200">
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Solicitar carta de transferência
                </h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1">
                    Este pedido será analisado pelo conselho e pela secretaria da igreja antes da emissão da carta.
                </p>
            </div>
            <a href="{{ route('memberpanel.transfers.index') }}"
               class="px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200 dark:border-slate-700 transition-colors font-bold flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-48 opacity-10 pointer-events-none">
                <x-icon name="envelope-open-text" class="w-full h-full text-emerald-500" />
            </div>

            <form id="requestTransferForm" class="space-y-6 relative z-10">
                @csrf

                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-slate-300">
                        <span class="font-semibold">Importante:</span> a carta de transferência é um documento oficial que
                        expressa comunhão entre igrejas batistas. Ela é emitida pela igreja atual e encaminhada à igreja
                        de destino, após avaliação pastoral e do conselho.
                    </p>

                    <div>
                        <label for="to_church" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Igreja de destino <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="to_church" id="to_church" required
                               class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/40 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                               placeholder="Ex.: Primeira Igreja Batista em Cidade X">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800 mt-4">
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 flex items-center justify-center">
                        <x-icon name="paper-plane" class="w-5 h-5 mr-2" />
                        Enviar pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('requestTransferForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    window.dispatchEvent(new CustomEvent('loading-overlay:show'));

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('{{ route("memberpanel.transfers.store") }}', {
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
            window.location.href = result.redirect || '{{ route("memberpanel.transfers.index") }}';
        } else {
            alert(result.message || 'Erro ao enviar pedido.');
        }
    } catch (error) {
        window.dispatchEvent(new CustomEvent('stop-loading'));
        console.error(error);
        alert('Erro ao processar solicitação.');
    }
});
</script>
@endsection

