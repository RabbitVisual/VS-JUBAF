@extends('homepage::components.layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 py-16">
    <div class="max-w-lg w-full text-center">
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-12 shadow-sm">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-6">
                <x-icon name="store" style="duotone" class="w-10 h-10 text-amber-600 dark:text-amber-400" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('marketplace::messages.name') }}</h1>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Estamos organizando nosso estoque para a próxima campanha missionária. Voltamos em breve!</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">Agradecemos sua compreensão e seu apoio.</p>
            <a href="{{ route('homepage.index') }}" class="inline-flex items-center mt-8 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors">
                <x-icon name="house" style="duotone" class="w-5 h-5 mr-2" /> Voltar ao início
            </a>
        </div>
    </div>
</div>
@endsection
