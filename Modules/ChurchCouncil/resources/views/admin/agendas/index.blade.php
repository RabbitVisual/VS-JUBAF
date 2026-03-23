@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-8">
    <!-- Hero Header (padrão configuração) -->
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Diretoria</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Pautas</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ __('churchcouncil::messages.view_agendas') }}</h1>
                <p class="text-gray-300 max-w-xl">{{ __('churchcouncil::messages.agendas_subtitle') }}</p>
            </div>
            <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                <a href="{{ route('admin.churchcouncil.meetings.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5" /> {{ __('churchcouncil::messages.view_meetings') }}
                </a>
                <a href="{{ route('admin.churchcouncil.agendas.create', $meeting) }}" class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="plus" class="w-5 h-5 text-blue-600" /> {{ __('churchcouncil::messages.new_agenda') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
        <div class="relative">
        <form method="GET" action="{{ route('admin.churchcouncil.agendas.index', $meeting) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <!-- Status Filter -->
            <div class="md:col-span-3">
                <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('churchcouncil::messages.current_status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">{{ __('churchcouncil::messages.all_statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="discussed" {{ request('status') === 'discussed' ? 'selected' : '' }}>Em Discussão</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovada</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitada</option>
                    <option value="postponed" {{ request('status') === 'postponed' ? 'selected' : '' }}>Adiada</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="md:col-span-3">
                <label for="priority" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('churchcouncil::messages.priority') }}</label>
                <select name="priority" id="priority" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todas as prioridades</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>



            <!-- Actions -->
            <div class="md:col-span-3 flex items-end">
                <div class="flex gap-2 w-full">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center">
                        <x-icon name="filter" class="w-5 h-5 mr-2" />
                        {{ __('churchcouncil::messages.filter') }}
                    </button>
                    @if (request()->hasAny(['status', 'priority', 'meeting_id', 'search']))
                        <a href="{{ route('admin.churchcouncil.agendas.index', $meeting) }}"
                           class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                            <x-icon name="x" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <!-- Agendas Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>
        <div class="relative">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pauta</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reunião</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioridade</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Apresentado por</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($agendas as $agenda)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ Str::limit($agenda->title, 50) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($agenda->description, 60) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            @if($agenda->meeting)
                                <div class="font-medium text-blue-600 dark:text-blue-400">{{ Str::limit($agenda->meeting->title, 30) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $agenda->meeting->scheduled_date->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                    Não associada
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                @if ($agenda->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($agenda->status === 'discussed') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($agenda->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($agenda->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @elseif($agenda->status === 'postponed') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ $agenda->status_display }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                @if ($agenda->priority === 'high' || $agenda->priority === 'urgent') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @elseif($agenda->priority === 'normal') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ $agenda->priority_display }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            <div class="flex items-center">
                                @if($agenda->presenter && $agenda->presenter->user && $agenda->presenter->user->photo)
                                    <img src="{{ asset('storage/' . $agenda->presenter->user->photo) }}" alt="" class="h-6 w-6 rounded-full mr-2 ring-2 ring-white dark:ring-gray-700 object-cover">
                                @else
                                    <div class="h-6 w-6 rounded-full bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-2 ring-2 ring-white dark:ring-gray-700">
                                        <span class="text-xs font-bold text-white">
                                            {{ substr($agenda->presenter?->user->name ?? '?', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                {{ $agenda->presenter?->user->name ?? 'Não informado' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if($agenda->meeting)
                                    <a href="{{ route('admin.churchcouncil.meetings.show', $agenda->meeting) }}"
                                       class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Ver Reunião">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>
                                @endif

                                <a href="{{ route('admin.churchcouncil.agendas.edit', ['meeting' => $agenda->meeting->id, 'agenda' => $agenda->id]) }}"
                                   class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors" title="Editar">
                                    <x-icon name="pencil" class="w-5 h-5" />
                                </a>

                                @if ($agenda->status === 'discussed' && $agenda->meeting && $agenda->meeting->status === 'in_progress')
                                    <form method="POST" action="{{ route('admin.churchcouncil.agendas.decision', ['meeting' => $agenda->meeting->id, 'agenda' => $agenda->id]) }}"
                                          onsubmit="return confirm('Aprovar esta pauta?')" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Aprovar">
                                            <x-icon name="check" class="w-5 h-5" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.churchcouncil.agendas.decision', ['meeting' => $agenda->meeting->id, 'agenda' => $agenda->id]) }}"
                                          onsubmit="return confirm('Rejeitar esta pauta?')" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Rejeitar">
                                            <x-icon name="x" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-4">
                                    <x-icon name="clipboard-list" class="h-8 w-8 text-blue-500 dark:text-blue-400" />
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhuma pauta encontrada</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                    @if (request()->hasAny(['status', 'priority', 'search', 'date_from']))
                                        Não foram encontradas pautas com os filtros aplicados.
                                    @else
                                        Comece criando a primeira pauta da diretoria.
                                    @endif
                                </p>
                                <div class="mt-6">
                                    @if (request()->hasAny(['status', 'priority', 'search', 'date_from']))
                                        <a href="{{ route('admin.churchcouncil.agendas.index', $meeting) }}"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            Limpar Filtros
                                        </a>
                                    @else
                                        <a href="{{ route('admin.churchcouncil.agendas.create', $meeting) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                            <x-icon name="plus" class="w-5 h-5 mr-2" />
                                            Criar Pauta
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agendas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                {{ $agendas->appends(request()->query())->links() }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

