@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Novo template</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Use variáveis como <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">&#123;&#123; title &#125;&#125;</code>, <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">&#123;&#123; message &#125;&#125;</code>, <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">&#123;&#123; action_url &#125;&#125;</code> no corpo.</p>
            </div>
            <a href="{{ route('admin.notifications.templates.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border rounded-md">Voltar</a>
        </div>

        <form action="{{ route('admin.notifications.templates.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf
            @include('notifications::admin.control.templates._form')
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.notifications.templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border rounded-md">Cancelar</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">Criar</button>
            </div>
        </form>
    </div>
@endsection
