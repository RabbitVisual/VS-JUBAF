@extends('admin::components.layouts.master')

@section('title', 'Monitoramento de Senhas')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Segurança</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Monitoramento</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Monitoramento de Senhas</h1>
                <p class="text-gray-300 max-w-xl">Acompanhe todos os pedidos de recuperação de acesso realizados no sistema. Todos os pedidos são registrados aqui para auditoria.</p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.password-resets.settings') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="gear" class="w-5 h-5 text-blue-600" />
                    Configurar E-mail
                </a>
            </div>
        </div>
    </div>

    <!-- Dica -->
    <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex items-start gap-3">
        <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
        <p class="text-sm text-blue-800 dark:text-blue-200">Todos os pedidos de recuperação de senha são registrados aqui para auditoria. Use a página de configurações para personalizar o e-mail enviado ao usuário.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total de Pedidos</p>
                    <p class="mt-1 text-3xl font-black text-gray-900 dark:text-white">{{ $resets->total() }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <x-icon name="paper-plane" class="w-6 h-6" />
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
        <div class="absolute right-0 top-0 w-40 h-40 bg-gray-50 dark:bg-gray-700/30 rounded-bl-full -mr-12 -mt-12"></div>
        <div class="relative overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Membro</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo / Identificador</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data / Hora</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($resets as $reset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($reset->user)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                                            {{ substr($reset->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $reset->user->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $reset->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-red-500 font-medium italic">Usuário não encontrado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold uppercase {{ $reset->type === 'email' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' }}">
                                        {{ $reset->type }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $reset->identifier }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $reset->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                {{ $reset->ip_address }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800/50">
                                    {{ $reset->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-gray-500 dark:text-gray-400">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                        <x-icon name="inbox" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <p class="font-medium text-gray-900 dark:text-white">Nenhuma solicitação registrada</p>
                                    <p class="text-sm mt-1">Os pedidos de recuperação de senha aparecerão aqui quando forem realizados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            {{ $resets->links() }}
        </div>
    </div>
</div>
@endsection
