@extends('admin::components.layouts.master')

@section('title', 'Nova Campanha de E-mail')

@section('content')
<div class="container-fluid px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Enviar E-mail para Assinantes</h1>
        <a href="{{ route('admin.homepage.newsletter.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Cancelar
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-500/50 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Ops!</strong>
            <span class="block sm:inline">Verifique os erros abaixo.</span>
             <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.homepage.newsletter.send') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="subject" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Assunto do E-mail</label>
                <input type="text" name="subject" id="subject" class="shadow appearance-none border dark:border-gray-700 rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline" required placeholder="Ex: Novidades da semana">
            </div>

            <div class="mb-6">
                <label for="content" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Conteúdo do E-mail (HTML permitido)</label>
                <textarea name="content" id="content" rows="10" class="shadow appearance-none border dark:border-gray-700 rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Este e-mail será enviado para todos os assinantes ativos.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline flex items-center" onclick="return confirm('Tem certeza que deseja enviar este e-mail para todos os assinantes? Esta ação não pode ser desfeita.');">
                    <x-icon name="paper-plane" style="duotone" class="w-4 h-4 mr-2" />
                    Enviar Campanha
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

