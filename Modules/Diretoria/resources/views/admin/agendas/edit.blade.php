@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ __('diretoria::messages.edit_agenda') }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('diretoria::messages.meeting') }}:
                    {{ $meeting->title }}</p>
            </div>
            <a href="{{ route('admin.Diretoria.agendas.index', $meeting) }}"
                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                {{ __('diretoria::messages.back') }}
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form id="editAgendaForm" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Agenda Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <x-icon name="pencil-alt" class="w-5 h-5" />
                        </div>
                        {{ __('diretoria::messages.edit_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Título da Pauta <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                value="{{ old('title', $agenda->title) }}"
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
                                    <option value="normal"
                                        {{ old('priority', $agenda->priority) == 'normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="low"
                                        {{ old('priority', $agenda->priority) == 'low' ? 'selected' : '' }}>Baixa</option>
                                    <option value="high"
                                        {{ old('priority', $agenda->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgent"
                                        {{ old('priority', $agenda->priority) == 'urgent' ? 'selected' : '' }}>Urgente
                                    </option>
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

                        <div>
                            <label for="presented_by"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Apresentado por
                            </label>
                            <div class="relative">
                                <select name="presented_by" id="presented_by"
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Selecione o membro (Opcional)</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('presented_by', $agenda->presented_by) == $member->id ? 'selected' : '' }}>
                                            {{ $member->user->name }} ({{ $member->role_display }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Descrição Detalhada <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="6" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-y"
                                placeholder="Descreva os detalhes da pauta para discussão...">{{ old('description', $agenda->description) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Status Atual
                            </label>
                            <div class="relative">
                                <select name="status" id="status" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="pending"
                                        {{ old('status', $agenda->status) == 'pending' ? 'selected' : '' }}>Pendente
                                    </option>
                                    <option value="discussed"
                                        {{ old('status', $agenda->status) == 'discussed' ? 'selected' : '' }}>Em Discussão
                                    </option>
                                    <option value="approved"
                                        {{ old('status', $agenda->status) == 'approved' ? 'selected' : '' }}>Aprovada
                                    </option>
                                    <option value="rejected"
                                        {{ old('status', $agenda->status) == 'rejected' ? 'selected' : '' }}>Rejeitada
                                    </option>
                                    <option value="postponed"
                                        {{ old('status', $agenda->status) == 'postponed' ? 'selected' : '' }}>Adiada
                                    </option>
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="flex items-start gap-3 p-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 cursor-pointer">
                                <input type="checkbox" name="requires_assembly_vote" value="1"
                                    {{ old('requires_assembly_vote', $agenda->requires_assembly_vote) ? 'checked' : '' }}
                                    class="mt-1 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-gray-700">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                        Encaminhar decisão à Assembleia da Igreja
                                    </span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Quando marcado, esta pauta será tratada como recomendação à assembleia; o resultado
                                        final será registrado após a votação da igreja.
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                @if ($agenda->versions && $agenda->versions->count() > 0)
                    <div class="mt-10 border-t border-dashed border-gray-100 dark:border-gray-700 pt-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-icon name="history" class="w-4 h-4" />
                            Histórico de versões da pauta
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                            As versões registram como o texto e o status desta pauta foram sendo refinados ao longo do
                            processo.
                        </p>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach ($agenda->versions as $version)
                                <div
                                    class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/60 rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-[10px] font-bold uppercase tracking-wide">
                                            v{{ $version->version }}
                                        </span>
                                        <span>{{ $version->created_at->format('d/m/Y H:i') }}</span>
                                        @if ($version->creator)
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                por {{ $version->creator->name }}
                                            </span>
                                        @endif
                                    </div>
                                    @php($payload = $version->payload ?? [])
                                    <span class="text-[11px] font-semibold text-gray-500">
                                        {{ $payload['status'] ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form Actions -->
                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.Diretoria.agendas.index', $meeting) }}"
                        class="px-6 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold transition-all text-center">
                        {{ __('diretoria::messages.cancel') }}
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        {{ __('diretoria::messages.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('editAgendaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('loading-overlay:show'));
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(
                    '{{ route('admin.Diretoria.agendas.update', ['meeting' => $meeting->id, 'agenda' => $agenda->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ...data,
                            _method: 'PUT'
                        })
                    });

                const result = await response.json();
                window.dispatchEvent(new CustomEvent('stop-loading'));

                if (result.success) {
                    window.location.href = '{{ route('admin.Diretoria.agendas.index', $meeting) }}';
                } else {
                    alert(result.message || '{{ __('diretoria::messages.agenda_update_error') }}');
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                console.error(error);
                alert('{{ __('diretoria::messages.request_error') }}');
            }
        });
    </script>
@endsection
