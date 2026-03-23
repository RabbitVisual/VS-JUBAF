@extends('admin::components.layouts.master')

@section('title', 'Perfil do Beneficiário | Ação Social')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('socialaction.admin.beneficiaries.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-rose-600 transition-colors border border-gray-100 dark:border-gray-700">
                <x-icon name="arrow-left" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Perfil do Beneficiário</h1>
                <p class="text-sm text-gray-500">Gestão detalhada e histórico de assistência.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('socialaction.admin.beneficiaries.edit', $beneficiary->id) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 transition-all gap-2">
                <x-icon name="pen-to-square" />
                Editar Dados
            </a>
            <a href="{{ route('socialaction.admin.assistance.create', ['beneficiary_id' => $beneficiary->id]) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-xl font-bold hover:bg-rose-700 shadow-lg shadow-rose-500/20 transition-all gap-2">
                <x-icon name="plus" />
                Registrar Assistência
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-20">
        {{-- Coluna Lateral: Dados e Status --}}
        <div class="space-y-6">
            {{-- Card Principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="h-24 bg-linear-to-r from-rose-500 to-orange-400"></div>
                <div class="px-6 pb-6">
                    <div class="relative flex justify-center -mt-12 mb-4">
                        <div class="w-24 h-24 rounded-3xl bg-white dark:bg-gray-800 p-1 shadow-xl">
                            <div class="w-full h-full rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-rose-500 text-4xl font-black">
                                {{ substr($beneficiary->full_name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $beneficiary->full_name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider mt-2 {{
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

                    <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 font-medium">Cadastrado em</span>
                            <span class="text-gray-900 dark:text-white font-bold">{{ $beneficiary->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 font-medium">Tamanho Família</span>
                            <span class="text-gray-900 dark:text-white font-bold">{{ $beneficiary->family_size }} pessoas</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 font-medium">Renda Mensal</span>
                            <span class="text-gray-900 dark:text-white font-bold">R$ {{ number_format($beneficiary->monthly_income, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contato e Endereço --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="map-location-dot" class="text-rose-500 text-sm" />
                    Localização & Contato
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 shrink-0">
                            <x-icon name="phone" class="text-xs" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-black text-gray-400">Telefone</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $beneficiary->phone ?: 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 shrink-0">
                            <x-icon name="location-dot" class="text-xs" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-black text-gray-400">Endereço</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $beneficiary->address ?: 'Não informado' }}</p>
                            <p class="text-xs text-gray-500">{{ $beneficiary->neighborhood }} - {{ $beneficiary->city }}/{{ $beneficiary->state }}</p>
                        </div>
                    </div>
                    @if($beneficiary->latitude && $beneficiary->longitude)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $beneficiary->latitude }},{{ $beneficiary->longitude }}" target="_blank" class="flex items-center justify-center w-full py-2.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold hover:bg-rose-100 transition-colors gap-2">
                            <x-icon name="map-pin" /> Ver no Google Maps
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna Principal: Necessidades, Notas e histórico --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Necessidades e Notas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Necessidades Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="list-check" class="text-rose-500 text-sm" />
                        Necessidades Identificadas
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @if($beneficiary->needs)
                            @foreach($beneficiary->needs as $need)
                                @php $labels = \Modules\SocialAction\App\Models\SocialBeneficiary::needsLabels(); @endphp
                                <span class="px-3 py-1.5 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 rounded-xl text-xs font-bold flex items-center gap-1.5 border border-rose-100 dark:border-rose-900/30">
                                    <x-icon name="circle" class="text-[6px]" />
                                    {{ $labels[$need] ?? $need }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-400 italic">Nenhuma necessidade específica marcada.</p>
                        @endif
                    </div>
                </div>

                {{-- Notas Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="comment-medical" class="text-rose-500 text-sm" />
                        Observações Pastorais
                    </h3>
                    @if($beneficiary->pastoral_notes)
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-600 italic">
                            "{{ $beneficiary->pastoral_notes }}"
                        </p>
                    @else
                        <p class="text-sm text-gray-400 italic">Sem notas registradas.</p>
                    @endif
                </div>
            </div>

            {{-- Histórico de Assistências --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="history" class="text-rose-500 text-sm" />
                        Histórico de Assistência
                    </h3>
                    <span class="text-xs font-bold bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg text-gray-500">{{ $assistanceCount }} registros</span>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($beneficiary->assistances as $assistance)
                        <div class="p-6 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 flex-shrink-0">
                                @if($assistance->type === 'financial')
                                    <x-icon name="dollar-sign" class="text-green-500" />
                                @elseif($assistance->type === 'kit')
                                    <x-icon name="box-open" class="text-purple-500" />
                                @else
                                    <x-icon name="utensils" class="text-blue-500" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $assistance->type === 'kit' ? $assistance->kit?->name : ($assistance->type === 'financial' ? 'Auxílio Financeiro' : ($assistance->pantryItem?->name ?? 'Item avulso')) }}
                                    </h4>
                                    <span class="text-xs text-gray-400 font-medium">{{ $assistance->registered_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($assistance->notes)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $assistance->notes }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        @if($assistance->type === 'financial')
                                            R$ {{ number_format($assistance->amount, 2, ',', '.') }}
                                        @else
                                            Qtde: {{ number_format($assistance->quantity, 0) }}
                                        @endif
                                    </span>
                                    @if($assistance->volunteer)
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <x-icon name="user-check" class="text-[10px]" />
                                            Por: {{ $assistance->volunteer->user?->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 text-sm italic">
                            <x-icon name="inbox" class="text-2xl block mb-2 opacity-20" />
                            Nenhuma assistência registrada para este beneficiário ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
