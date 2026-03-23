@php
    $user = auth()->user();
    $userPhoto =
        $user && $user->photo
            ? \Illuminate\Support\Facades\Storage::url($user->photo) . '?v=' . $user->updated_at->timestamp
            : null;
    $userName = $user->first_name ?? ($user->name ?? 'Liderança');
@endphp

<nav
    class="sticky top-0 z-50 w-full bg-slate-800 dark:bg-slate-950 border-b border-amber-900/30 dark:border-amber-800/20 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" type="button"
                    class="lg:hidden p-2 text-slate-400 hover:text-amber-300 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                    <x-icon name="bars" class="w-6 h-6" />
                </button>
                <div class="hidden md:flex items-center space-x-2 text-sm">
                    <a href="{{ route('lideranca.dashboard') }}"
                        class="flex items-center text-amber-200/80 hover:text-amber-300 transition-colors font-medium">
                        <x-icon name="house" class="w-4 h-4 mr-1.5" />
                        Gabinete
                    </a>
                    @if (isset($breadcrumb))
                        <x-icon name="chevron-right" class="w-3.5 h-3.5 text-amber-600/60" />
                        <span class="text-white font-semibold tracking-wide">{{ $breadcrumb }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if ($user && $user->isAdmin() && Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 hover:text-white border border-slate-600 transition-colors">
                        <x-icon name="gear" class="w-4 h-4" />
                        Ir para Admin
                    </a>
                @endif

                <button id="theme-toggle" type="button"
                    class="p-2.5 rounded-xl text-slate-400 hover:text-amber-300 hover:bg-slate-700 focus:outline-none transition-all">
                    <span class="hidden dark:inline"><x-icon name="sun-bright" class="w-5 h-5 text-amber-400" /></span>
                    <span class="inline dark:hidden"><x-icon name="moon-stars" class="w-5 h-5" /></span>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 p-1.5 rounded-xl text-slate-300 hover:bg-slate-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                        @if ($userPhoto)
                            <img src="{{ $userPhoto }}" alt="" class="w-8 h-8 rounded-lg object-cover">
                        @else
                            <span
                                class="w-8 h-8 rounded-lg bg-amber-500/30 flex items-center justify-center text-amber-300 font-bold text-sm">{{ strtoupper(mb_substr($userName, 0, 1)) }}</span>
                        @endif
                        <span
                            class="hidden sm:inline text-sm font-medium max-w-32 truncate">{{ $userName }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 py-1 bg-slate-800 dark:bg-slate-900 border border-amber-900/30 rounded-xl shadow-xl z-50">
                        <a href="{{ route('lideranca.dashboard') }}"
                            class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">Dashboard</a>
                        @if (Route::has('lideranca.profile.show'))
                            <a href="{{ route('lideranca.profile.show') }}"
                                class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">Meu
                                perfil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-red-300">Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
