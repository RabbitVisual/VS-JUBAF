@extends('admin::components.layouts.master')

@section('title', 'Moderação de Testemunhos')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Moderação de Testemunhos</h1>
            <p class="text-gray-600 dark:text-gray-400">Analise e aprove relatos para o Mural de Testemunhos.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.intercessor.moderation.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                <span>Voltar</span>
            </a>
            <a href="{{ route('admin.intercessor.dashboard') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                <span>Painel</span>
            </a>
        </div>
    </div>

    @if($pendingTestimonies->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Autor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Testemunho</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pendingTestimonies as $testimony)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-100 dark:border-blue-800/50 shadow-sm">
                                            {{ substr($testimony->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $testimony->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                 ID do Pedido: #{{ $testimony->prayer_request_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 dark:text-gray-300 max-w-xl line-clamp-2 italic">
                                        "{!! \Modules\Intercessor\App\Services\BibleParser::parse($testimony->content) !!}"
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $testimony->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <form action="{{ route('admin.intercessor.moderation.testimony.approve', $testimony) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 rounded-lg text-xs font-bold transition-all flex items-center">
                                                <x-icon name="check" class="w-4 h-4 mr-1" />
                                                Aprovar
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.intercessor.moderation.testimony.reject', $testimony) }}" method="POST" class="inline" onsubmit="return confirm('Rejeitar este testemunho?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 rounded-lg text-xs font-bold transition-all flex items-center">
                                                <x-icon name="x" class="w-4 h-4 mr-1" />
                                                Rejeitar
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
            <div class="w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="star" class="w-12 h-12 text-indigo-400 dark:text-indigo-500" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sem testemunhos pendentes</h3>
            <p class="text-gray-600 dark:text-gray-400 text-center max-w-md mb-8">Nenhum testemunho novo foi enviado para moderação.</p>
            <a href="{{ route('admin.intercessor.moderation.index') }}" class="inline-flex items-center px-8 py-3 text-base font-bold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                Ver Moderação de Pedidos
            </a>
        </div>
    @endif
</div>
@endsection

