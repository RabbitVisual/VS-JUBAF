@extends('admin::components.layouts.master')

@section('title', 'Editar Professor | ' . $teacher->user->name)

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded">Magistério</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Editar <span class="text-indigo-600">Professor</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gestão de corpo docente e atribuições de classe para {{ $teacher->user->name }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.teachers.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        <form action="{{ route('admin.ebd.teachers.update', $teacher) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- User Association -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Usuário do Sistema</label>
                    <div class="relative group">
                        <x-icon name="user-tie" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                        <select name="user_id" required class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer @error('user_id') ring-2 ring-red-500/20 @enderror">
                            <option value="">Selecione o usuário</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $teacher->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Class Association -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Classe Titular</label>
                    <div class="relative group">
                        <x-icon name="school" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" />
                        <select name="class_id" required class="w-full pl-11 pr-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                            <option value="">Selecione a classe</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $teacher->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->age_group_display }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cargo / Função</label>
                    <select name="role" required class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                        <option value="teacher" {{ old('role', $teacher->role) === 'teacher' ? 'selected' : '' }}>Professor Regente</option>
                        <option value="assistant" {{ old('role', $teacher->role) === 'assistant' ? 'selected' : '' }}>Auxiliar de Classe</option>
                        <option value="substitute" {{ old('role', $teacher->role) === 'substitute' ? 'selected' : '' }}>Professor Substituto</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Início do Magistério</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $teacher->start_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>

                <!-- Status & Active -->
                <div class="md:col-span-1">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-2xl cursor-pointer group transition-all hover:bg-gray-100 dark:hover:bg-gray-800/50 w-fit pr-12">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600 rounded-full"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Professor Ativo</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">Habilitar recursos de professor no painel</span>
                        </div>
                    </label>
                </div>

                <!-- End Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data de Desligamento</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $teacher->end_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Observações de Prática Docente</label>
                    <textarea name="notes" rows="4" placeholder="Detalhes sobre a atuação deste professor..."
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none">{{ old('notes', $teacher->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-8 border-t border-gray-50 dark:border-gray-800">
                <a href="{{ route('admin.ebd.teachers.index') }}" class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">Cancelar</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl shadow-indigo-600/20 active:scale-95">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

