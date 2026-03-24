@extends('memberpanel::components.layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('page-title', 'Dashboard JUBAF')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <!-- Dashboard Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Painel da Associação</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Bem-vindo ao centro do jovem batista.</p>
                </div>
            </div>

            <!-- Hero Section: Welcome & Context (JUBAF) -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                </div>

                <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-10 z-10">
                    <div class="relative group shrink-0">
                        <div class="w-28 h-28 rounded-full p-[3px] bg-linear-to-br from-blue-500 via-purple-500 to-indigo-500 shadow-xl shadow-blue-500/20">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-white dark:border-slate-900 bg-gray-100 dark:bg-slate-800 relative z-10">
                                @if ($user->photo)
                                    <img src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl font-black text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-slate-900">
                                        {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 mb-2">
                            <x-icon name="church" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">JUBAF Online</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                            Olá, {{ $user->first_name ?? explode(' ', $user->name)[0] }}! <br/> 
                            <span class="text-2xl text-indigo-500 block mt-2">Representando a {{ $user->igreja ? $user->igreja->nome : 'Igreja Não Informada' }}</span>
                        </h1>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg leading-relaxed">
                            É uma alegria ter você aqui representando sua congregação local na Associação.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Cards (Meus Eventos, Desafio Biblico, Mural) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Meus Eventos -->
                <a href="{{ route('memberpanel.events.index') }}" class="group block relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <x-icon name="ticket" class="w-24 h-24 text-indigo-500 -mr-6 -mt-6" />
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <x-icon name="calendar-check" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">Meus Eventos</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 font-medium mb-3">Você tem {{ $inscricoesCount }} inscrições válidas.</p>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider group-hover:underline">Acessar Eventos &rarr;</span>
                    </div>
                </a>

                <!-- Desafio Bíblico -->
                <a href="{{ route('memberpanel.bible.plans.index') }}" class="group block relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <x-icon name="book-bible" class="w-24 h-24 text-amber-500 -mr-6 -mt-6" />
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                            <x-icon name="fire" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">Desafio Bíblico</h3>
                        @if($desafioBiblico)
                            <p class="text-sm text-gray-500 dark:text-slate-400 font-medium mb-3 line-clamp-1">Ativo: {{ $desafioBiblico->title }}</p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-slate-400 font-medium mb-3">Nenhum desafio no momento.</p>
                        @endif
                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider group-hover:underline">Abrir Planos &rarr;</span>
                    </div>
                </a>

                <!-- Mural Oficial -->
                <div class="group relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <x-icon name="newspaper" class="w-24 h-24 text-blue-500 -mr-6 -mt-6" />
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4">
                            <x-icon name="bullhorn" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-4">Mural Oficial</h3>
                        
                        <div class="space-y-3">
                            @forelse($avisosRecentes as $aviso)
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></div>
                                    <p class="text-sm text-gray-700 dark:text-slate-300 font-medium line-clamp-1">{{ $aviso->titulo }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Nenhum aviso no momento.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile & Gamification Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gamification -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="trophy" class="w-5 h-5 text-amber-500" /> Meu Progresso Espiritual
                    </h3>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">XP Atual: {{ $stats['points'] }}</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $stats['level']['name'] }}</p>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-3 mb-2 overflow-hidden">
                        <div class="bg-amber-500 h-3 rounded-full" style="width: {{ $stats['progress_percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Complete leitura bíblica e eventos para subir de nível.</p>
                </div>

                <!-- Perfil Completion -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="id-card" class="w-5 h-5 text-emerald-500" /> Completude do Perfil
                    </h3>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dados do Cadastro</p>
                        <p class="text-sm font-bold text-emerald-500">{{ $stats['profile_completion'] }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-3 mb-4 overflow-hidden">
                        <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ $stats['profile_completion'] }}%"></div>
                    </div>
                    @if($stats['profile_completion'] < 100)
                        <a href="{{ route('memberpanel.profile.edit') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Completar Perfil &rarr;</a>
                    @else
                        <p class="text-xs text-emerald-500 font-medium">Perfil preenchido perfeitamente!</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
