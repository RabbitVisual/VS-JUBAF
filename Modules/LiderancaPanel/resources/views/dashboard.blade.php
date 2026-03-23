@extends('liderancapanel::components.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        {{-- Hero --}}
        @php
            $hour = (int) date('H');
            $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
        @endphp
        <div
            class="relative overflow-hidden rounded-3xl bg-linear-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span
                            class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200 text-xs font-bold uppercase tracking-wider">Gabinete
                            de Liderança</span>
                        <span
                            class="px-3 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-bold uppercase tracking-wider">Cuidado
                            & Alimentação</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight mb-2">
                        {{ $greeting }}, {{ auth()->user()->first_name ?? (auth()->user()->name ?? 'lideranca') }}!
                    </h1>
                    <p class="text-slate-300 text-lg max-w-xl">
                        Bem-vindo ao seu gabinete. Aqui você acompanha o rebanho, pendências ministeriais e a saúde da igreja.
                    </p>
                </div>
                <div class="hidden md:block shrink-0">
                    <div
                        class="w-28 h-28 rounded-full bg-linear-to-tr from-amber-500 to-amber-600 p-1 shadow-2xl shadow-amber-500/30 flex items-center justify-center border-4 border-slate-800">
                        @if (auth()->user()->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->photo) }}" alt=""
                                class="w-full h-full rounded-full object-cover">
                        @else
                            <span
                                class="text-3xl font-black text-white">{{ strtoupper(mb_substr(auth()->user()->first_name ?? (auth()->user()->name ?? 'P'), 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Widgets --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <a href="{{ route('lideranca.rebanho.index') }}"
                class="group block rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-amber-500/40 transition-all duration-200 p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <x-icon name="users-rays" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Ovelhas</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_ovelhas'] ?? 0 }}</p>
                    </div>
                </div>
                <p
                    class="mt-3 text-xs text-gray-500 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400">
                    Ver lista de membros</p>
            </a>

            @if (Route::has('lideranca.oracao.index'))
                <a href="{{ route('lideranca.oracao.index') }}"
                    class="group block rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-amber-500/40 transition-all duration-200 p-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <x-icon name="clipboard-check" class="w-6 h-6 text-teal-600 dark:text-teal-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendências Pastorais</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pedidos_oracao'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 group-hover:text-teal-600">Ver pendências
                    </p>
                </a>
            @endif

            @if (Route::has('lideranca.sermoes.index'))
                <a href="{{ route('lideranca.sermoes.index') }}"
                    class="group block rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-amber-500/40 transition-all duration-200 p-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <x-icon name="book-bible" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Próximos Sermões</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $stats['proximos_sermoes'] ?? 0 }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 group-hover:text-indigo-600">Estúdio da Palavra
                    </p>
                </a>
            @endif

            <div class="rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                        <x-icon name="cake-candles" class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aniversariantes da Semana</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $aniversariantes->count() }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Contato pastoral da semana</p>
            </div>
        </div>

        {{-- Pendências pastorais --}}
        @if (isset($pedidosOracaoPendentes) && $pedidosOracaoPendentes->isNotEmpty())
            <div
                class="rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="clipboard-check" class="w-5 h-5 text-teal-500" />
                        Pendências pastorais
                    </h2>
                    <a href="{{ route('lideranca.oracao.index') }}"
                        class="text-sm font-medium text-teal-600 dark:text-teal-400 hover:underline">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-96 overflow-y-auto">
                    @foreach ($pedidosOracaoPendentes as $req)
                        <div
                            class="px-6 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $req->title ?? 'Solicitação pastoral' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $req->is_anonymous ? 'Anônimo' : $req->user->name ?? '—' }} ·
                                    {{ $req->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <form action="{{ route('lideranca.oracao.marcar-orado', $req) }}" method="POST"
                                class="shrink-0">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">Concluir</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Aniversariantes + Elias --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div
                class="xl:col-span-2 rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="cake-candles" class="w-5 h-5 text-rose-500" />
                        Aniversariantes desta semana
                    </h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-80 overflow-y-auto">
                    @forelse($aniversariantes as $membro)
                        <div
                            class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-700 dark:text-amber-300 font-bold text-sm">
                                    @if ($membro->data_nascimento && ($d = $membro->data_nascimento))
                                        {{ $d->format('d') }}
                                    @else
                                        ?
                                    @endif
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $membro->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if ($membro->data_nascimento)
                                            {{ $membro->data_nascimento->format('d/m') }}
                                        @endif
                                        @if ($membro->email)
                                            · {{ $membro->email }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if (Route::has('lideranca.rebanho.show'))
                                <a href="{{ route('lideranca.rebanho.show', $membro) }}"
                                    class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">Ver
                                    perfil</a>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <x-icon name="cake-candles" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                            <p>Nenhum aniversariante nesta semana.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Elias (Conselheiro Estratégico) --}}
            <div
                class="rounded-2xl bg-linear-to-br from-slate-800 to-slate-900 border border-amber-900/30 shadow-sm p-6 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2 mb-3">
                    <x-icon name="book-bible" class="w-5 h-5 text-amber-400" />
                    Elias · Conselheiro
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed mb-4">
                    Aqui você verá insights sobre a saúde da igreja: frequência na EBD, alertas em alta e
                    sugestões de cuidado pastoral.
                </p>
                @if (isset($eliasInsight) && !empty($eliasInsight['content']))
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-100 text-sm">
                        {{ $eliasInsight['content'] }}
                    </div>
                @else
                    <p class="text-slate-400 text-sm italic">Use o painel para acompanhar métricas e tomar decisões com base
                        nos dados do rebanho.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
