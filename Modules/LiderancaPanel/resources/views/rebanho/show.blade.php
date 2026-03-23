@extends('liderancapanel::components.layouts.master')

@section('title', 'Perfil: ' . ($user->first_name ?? $user->name))

@section('content')
    <div class="space-y-8" x-data="{ analysisOpen: false, analysisText: '', analysisLoading: false }">
        {{-- Header --}}
        <div
            class="relative rounded-3xl overflow-hidden bg-slate-800 dark:bg-slate-900 border border-amber-900/30 shadow-xl">
            <div class="h-40 bg-gradient-to-br from-amber-900/40 via-slate-800 to-slate-900 relative"></div>
            <div class="px-8 pb-8 flex flex-col md:flex-row gap-6 items-end -mt-20 relative">
                <div class="relative">
                    <div
                        class="w-32 h-32 rounded-2xl border-4 border-slate-800 dark:border-slate-900 shadow-2xl overflow-hidden bg-slate-700">
                        @if ($user->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->photo) }}" alt="{{ $user->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-amber-200">
                                <x-icon name="user" class="w-16 h-16" />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 pb-1">
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                        {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</h1>
                    <p class="text-amber-200/90 text-sm mt-1">{{ $user->role->name ?? 'Membro' }}</p>
                    <div class="flex flex-wrap gap-4 mt-3 text-sm text-slate-300">
                        @if ($user->email)
                            <span class="flex items-center gap-1.5"><x-icon name="envelope"
                                    class="w-4 h-4 text-amber-400/80" /> {{ $user->email }}</span>
                        @endif
                        @if ($user->cellphone)
                            <span class="flex items-center gap-1.5"><x-icon name="phone"
                                    class="w-4 h-4 text-amber-400/80" /> {{ $user->cellphone }}</span>
                        @endif
                        @if ($user->membership_date)
                            <span class="flex items-center gap-1.5"><x-icon name="calendar"
                                    class="w-4 h-4 text-amber-400/80" /> Membro desde
                                {{ $user->membership_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('lideranca.rebanho.index') }}"
                        class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-200 hover:bg-amber-500/30 text-sm font-medium transition-colors">
                        <x-icon name="arrow-left" class="w-4 h-4" /> Voltar ao Rebanho
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Ministérios --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                        <h2
                            class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <x-icon name="church" class="w-5 h-5 text-amber-500" /> Ministérios
                        </h2>
                    </div>
                    <div class="p-6">
                        @forelse($user->ministries ?? [] as $ministry)
                            <div class="py-2 border-b border-gray-100 dark:border-slate-700 last:border-0">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $ministry->name }}</p>
                                @if ($ministry->pivot && isset($ministry->pivot->role))
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ministry->pivot->role }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 py-2">Nenhum ministério vinculado.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Árvore Genealógica e Análise Elias --}}
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between flex-wrap gap-2">
                        <h2
                            class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <x-icon name="people-group" class="w-5 h-5 text-amber-500" /> Família & Relacionamentos
                        </h2>
                        @if (Route::has('admin.users.family-tree-analysis'))
                            <button type="button"
                                @click="analysisLoading = true; analysisOpen = true; fetch('{{ route('admin.users.family-tree-analysis', $user) }}').then(r => r.json()).then(d => { analysisText = d.analysis || d.message || ''; analysisLoading = false; }).catch(() => { analysisText = 'Não foi possível carregar a análise.'; analysisLoading = false; })"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-xs font-bold uppercase tracking-wider hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                                <x-icon name="robot" class="w-4 h-4" /> Análise Elias
                            </button>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($user->relationships ?? [] as $rel)
                                <div
                                    class="p-4 bg-gray-50 dark:bg-slate-700/30 rounded-xl border border-gray-100 dark:border-slate-600">
                                    <div class="flex items-center gap-3">
                                        @if ($rel->related_user_id && $rel->relatedUser)
                                            @if (Route::has('lideranca.rebanho.show'))
                                                <a href="{{ route('lideranca.rebanho.show', $rel->relatedUser) }}"
                                                    class="shrink-0">
                                            @endif
                                            @if ($rel->relatedUser->photo)
                                                <img class="h-12 w-12 rounded-full object-cover border-2 border-amber-500/30"
                                                    src="{{ \Illuminate\Support\Facades\Storage::url($rel->relatedUser->photo) }}"
                                                    alt="">
                                            @else
                                                <div
                                                    class="h-12 w-12 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-300 text-sm font-bold">
                                                    {{ strtoupper(mb_substr($rel->relatedUser->first_name ?? ($rel->relatedUser->name ?? '?'), 0, 1)) }}
                                                </div>
                                            @endif
                                            @if (Route::has('lideranca.rebanho.show'))
                                                </a>
                                            @endif
                                            <div class="min-w-0">
                                                @if (Route::has('lideranca.rebanho.show'))
                                                    <a href="{{ route('lideranca.rebanho.show', $rel->relatedUser) }}"
                                                        class="font-bold text-gray-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 block truncate">{{ $rel->relatedUser->name }}</a>
                                                @else
                                                    <span
                                                        class="font-bold text-gray-900 dark:text-white block truncate">{{ $rel->relatedUser->name }}</span>
                                                @endif
                                                <span
                                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $rel->relationship_type_label }}</span>
                                                @if ($rel->status === 'accepted')
                                                    <span
                                                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Confirmado</span>
                                                @elseif($rel->status === 'pending')
                                                    <span
                                                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pendente</span>
                                                @endif
                                            </div>
                                        @else
                                            <div
                                                class="h-12 w-12 rounded-full bg-gray-200 dark:bg-slate-600 flex items-center justify-center text-gray-400 shrink-0">
                                                <x-icon name="user" class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <span
                                                    class="font-bold text-gray-900 dark:text-white">{{ $rel->related_name ?? '—' }}</span>
                                                <span
                                                    class="text-xs text-gray-500 dark:text-gray-400 block">{{ $rel->relationship_type_label }}
                                                    · Não membro</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="col-span-2 py-8 text-center border-2 border-dashed border-gray-200 dark:border-slate-600 rounded-xl">
                                    <x-icon name="people-group"
                                        class="w-10 h-10 text-gray-400 dark:text-gray-500 mx-auto mb-2" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum vínculo familiar cadastrado.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Modal Análise Elias --}}
                <div x-show="analysisOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                    @click.self="analysisOpen = false">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-amber-900/20 max-w-lg w-full p-6"
                        @click.stop>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-icon name="robot" class="w-5 h-5 text-amber-500" /> Análise da Árvore (Elias)
                        </h3>
                        <div class="min-h-[120px] text-sm text-gray-700 dark:text-gray-300">
                            <template x-if="analysisLoading">
                                <p class="text-amber-600 dark:text-amber-400">Carregando análise...</p>
                            </template>
                            <template x-if="!analysisLoading && analysisText">
                                <p x-text="analysisText" class="whitespace-pre-wrap"></p>
                            </template>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" @click="analysisOpen = false"
                                class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-600 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
