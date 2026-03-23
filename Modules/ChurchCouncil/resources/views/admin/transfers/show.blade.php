@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                Carta de transferência – {{ $letter->member->name ?? 'Membro removido' }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                @if($letter->direction === \Modules\ChurchCouncil\App\Models\TransferLetter::DIRECTION_OUTGOING)
                    Saída para {{ $letter->to_church ?? 'igreja destino não informada' }}
                @else
                    Entrada vinda de {{ $letter->from_church ?? 'igreja origem não informada' }}
                @endif
            </p>
        </div>
        <a href="{{ route('admin.churchcouncil.transfers.index') }}"
           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                @if($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_COUNCIL)
                    bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_PENDING_ASSEMBLY)
                    bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_SENT)
                    bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                @elseif($letter->status === \Modules\ChurchCouncil\App\Models\TransferLetter::STATUS_ACKNOWLEDGED)
                    bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                @else
                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                @endif">
                {{ ucfirst(str_replace('_', ' ', $letter->status)) }}
            </span>
            @if($letter->issued_at)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Emitida em {{ $letter->issued_at->format('d/m/Y') }}
                </span>
            @endif
            @if($letter->received_at)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Recebida em {{ $letter->received_at->format('d/m/Y') }}
                </span>
            @endif
        </div>

        @if($letter->file_path)
            <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    Documento anexado de carta de transferência.
                </p>
                <a href="{{ route('admin.churchcouncil.transfers.download', $letter) }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2">
                    <x-icon name="download" class="w-4 h-4" />
                    Baixar PDF
                </a>
            </div>
        @else
            <div class="mt-2">
                <form id="uploadTransferFileForm" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    @csrf
                    <input type="file" name="file" accept="application/pdf"
                           class="text-xs text-gray-700 dark:text-gray-200">
                    <button type="submit"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2">
                        <x-icon name="upload" class="w-4 h-4" />
                        Anexar PDF
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
const uploadForm = document.getElementById('uploadTransferFileForm');
if (uploadForm) {
    uploadForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('loading-overlay:show'));

        const formData = new FormData(this);

        try {
            const response = await fetch('{{ route("admin.churchcouncil.transfers.upload", $letter) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector(\'meta[name="csrf-token"]\').content,
                },
                body: formData,
            });

            const result = await response.json();
            window.dispatchEvent(new CustomEvent('stop-loading'));

            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Erro ao anexar documento.');
            }
        } catch (error) {
            window.dispatchEvent(new CustomEvent('stop-loading'));
            console.error(error);
            alert('Erro ao processar solicitação.');
        }
    });
}
</script>
@endsection

