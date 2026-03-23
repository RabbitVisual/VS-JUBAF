@extends('memberpanel::components.layouts.master')

@section('title', 'Painel de Intercessão')
@php use Illuminate\Support\Facades\Storage; @endphp


@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-blue-600 dark:text-blue-400">Intercessão</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">
                Olá, {{ explode(' ', auth()->user()->name)[0] }}!
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">"Orai uns pelos outros, para que sareis." - Tiago 5:16</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl flex items-center gap-2">
                 <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
                 <span class="text-xs font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">Painel Ativo</span>
             </div>
             <a href="{{ route('member.intercessor.room.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Mural de Orações
             </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Urgent -->
        <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 hover:-translate-y-1 transition-all hover:shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="exclamation" style="duotone" class="w-7 h-7 text-red-600 dark:text-red-400" />
                </div>
                <span class="text-[10px] font-black text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/40 px-3 py-1 rounded-full uppercase tracking-widest">Urgentes</span>
            </div>
            <div class="flex flex-col">
                <span class="text-4xl font-black text-gray-900 dark:text-white mb-1">{{ $urgentRequestsCount }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Precisam de Atenção</span>
            </div>
        </div>

        <!-- Pending (General) -->
        <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 hover:-translate-y-1 transition-all hover:shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="collection" style="duotone" class="w-7 h-7 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/40 px-3 py-1 rounded-full uppercase tracking-widest">Mural</span>
            </div>
            <div class="flex flex-col">
                <span class="text-4xl font-black text-gray-900 dark:text-white mb-1">{{ $pendingRequestsCount }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total de Pedidos</span>
            </div>
        </div>

        <!-- My Commitments -->
        <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 hover:-translate-y-1 transition-all hover:shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="hand" style="duotone" class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/40 px-3 py-1 rounded-full uppercase tracking-widest">Compromissos</span>
            </div>
            <div class="flex flex-col">
                <span class="text-4xl font-black text-gray-900 dark:text-white mb-1">{{ $myCommitmentsCount }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Meus Atendimentos</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Urgent & Feed -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Urgent Requests -->
            @if($urgentRequests->count() > 0)
            <section>
                <div class="flex items-center gap-2 mb-6">
                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 animate-pulse"></div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-wide">Atenção Prioritária</h3>
                </div>
                <div class="grid gap-4">
                    @foreach($urgentRequests as $request)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-l-8 border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all group" style="border-left-color: #ef4444;">
                             <div class="flex justify-between items-center gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-widest bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            {{ $request->category->name }}
                                        </span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $request->created_at->diffForHumans() }}</span>
                                    </div>

                                    <h4 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-3">
                                        <a href="{{ route('member.intercessor.room.show', $request) }}">
                                            {{ $request->title }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed mb-6 font-medium">{{ $request->description }}</p>

                                    <div class="flex items-center gap-3 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-t border-gray-50 dark:border-gray-700/50 pt-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                                <x-icon name="user" style="duotone" class="w-3 h-3" />
                                            </div>
                                            {{ $request->is_anonymous ? 'Anônimo' : $request->user->name }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('member.intercessor.room.show', $request) }}"
                                   class="w-12 h-12 flex items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-600 rounded-2xl group-hover:bg-red-600 group-hover:text-white transition-all shadow-sm">
                                    <x-icon name="arrow-right" style="duotone" class="w-5 h-5" />
                                </a>
                             </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Recent Feed -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
                        <x-icon name="clock" style="duotone" class="w-5 h-5 text-gray-400" />
                        Novos Pedidos
                    </h3>
                    <a href="{{ route('member.intercessor.room.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 uppercase tracking-wide">Ver Todos</a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentRequests as $request)
                        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-sm shadow-sm overflow-hidden">
                                        @if(!$request->is_anonymous && $request->user->photo)
                                            <img src="{{ Storage::url($request->user->photo) }}" alt="{{ $request->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($request->is_anonymous ? 'A' : $request->user->name, 0, 1) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-4">
                                        <div>
                                            <h5 class="text-base font-bold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                <a href="{{ route('member.intercessor.room.show', $request) }}">
                                                    {{ $request->title }}
                                                </a>
                                            </h5>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1 mb-2">{{ $request->description }}</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide whitespace-nowrap">{{ $request->created_at->diffForHumans() }}</span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                            {{ $request->category->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500">
                            <x-icon name="collection" style="duotone" class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                            <p class="font-bold text-gray-400">Nenhum pedido recente.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Right Column: My Commitments -->
        <div class="space-y-8">
            <section>
                <div class="flex items-center gap-2 mb-6">
                    <x-icon name="heart" style="duotone" class="w-5 h-5 text-purple-600" />
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-wide">Compromissos</h3>
                </div>

                @if($myCommitments->count() > 0)
                    <div class="space-y-4">
                        @foreach($myCommitments as $commitment)
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-purple-200 dark:hover:border-purple-900/50 transition-all group">
                                <div class="flex items-start justify-between mb-4">
                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full uppercase tracking-widest border border-emerald-100/50">Em Oração</span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $commitment->created_at->format('d/m') }}</span>
                                </div>

                                <h4 class="text-base font-black text-gray-900 dark:text-white mb-4 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors line-clamp-2 leading-tight">
                                    <a href="{{ route('member.intercessor.room.show', $commitment->request) }}">
                                        {{ $commitment->request->title }}
                                    </a>
                                </h4>

                                <div class="flex items-center gap-3 mb-6">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-black text-gray-500 dark:text-gray-400 overflow-hidden shadow-inner uppercase">
                                        @if(!$commitment->request->is_anonymous && $commitment->request->user->photo)
                                            <img src="{{ Storage::url($commitment->request->user->photo) }}" alt="{{ $commitment->request->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($commitment->request->is_anonymous ? 'A' : $commitment->request->user->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 truncate max-w-[150px] uppercase tracking-tighter">
                                        {{ $commitment->request->is_anonymous ? 'Anônimo' : $commitment->request->user->name }}
                                    </span>
                                </div>

                                <a href="{{ route('member.intercessor.room.show', $commitment->request) }}"
                                   class="block w-full text-center text-xs font-black text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30 bg-purple-50 dark:bg-purple-900/10 rounded-2xl py-3 hover:bg-purple-600 hover:text-white transition-all uppercase tracking-widest shadow-sm">
                                    Acessar Sala
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-3xl p-8 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm text-gray-400">
                             <x-icon name="hand" style="duotone" class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-4">Você ainda não assumiu compromissos de oração.</p>
                        <a href="{{ route('member.intercessor.room.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline uppercase tracking-wide">Explorar Mural</a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
