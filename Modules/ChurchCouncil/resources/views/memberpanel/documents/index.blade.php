@extends('memberpanel::components.layouts.master')

@section('title', 'Documentos - Conselho da Igreja')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-200 font-sans transition-colors duration-200">
    <!-- Hero Section -->
    <div class="relative bg-linear-to-r from-cyan-600 to-cyan-500 dark:from-slate-900 dark:to-slate-950 border-b border-cyan-200 dark:border-cyan-900/30 p-6 md:p-10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-cyan-100 dark:text-cyan-500 font-bold uppercase tracking-widest text-xs">Arquivo Digital</p>
                <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                    Documentos Oficiais
                </h1>
                <p class="text-cyan-50 dark:text-slate-400 font-medium max-w-xl">
                    Acesse atas, estatutos, regimentos e resoluções do conselho.
                </p>
            </div>

            <div class="bg-white/10 dark:bg-slate-900/50 backdrop-blur-md rounded-xl p-4 border border-white/20 dark:border-slate-800 w-full md:w-auto shadow-lg">
                <form action="{{ route('memberpanel.churchcouncil.documents.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="relative">
                        <x-icon name="filter" class="w-4 h-4 text-cyan-100 dark:text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" />
                        <select name="type" onchange="this.form.submit()" class="pl-9 pr-8 py-2 rounded-lg bg-white/20 dark:bg-slate-800 border-white/30 dark:border-slate-700 text-white dark:text-slate-200 text-sm focus:ring-cyan-300 dark:focus:ring-cyan-500 focus:border-cyan-300 dark:focus:border-cyan-500 w-full md:w-48 appearance-none cursor-pointer hover:bg-white/30 dark:hover:bg-slate-700 transition-colors placeholder-cyan-200 dark:placeholder-slate-500">
                            <option value="" class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Todos os Tipos</option>
                            <option value="statute" {{ request('type') == 'statute' ? 'selected' : '' }} class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Estatuto</option>
                            <option value="regiment" {{ request('type') == 'regiment' ? 'selected' : '' }} class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Regimento</option>
                            <option value="minute" {{ request('type') == 'minute' ? 'selected' : '' }} class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Ata</option>
                            <option value="resolution" {{ request('type') == 'resolution' ? 'selected' : '' }} class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Resolução</option>
                             <option value="other" {{ request('type') == 'other' ? 'selected' : '' }} class="text-gray-900 dark:text-slate-200 bg-white dark:bg-slate-900">Outros</option>
                        </select>
                    </div>

                    @if(request('type'))
                        <a href="{{ route('memberpanel.churchcouncil.documents.index') }}" class="flex items-center justify-center px-4 py-2 text-sm font-bold text-cyan-50 hover:text-white hover:bg-white/20 rounded-lg transition-all border border-transparent hover:border-white/30">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($documents as $document)
                <div class="group bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-800 hover:shadow-md dark:hover:shadow-cyan-500/10 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 duration-300 border border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800
                            @if($document->document_type == 'statute') text-blue-600 dark:text-blue-400
                            @elseif($document->document_type == 'minute') text-green-600 dark:text-green-400
                            @elseif($document->document_type == 'resolution') text-purple-600 dark:text-purple-400
                            @else text-gray-500 dark:text-slate-400 @endif">
                            <x-icon name="file-lines" class="w-7 h-7" />
                        </div>
                        <span class="px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                            {{ $document->document_date->format('d M Y') }}
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 leading-tight group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors" title="{{ $document->title }}">
                        {{ $document->title }}
                    </h3>

                    <div class="h-12 mb-4">
                        @if($document->description)
                            <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                {{ $document->description }}
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-800 pt-4 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">Tipo</span>
                            <span class="text-sm font-bold text-gray-700 dark:text-slate-300">
                                @if($document->document_type == 'statute') Estatuto
                                @elseif($document->document_type == 'regiment') Regimento
                                @elseif($document->document_type == 'minute') Ata
                                @elseif($document->document_type == 'resolution') Resolução
                                @else Outro @endif
                            </span>
                        </div>

                        <a href="{{ route('memberpanel.churchcouncil.documents.download', $document) }}"
                           class="relative inline-flex items-center justify-center px-4 py-2 bg-cyan-600 text-white rounded-lg text-xs font-bold uppercase tracking-wide hover:bg-cyan-700 dark:hover:bg-cyan-500 transition-all shadow-md hover:shadow-lg dark:hover:shadow-cyan-500/20 hover:-translate-y-0.5">
                            <x-icon name="download" class="w-4 h-4 mr-2" />
                            Baixar
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-gray-300 dark:border-slate-700">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-slate-700">
                        <x-icon name="file-magnifying-glass" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum documento encontrado</h3>
                    <p class="text-gray-500 dark:text-slate-400">Não há documentos disponíveis com os filtros atuais.</p>
                    @if(request('type'))
                         <a href="{{ route('memberpanel.churchcouncil.documents.index') }}" class="mt-4 inline-block text-sm font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300 transition-colors">Limpar Filtros</a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-8 border-t border-gray-200 dark:border-slate-800 pt-6">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

