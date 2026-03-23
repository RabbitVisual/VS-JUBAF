@extends('memberpanel::components.layouts.master')

@section('title', 'Convites de parentesco')
@section('page-title', 'Convites de parentesco')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Painel</a>
                <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                <span class="text-gray-900 dark:text-white font-medium">Convites de parentesco</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Vínculos familiares</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">Convites que você recebeu e vínculos que você solicitou. Aceite ou recuse os convites; solicite novos em "Solicitar vínculo".</p>
                </div>
                <a href="{{ route('memberpanel.relationships.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-colors shrink-0">
                    <x-icon name="plus" class="w-5 h-5" />
                    Solicitar vínculo familiar
                </a>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm font-medium text-amber-800 dark:text-amber-200">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                @if ($invites->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 mb-4">
                            <x-icon name="people-group" class="w-7 h-7" />
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Nenhum convite pendente</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Quando alguém te marcar como familiar, o convite aparecerá aqui.</p>
                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <a href="{{ route('memberpanel.relationships.create') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                <x-icon name="plus" class="w-4 h-4" />
                                Solicitar vínculo familiar
                            </a>
                            <a href="{{ route('memberpanel.profile.show') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                <x-icon name="user" class="w-4 h-4" />
                                Ir para meu perfil
                            </a>
                        </div>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                        @foreach ($invites as $invite)
                            <li class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <x-icon name="people-group" class="w-6 h-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ $invite->user->name ?? $invite->user->email }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                            Te marcou como <strong>{{ $invite->relationship_type_label }}</strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <form action="{{ route('memberpanel.relationships.accept', $invite) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors shadow-sm">
                                            <x-icon name="circle-check" class="w-4 h-4" />
                                            Aceitar
                                        </button>
                                    </form>
                                    <form action="{{ route('memberpanel.relationships.reject', $invite) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 text-sm font-bold transition-colors border border-gray-200 dark:border-slate-700">
                                            <x-icon name="circle-xmark" class="w-4 h-4" />
                                            Recusar
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
