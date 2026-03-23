@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Culto | Worship')

@section('content')
<div class="space-y-8" x-data="worshipSetlistManager({{ $setlist->id }}, '{{ $setlist->status->value }}')">
        <!-- Edit Modal -->
        <template x-if="editModalOpen">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-3xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">
                    <form action="{{ route('worship.admin.setlists.update', $setlist->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="p-6 md:p-8 space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Editar evento</h3>
                                <button type="button" @click="editModalOpen = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                    <x-icon name="x" class="w-5 h-5" />
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título</label>
                                    <input type="text" name="title" value="{{ $setlist->title }}" required
                                        class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data e hora</label>
                                    <input type="datetime-local" name="scheduled_at" value="{{ $setlist->scheduled_at->format('Y-m-d\TH:i') }}" required
                                        class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Líder</label>
                                    <select name="leader_id" required class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 dark:text-white appearance-none">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $setlist->leader_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notas</label>
                                    <textarea name="description" rows="3" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 text-gray-700 dark:text-gray-300">{{ $setlist->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 md:px-8 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancelar</button>
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold shadow-lg shadow-purple-500/20 transition-all">
                                <x-icon name="check" class="w-4 h-4 mr-1.5" />
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <!-- Header -->
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('worship.admin.setlists.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 transition-colors shrink-0">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <nav class="flex items-center gap-2 text-[10px] font-black text-purple-600 dark:text-purple-500 uppercase tracking-widest mb-1">
                        <a href="{{ route('worship.admin.dashboard') }}" class="hover:underline">Louvor</a>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                        <a href="{{ route('worship.admin.setlists.index') }}" class="hover:underline">Cultos</a>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                        <span class="text-gray-400 dark:text-gray-500 truncate">{{ $setlist->title }}</span>
                    </nav>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight truncate">{{ $setlist->title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-3 flex-wrap">
                        <span class="flex items-center gap-1.5"><x-icon name="calendar" class="w-4 h-4 text-purple-500" /> {{ $setlist->scheduled_at->translatedFormat('l, d \d\e F \à\s H:i') }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                        <span class="flex items-center gap-1.5"><x-icon name="user" class="w-4 h-4 text-purple-500" /> {{ $setlist->leader->name }}</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="w-2 h-2 rounded-full" :class="'bg-' + statusColor + '-500'"></span>
                        <span x-text="statusLabel"></span>
                        <x-icon name="chevron-down" class="w-4 h-4 opacity-60" />
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                        @foreach(\Modules\Worship\App\Enums\SetlistStatus::cases() as $status)
                            <button type="button" @click="updateStatus('{{ $status->value }}', '{{ $status->label() }}', '{{ $status->color() }}'); open = false"
                                class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-center justify-between">
                                <span>{{ $status->label() }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $status->color() }}-500"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <button type="button" @click="editModalOpen = true"
                    class="p-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 transition-colors" title="Editar evento">
                    <x-icon name="pencil" class="w-5 h-5" />
                </button>

                <a href="{{ route('admin.projection.console', $setlist->id) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-gray-950 text-white font-bold text-sm hover:bg-black dark:hover:bg-black transition-colors border border-gray-700 dark:border-gray-600">
                    <x-icon name="presentation-screen" class="w-5 h-5 text-blue-400" />
                    Console de Projeção
                </a>
                <a href="{{ route('worship.member.stage.view', $setlist->id) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/20 transition-colors">
                    <x-icon name="play" class="w-5 h-5" />
                    Modo Palco
                </a>
                <a href="{{ route('worship.admin.setlists.print', $setlist->id) }}" target="_blank" rel="noopener"
                    class="p-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 transition-colors" title="Imprimir">
                    <x-icon name="printer" class="w-5 h-5" />
                </a>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

            <!-- Left Column: Playlist (8 cols) -->
            <div class="xl:col-span-8 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <x-icon name="music-note" class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Repertório</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $setlist->items->count() }} músicas</p>
                            </div>
                        </div>
                        <form id="add-song-form" action="{{ route('worship.admin.setlists.addSong', $setlist->id) }}" method="POST" class="flex items-center gap-2"
                            @submit="if (!document.getElementById('add-song-id').value) { $event.preventDefault(); $el.querySelector('input[type=text]').focus(); }"
                            x-data="{
                                query: '',
                                results: [],
                                loading: false,
                                open: false,
                                async fetchSongs() {
                                    const q = this.query.trim();
                                    if (q.length < 2) { this.results = []; return; }
                                    this.loading = true;
                                    try {
                                        const r = await fetch('/api/v1/worship/songs?q=' + encodeURIComponent(q) + '&limit=40', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                                        const j = await r.json();
                                        this.results = (j && j.data) ? j.data : [];
                                    } catch (e) { this.results = []; }
                                    this.loading = false;
                                },
                                selectSong(id) {
                                    document.getElementById('add-song-id').value = id;
                                    document.getElementById('add-song-form').submit();
                                }
                            }"
                            @click.away="open = false">
                            @csrf
                            <input type="hidden" name="song_id" id="add-song-id" value="">
                            <div class="relative min-w-[220px] sm:min-w-[320px]">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <x-icon name="search" class="h-4 w-4" />
                                </div>
                                <input type="text"
                                    x-model="query"
                                    @input.debounce.250ms="fetchSongs(); open = true"
                                    @focus="if (query.trim().length >= 2) open = true"
                                    placeholder="Digite título ou artista (mín. 2 letras)..."
                                    autocomplete="off"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-purple-500 placeholder-gray-400">
                                <div x-show="open" x-cloak
                                    class="absolute top-full left-0 right-0 mt-1 max-h-72 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-xl z-50">
                                    <template x-if="query.trim().length > 0 && query.trim().length < 2">
                                        <div class="p-3 text-sm text-gray-500 dark:text-gray-400">Digite ao menos 2 caracteres para buscar.</div>
                                    </template>
                                    <template x-if="query.trim().length >= 2 && loading">
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">Buscando...</div>
                                    </template>
                                    <template x-if="query.trim().length >= 2 && !loading && results.length === 0">
                                        <div class="p-3 text-sm text-gray-500 dark:text-gray-400">Nenhuma música encontrada.</div>
                                    </template>
                                    <template x-if="results.length > 0">
                                        <ul class="py-1">
                                            <template x-for="song in results" :key="song.id">
                                                <li>
                                                    <button type="button"
                                                        @click="selectSong(song.id)"
                                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 flex items-center gap-2">
                                                        <x-icon name="music-note" class="w-4 h-4 text-purple-500 shrink-0" />
                                                        <span class="truncate" x-text="song.title + (song.artist ? ' — ' + song.artist : '')"></span>
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                </div>
                            </div>
                            <button type="submit" class="p-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white transition-colors shrink-0" title="Adicionar música selecionada">
                                <x-icon name="plus" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4" id="playlist-items">
                            @forelse($setlist->items as $item)
                            <div class="group flex flex-col rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 hover:border-purple-300 dark:hover:border-purple-500/50 transition-colors"
                                 data-id="{{ $item->id }}">
                                <div class="flex items-center justify-between p-4">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="cursor-move p-2 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors shrink-0" title="Arrastar">
                                            <x-icon name="menu" class="w-5 h-5" />
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex flex-col items-center justify-center shrink-0">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase">BPM</span>
                                            <span class="text-base font-black text-gray-900 dark:text-white leading-none">{{ optional($item->song)->bpm ?? '—' }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ optional($item->song)->title ?? 'Música removida' }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ optional($item->song)->artist ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="flex flex-col items-end">
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Tom</span>
                                            <select class="px-2.5 py-1.5 text-xs font-bold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-purple-600 dark:text-purple-400 rounded-lg focus:ring-2 focus:ring-purple-500 appearance-none"
                                                @change="updateItemKey({{ $item->id }}, $event.target.value)">
                                                @foreach(\Modules\Worship\App\Enums\MusicalKey::cases() as $key)
                                                    <option value="{{ $key->value }}" {{ ($item->override_key?->value ?? '') == $key->value ? 'selected' : '' }}>{{ $key->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <form action="{{ route('worship.admin.setlists.removeSong', $item->id) }}" method="POST" onsubmit="return confirm('Remover esta música do repertório?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Remover">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="px-4 pb-4 pt-0 flex items-center gap-2 border-t border-gray-100 dark:border-gray-700/50 mt-0">
                                    <x-icon name="information-circle" class="w-4 h-4 text-gray-400 shrink-0" />
                                    <input type="text" placeholder="Nota para a equipe (opcional)"
                                        value="{{ $item->arrangement_note }}"
                                        @blur="updateItemNote({{ $item->id }}, $event.target.value)"
                                        class="flex-1 bg-transparent border-0 text-sm text-gray-600 dark:text-gray-400 focus:ring-0 placeholder-gray-400 min-w-0">
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-16 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                                <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                                    <x-icon name="music-note" class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Nenhuma música no repertório</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-xs mb-4">Adicione músicas acima para montar o setlist e usar no console de projeção.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Roster & Notes (4 cols) -->
            <div class="xl:col-span-4 space-y-6">
                <!-- Roster Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <x-icon name="users" class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Escala</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $setlist->roster->count() }} músicos</p>
                            </div>
                        </div>
                        <a href="{{ route('worship.admin.rosters.print', $setlist->id) }}" target="_blank" rel="noopener" class="p-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors" title="Imprimir escala">
                            <x-icon name="printer" class="w-5 h-5" />
                        </a>
                    </div>

                    <form action="{{ route('worship.admin.rosters.store', $setlist->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <select name="user_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-emerald-500 appearance-none">
                            <option value="">Músico...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <select name="instrument_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-emerald-500 appearance-none">
                            <option value="">Instrumento / função...</option>
                            @foreach($instruments as $instrument)
                                <option value="{{ $instrument->id }}">{{ $instrument->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors">
                            Adicionar à escala
                        </button>
                    </form>

                    <div class="space-y-2">
                        @foreach($setlist->roster as $roster)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative shrink-0">
                                    @if(!empty(optional($roster->user)->avatar_url))
                                        <img src="{{ $roster->user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 dark:border-gray-600" alt="">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                            <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase">{{ Str::limit(optional($roster->user)->name ?? '?', 1, '') }}</span>
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800 bg-{{ $roster->status?->color() ?? 'gray' }}-500"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ optional($roster->user)->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ optional($roster->instrument)->name ?? '—' }}</p>
                                </div>
                            </div>
                            <form action="{{ route('worship.admin.rosters.destroy', $roster->id) }}" method="POST" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Remover">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <x-icon name="information-circle" class="w-5 h-5" />
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Orientações</h2>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 leading-relaxed">
                        {{ $setlist->description ?: 'Nenhuma orientação cadastrada para este culto.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('worshipSetlistManager', (setlistId, initialStatus) => ({
            setlistId: setlistId,
            editModalOpen: false,
            currentStatus: initialStatus,
            statusLabel: '{{ $setlist->status->label() }}',
            statusColor: '{{ $setlist->status->color() }}',

            async updateStatus(newStatus, label, color) {
                try {
                    await axios.patch(`{{ route('worship.admin.setlists.updateStatus', $setlist->id) }}`, {
                        status: newStatus
                    });
                    this.currentStatus = newStatus;
                    this.statusLabel = label;
                    this.statusColor = color;
                    // Optional: show toast
                } catch (error) {
                    console.error('Failed to update status', error);
                }
            },

            async updateItemKey(itemId, newKey) {
                try {
                    await axios.post(`/admin/worship/setlist-items/${itemId}/update`, {
                        override_key: newKey
                    });
                } catch (error) {
                    console.error('Failed to update key', error);
                }
            },

            async updateItemNote(itemId, note) {
                try {
                    await axios.post(`/admin/worship/setlist-items/${itemId}/update`, {
                        arrangement_note: note
                    });
                } catch (error) {
                    console.error('Failed to update note', error);
                }
            },

            async updateOrder(newOrder) {
                try {
                    await axios.post(`/admin/worship/setlists/${this.setlistId}/reorder`, {
                        items: newOrder
                    });
                } catch (error) {
                    console.error('Failed to update order', error);
                }
            }
        }));
    });

    // Optional: SortableJS integration for drag and drop
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('playlist-items');
        if (el && typeof Sortable !== 'undefined') {
            Sortable.create(el, {
                handle: '.cursor-move',
                animation: 150,
                onEnd: function (evt) {
                    const items = Array.from(el.querySelectorAll('[data-id]')).map((item, index) => ({
                        id: item.dataset.id,
                        order: index + 1
                    }));

                    const component = Alpine.$data(document.querySelector('[x-data^="worshipSetlistManager"]'));
                    if (component) component.updateOrder(items);
                }
            });
        }
    });
</script>
@endpush
@endsection

