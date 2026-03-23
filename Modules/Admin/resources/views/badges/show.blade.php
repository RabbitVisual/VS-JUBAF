@extends('admin::components.layouts.master')

@section('title', 'Detalhes do Badge')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $badge->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Detalhes e usuários com este badge</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.badges.edit', $badge) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <x-icon name="pen-to-square" class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.badges.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    Voltar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:text-green-300 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Badge Info -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 mx-auto rounded-lg bg-{{ $badge->color }}-100 dark:bg-{{ $badge->color }}-900/30 flex items-center justify-center mb-4">
                            <x-icon name="{{ $badge->icon }}" class="w-12 h-12 text-{{ $badge->color }}-600 dark:text-{{ $badge->color }}-400" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $badge->name }}</h3>
                        @if ($badge->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $badge->description }}</p>
                        @endif
                    </div>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Critério</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ ucfirst(str_replace('_', ' ', $badge->criteria_type)) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Pontos Necessários</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $badge->points_required ?? 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                @if ($badge->is_active)
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        Ativo
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Inativo
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Users List -->
            <div class="md:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Usuários com este Badge ({{ $users->total() }})
                        </h2>
                        <form action="{{ route('admin.badges.award', $badge) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="user_id" required
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                <option value="">Selecione um usuário</option>
                                @foreach (\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                                    @if (!$user->badges()->where('badge_id', $badge->id)->exists())
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                Atribuir
                            </button>
                        </form>
                    </div>

                    @if ($users->count() > 0)
                        <div class="space-y-3">
                            @foreach ($users as $user)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Conquistado em {{ \Carbon\Carbon::parse($user->pivot->earned_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.badges.remove-user', [$badge, $user]) }}" method="POST"
                                        onsubmit="return confirm('Remover badge deste usuário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <x-icon name="trash-can" class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-icon name="user-slash" class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" />
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nenhum usuário possui este badge ainda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

