@extends('memberpanel::components.layouts.master')

@section('page-title', 'Mural de Testemunhos')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-emerald-600 dark:text-emerald-400">Mural de Testemunhos</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Mural de Testemunhos</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Veja como Deus tem respondido às intercessões da nossa igreja.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-2">
                 <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                 <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">{{ $testimonies->total() }} Testemunhos</span>
             </div>
             <a href="{{ route('member.intercessor.room.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Mural de Orações
             </a>
        </div>
    </div>

    @if($testimonies->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500">
                <x-icon name="check" style="duotone" class="w-10 h-10" />
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Aguardando Testemunhos</h3>
            <p class="text-gray-500 max-w-sm mx-auto font-medium">Os testemunhos aparecerão aqui à medida que os irmãos compartilharem suas bênçãos.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($testimonies as $testimony)
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="p-6 flex-1">
                        <!-- Category & Date -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-full">
                                {{ $testimony->category->name }}
                            </span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ $testimony->answered_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-2 line-clamp-2">
                                {{ $testimony->title }}
                            </h3>
                            <div class="relative">
                                <x-icon name="quote-left" style="duotone" class="absolute -top-1 -left-1 w-6 h-6 text-gray-100 dark:text-gray-700 -z-10" />
                                <p class="text-gray-600 dark:text-gray-400 text-sm italic line-clamp-6 leading-relaxed">
                                    "{{ $testimony->testimony }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 mt-auto flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-xs font-black text-indigo-600 uppercase">
                                {{ substr($testimony->user->first_name ?? $testimony->user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                                    {{ $testimony->is_anonymous ? 'Irmão(ã) Anônimo' : ($testimony->user->first_name ?? $testimony->user->name) }}
                                </span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">Testemunhou</span>
                            </div>
                        </div>
                        <a href="{{ route('member.intercessor.room.show', $testimony) }}" class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-emerald-500 transition-colors shadow-sm">
                            <x-icon name="eye" style="duotone" class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $testimonies->links() }}
        </div>
    @endif
</div>
@endsection

