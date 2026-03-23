@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar template</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Chave: {{ $template->key }}</p>
            </div>
            <a href="{{ route('admin.notifications.templates.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border rounded-md">Voltar</a>
        </div>

        <form action="{{ route('admin.notifications.templates.update', $template) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf
            @method('PUT')
            @include('notifications::admin.control.templates._form', ['template' => $template])
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.notifications.templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border rounded-md">Cancelar</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">Salvar</button>
            </div>
        </form>
    </div>
@endsection
