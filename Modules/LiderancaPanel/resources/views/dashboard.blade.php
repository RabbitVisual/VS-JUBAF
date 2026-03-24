@extends('liderancapanel::components.layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('title', 'Dashboard Liderança')

@section('content')
    <div class="space-y-8 pb-12">
        {{-- Hero --}}
        @php
            $hour = (int) date('H');
            $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
        @endphp
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-indigo-800 via-indigo-900 to-indigo-800 text-white shadow-xl border border-indigo-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-indigo-500/20 to-transparent"></div>
            <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="px-3 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-200 text-xs font-bold uppercase tracking-wider">
                            Gabinete de Liderança JUBAF
                        </span>
                        <span class="px-3 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-bold uppercase tracking-wider">
                            Painel Conjunto
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight mb-2">
                        {{ $greeting }}, {{ $user->first_name ?? ($user->name ?? 'Liderança') }}!
                    </h1>
                    <p class="text-indigo-200 text-lg max-w-xl">
                        Representando a {{ $user->igreja ? $user->igreja->nome : 'Igreja Não Informada' }}. <br/>
                        Acompanhe o engajamento da juventude da sua congregação nas atividades oficiais da JUBAF.
                    </p>
                </div>
                <div class="hidden md:block shrink-0">
                    <div class="w-28 h-28 rounded-full bg-linear-to-tr from-indigo-500 to-indigo-600 p-1 shadow-2xl shadow-indigo-500/30 flex items-center justify-center border-4 border-slate-800">
                        @if ($user->photo)
                            <img src="{{ Storage::url($user->photo) }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">{{ strtoupper(mb_substr($user->first_name ?? ($user->name ?? 'L'), 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Nova Caravana (Destaque) --}}
        <div class="bg-gradient-to-r from-orange-400 to-red-500 rounded-3xl p-8 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-6 z-10">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/30 shrink-0">
                    <x-icon name="bus-school" class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight mb-1 drop-shadow-sm">Inscrever Nova Caravana</h2>
                    <p class="text-orange-100 max-w-xl font-medium drop-shadow-sm">Simplifique a gestão: inscreva vários jovens da sua igreja de uma só vez nos próximos eventos da Associação JUBAF e garanta os melhores lotes.</p>
                </div>
            </div>
            <a href="{{ route('lideranca.eventos.index') }}" class="z-10 shrink-0 px-6 py-4 rounded-xl bg-white text-orange-600 font-bold hover:bg-orange-50 hover:scale-105 transition-all shadow-md mt-4 md:mt-0">
                Ver Eventos Disponíveis &rarr;
            </a>
        </div>

        {{-- Quick Stats & Acesso Rápido JUBAF --}}
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Eventos -->
            <a href="{{ route('lideranca.eventos.index') }}" class="group block rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-indigo-500/40 transition-all duration-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <x-icon name="ticket" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Minhas Inscrições</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $inscricoesCount }}</p>
                    </div>
                </div>
            </a>

            <!-- Ovelhas -->
            <a href="{{ route('lideranca.rebanho.index') }}" class="group block rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-amber-500/40 transition-all duration-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <x-icon name="users" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nosso Grupo</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_ovelhas'] ?? 0 }}</p>
                    </div>
                </div>
            </a>

            <!-- Mural da Associação -->
            <div class="xl:col-span-2 rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between overflow-hidden relative">
                <div class="absolute right-0 bottom-0 opacity-5 pointer-events-none">
                    <x-icon name="newspaper" class="w-32 h-32 text-blue-900 translate-x-4 translate-y-4" />
                </div>
                <div class="flex items-center gap-2 mb-3 z-10">
                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-slate-400">Mural Oficial da Associação</h3>
                </div>
                <div class="space-y-3 z-10 flex-1">
                    @forelse($avisosRecentes as $aviso)
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-900 dark:text-white font-medium line-clamp-1 truncate w-3/4">{{ $aviso->titulo }}</p>
                            <span class="text-xs text-gray-400">{{ $aviso->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Nenhum aviso no momento.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Materiais de Apoio (Sermões) --}}
            <div class="xl:col-span-2 rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="books" class="w-5 h-5 text-indigo-500" />
                        Materiais de Apoio para sua Igreja
                    </h2>
                    <a href="{{ route('lideranca.sermoes.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Ver Catálogo</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-96 overflow-y-auto p-4 space-y-4">
                    @forelse($sermoesRecentes as $sermon)
                        <div class="flex gap-4 group">
                            <div class="w-24 h-16 shrink-0 rounded-lg bg-gray-200 dark:bg-slate-700 overflow-hidden relative">
                                @if($sermon->cover_image)
                                    <img src="{{ Storage::url($sermon->cover_image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900">
                                        <x-icon name="book-bible" class="w-6 h-6 text-indigo-500 opacity-50" />
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white leading-tight mb-1 group-hover:text-indigo-500 transition-colors">
                                    {{ $sermon->title }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($sermon->sermon_date)->format('d/m/Y') }} &bull; {{ $sermon->preacher_name }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <x-icon name="books" class="w-12 h-12 text-gray-300 mx-auto mb-2" />
                            <p class="text-gray-500 text-sm">Ainda não há materiais de estudo publicados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Desafio Bíblico Sidebar --}}
            <div class="rounded-2xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm p-6">
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4 text-amber-600 dark:text-amber-400">
                    <x-icon name="fire-flame-curved" class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Desafio Bíblico</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Motive os jovens de sua congregação a participarem do plano atual.</p>
                
                @if($desafioBiblico)
                    <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-900/10">
                        <p class="font-bold text-gray-900 dark:text-white mb-1">{{ $desafioBiblico->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ Str::limit(strip_tags($desafioBiblico->description), 80) }}</p>
                        <a href="{{ route('lideranca.bible.plans.index' ?? '#') }}" class="inline-flex w-full justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">Acessar Desafio</a>
                    </div>
                @else
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-900">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Nenhum plano de leitura comunitário ativo no momento.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
