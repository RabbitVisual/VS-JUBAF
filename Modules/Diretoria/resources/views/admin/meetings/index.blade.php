@extends('admin::components.layouts.master')

@section('title', 'Reuniões da Diretoria - Administração')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Reuniões da Diretoria</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie as assembleias e reuniões ordinárias ou
                    extraordinárias.</p>
            </div>
            <a href="{{ route('admin.Diretoria.meetings.create') }}"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
                <x-icon name="plus" class="w-5 h-5 mr-2" />
                Nova Reunião
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <form method="GET" action="{{ route('admin.Diretoria.meetings.index') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label for="status"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Todos os status</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Agendada
                        </option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluída
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada
                        </option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="type"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="type" id="type"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Todos os tipos</option>
                        <option value="ordinary" {{ request('type') === 'ordinary' ? 'selected' : '' }}>Ordinária</option>
                        <option value="extraordinary" {{ request('type') === 'extraordinary' ? 'selected' : '' }}>
                            Extraordinária</option>
                        <option value="emergency" {{ request('type') === 'emergency' ? 'selected' : '' }}>Emergencial
                        </option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="date_from" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">A partir
                        de</label>
                    <div class="relative">
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div class="md:col-span-3 flex items-end">
                    <div class="flex gap-2 w-full">
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-gray-600 hover:bg-gray-800 dark:hover:bg-gray-500 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center">
                            <x-icon name="filter" class="w-5 h-5 mr-2" />
                            Filtrar
                        </button>
                        @if (request()->hasAny(['status', 'type', 'date_from']))
                            <a href="{{ route('admin.Diretoria.meetings.index') }}"
                                class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                <x-icon name="x" class="w-5 h-5" />
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Meetings List -->
        <div class="space-y-4">
            @forelse ($meetings as $meeting)
                <div
                    class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <!-- Meeting Info -->
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-14 h-14 rounded-3xl flex items-center justify-center
                                @if ($meeting->status === 'scheduled') bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400
                                @elseif($meeting->status === 'in_progress') bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400
                                @elseif($meeting->status === 'completed') bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                                @else bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 @endif">
                                    <span class="text-lg font-bold">
                                        {{ $meeting->scheduled_date->format('d') }}
                                    </span>
                                    <span class="text-xs uppercase font-medium -mt-4 absolute pt-8">
                                        {{ $meeting->scheduled_date->translatedFormat('M') }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $meeting->title }}
                                    </h3>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                    @if ($meeting->meeting_type === 'ordinary') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($meeting->meeting_type === 'extraordinary') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @endif">
                                        {{ $meeting->meeting_type_display }}
                                    </span>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <x-icon name="clock" class="w-4 h-4 mr-1.5" />
                                        {{ $meeting->scheduled_date->format('H:i') }}
                                    </div>
                                    @if ($meeting->location)
                                        <div class="flex items-center">
                                            <x-icon name="location-marker" class="w-4 h-4 mr-1.5" />
                                            {{ $meeting->location }}
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <div class="flex -space-x-2 overflow-hidden">
                                            @foreach ($meeting->participant_members->take(3) as $participant)
                                                @php
                                                    $pName =
                                                        $participant->user->name ?? ($participant->name ?? 'Membro');
                                                    $pPhoto = $participant->user->photo ?? null;
                                                @endphp
                                                @if ($pPhoto)
                                                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover"
                                                        src="{{ asset('storage/' . $pPhoto) }}" alt="{{ $pName }}"
                                                        title="{{ $pName }}">
                                                @else
                                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300"
                                                        title="{{ $pName }}">
                                                        {{ substr($pName, 0, 1) }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if ($meeting->participant_members->count() > 3)
                                                <div
                                                    class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                                    +{{ $meeting->participant_members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($meeting->president)
                                        <div class="flex items-center" title="Presidente">
                                            <x-icon name="user" class="w-4 h-4 mr-1.5" />
                                            {{ $meeting->president->user->name }}
                                        </div>
                                    @endif
                                </div>

                                @if ($meeting->description)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $meeting->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Actions & Status -->
                        <div class="flex flex-col md:items-end gap-3 flex-shrink-0">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if ($meeting->status === 'scheduled') bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300
                            @elseif($meeting->status === 'in_progress') bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300
                            @elseif($meeting->status === 'completed') bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-300
                            @else bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300 @endif">
                                <span
                                    class="w-2 h-2 rounded-full mr-2
                                @if ($meeting->status === 'scheduled') bg-blue-500
                                @elseif($meeting->status === 'in_progress') bg-green-500 animate-pulse
                                @elseif($meeting->status === 'completed') bg-gray-500
                                @else bg-red-500 @endif"></span>
                                {{ $meeting->status_display }}
                            </span>

                            <div class="flex items-center gap-2">
                                @if ($meeting->status === 'scheduled')
                                    <button onclick="startMeeting({{ $meeting->id }})"
                                        class="text-sm px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                                        Iniciar
                                    </button>
                                @elseif ($meeting->status === 'in_progress')
                                    <button onclick="endMeeting({{ $meeting->id }})"
                                        class="text-sm px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                                        Encerrar
                                    </button>
                                @endif

                                <a href="{{ route('admin.Diretoria.meetings.show', $meeting) }}"
                                    class="text-sm px-3 py-1.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="text-center py-16 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div
                        class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="calendar" class="w-10 h-10 text-blue-500 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhuma reunião encontrada</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        @if (request()->hasAny(['status', 'type', 'date_from']))
                            Não encontramos resultados para sua busca. Tente remover os filtros.
                        @else
                            Agende reuniões ordinárias ou extraordinárias para manter a diretoria organizada.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if (request()->hasAny(['status', 'type', 'date_from']))
                            <a href="{{ route('admin.Diretoria.meetings.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Limpar Filtros
                            </a>
                        @else
                            <a href="{{ route('admin.Diretoria.meetings.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <x-icon name="plus" class="w-5 h-5 mr-2" />
                                Agendar Primeira Reunião
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($meetings->hasPages())
            <div
                class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-100 dark:border-gray-700 border-dashed rounded-b-2xl">
                {{ $meetings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script>
        function startMeeting(meetingId) {
            if (confirm('Tem certeza que deseja iniciar esta reunião? O status mudará para "Em Andamento".')) {
                fetch(`{{ url('admin/conselho/reunioes') }}/${meetingId}/iniciar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao iniciar reunião: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao iniciar reunião. Verifique sua conexão.');
                        console.error(error);
                    });
            }
        }

        function endMeeting(meetingId) {
            // Simple prompt for now, can be improved to a modal later
            const minutes = prompt('Deseja adicionar um resumo ou ata rápida para o encerramento? (Opcional)');
            if (minutes !== null) {
                fetch(`{{ url('admin/conselho/reunioes') }}/${meetingId}/encerrar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            minutes: minutes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao encerrar reunião: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao encerrar reunião. Verifique sua conexão.');
                        console.error(error);
                    });
            }
        }
    </script>
@endsection
