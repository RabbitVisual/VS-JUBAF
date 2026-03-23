@extends('homepage::components.layouts.master')

@section('title', 'Bíblia Online')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
            <x-icon name="book" class="w-10 h-10 text-amber-600 dark:text-amber-400" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Bíblia Online</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8">Nenhuma versão da Bíblia está disponível no momento. Tente novamente mais tarde.</p>
        <a href="{{ route('homepage.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-semibold hover:opacity-90 transition-opacity">
            <x-icon name="arrow-left" class="w-5 h-5" />
            Voltar ao início
        </a>
    </div>
</div>
@endsection
