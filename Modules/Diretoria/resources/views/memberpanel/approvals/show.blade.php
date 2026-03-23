@extends('memberpanel::components.layouts.master')

@section('title', 'Detalhes da Aprovação - Diretoria')

@section('content')
    <div
        class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white">Detalhes da Aprovação</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">Visualize os detalhes da solicitação de aprovação</p>
                </div>
                <a href="{{ route('memberpanel.Diretoria.approvals.index') }}"
                    class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-amber-600 dark:hover:text-white transition-colors text-sm font-bold shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700">
                    Voltar
                </a>
            </div>

            <!-- Approval Details -->
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 p-6 relative overflow-hidden transition-colors duration-200">
                <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                    <x-icon name="circle-check" class="w-32 h-32 text-amber-500" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-6">
                            Informações Básicas</h3>
                        <div class="space-y-6">
                            <div>
                                <p
                                    class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                                    Título</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $approval->title ?? $approval->approval_type_display }}</p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2">
                                    Tipo de Aprovação</p>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border
                                @if ($approval->approval_type === 'budget') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20
                                @elseif($approval->approval_type === 'project') bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                                @elseif($approval->approval_type === 'personnel') bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                                @elseif($approval->approval_type === 'policy') bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20
                                @elseif($approval->approval_type === 'facility') bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20
                                @else bg-gray-100 text-gray-600 border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 @endif">
                                    {{ $approval->approval_type_display }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2">
                                    Status</p>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border
                                @if ($approval->status === 'pending') bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-500 dark:border-yellow-500/20 animate-pulse
                                @elseif($approval->status === 'approved') bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20
                                @elseif($approval->status === 'rejected') bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20
                                @else bg-gray-100 text-gray-600 border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 @endif">
                                    {{ $approval->status_display }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Request Information -->
                    <div>
                        <h3 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-6">
                            Informações da Solicitação</h3>
                        <div class="space-y-6">
                            <div>
                                <p
                                    class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                                    Solicitante</p>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-slate-400">
                                        {{ substr($approval->requester->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $approval->requester->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-500">
                                            {{ $approval->requester->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                                    Data de Solicitação</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $approval->submitted_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if ($approval->approver)
                                <div>
                                    <p
                                        class="text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                                        Aprovador</p>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-slate-400">
                                            {{ substr($approval->approver->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $approval->approver->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-500">
                                                {{ $approval->approved_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if ($approval->description)
                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-slate-800 relative z-10">
                        <h3 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-4">
                            Descrição</h3>
                        <div
                            class="bg-gray-50 dark:bg-slate-950/50 rounded-xl p-6 border border-gray-100 dark:border-slate-800">
                            <p class="text-gray-600 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                                {{ $approval->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- Decision Notes -->
                @if ($approval->decision_notes)
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-800 relative z-10">
                        <h3 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-4">
                            Notas da Decisão</h3>
                        <div
                            class="bg-amber-50 dark:bg-amber-900/10 rounded-xl p-6 border border-amber-200 dark:border-amber-500/20">
                            <p class="text-amber-800 dark:text-amber-200/80 whitespace-pre-wrap leading-relaxed italic">
                                "{{ $approval->decision_notes }}"</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
