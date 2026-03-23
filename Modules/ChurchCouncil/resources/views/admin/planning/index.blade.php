@extends('admin::components.layouts.master')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Homologação de Planejamento
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Eventos que exigem aprovação do conselho antes de serem publicados.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.events.index') }}"
                   class="inline-flex items-center px-3 py-2 text-xs font-bold uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <x-icon name="calendar" class="w-4 h-4 mr-1.5" />
                    <span>Ir para Eventos</span>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.churchcouncil.planning.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">
                        Ministério
                    </label>
                    <select name="ministry_id" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 text-sm text-gray-900 dark:text-white px-3 py-2">
                        <option value="">Todos</option>
                        @foreach($ministries as $ministry)
                            <option value="{{ $ministry->id }}" @selected(request('ministry_id') == $ministry->id)>
                                {{ $ministry->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">
                        Situação
                    </span>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Apenas solicitações pendentes ou em revisão são exibidas aqui.
                    </p>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-blue-700 shadow-sm hover:shadow-md transition">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.churchcouncil.planning.index') }}"
                       class="px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-300 text-xs hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">
                        Solicitações de publicação de eventos
                    </span>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $approvals->total() }} itens
                </span>
            </div>

            @if($approvals->isEmpty())
                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Nenhum evento aguardando homologação do conselho no momento.
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($approvals as $approval)
                        @php
                            /** @var \Modules\Events\App\Models\Event|null $event */
                            $event = $approval->approvable instanceof \Modules\Events\App\Models\Event ? $approval->approvable : null;
                        @endphp
                        <div class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        Evento aguardando conselho
                                    </span>
                                    @if($event)
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                            {{ $event->title }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                            Evento não encontrado
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    @if($event && $event->start_date)
                                        <span class="mr-3">
                                            <x-icon name="clock" class="w-3 h-3 inline mr-1" />
                                            {{ $event->start_date->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                    @if($event && $event->ministry)
                                        <span class="mr-3">
                                            <x-icon name="users" class="w-3 h-3 inline mr-1" />
                                            {{ $event->ministry->name }}
                                        </span>
                                    @endif
                                    @if($event && $event->ministryPlan)
                                        <span class="mr-3">
                                            <x-icon name="clipboard-list" class="w-3 h-3 inline mr-1" />
                                            Plano: {{ $event->ministryPlan->title }}
                                        </span>
                                    @endif
                                    @if($approval->requester)
                                        <span>
                                            <x-icon name="user" class="w-3 h-3 inline mr-1" />
                                            Solicitado por {{ $approval->requester->name }}
                                        </span>
                                    @endif
                                </div>
                                @if($approval->request_details)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $approval->request_details }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2 justify-end">
                                @if($event)
                                    <a href="{{ route('admin.events.events.show', $event) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                                        <x-icon name="eye" class="w-3.5 h-3.5 mr-1" />
                                        Ver Evento
                                    </a>
                                @endif
                                <button type="button"
                                        class="btn-cc-approve inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm hover:shadow-md transition"
                                        data-approve-url="{{ route('admin.churchcouncil.approvals.approve', $approval) }}">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 mr-1" />
                                    Aprovar
                                </button>
                                <button type="button"
                                        class="btn-cc-reject inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm hover:shadow-md transition"
                                        data-reject-url="{{ route('admin.churchcouncil.approvals.reject', $approval) }}">
                                    <x-icon name="x-circle" class="w-3.5 h-3.5 mr-1" />
                                    Rejeitar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $approvals->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            }

            document.querySelectorAll('.btn-cc-approve').forEach(function (btn) {
                btn.addEventListener('click', async function () {
                    if (!confirm('Aprovar este evento para publicação?')) return;
                    const url = btn.getAttribute('data-approve-url');
                    const formData = new FormData();
                    formData.append('notes', '');
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
                        body: formData,
                    });
                    const data = await resp.json();
                    if (!resp.ok || data.success === false) {
                        alert(data.message || 'Não foi possível aprovar a solicitação.');
                        return;
                    }
                    window.location.reload();
                });
            });

            document.querySelectorAll('.btn-cc-reject').forEach(function (btn) {
                btn.addEventListener('click', async function () {
                    const reason = prompt('Informe o motivo da rejeição:');
                    if (!reason) return;
                    const url = btn.getAttribute('data-reject-url');
                    const formData = new FormData();
                    formData.append('reason', reason);
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
                        body: formData,
                    });
                    const data = await resp.json();
                    if (!resp.ok || data.success === false) {
                        alert(data.message || 'Não foi possível rejeitar a solicitação.');
                        return;
                    }
                    window.location.reload();
                });
            });
        });
    </script>
@endpush

