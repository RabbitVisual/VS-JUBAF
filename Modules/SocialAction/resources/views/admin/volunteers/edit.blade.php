@extends('admin::components.layouts.master')

@section('title', 'Editar Voluntário | Ação Social')

@section('content')
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('socialaction.admin.volunteers.index') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-500 hover:text-sky-600 transition-colors">
            <x-icon name="arrow-left" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $volunteer->user?->name ?? 'Voluntário' }}</h1>
            <p class="text-sm text-gray-500">Atualizar dados do voluntário</p>
        </div>
    </div>

    <form action="{{ route('socialaction.admin.volunteers.update', $volunteer->id) }}" method="POST" class="max-w-3xl mx-auto space-y-6">
        @csrf @method('PUT')

        {{-- Dados Básicos --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="p-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-600 rounded-lg"><x-icon name="user" /></span>
                Identificação — <span class="font-normal text-gray-500">{{ $volunteer->user?->email }}</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Função <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 py-3 px-4">
                        <option value="helper" {{ old('role', $volunteer->role) === 'helper' ? 'selected' : '' }}>Auxiliar</option>
                        <option value="leader" {{ old('role', $volunteer->role) === 'leader' ? 'selected' : '' }}>Líder</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone', $volunteer->phone) }}" placeholder="(xx) xxxxx-xxxx"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 py-3 px-4">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Horas Dedicadas</label>
                    <input type="number" name="total_hours" value="{{ old('total_hours', $volunteer->total_hours) }}" step="0.5" min="0"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 py-3 px-4">
                </div>
            </div>
        </div>

        {{-- Habilidades --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="p-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-lg"><x-icon name="star" /></span>
                Habilidades
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($skillsLabels as $key => $label)
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-900/20 hover:border-sky-300 dark:hover:border-sky-700 transition-all has-[:checked]:bg-sky-50 has-[:checked]:border-sky-400 has-[:checked]:dark:bg-sky-900/30">
                        <input type="checkbox" name="skills[]" value="{{ $key }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                            {{ in_array($key, old('skills', $volunteer->skills ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Disponibilidade --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="p-1.5 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-lg"><x-icon name="calendar-days" /></span>
                Disponibilidade Semanal
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($availabilityLabels as $key => $label)
                    <label class="flex flex-col items-center gap-1 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-300 dark:hover:border-green-700 transition-all has-[:checked]:bg-green-50 has-[:checked]:border-green-400 has-[:checked]:dark:bg-green-900/30 text-center">
                        <input type="checkbox" name="availability[]" value="{{ $key }}" class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            {{ in_array($key, old('availability', $volunteer->availability ?? [])) ? 'checked' : '' }}>
                        <span class="text-xs text-gray-700 dark:text-gray-300 font-medium mt-1">{{ substr($label, 0, 3) }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Bio + Status --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="p-1.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg"><x-icon name="pen" /></span>
                Observações
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Bio / Testemunho</label>
                    <textarea name="bio" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white shadow-sm focus:border-sky-500 focus:ring-sky-500 py-3 px-4 resize-none">{{ old('bio', $volunteer->bio) }}</textarea>
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                        {{ old('is_active', $volunteer->is_active) ? 'checked' : '' }}>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Voluntário Ativo</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('socialaction.admin.volunteers.index') }}" class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold shadow-lg shadow-sky-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                <x-icon name="floppy-disk" /> Salvar Alterações
            </button>
        </div>
    </form>
@endsection
