@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6 max-w-4xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Caso disciplinar – {{ $case->member->name ?? 'Membro removido' }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Tipo: {{ ucfirst(str_replace('_', ' ', $case->case_type)) }} • Aberto em
                    {{ $case->created_at->format('d/m/Y') }}
                </p>
            </div>
            <a href="{{ route('admin.churchcouncil.discipline.index') }}"
                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                        <x-icon name="file-alt" class="w-4 h-4" />
                        Resumo liderancaal
                    </h2>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">
                        {{ $case->summary }}
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <x-icon name="stream" class="w-4 h-4" />
                        Linha do tempo (Mt 18)
                    </h2>

                    @if ($case->actions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($case->actions as $action)
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 w-2 h-2 rounded-full bg-red-500"></div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                            {{ $action->performed_at?->format('d/m/Y H:i') }} • {{ $action->stage }}
                                        </p>
                                        @if ($action->performer)
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                                Registrado por {{ $action->performer->name }}
                                            </p>
                                        @endif
                                        @if ($action->notes)
                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 whitespace-pre-line">
                                                {{ $action->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma ação registrada além da abertura do
                            caso.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Situação atual</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                        Status:
                    </p>
                    <p
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                    @if ($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_OPENED) bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                    @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_UNDER_CARE)
                        bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                    @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_RECOMMENDED_TO_ASSEMBLY)
                        bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                    @elseif($case->status === \Modules\ChurchCouncil\App\Models\DisciplineCase::STATUS_DECIDED_BY_ASSEMBLY)
                        bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300
                    @else
                        bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                    </p>

                    @if ($case->current_stage)
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Etapa atual: <span
                                class="font-semibold text-gray-800 dark:text-gray-100">{{ $case->current_stage }}</span>
                        </p>
                    @endif

                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        Aberto por {{ $case->openedBy->name ?? '—' }} em {{ $case->created_at->format('d/m/Y H:i') }}.
                        @if ($case->closed_at)
                            <br>Encerrado em {{ $case->closed_at->format('d/m/Y H:i') }}.
                        @endif
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <x-icon name="paperclip" class="w-4 h-4" />
                        Documentos anexados (acesso protegido)
                    </h2>
                    @if ($case->files->count() > 0)
                        <ul class="space-y-2 text-xs text-gray-700 dark:text-gray-200">
                            @foreach ($case->files as $file)
                                <li class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="font-semibold">
                                            {{ $file->original_name }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $file->created_at->format('d/m/Y H:i') }}
                                            @if ($file->uploader)
                                                • por {{ $file->uploader->name }}
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.churchcouncil.discipline.files.download', [$case, $file]) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition">
                                        <x-icon name="download" class="w-3.5 h-3.5 mr-1" />
                                        Baixar
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Nenhum documento anexado a este caso.
                        </p>
                    @endif

                    <form id="disciplineFileForm" class="mt-4 space-y-2" enctype="multipart/form-data">
                        @csrf
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Anexar novo documento
                        </label>
                        <input type="file" name="file" id="discipline_file"
                            class="block w-full text-xs text-gray-700 dark:text-gray-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            Apenas equipe de liderança verá este arquivo. Tamanho máximo 10MB.
                        </p>
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest flex items-center gap-2 shadow-sm hover:shadow-emerald-500/30">
                                <x-icon name="upload" class="w-3.5 h-3.5" />
                                Anexar
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Registrar nova ação</h2>
                    <form id="disciplineActionForm" class="space-y-3">
                        @csrf
                        <div>
                            <label for="stage" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Etapa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="stage" name="stage" required
                                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-xs">
                        </div>
                        <div>
                            <label for="notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Notas liderancaais
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-xs resize-y"></textarea>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Atualizar status do caso
                            </label>
                            <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-xs">
                                <option value="">Manter status atual</option>
                                <option value="opened">Aberto</option>
                                <option value="under_care">Em acompanhamento</option>
                                <option value="recommended_to_assembly">Encaminhado à assembleia</option>
                                <option value="decided_by_assembly">Decidido em assembleia</option>
                                <option value="closed">Encerrado</option>
                            </select>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-blue-500/30">
                                <x-icon name="plus" class="w-4 h-4" />
                                Registrar ação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('disciplineActionForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('loading-overlay:show'));

                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData.entries());

                    try {
                        const response = await fetch(
                            '{{ route('admin.churchcouncil.discipline.store-action', $case) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(\'meta[name="csrf-token"]\').content,
                                    },
                                    body: JSON.stringify(data),
                                });

                            const result = await response.json(); window.dispatchEvent(new CustomEvent(
                                'stop-loading'));

                            if (result.success) {
                                location.reload();
                            } else {
                                alert(result.message || 'Erro ao registrar ação.');
                            }
                        }
                        catch (error) {
                            window.dispatchEvent(new CustomEvent('stop-loading'));
                            console.error(error);
                            alert('Erro ao processar solicitação.');
                        }
                    });

                document.getElementById('disciplineFileForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const form = this;
                        const fileInput = form.querySelector('input[type="file"]');
                        if (!fileInput.files.length) {
                            alert('Selecione um arquivo para anexar.');
                            return;
                        }

                        window.dispatchEvent(new CustomEvent('loading-overlay:show'));

                        const formData = new FormData();
                        formData.append('file', fileInput.files[0]);

                        try {
                            const response = await fetch(
                                '{{ route('admin.churchcouncil.discipline.files.store', $case) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(\'meta[name="csrf-token"]\').content,
                                        },
                                        body: formData,
                                    });

                                const result = await response.json(); window.dispatchEvent(new CustomEvent(
                                    'stop-loading'));

                                if (result.success) {
                                    location.reload();
                                } else {
                                    alert(result.message || 'Erro ao anexar documento.');
                                }
                            }
                            catch (error) {
                                window.dispatchEvent(new CustomEvent('stop-loading'));
                                console.error(error);
                                alert('Erro ao processar upload.');
                            }
                        });
    </script>
@endsection
