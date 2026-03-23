@extends('liderancapanel::components.layouts.master')

@section('title', __('diretoria::messages.approvals'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="scale-balanced" class="w-7 h-7 text-amber-500" />
                    {{ __('diretoria::messages.pending_approvals') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('diretoria::messages.diretoria_panel_intro') }}</p>
            </div>
            <a href="{{ route('lideranca.conselho.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('diretoria::messages.back') }}
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($pendingApprovals as $approval)
                    <a href="{{ route('lideranca.conselho.approvals.show', $approval) }}"
                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    {{ $approval->approval_type_display ?? 'Solicitação' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $approval->requester->name ?? '' }} ·
                                    {{ $approval->submitted_at ? $approval->submitted_at->format('d/m/Y H:i') : '' }}
                                </p>
                            </div>
                            <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-12 h-12 mx-auto mb-2 text-green-500 opacity-50" />
                        <p>{{ __('diretoria::messages.pending_items') }}</p>
                    </div>
                @endforelse
            </div>
            @if ($pendingApprovals->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $pendingApprovals->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
