@extends('memberpanel::components.layouts.master')

@section('title', 'Criar Nova Pauta - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <div class="max-w-3xl mx-auto p-6 space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Criar Nova Pauta</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">Proponha uma nova pauta para discussão em reunião</p>
                </div>
                <a href="{{ route('memberpanel.Diretoria.agendas.index') }}"
                    class="px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-white transition-colors font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700 flex items-center justify-center sm:w-auto w-full">
                    <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                    Voltar
                </a>
            </div>

            <!-- Form -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 md:p-8 relative overflow-hidden transition-colors duration-200">
                <div class="absolute top-0 right-0 p-6 opacity-5">
                    <x-icon name="file-lines" class="w-64 h-64 text-emerald-600 dark:text-emerald-500" />
                </div>

                <form id="createAgendaForm" class="space-y-6 relative z-10">
                    @csrf

                    <!-- Meeting Selection -->
                    <div>
                        <label for="meeting_id"
                            class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                            Reunião <span class="text-emerald-600 dark:text-emerald-500">*</span>
                        </label>
                        <select name="meeting_id" id="meeting_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm">
                            <option value="">Selecione uma reunião</option>
                            @foreach ($upcomingMeetings as $meeting)
                                <option value="{{ $meeting->id }}">
                                    {{ $meeting->title }} - {{ $meeting->scheduled_date->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500 dark:text-slate-500 flex items-center gap-1">
                            <x-icon name="circle-info" class="w-3 h-3" />
                            Apenas reuniões futuras podem receber novas pautas
                        </p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title"
                            class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                            Título da Pauta <span class="text-emerald-600 dark:text-emerald-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder-gray-400 dark:placeholder-slate-600"
                            placeholder="Ex: Aprovação de novo projeto">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                            Descrição <span class="text-emerald-600 dark:text-emerald-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 resize-y"
                            placeholder="Descreva detalhadamente a pauta que deseja propor..."></textarea>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority"
                            class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                            Prioridade <span class="text-emerald-600 dark:text-emerald-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-950/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm">
                            <option value="low">Baixa</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-100 dark:border-slate-800 mt-8">
                        <a href="{{ route('memberpanel.Diretoria.agendas.index') }}"
                            class="px-6 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white font-bold transition-all text-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 hover:-translate-y-0.5 flex items-center justify-center">
                            <x-icon name="check" class="w-5 h-5 mr-2" />
                            Criar Pauta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('createAgendaForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('{{ route('memberpanel.Diretoria.agendas.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect ||
                        '{{ route('memberpanel.Diretoria.agendas.index') }}';
                } else {
                    alert(result.message || 'Erro ao criar pauta');
                }
            } catch (error) {
                console.error(error);
                alert('Erro ao processar solicitação');
            }
        });
    </script>
@endsection
