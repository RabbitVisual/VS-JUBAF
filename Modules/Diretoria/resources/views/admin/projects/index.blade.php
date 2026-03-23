@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" style="duotone" class="w-5 h-5 shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" style="duotone" class="w-5 h-5 shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <!-- Breadcrumb + Header -->
        <div class="flex flex-col gap-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('admin.Diretoria.index') }}"
                    class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors flex items-center gap-1">
                    <x-icon name="users-rectangle" style="duotone" class="w-4 h-4" />
                    {{ __('Diretoria::messages.diretoria_title') }}
                </a>
                <x-icon name="chevron-right" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                <span class="text-gray-900 dark:text-white font-bold flex items-center gap-1.5">
                    <x-icon name="diagram-project" style="duotone" class="w-4 h-4 text-blue-500" />
                    {{ __('Diretoria::messages.projects') }}
                </span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Projetos & Propostas</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Analise e aprove projetos submetidos pelos
                        ministérios.</p>
                </div>
                <a href="{{ route('admin.Diretoria.projects.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30">
                    <x-icon name="plus" style="duotone" class="w-5 h-5 shrink-0" />
                    Nova Proposta
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <form action="{{ route('admin.Diretoria.projects.index') }}" method="GET"
                class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="flex-1 md:max-w-xs">
                    <label
                        class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" onchange="this.form.submit()"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl py-2.5 px-4 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submetidos
                        </option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Em Revisão
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovados</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitados
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluídos
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados
                        </option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.Diretoria.projects.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        <x-icon name="arrow-rotate-left" style="duotone" class="w-4 h-4" />
                        {{ __('Diretoria::messages.clear_filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Projects Grid -->
        @if ($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col hover:border-blue-200 dark:hover:border-blue-800 transition-all overflow-hidden group">
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-start gap-3 mb-3">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize shrink-0
                                    @if ($project->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($project->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @elseif($project->status == 'under_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @elseif($project->status == 'completed') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300
                                    @elseif($project->status == 'submitted') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    @if ($project->status == 'approved')
                                        <x-icon name="check" style="duotone" class="w-3 h-3" />
                                    @elseif($project->status == 'rejected')
                                        <x-icon name="xmark" style="duotone" class="w-3 h-3" />
                                    @elseif($project->status == 'under_review')
                                        <x-icon name="clock" style="duotone" class="w-3 h-3" />
                                    @endif
                                    {{ match ($project->status) {
                                        'submitted' => 'Submetido',
                                        'under_review' => 'Em Análise',
                                        'approved' => 'Aprovado',
                                        'rejected' => 'Rejeitado',
                                        'draft' => 'Rascunho',
                                        'completed' => 'Concluído',
                                        'cancelled' => 'Cancelado',
                                        default => $project->status,
                                    } }}
                                </span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <x-icon name="calendar" style="duotone" class="w-3.5 h-3.5" />
                                    {{ $project->created_at->format('d/m/Y') }}
                                </span>
                            </div>

                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $project->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-4 flex-1">
                                {{ $project->description }}</p>

                            <div class="space-y-2 mt-auto">
                                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    @if ($project->proposer)
                                        @if ($project->proposer->photo ?? null)
                                            <img src="{{ asset('storage/' . $project->proposer->photo) }}" alt=""
                                                class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                                {{ strtoupper(substr($project->proposer->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span
                                            class="font-medium text-gray-700 dark:text-gray-300">{{ $project->proposer->name }}</span>
                                    @else
                                        <x-icon name="user" style="duotone" class="w-4 h-4 text-gray-400" />
                                        <span>Usuário</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($project->ministry)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            <x-icon name="church" style="duotone" class="w-3 h-3" />
                                            {{ $project->ministry->name }}
                                        </span>
                                    @elseif($project->department)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $project->department }}
                                        </span>
                                    @endif
                                    @if ($project->estimated_cost && (float) $project->estimated_cost > 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                            <x-icon name="money-bill-wave" style="duotone"
                                                class="w-3.5 h-3.5 text-green-600 dark:text-green-400" />
                                            R$ {{ number_format((float) $project->estimated_cost, 2, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                {{ $project->ministry?->name ?? ($project->department ?? 'Geral') }}
                            </span>
                            <a href="{{ route('admin.Diretoria.projects.show', $project) }}"
                                class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                {{ __('Diretoria::messages.view') }}
                                <x-icon name="arrow-right" style="duotone" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($projects->hasPages())
                <div class="pt-2">
                    {{ $projects->links() }}
                </div>
            @endif
        @else
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 md:p-16 text-center">
                <div
                    class="w-20 h-20 rounded-3xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mx-auto mb-5">
                    <x-icon name="diagram-project" style="duotone" class="w-10 h-10 text-blue-500 dark:text-blue-400" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum projeto encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Nenhum projeto ou proposta foi encontrado
                    com os filtros selecionados. Crie uma nova proposta ou limpe os filtros.</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('admin.Diretoria.projects.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30">
                        <x-icon name="plus" style="duotone" class="w-5 h-5 shrink-0" />
                        Nova Proposta
                    </a>
                    <a href="{{ route('admin.Diretoria.projects.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                        <x-icon name="arrow-rotate-left" style="duotone" class="w-5 h-5 shrink-0" />
                        Limpar Filtros
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
