@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Broadcast em massa</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Envie uma notificação para todos os membros ou grupos específicos.</p>
            </div>
            <a href="{{ route('admin.notifications.control.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                Voltar
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.notifications.broadcast.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                @error('title')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Mensagem *</label>
                <textarea name="message" id="message" rows="4" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                <select name="type" id="type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="info" {{ old('type', 'info') === 'info' ? 'selected' : '' }}>Informação</option>
                    <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Sucesso</option>
                    <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Aviso</option>
                    <option value="error" {{ old('type') === 'error' ? 'selected' : '' }}>Erro</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Destino *</label>
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="all" {{ old('target', 'all') === 'all' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Todos os membros ativos</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="roles" {{ old('target') === 'roles' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Por cargo (roles)</span>
                    </label>
                    <div id="target-roles-wrap" class="ml-6 mt-2 {{ old('target') !== 'roles' ? 'hidden' : '' }}">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center gap-2 mr-4 cursor-pointer">
                                <input type="checkbox" name="target_roles[]" value="{{ $role->slug }}" {{ in_array($role->slug, old('target_roles', [])) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $role->name ?? $role->slug }}</span>
                            </label>
                        @endforeach
                    </div>
                    <br>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="target" value="ministries" {{ old('target') === 'ministries' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Por ministério</span>
                    </label>
                    <div id="target-ministries-wrap" class="ml-6 mt-2 {{ old('target') !== 'ministries' ? 'hidden' : '' }}">
                        @foreach($ministries as $m)
                            <label class="inline-flex items-center gap-2 mr-4 cursor-pointer">
                                <input type="checkbox" name="target_ministries[]" value="{{ $m->id }}" {{ in_array((string)$m->id, old('target_ministries', [])) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $m->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.notifications.control.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-md">
                    Enviar notificação
                </button>
            </div>
        </form>

        <script>
            document.querySelectorAll('input[name="target"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.getElementById('target-roles-wrap').classList.toggle('hidden', this.value !== 'roles');
                    document.getElementById('target-ministries-wrap').classList.toggle('hidden', this.value !== 'ministries');
                });
            });
        </script>
    </div>
@endsection
