@extends('admin::components.layouts.master')

@section('title', 'Pedidos de Oração | Ação Social')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-check" /> {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pedidos de Oração</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie os pedidos enviados pela comunidade e beneficiários.</p>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300 rounded-xl font-medium">
                <x-icon name="clock" /> {{ $counts['pending'] }} pendentes
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl font-medium">
                <x-icon name="hands-praying" /> {{ $counts['prayed'] }} orados
            </span>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
        @foreach(['all' => 'Todos', 'pending' => 'Aguardando', 'prayed' => 'Orados', 'archived' => 'Arquivados'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
               class="px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-colors
                   {{ $status === $key
                       ? 'border-teal-500 text-teal-600 dark:text-teal-400'
                       : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
               {{ $label }}
               @if($key === 'pending' && $counts['pending'] > 0)
                   <span class="ml-1 text-xs font-bold bg-yellow-500 text-white rounded-full px-1.5 py-0.5">{{ $counts['pending'] }}</span>
               @endif
            </a>
        @endforeach
    </div>

    @if($requests->isEmpty())
        <div class="py-20 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <x-icon name="hands-praying" class="text-5xl text-teal-300 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nenhum pedido {{ $status !== 'all' ? 'nesta categoria' : 'registrado' }}</h3>
            <p class="text-gray-500 mt-1">Os membros podem enviar pedidos pelo painel do membro.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($requests as $prayer)
                @php
                    $badgeColors = [
                        'pending'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
                        'prayed'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800',
                        'archived' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-600',
                    ];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-5 flex gap-4 hover:shadow-md transition-shadow">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg flex-shrink-0">
                        <x-icon name="hands-praying" />
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-bold text-gray-900 dark:text-white text-sm">
                                {{ $prayer->is_anonymous ? '🕵️ Anônimo' : $prayer->name }}
                            </span>
                            <span class="text-xs border rounded-full px-2 py-0.5 font-medium {{ $badgeColors[$prayer->status] }}">
                                {{ $prayer->status_label }}
                            </span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $prayer->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $prayer->request }}</p>
                        @if($prayer->prayed_at)
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                                <x-icon name="check" /> Orado em {{ $prayer->prayed_at->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-1 flex-shrink-0">
                        @if($prayer->status === 'pending')
                            <form action="{{ route('socialaction.admin.prayer.prayed', $prayer->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors w-full">
                                    🙏 Marcar Orado
                                </button>
                            </form>
                        @endif
                        @if($prayer->status !== 'archived')
                            <form action="{{ route('socialaction.admin.prayer.archive', $prayer->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors w-full">
                                    Arquivar
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('socialaction.admin.prayer.destroy', $prayer->id) }}" method="POST" onsubmit="return confirm('Remover permanentemente?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg font-medium transition-colors w-full">
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $requests->links() }}</div>
    @endif
@endsection
