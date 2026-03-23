@extends('admin::components.layouts.master')

@section('title', 'Equipe de Intercessão')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Equipe de Intercessão</h1>
            <p class="text-gray-600 dark:text-gray-400">Gerencie os voluntários e membros dedicados ao ministério.</p>
        </div>
        <a href="{{ route('admin.intercessor.team.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
            <x-icon name="plus" class="w-5 h-5 mr-2" />
            <span>Novo Membro</span>
        </a>
    </div>

    @if($teamMembers->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Membro</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Função</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Data de Entrada</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($teamMembers as $member)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-11 w-11 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center text-gray-500 font-bold border border-gray-200 dark:border-gray-600 shadow-sm group-hover:scale-105 transition-transform duration-300 overflow-hidden">
                                            @if($member->photo)
                                                <img src="{{ $member->photo }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                            @else
                                                {{ substr($member->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $member->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $member->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/50">
                                        Voluntário
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs text-gray-500 dark:text-gray-400 font-medium italic">
                                    {{ $member->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <form action="{{ route('admin.intercessor.team.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Deseja realmente remover este membro da equipe?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-800/50" title="Remover da Equipe">
                                                <x-icon name="user-remove" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
            <div class="w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="users" class="w-12 h-12 text-blue-400 dark:text-blue-500" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Nenhum membro na equipe</h3>
            <p class="text-gray-600 dark:text-gray-400 text-center max-w-md mb-8">Adicione voluntários para ajudarem na moderação e no atendimento dos pedidos de oração.</p>
            <a href="{{ route('admin.intercessor.team.create') }}" class="inline-flex items-center px-8 py-4 text-base font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                <x-icon name="plus" class="w-6 h-6 mr-2" />
                Adicionar Primeiro Membro
            </a>
        </div>
    @endif
</div>
@endsection

