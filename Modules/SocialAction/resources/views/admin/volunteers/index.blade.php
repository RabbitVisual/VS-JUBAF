@extends('admin::components.layouts.master')

@section('title', 'Voluntários | Ação Social')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-check" class="flex-shrink-0" />{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="circle-xmark" class="flex-shrink-0" />{{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Voluntários</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Equipe engajada na missão social. Total de horas: <strong class="text-gray-900 dark:text-white">{{ number_format($totalHours, 1, ',', '.') }}h</strong>
            </p>
        </div>
        <a href="{{ route('socialaction.admin.volunteers.create') }}" class="inline-flex items-center px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all hover:scale-105 active:scale-95 gap-2">
            <x-icon name="user-plus" />
            Novo Voluntário
        </a>
    </div>

    @if($volunteers->isEmpty())
        <div class="py-24 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-sky-100 dark:bg-sky-900/30 mb-5 text-sky-500">
                <x-icon name="people-pulling" class="text-4xl" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nenhum voluntário cadastrado</h3>
            <p class="text-gray-500 mt-2 mb-6">Cadastre membros dispostos a servir na ação social.</p>
            <a href="{{ route('socialaction.admin.volunteers.create') }}" class="inline-flex items-center px-6 py-3 bg-sky-600 text-white rounded-xl font-semibold gap-2 hover:bg-sky-700 transition-colors">
                <x-icon name="user-plus" />
                Cadastrar Primeiro Voluntário
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($volunteers as $volunteer)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 flex flex-col relative">
                    {{-- Action Buttons --}}
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                        <a href="{{ route('socialaction.admin.volunteers.edit', $volunteer->id) }}"
                           class="p-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-sky-100 hover:text-sky-600 dark:hover:bg-sky-900 transition-colors" title="Editar">
                            <x-icon name="pen-to-square" class="text-xs" />
                        </a>
                        <form action="{{ route('socialaction.admin.volunteers.destroy', $volunteer->id) }}" method="POST" onsubmit="return confirm('Remover este voluntário?')">
                            @csrf @method('DELETE')
                             <button type="submit" class="p-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900 transition-colors" title="Remover">
                                <x-icon name="trash" class="text-xs" />
                            </button>
                        </form>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-linear-to-br from-sky-400 to-indigo-600 flex items-center justify-center text-white text-base font-bold uppercase shadow-md">
                            {{ substr($volunteer->user?->name ?? 'V', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 dark:text-white truncate text-sm">{{ $volunteer->user?->name ?? 'N/A' }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $volunteer->role === 'leader' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' }}">
                                {{ $volunteer->role_label }}
                            </span>
                        </div>
                    </div>

                    {{-- Skills --}}
                    @if($volunteer->skills)
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach(array_slice($volunteer->skills, 0, 3) as $skill)
                                @php $labels = \Modules\SocialAction\App\Models\SocialVolunteer::skillsLabels(); @endphp
                                <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">{{ $labels[$skill] ?? $skill }}</span>
                            @endforeach
                            @if(count($volunteer->skills) > 3)
                                <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full">+{{ count($volunteer->skills) - 3 }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="mt-auto pt-3 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <x-icon name="clock" class="text-gray-400" />
                            {{ number_format($volunteer->total_hours, 1, ',', '.') }}h dedicadas
                        </div>
                        <span class="inline-flex items-center gap-1 text-xs {{ $volunteer->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $volunteer->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $volunteer->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $volunteers->links() }}</div>
    @endif
@endsection
