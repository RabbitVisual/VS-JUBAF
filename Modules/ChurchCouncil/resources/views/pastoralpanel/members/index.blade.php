@extends('pastoralpanel::components.layouts.master')

@section('title', __('churchcouncil::messages.members'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-1">
                <a href="{{ route('pastor.conselho.index') }}" class="hover:text-white transition-colors">{{ __('churchcouncil::messages.council') }}</a>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                <span class="text-white font-bold">{{ __('churchcouncil::messages.members') }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="users" class="w-7 h-7 text-amber-500" />
                {{ __('churchcouncil::messages.members') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('churchcouncil::messages.counselors_registered') }}</p>
        </div>
        <a href="{{ route('pastor.conselho.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-gray-800 dark:text-white font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" /> {{ __('churchcouncil::messages.back') }}
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="divide-y divide-gray-200 dark:divide-slate-700">
            @forelse($members as $member)
                <div class="px-6 py-4 flex flex-wrap items-center gap-4 hover:bg-gray-50 dark:hover:bg-slate-700/30">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">
                        {{ substr($member->user->name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $member->user->name ?? __('churchcouncil::messages.no_info') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $member->council_position ?? '' }}
                            · {{ $member->role_display ?? $member->council_role }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ __('churchcouncil::messages.term_start') }}: {{ $member->term_start ? $member->term_start->format('d/m/Y') : '—' }}
                            @if($member->term_end) · {{ __('churchcouncil::messages.term_end') }}: {{ $member->term_end->format('d/m/Y') }} @endif
                        </p>
                    </div>
                    @if($member->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Ativo</span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <x-icon name="users" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>Nenhum membro encontrado.</p>
                </div>
            @endforelse
        </div>
        @if($members->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
            {{ $members->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
