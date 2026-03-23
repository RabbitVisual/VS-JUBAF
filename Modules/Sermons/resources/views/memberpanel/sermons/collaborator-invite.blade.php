@extends('memberpanel::components.layouts.master')

@section('title', 'Convite para co-autoria')

@section('content')
    <div class="max-w-md mx-auto py-8 px-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Convite para co-autoria</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Você foi convidado a colaborar no sermão <strong>"{{ $collaborator->sermon->title }}"</strong>,
                de <strong>{{ $collaborator->sermon->user->name ?? 'N/A' }}</strong>. Ao aceitar, você poderá editar o conteúdo junto com o autor.
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Obrigado por considerar este convite. Escolha como deseja prosseguir:</p>
            <form method="post" action="{{ route('memberpanel.sermons.collaborator.respond', $collaborator) }}" class="flex gap-3">
                @csrf
                <button type="submit" name="action" value="accept"
                    class="flex-1 px-4 py-3 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition-colors">Aceitar convite</button>
                <button type="submit" name="action" value="reject"
                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Recusar</button>
            </form>
        </div>
    </div>
@endsection
