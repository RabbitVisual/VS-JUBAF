@extends('admin::components.layouts.master')

@section('title', 'Detalhes da Mensagem')

@section('content')
<div class="container-fluid px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detalhes da Mensagem</h1>
        <a href="{{ route('admin.homepage.contacts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors">
            Voltar
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden p-6 border border-gray-100 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Remetente</h3>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $message->name }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ $message->email }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $message->phone ?? 'Telefone não informado' }}</p>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Data de Envio</h3>
                <p class="text-lg text-gray-900 dark:text-gray-200">{{ $message->created_at->format('d/m/Y \à\s H:i') }}</p>

                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1 mt-4">Status</h3>
                @if($message->read_at)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Lida em {{ $message->read_at->format('d/m/Y H:i') }}</span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">Nova</span>
                @endif
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Mensagem</h3>
            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-lg text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed text-lg border border-gray-200 dark:border-gray-700">
                {{ $message->message }}
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
             <a href="mailto:{{ $message->email }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <x-icon name="envelope" class="w-4 h-4 mr-2" />
                Responder por E-mail
            </a>
            <form action="{{ route('admin.homepage.contacts.destroy', $message->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Excluir</button>
            </form>
        </div>
    </div>
</div>
@endsection

