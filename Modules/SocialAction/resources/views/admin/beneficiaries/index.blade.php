@extends('admin::components.layouts.master')

@section('title', 'Beneficiários | Ação Social')

@section('content')
    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-check" /> {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Beneficiários</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gestão de famílias e pessoas assistidas.</p>
        </div>
        <a href="{{ route('socialaction.admin.beneficiaries.create') }}" class="inline-flex items-center px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all hover:scale-105 active:scale-95 gap-2">
            <x-icon name="user-plus" />
            Novo Beneficiário
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        @php
            $kpiItems = [
                ['label' => 'Total',    'value' => $stats['total'],    'icon' => 'users',  'color' => 'blue',   'status' => 'all'],
                ['label' => 'Ativos',   'value' => $stats['active'],   'icon' => 'heart',  'color' => 'green',  'status' => 'active'],
                ['label' => 'Inativos', 'value' => $stats['inactive'], 'icon' => 'user-slash', 'color' => 'gray', 'status' => 'inactive'],
                ['label' => 'Atenção',  'value' => $stats['flagged'],  'icon' => 'triangle-exclamation', 'color' => 'red', 'status' => 'flagged'],
            ];
        @endphp
        @foreach($kpiItems as $item)
            <a href="{{ route('socialaction.admin.beneficiaries.index', ['status' => $item['status']]) }}"
               class="bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 hover:border-{{ $item['color'] }}-300 transition-colors">
                <div class="p-3 bg-{{ $item['color'] }}-100 dark:bg-{{ $item['color'] }}-900/30 text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400 rounded-xl">
                    <x-icon :name="$item['icon']" class="text-xl" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $item['label'] }}</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $item['value'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Filters and Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-8">
        <form action="{{ route('socialaction.admin.beneficiaries.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <x-icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou cidade..."
                       class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border-transparent focus:bg-white dark:focus:bg-gray-700 focus:ring-rose-500 focus:border-rose-500 rounded-xl transition-all">
            </div>
            <div class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="bg-gray-50 dark:bg-gray-700/50 border-transparent focus:ring-rose-500 focus:border-rose-500 rounded-xl py-2.5 px-4 text-sm font-medium">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                    <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Pendente/Atenção</option>
                </select>
                <button type="submit" class="px-6 bg-gray-900 dark:bg-gray-700 text-white rounded-xl font-bold hover:bg-gray-800 transition-colors">Filtrar</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('socialaction.admin.beneficiaries.index') }}" class="p-2.5 text-gray-400 hover:text-rose-500 transition-colors" title="Limpar Filtros">
                        <x-icon name="filter-circle-xmark" class="text-xl" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Content Grid --}}
    @if($beneficiaries->isEmpty())
        <div class="py-24 text-center bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-100 dark:border-gray-700">
            <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-icon name="users-slash" class="text-4xl" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum beneficiário encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Não encontramos registros para os critérios informados. Comece cadastrando uma nova família.</p>
            <a href="{{ route('socialaction.admin.beneficiaries.create') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 text-white rounded-xl font-bold hover:bg-rose-700 transition-all">
                Cadastrar Família
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($beneficiaries as $beneficiary)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-rose-200 dark:hover:border-rose-900/50 transition-all duration-300 flex flex-col relative">
                    {{-- Status Badge --}}
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{
                            match($beneficiary->status) {
                                'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'inactive' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                'flagged' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-blue-100 text-blue-700',
                            }
                        }}">
                            {{ $beneficiary->status_label }}
                        </span>
                    </div>

                    {{-- Avatar & Identity --}}
                    <div class="flex flex-col items-center text-center mb-6 pt-2">
                        <div class="w-20 h-20 rounded-3xl bg-linear-to-br from-rose-500 to-orange-400 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-rose-500/20 mb-4 group-hover:scale-105 transition-transform">
                            {{ substr($beneficiary->full_name, 0, 1) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight line-clamp-1 mb-1">{{ $beneficiary->full_name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5 justify-center">
                            <x-icon name="users" class="text-xs" />
                            {{ $beneficiary->family_size }} {{ $beneficiary->family_size > 1 ? 'pessoas' : 'pessoa' }}
                        </p>
                    </div>

                    {{-- Info List --}}
                    <div class="space-y-3 flex-1">
                        <div class="flex items-start gap-3 text-xs">
                            <x-icon name="map-location-dot" class="mt-0.5 text-gray-400 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-300 line-clamp-2">{{ $beneficiary->full_address ?: 'Endereço não informado' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <x-icon name="phone" class="text-gray-400 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-300">{{ $beneficiary->phone ?: 'Sem telefone' }}</span>
                        </div>
                        @if($beneficiary->monthly_income)
                            <div class="flex items-center gap-3 text-xs">
                                <x-icon name="receipt" class="text-gray-400 shrink-0" />
                                <span class="text-gray-600 dark:text-gray-300">Renda: R$ {{ number_format($beneficiary->monthly_income, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Needs Badges --}}
                        @if($beneficiary->needs)
                            <div class="flex flex-wrap gap-1 mt-4">
                                @foreach($beneficiary->needs as $need)
                                    @php $labels = \Modules\SocialAction\App\Models\SocialBeneficiary::needsLabels(); @endphp
                                    <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-md text-[10px] font-bold">
                                        {{ $labels[$need] ?? $need }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-700/50 grid grid-cols-3 gap-2">
                        <a href="{{ route('socialaction.admin.beneficiaries.show', $beneficiary->id) }}"
                           class="flex flex-col items-center justify-center p-2 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-rose-600 transition-colors">
                            <x-icon name="eye" class="text-sm mb-1" />
                            <span class="text-[9px] font-bold uppercase">Ver</span>
                        </a>
                        <a href="{{ route('socialaction.admin.beneficiaries.edit', $beneficiary->id) }}"
                           class="flex flex-col items-center justify-center p-2 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-blue-600 transition-colors">
                            <x-icon name="pen-to-square" class="text-sm mb-1" />
                            <span class="text-[9px] font-bold uppercase">Editar</span>
                        </a>
                        <form action="{{ route('socialaction.admin.beneficiaries.destroy', $beneficiary->id) }}" method="POST" onsubmit="return confirm('Excluir este beneficiário permanentemente?')" class="h-full">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex flex-col items-center justify-center p-2 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-500 hover:text-red-600 transition-colors h-full">
                                <x-icon name="trash" class="text-sm mb-1" />
                                <span class="text-[9px] font-bold uppercase">Excluir</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $beneficiaries->links() }}
        </div>
    @endif
@endsection
