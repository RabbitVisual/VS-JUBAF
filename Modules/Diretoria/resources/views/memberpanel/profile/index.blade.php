@extends('memberpanel::components.layouts.master')

@section('title', 'Meu Perfil - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header / Cover -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-sm border border-gray-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
                <div class="h-32 bg-linear-to-r from-blue-600 to-indigo-600 dark:from-blue-900 dark:to-indigo-900 relative">
                    <div class="absolute inset-0 opacity-20">
                        <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                        </svg>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <div class="flex flex-col md:flex-row items-end -mt-12 mb-4 gap-6">
                        <!-- Avatar -->
                        <div class="flex-shrink-0 relative">
                            <div
                                class="h-24 w-24 md:h-32 md:w-32 rounded-full ring-4 ring-white dark:ring-slate-900 bg-white dark:bg-slate-800 flex items-center justify-center overflow-hidden shadow-lg border border-gray-100 dark:border-slate-700">
                                @if ($member->user->photo)
                                    <img class="h-full w-full object-cover"
                                        src="{{ asset('storage/' . $member->user->photo) }}"
                                        alt="{{ $member->user->name }}">
                                @else
                                    <div
                                        class="h-full w-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-3xl font-bold text-gray-400 dark:text-slate-500">
                                        {{ substr($member->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 pb-2">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ $member->user->name }}
                                    </h1>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30">
                                            {{ $member->role_display }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-slate-400">
                                            Membro desde
                                            {{ $member->term_start ? $member->term_start->format('Y') : 'Data n/a' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border border-current {{ $member->isActive() ? 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:text-emerald-400 dark:border-emerald-500/30 dark:bg-emerald-500/10' : 'text-red-600 bg-red-50 border-red-200 dark:text-red-400 dark:border-red-500/30 dark:bg-red-500/10' }}">
                                        <span
                                            class="w-2 h-2 rounded-full {{ $member->isActive() ? 'bg-emerald-500' : 'bg-red-500' }} mr-2"></span>
                                        {{ $member->isActive() ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio / Responsibilities -->
                    @if ($member->responsibilities)
                        <div class="mt-4 pt-6 border-t border-gray-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Responsabilidades</h3>
                            <p class="text-gray-600 dark:text-slate-400 text-sm leading-relaxed">
                                {{ $member->responsibilities }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sidebar: Stats & Edit -->
                <div class="space-y-6">
                    <!-- Stats -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-sm border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase mb-4 tracking-wider">
                            Estatísticas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 p-4 rounded-xl text-center">
                                <span
                                    class="block text-2xl font-black text-blue-600 dark:text-blue-400">{{ $stats['total_meetings'] }}</span>
                                <span class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase">Reuniões</span>
                            </div>
                            <div
                                class="bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 p-4 rounded-xl text-center">
                                <span
                                    class="block text-2xl font-black text-purple-600 dark:text-purple-400">{{ $stats['total_votes'] }}</span>
                                <span class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase">Votos</span>
                            </div>
                            <div
                                class="bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 p-4 rounded-xl text-center">
                                <span
                                    class="block text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['approved_agendas'] }}</span>
                                <span class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase">Pautas
                                    Aprov.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-sm border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200"
                        x-data="{ editing: false }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Dados
                                Pessoais</h3>
                            <button @click="editing = !editing"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-bold transition-colors">
                                <span x-show="!editing">Editar</span>
                                <span x-show="editing">Cancelar</span>
                            </button>
                        </div>

                        <!-- Display Mode -->
                        <div x-show="!editing" class="space-y-4">
                            <div>
                                <span class="block text-xs text-gray-500 dark:text-slate-500">Email</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $member->user->email }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 dark:text-slate-500">Cargo na Diretoria</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $member->diretoria_position ?? 'Membro' }}</span>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <form x-show="editing" action="{{ route('memberpanel.Diretoria.profile.update') }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Email</label>
                                <input type="email" value="{{ $member->user->email }}" disabled
                                    class="w-full bg-gray-100 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-500 cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-slate-600 mt-1">O email não pode ser alterado por
                                    aqui.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Resumo /
                                    Responsabilidades</label>
                                <textarea name="responsibilities" rows="3"
                                    class="w-full bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-slate-200 focus:ring-blue-500 focus:border-blue-500">{{ $member->responsibilities }}</textarea>
                            </div>
                            <button type="submit"
                                class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm transition-colors text-sm">
                                Salvar Alterações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Recent Votes -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm dark:shadow-sm border border-gray-200 dark:border-slate-800 p-6 transition-colors duration-200">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-icon name="circle-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-500" />
                            Últimos Votos
                        </h3>

                        @if ($recentvotes->count() > 0)
                            <div class="flow-root">
                                <ul class="-my-5 divide-y divide-gray-100 dark:divide-slate-800">
                                    @foreach ($recentvotes as $vote)
                                        <li class="py-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-1">
                                                    @if ($vote->vote === 'yes')
                                                        <span
                                                            class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30">
                                                            <x-icon name="check" class="w-4 h-4" />
                                                        </span>
                                                    @elseif($vote->vote === 'no')
                                                        <span
                                                            class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30">
                                                            <x-icon name="xmark" class="w-4 h-4" />
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-500 border border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                                                            <x-icon name="minus" class="w-4 h-4" />
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        {{ $vote->agenda->title }}
                                                    </p>
                                                    <div
                                                        class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-500 mt-0.5">
                                                        <span>{{ $vote->agenda->meeting->title }}</span>
                                                        <span>•</span>
                                                        <span>{{ $vote->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @if ($vote->comments)
                                                        <p class="mt-1 text-xs text-gray-400 dark:text-slate-400 italic">
                                                            "{{ $vote->comments }}"</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-slate-500 text-center py-4">Nenhum voto registrado
                                recentemente.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
