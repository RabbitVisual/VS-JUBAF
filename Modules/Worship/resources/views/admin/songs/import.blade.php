@extends('admin::components.layouts.master')

@section('title', 'Importar músicas | Worship')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('worship.admin.songs.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('worship.admin.songs.index') }}" class="hover:underline">Biblioteca</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Importar</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Importar músicas</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">ChordPro (.cho, .pro) ou OpenSong / OpenLyrics (XML).</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <x-icon name="file-lines" class="w-5 h-5" />
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">ChordPro</h2>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Importe até 300 músicas. Use um <strong>.zip</strong> para importação em massa.</p>
            <form action="{{ route('worship.admin.songs.import-chordpro') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ZIP (até 300 músicas)</label>
                    <input type="file" name="zip_file" accept=".zip" class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-indigo-600 file:text-white file:cursor-pointer hover:file:bg-indigo-700">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ou vários arquivos .cho, .pro, .txt:</p>
                <input type="file" name="files[]" accept=".cho,.pro,.txt" multiple class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-indigo-500 file:text-white">
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition">
                    Importar ChordPro
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <x-icon name="file-code" class="w-5 h-5" />
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">OpenSong / OpenLyrics</h2>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Importe até 1500 músicas. XML OpenLyrics (OpenLP) ou OpenSong. Use .zip para importação em massa.</p>
            <form action="{{ route('worship.admin.songs.import-opensong') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ZIP (até 1500 músicas)</label>
                    <input type="file" name="opensong_zip" accept=".zip" class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-amber-600 file:text-white file:cursor-pointer hover:file:bg-amber-700">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ou vários XML:</p>
                <input type="file" name="opensong_files[]" accept=".xml,.txt" multiple class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-amber-500 file:text-white">
                <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition">
                    Importar OpenSong
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                <x-icon name="arrows-rotate" class="w-5 h-5" />
            </div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Reimportar em massa</h2>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Atualiza músicas que já existem (por título + autor). Não cria novas. Até 1500 arquivos por ZIP.</p>
        <form action="{{ route('worship.admin.songs.reimport-bulk') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ZIP com .cho, .pro e/ou .xml</label>
                <input type="file" name="reimport_zip" accept=".zip" class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-gray-600 file:text-white">
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Ou vários arquivos:</p>
            <input type="file" name="reimport_files[]" accept=".cho,.pro,.xml,.txt" multiple class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-gray-500 file:text-white">
            <button type="submit" class="w-full py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl text-sm font-bold transition">
                Reimportar em massa
            </button>
        </form>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        <a href="{{ route('worship.admin.songs.index') }}" class="text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
            <x-icon name="arrow-left" class="w-4 h-4 inline mr-1" />
            Voltar à biblioteca
        </a>
        <a href="{{ route('worship.admin.songs.create') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
            Cadastrar música manualmente
        </a>
    </div>
</div>
@endsection
