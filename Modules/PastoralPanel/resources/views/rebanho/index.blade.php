@extends('pastoralpanel::components.layouts.master')

@section('title', 'Rebanho')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-amber-900/30 text-white p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <x-icon name="users-rays" class="w-7 h-7 text-amber-400" />
                    Rebanho
                </h1>
                <p class="text-slate-300 mt-1">Lista de membros ativos para cuidado pastoral.</p>
            </div>
            @if(Route::has('admin.users.create'))
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                <x-icon name="user-plus" class="w-5 h-5" />
                Novo membro (Admin)
            </a>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <form action="{{ route('pastor.rebanho.index') }}" method="GET" class="p-4 border-b border-gray-200 dark:border-slate-700">
            <div class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou e-mail..."
                    class="flex-1 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium flex items-center gap-2">
                    <x-icon name="magnifying-glass" class="w-5 h-5" />
                    Buscar
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-slate-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">E-mail</th>
                        <th class="px-6 py-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if(Route::has('pastor.rebanho.show'))
                            <a href="{{ route('pastor.rebanho.show', $user) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">Ver perfil</a>
                            @elseif(Route::has('admin.users.show'))
                            <a href="{{ route('admin.users.show', $user) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">Ver perfil</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum membro encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
