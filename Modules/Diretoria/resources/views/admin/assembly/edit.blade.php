@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6 max-w-3xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Registrar decisão da assembleia
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Pauta: {{ $agenda->title }} (Reunião: {{ $agenda->meeting->title ?? '—' }})
                </p>
            </div>
            <a href="{{ route('admin.Diretoria.assembly.index') }}"
                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form id="assemblyDecisionForm" class="space-y-6">
                @csrf

                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Resumo da pauta</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                        {{ $agenda->description }}
                    </p>
                    @if ($agenda->decision)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <strong>Decisão da diretoria:</strong> {{ $agenda->decision }}
                        </p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Resultado da votação em assembleia <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" name="assembly_decision" value="approved"
                                    {{ $agenda->assembly_decision === 'approved' ? 'checked' : '' }}
                                    class="w-4 h-4 text-green-600 border-gray-300 dark:border-gray-600 focus:ring-green-500">
                                Aprovado
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="radio" name="assembly_decision" value="rejected"
                                    {{ $agenda->assembly_decision === 'rejected' ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500">
                                Rejeitado
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="assembly_votes_for"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Votos a favor
                        </label>
                        <input type="number" min="0" name="assembly_votes_for" id="assembly_votes_for"
                            value="{{ old('assembly_votes_for', $agenda->assembly_votes_for) }}"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm">
                    </div>
                    <div>
                        <label for="assembly_votes_against"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Votos contra
                        </label>
                        <input type="number" min="0" name="assembly_votes_against" id="assembly_votes_against"
                            value="{{ old('assembly_votes_against', $agenda->assembly_votes_against) }}"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm">
                    </div>
                    <div>
                        <label for="assembly_votes_abstain"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Abstenções
                        </label>
                        <input type="number" min="0" name="assembly_votes_abstain" id="assembly_votes_abstain"
                            value="{{ old('assembly_votes_abstain', $agenda->assembly_votes_abstain) }}"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white text-sm">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        Salvar decisão
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('assemblyDecisionForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('loading-overlay:show'));

                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData.entries());

                    try {
                        const response = await fetch(
                            '{{ route('admin.Diretoria.assembly.store-decision', $agenda) }}', {
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
                                window.location.href = result.redirect ||
                                    '{{ route('admin.Diretoria.assembly.index') }}';
                            } else {
                                alert(result.message || 'Erro ao registrar decisão.');
                            }
                        }
                        catch (error) {
                            window.dispatchEvent(new CustomEvent('stop-loading'));
                            console.error(error);
                            alert('Erro ao processar solicitação.');
                        }
                    });
    </script>
@endsection
