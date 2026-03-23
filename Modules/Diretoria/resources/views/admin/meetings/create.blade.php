@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Nova Reunião</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Agende uma nova reunião da diretoria.</p>
            </div>
            <a href="{{ route('admin.Diretoria.meetings.index') }}"
                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium flex items-center justify-center sm:w-auto w-full">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Voltar
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form id="createMeetingForm" class="space-y-8">
                @csrf

                <!-- Meeting Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        Informações da Reunião
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Título da Reunião <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                placeholder="Ex: Reunião Ordinária da Diretoria - Janeiro/2026">
                        </div>

                        <div>
                            <label for="meeting_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Tipo de Reunião <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="meeting_type" id="meeting_type" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Selecione o tipo</option>
                                    <option value="ordinary" {{ old('meeting_type') == 'ordinary' ? 'selected' : '' }}>
                                        Ordinária</option>
                                    <option value="extraordinary"
                                        {{ old('meeting_type') == 'extraordinary' ? 'selected' : '' }}>Extraordinária
                                    </option>
                                    <option value="emergency" {{ old('meeting_type') == 'emergency' ? 'selected' : '' }}>
                                        Emergencial</option>
                                </select>
                                <x-icon name="chevron-down"
                                    class="w-5 h-5 absolute right-3 top-3 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

                        <div>
                            <label for="scheduled_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Data e Hora <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="scheduled_date" id="scheduled_date" required
                                value="{{ old('scheduled_date') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Local
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                placeholder="Ex: Sala de Reuniões 1">
                        </div>

                        <div>
                            <label for="president_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Presidente da Reunião
                            </label>
                            <div class="relative">
                                <select name="president_id" id="president_id"
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Selecione o presidente</option>
                                    @foreach ($presidents as $president)
                                        <option value="{{ $president->id }}"
                                            {{ old('president_id') == $president->id ? 'selected' : '' }}>
                                            {{ $president->user->name }} ({{ $president->role_display }})
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
                                Descrição / Pauta Inicial
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-y"
                                placeholder="Descreva brevemente os objetivos desta reunião...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Participants -->
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <x-icon name="users" class="w-5 h-5" />
                        </div>
                        Participantes Convocados
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($diretoriaMembers as $member)
                            <label
                                class="group flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all hover:border-blue-300 dark:hover:border-blue-700">
                                <input type="checkbox" name="participant_ids[]" value="{{ $member->id }}"
                                    {{ in_array($member->id, old('participant_ids', [])) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-gray-700">
                                <div class="ml-3 flex items-center">
                                    @if ($member->user->photo)
                                        <img src="{{ asset('storage/' . $member->user->photo) }}"
                                            class="w-8 h-8 rounded-full mr-2 object-cover">
                                    @else
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center mr-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $member->user->name }}</span>
                                        <span
                                            class="block text-xs text-gray-500 dark:text-gray-400">{{ $member->role_display }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.Diretoria.meetings.index') }}"
                        class="px-6 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold transition-all text-center">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                        <x-icon name="check" class="w-5 h-5 mr-2" />
                        Agendar Reunião
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('createMeetingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('loading-overlay:show'));
            const formData = new FormData(this);
            const participantIds = Array.from(this.querySelectorAll('input[name="participant_ids[]"]:checked'))
                .map(cb => cb.value);

            const data = {
                title: formData.get('title'),
                meeting_type: formData.get('meeting_type'),
                scheduled_date: formData.get('scheduled_date'),
                location: formData.get('location'),
                president_id: formData.get('president_id'),
                description: formData.get('description'),
                participant_ids: participantIds
            };

            try {
                const response = await fetch('{{ route('admin.Diretoria.meetings.store') }}', {
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
                    window.location.href = result.redirect || '{{ route('admin.Diretoria.meetings.index') }}';
                } else {
                    alert(result.message || '{{ __('Diretoria::messages.meeting_start_error') }}');
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                console.error(error);
                alert('{{ __('Diretoria::messages.request_error') }}');
            }
        });
    </script>
@endsection
