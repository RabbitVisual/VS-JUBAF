@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dead Letter Queue (DLQ)</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Notificações que falharam definitivamente. Tente reenviar após corrigir o problema.</p>
            </div>
            <a href="{{ route('admin.notifications.control.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                <x-icon name="chart-line" class="w-4 h-4 mr-1.5" /> Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 flex items-center gap-3">
                <x-icon name="circle-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 flex items-center gap-3">
                <x-icon name="circle-exclamation" class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.notifications.dlq.index') }}" class="flex items-center gap-2">
            <label for="channel" class="text-sm font-medium text-gray-700 dark:text-gray-300">Canal:</label>
            <select name="channel" id="channel" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="">Todos</option>
                <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>E-mail</option>
                <option value="webpush" {{ request('channel') === 'webpush' ? 'selected' : '' }}>Web Push</option>
                <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
            </select>
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Canal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título / Resumo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Erro</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($failed as $f)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $f->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $f->user?->name ?? $f->user_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ $f->channel }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ $f->notification?->title ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400 max-w-xs truncate" title="{{ $f->error_message }}">
                                    {{ Str::limit($f->error_message, 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.notifications.dlq.retry', $f) }}" method="POST" class="inline js-dlq-retry-form">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Tentar reenviar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhuma notificação na DLQ.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($failed->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $failed->links() }}
                </div>
            @endif
        </div>
    </div>
    <script>
        document.querySelectorAll('.js-dlq-retry-form').forEach(function(form) {
            form.addEventListener('submit', function() {
                window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Reenviando...' } }));
            });
        });
    </script>
@endsection
