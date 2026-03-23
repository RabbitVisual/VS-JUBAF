@extends('homepage::components.layouts.master')

@section('title', 'Ministérios - Igreja Batista Avenida')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[40vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-br from-indigo-900 to-purple-900 z-0"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] z-0"></div>

        <!-- Animated Shapes -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2 z-0 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2 z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-200 text-sm font-semibold tracking-wider uppercase mb-4 backdrop-blur-sm">
                Servir é Amar
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">
                Nossos <span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-400 to-purple-400">Ministérios</span>
            </h1>
            <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto font-light leading-relaxed">
                Cada membro tem um lugar especial para servir e crescer espiritualmente. Descubra onde seus talentos podem brilhar.
            </p>
        </div>
    </section>

    <!-- Ministries Grid -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @if ($ministries->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($ministries as $ministry)
                        @php
                            $colors = ['indigo', 'purple', 'fuchsia', 'violet', 'rose', 'blue'];
                            $colorIndex = $loop->index % count($colors);
                            $color = $colors[$colorIndex];

                            $gradientFrom = 'from-' . $color . '-500';
                            $gradientTo = 'to-' . $color . '-600';
                        @endphp

                        <div class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 overflow-hidden border border-gray-100 dark:border-gray-800 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                            <!-- Header / Icon -->
                            <div class="relative p-8 bg-linear-to-br {{ $gradientFrom }} {{ $gradientTo }} overflow-hidden">
                                <!-- Decoration -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl translate-x-10 -translate-y-10 group-hover:scale-150 transition-transform duration-500"></div>

                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-inner">
                                        @if ($ministry->icon)
                                            <div class="scale-125">{!! $ministry->icon !!}</div>
                                        @else
                                            <x-icon name="church" style="duotone" class="w-8 h-8" />
                                        @endif
                                    </div>
                                    <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Ativo
                                    </span>
                                </div>

                                <h3 class="relative z-10 text-2xl font-bold text-white mt-6 group-hover:translate-x-1 transition-transform">{{ $ministry->name }}</h3>
                            </div>

                            <!-- Content -->
                            <div class="p-8 flex-1 flex flex-col">
                                @if ($ministry->description)
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6 line-clamp-3">
                                        {{ Str::limit($ministry->description, 150) }}
                                    </p>
                                @endif

                                <div class="mt-auto space-y-4">
                                    <!-- Stats -->
                                    <div class="flex items-center justify-between py-4 border-t border-gray-100 dark:border-gray-800">
                                        <div class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                            <x-icon name="users" style="duotone" class="w-4 h-4 mr-2" />
                                            {{ $ministry->active_members_count }} membros
                                        </div>
                                        @if ($ministry->leader)
                                            <div class="text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full">
                                                Líder: {{ explode(' ', $ministry->leader->name)[0] }}
                                            </div>
                                        @endif
                                    </div>

                                    @if ($ministry->meeting_schedule)
                                        <div class="flex items-start text-xs text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-lg">
                                            <x-icon name="clock" style="duotone" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                                            {{ $ministry->meeting_schedule }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="w-20 h-20 mx-auto bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="church" style="duotone" class="h-10 w-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum ministério encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400">Não há ministérios ativos no momento.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-24 relative overflow-hidden bg-gray-900">
        <div class="absolute inset-0 bg-linear-to-r from-indigo-900 to-blue-900 opacity-90"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl md:text-5xl font-bold mb-6 tracking-tight">Quer fazer parte da nossa família?</h2>
            <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Cada ministério oferece uma oportunidade única de servir a Deus e à comunidade.
                Encontre seu lugar na igreja!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                    class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-xl shadow-lg hover:bg-gray-100 hover:-translate-y-1 transition-all duration-200">
                    Entrar e Participar
                </a>
                <a href="{{ route('register') }}"
                    class="px-8 py-4 bg-transparent border-2 border-white/30 text-white font-bold rounded-xl hover:bg-white/10 hover:border-white transition-all duration-200">
                    Cadastrar-se
                </a>
            </div>
        </div>
    </section>
@endsection

