@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ __('churchcouncil::messages.new_agenda_for_meeting') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('churchcouncil::messages.add_agenda_for_meeting') }} {{ $meeting->title }}</p>
        </div>
        <a href="{{ route('admin.churchcouncil.agendas.index', $meeting) }}"
           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
            <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
            {{ __('churchcouncil::messages.back') }}
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form id="createAgendaForm" class="space-y-8">
            @csrf

            <!-- Agenda Information -->
            <div>
                 <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="document-text" class="w-5 h-5" />
                    </div>
                    Informações da Pauta
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Título da Pauta <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                            value="{{ old('title') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            placeholder="Ex: Aprovação do Orçamento Anual">
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Prioridade <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="priority" id="priority" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            <x-icon name="chevron-down" class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                        </div>
                    </div>

                    <div>
                        <label for="presented_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Apresentado por
                        </label>
                        <div class="relative">
                            <select name="presented_by" id="presented_by"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                <option value="">Selecione o membro (Opcional)</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('presented_by') == $member->id ? 'selected' : '' }}>
                                        {{ $member->user->name }} ({{ $member->role_display }})
                                    </option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Descrição Detalhada <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-y"
                            placeholder="Descreva os detalhes da pauta para discussão...">{{ old('description') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-start gap-3 p-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 cursor-pointer">
                            <input type="checkbox" name="requires_assembly_vote" value="1"
                                   class="mt-1 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-gray-700">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Encaminhar decisão à Assembleia da Igreja
                                </span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Use esta opção para pautas cujo desfecho final depende de votação em assembleia (ex.: orçamento anual, eleição de oficiais, mudanças estatutárias).
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.churchcouncil.agendas.index', $meeting) }}"
                   class="px-6 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold transition-all text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Salvar Pauta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createAgendaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    window.dispatchEvent(new CustomEvent('loading-overlay:show'));
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('{{ route("admin.churchcouncil.agendas.store", $meeting) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        window.dispatchEvent(new CustomEvent('stop-loading'));

        if (result.success) {
            window.location.href = '{{ route("admin.churchcouncil.agendas.index", $meeting) }}';
        } else {
            alert(result.message || '{{ __('churchcouncil::messages.agenda_update_error') }}');
        }
    } catch (error) {
        window.dispatchEvent(new CustomEvent('stop-loading'));
        console.error(error);
        alert('{{ __('churchcouncil::messages.request_error') }}');
    }
});
</script>
@endsection

