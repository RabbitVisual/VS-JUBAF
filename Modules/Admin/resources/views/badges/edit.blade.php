@extends('admin::components.layouts.master')

@section('title', 'Editar Badge')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Badge</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Edite as informações do badge</p>
            </div>
            <a href="{{ route('admin.badges.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('admin.badges.update', $badge) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nome do Badge <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $badge->name) }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Descrição
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors resize-none">{{ old('description', $badge->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="icon" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Ícone <span class="text-red-500">*</span>
                            </label>
                            <select id="icon" name="icon" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                                @foreach ($icons as $iconKey => $iconName)
                                    <option value="{{ $iconKey }}" {{ old('icon', $badge->icon) == $iconKey ? 'selected' : '' }}>
                                        {{ $iconName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Cor <span class="text-red-500">*</span>
                            </label>
                            <select id="color" name="color" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="blue" {{ old('color', $badge->color) == 'blue' ? 'selected' : '' }}>Azul</option>
                                <option value="green" {{ old('color', $badge->color) == 'green' ? 'selected' : '' }}>Verde</option>
                                <option value="yellow" {{ old('color', $badge->color) == 'yellow' ? 'selected' : '' }}>Amarelo</option>
                                <option value="purple" {{ old('color', $badge->color) == 'purple' ? 'selected' : '' }}>Roxo</option>
                                <option value="red" {{ old('color', $badge->color) == 'red' ? 'selected' : '' }}>Vermelho</option>
                                <option value="gray" {{ old('color', $badge->color) == 'gray' ? 'selected' : '' }}>Cinza</option>
                            </select>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="criteria_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Critério <span class="text-red-500">*</span>
                        </label>
                        <select id="criteria_type" name="criteria_type" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                            @foreach ($criteriaTypes as $typeKey => $typeName)
                                <option value="{{ $typeKey }}" {{ old('criteria_type', $badge->criteria_type) == $typeKey ? 'selected' : '' }}>
                                    {{ $typeName }}
                                </option>
                            @endforeach
                        </select>
                        @error('criteria_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="points_required" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Pontos Necessários
                        </label>
                        <input type="number" id="points_required" name="points_required" min="0" value="{{ old('points_required', $badge->points_required) }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('points_required')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Ordem
                            </label>
                            <input type="number" id="order" name="order" min="0" value="{{ old('order', $badge->order) }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center pt-8">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $badge->is_active) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Badge Ativo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.badges.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                        Atualizar Badge
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

