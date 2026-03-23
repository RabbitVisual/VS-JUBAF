@extends('memberpanel::components.layouts.master')

@section('page-title', (\Modules\Intercessor\App\Services\IntercessorSettings::get('room_label') ?? 'Sala de Oração') . ' - ' . $request->title)
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12"
     x-data="prayerTimer()"
     @open-bible-modal.window="openBibleModal($event.detail.ref)">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            @php
                $roomLabel = \Modules\Intercessor\App\Services\IntercessorSettings::get('room_label') ?? 'Sala de Oração';
            @endphp
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li>Intercessão</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-indigo-600 dark:text-indigo-400">{{ $roomLabel }}</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $roomLabel }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Foco total em intercessão e clamor.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('member.intercessor.room.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Voltar ao Mural
             </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative" data-tour="intercessor-room-detail">
        <div class="p-8 md:p-16 relative z-10">

            <!-- Category & Info -->
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <span class="px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-indigo-100/50">
                    {{ $request->category->name }}
                </span>
                @if($request->urgency_level === 'critical')
                    <span class="px-4 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100/50 animate-pulse">
                        Urgente
                    </span>
                @endif
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-auto">Publicado {{ $request->created_at->diffForHumans() }}</span>
            </div>

            <!-- Title -->
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-10 leading-tight tracking-tight">
                {{ $request->title }}
            </h2>

            <!-- Description -->
            <div class="prose dark:prose-invert prose-lg max-w-none mb-16 text-gray-600 dark:text-gray-300 leading-relaxed font-medium bg-gray-50 dark:bg-gray-900/50 p-8 md:p-12 rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50 italic">
                 {!! nl2br(\Modules\Intercessor\App\Services\BibleParser::parse($request->description)) !!}
            </div>

            <!-- Author info at bottom of content -->
            <div class="flex items-center gap-4 mb-16 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl w-fit shadow-sm">
                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black overflow-hidden">
                     @if(!$request->is_anonymous && $request->user && $request->user->photo)
                        <img src="{{ Storage::url($request->user->photo) }}" alt="{{ $request->user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ substr($request->is_anonymous ? 'A' : ($request->user->name ?? 'A'), 0, 1) }}
                    @endif
                </div>
                <div>
                    <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $request->is_anonymous ? 'Anônimo' : $request->user->name ?? 'Usuário' }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Solicitante</p>
                </div>
            </div>

            <!-- Focus Actions Section -->
            <div class="p-1 md:p-12 bg-gray-900 rounded-[3rem] border border-gray-800 shadow-2xl relative overflow-hidden group">
                 <!-- Aesthetic Glows -->
                 <div class="absolute inset-0 opacity-30 pointer-events-none">
                    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-600 rounded-full blur-[120px]"></div>
                    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-600 rounded-full blur-[100px]"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center max-w-2xl mx-auto py-10">
                    @php
                        $viewer = auth()->user();
                        $visibility = \Modules\Intercessor\App\Services\IntercessorSettings::get('show_intercessor_names');

                        $canSeeNames = false;
                        if ($visibility === 'all') {
                            $canSeeNames = true;
                        } elseif ($visibility === 'author_only' && $viewer && $viewer->id === $request->user_id) {
                            $canSeeNames = true;
                        } elseif ($visibility === 'intercessors_only' && $viewer && $viewer->role && in_array($viewer->role->slug, ['intercessor', 'prayer_team', 'pastor', 'admin'])) {
                            $canSeeNames = true;
                        }
                    @endphp

                    @if(auth()->id() == $request->user_id)
                        <div class="space-y-4">
                            <div class="w-20 h-20 bg-indigo-500/10 rounded-3xl flex items-center justify-center mx-auto border border-indigo-500/20">
                                <x-icon name="heart" style="duotone" class="w-10 h-10 text-indigo-500" />
                            </div>
                            <h3 class="text-3xl font-black text-white uppercase tracking-tight">Este é o seu pedido</h3>
                            <p class="text-gray-400 font-medium text-lg leading-relaxed">
                                Já existem {{ $request->commitments->count() }} intercessores clamando com você neste exato momento.
                            </p>

                            @if($canSeeNames && $request->commitments->count() > 0)
                                <div class="mt-6 text-xs text-gray-300 font-medium uppercase tracking-widest">
                                    <p class="mb-2 opacity-70">Intercessores envolvidos:</p>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @foreach($request->commitments->take(6) as $commitment)
                                            <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px]">
                                                {{ \Illuminate\Support\Str::limit($commitment->user->name ?? 'Intercessor', 18) }}
                                            </span>
                                        @endforeach
                                        @if($request->commitments->count() > 6)
                                            <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px]">
                                                +{{ $request->commitments->count() - 6 }} outros
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Timer and State -->
                         <div x-show="!isPraying" class="space-y-8">
                            <div class="w-24 h-24 bg-white/5 rounded-4xl flex items-center justify-center mx-auto border border-white/10 shadow-inner">
                                <x-icon name="hand" style="duotone" class="w-12 h-12 text-indigo-400" />
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-white uppercase tracking-tight mb-3">Entrar em Clamor</h3>
                                <p class="text-gray-400 font-medium text-lg leading-relaxed">Ao iniciar, você dedica um tempo exclusivo para interceder por esta vida.</p>
                            </div>
                            <form action="{{ route('member.intercessor.room.commit', $request) }}" method="POST" @submit.prevent="startPrayer" data-tour="intercessor-room-commit">
                                @csrf
                                <button type="submit" class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-4xl font-black text-base uppercase tracking-widest transition-all hover:scale-105 shadow-2xl shadow-indigo-600/40">
                                    Iniciar Intercessão
                                </button>
                            </form>
                        </div>

                        <div x-show="isPraying" x-cloak class="space-y-10 py-6">
                            <div class="relative inline-block">
                                <div class="absolute inset-0 bg-emerald-500 rounded-full blur-2xl opacity-20 animate-pulse"></div>
                                <div class="relative w-40 h-40 border-4 border-emerald-500/20 rounded-full flex flex-col items-center justify-center">
                                    <span class="text-emerald-500 text-3xl font-black tracking-tighter font-mono" x-text="formatTime(timer)"></span>
                                    <span class="text-[10px] font-black text-emerald-500/50 uppercase tracking-widest mt-1">Tempo de Clamor</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-white uppercase tracking-tight mb-2">Você está intercedendo</h3>
                                <p class="text-emerald-400/80 font-bold uppercase tracking-widest text-xs">Mantenha o foco espiritual e a conexão divina.</p>
                            </div>
                            <button type="button" @click="endPrayer" class="px-12 py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-4xl font-black text-base uppercase tracking-widest transition-all hover:scale-105 shadow-2xl shadow-emerald-500/30">
                                Concluir Período
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Interactions & Bible Section -->
            <div class="mt-20 grid grid-cols-1 lg:grid-cols-3 gap-12 pt-20 border-t border-gray-100 dark:border-gray-700">
                <!-- Left: Bible Interaction (Focus Tool) -->
                <div class="lg:col-span-1 border-r border-gray-50 dark:border-gray-700/50 pr-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-500">
                            <x-icon name="book-open" style="duotone" class="w-5 h-5" />
                        </div>
                        Ferramentas de Fé
                    </h3>

                    <div x-data="bibleSelector()" class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50">
                             <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-6">Selecione uma Palavra de Ânimo</p>
                             @include('intercessor::components.bible-selector')

                             <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                                <button
                                    @click="document.dispatchEvent(new CustomEvent('insert-verse', { detail: referenceString }))"
                                    :disabled="!referenceString"
                                    class="w-full py-4 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 font-black text-xs rounded-2xl border border-indigo-100 dark:border-indigo-900/50 shadow-sm hover:bg-indigo-50 transition-all uppercase tracking-widest disabled:opacity-50"
                                >
                                    Usar Referência
                                </button>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Middle/Right: Interactions List & Form -->
                <div class="lg:col-span-2 space-y-12">
                     <h3 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-4">
                        Relatos e Palavras
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-black text-gray-400 uppercase tracking-widest">
                            {{ $request->interactions->count() }}
                        </span>
                    </h3>

                    <!-- Quick Form -->
                    @if(auth()->id() !== $request->user_id)
                    <div class="bg-indigo-600 rounded-[2.5rem] p-10 shadow-2xl shadow-indigo-600/30">
                        <form action="{{ route('member.intercessor.room.interact', $request) }}" method="POST" id="interactionForm">
                            @csrf
                            <input type="hidden" name="type" value="comment">
                            <div class="space-y-6">
                                <textarea
                                    name="body"
                                    id="interaction_body"
                                    rows="4"
                                    required
                                    class="w-full px-8 py-6 bg-indigo-700/50 border border-indigo-400/30 rounded-4xl text-white placeholder-indigo-300 focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium leading-relaxed resize-none"
                                    placeholder="Compartilhe como você está orando ou uma promessa bíblica..."
                                ></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-10 py-4 bg-white text-indigo-600 font-black text-xs rounded-2xl transition-all shadow-xl hover:scale-105 active:scale-95 uppercase tracking-widest">
                                        Enviar Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-gray-100 dark:bg-gray-800/50 rounded-[2.5rem] p-8 border border-dashed border-gray-200 dark:border-gray-700 text-center">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">
                            Como autor, você pode responder aos intercessores individualmente nos comentários abaixo.
                        </p>
                    </div>
                    @endif

                    <!-- List -->
                    <div class="space-y-8" x-data="{ replyTo: null }">
                        @forelse($request->interactions->where('type', 'comment')->where('parent_id', null)->sortByDesc('created_at') as $interaction)
                             <div class="space-y-4">
                                <div class="flex gap-6 group">
                                    <div class="shrink-0 pt-1">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-sm font-black text-gray-400 uppercase shadow-inner overflow-hidden">
                                            @if($interaction->user->photo)
                                                <img src="{{ Storage::url($interaction->user->photo) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($interaction->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                                                {{ $interaction->user->name }}
                                                @if($interaction->user->id === $request->user_id)
                                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[8px] rounded uppercase font-black">Autor</span>
                                                @endif
                                            </span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $interaction->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-4xl rounded-tl-none p-6 border border-gray-100 dark:border-gray-700/50 relative group/box">
                                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed font-medium italic">
                                                "{!! \Modules\Intercessor\App\Services\BibleParser::parse($interaction->body) !!}"
                                            </p>

                                            @if(auth()->id() === $request->user_id && $interaction->user_id !== auth()->id())
                                                @php
                                                    $alreadyReplied = $request->interactions()
                                                        ->where('user_id', auth()->id())
                                                        ->whereHas('parent', function($q) use ($interaction) {
                                                            $q->where('user_id', $interaction->user_id);
                                                        })->exists();
                                                @endphp

                                                @if(!$alreadyReplied)
                                                <button @click="replyTo = (replyTo === {{ $interaction->id }} ? null : {{ $interaction->id }})"
                                                        class="absolute -bottom-3 right-6 px-4 py-1.5 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full opacity-0 group-hover/box:opacity-100 transition-all shadow-lg hover:bg-indigo-700 active:scale-95">
                                                    Responder
                                                </button>
                                                @endif
                                            @endif
                                        </div>

                                        <!-- Reply Form -->
                                        <div x-show="replyTo === {{ $interaction->id }}" x-cloak x-collapse class="mt-4 ml-4">
                                            <form action="{{ route('member.intercessor.room.interact', $request) }}" method="POST" class="flex gap-3">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $interaction->id }}">
                                                <input type="text" name="body" required placeholder="Sua resposta..."
                                                       class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-indigo-500 outline-none text-gray-700 dark:text-white">
                                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-md">
                                                    Enviar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Replies -->
                                @foreach($interaction->replies as $reply)
                                <div class="flex gap-4 ml-16">
                                    <div class="shrink-0">
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-500/20 flex items-center justify-center text-[10px] font-black text-blue-500 uppercase overflow-hidden">
                                            @if($reply->user->photo)
                                                <img src="{{ Storage::url($reply->user->photo) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($reply->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-blue-50/30 dark:bg-blue-900/10 rounded-2xl p-4 border border-blue-100/30 dark:border-blue-500/10">
                                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-tighter mb-1">{{ $reply->user->name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium italic leading-relaxed">
                                            "{!! \Modules\Intercessor\App\Services\BibleParser::parse($reply->body) !!}"
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                             </div>
                        @empty
                             <div class="text-center py-20 bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800/50">
                                <x-icon name="chat" style="duotone" class="w-12 h-12 mx-auto text-gray-200 mb-4" />
                                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nenhuma interação ainda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bible Modal -->
    <div
        x-show="bibleModal.isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-100 overflow-y-auto"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click.self="bibleModal.isOpen = false">
                <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md" @click="bibleModal.isOpen = false"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="bibleModal.isOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-800 z-50"
            >
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                            <x-icon name="book-bible" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight" x-text="bibleModal.reference"></h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mt-1">Palavra de Deus</p>
                        </div>
                    </div>
                    <button @click="bibleModal.isOpen = false" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="xmark" style="duotone" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-8 py-10 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <!-- Loading State -->
                    <div x-show="bibleModal.loading" class="py-20 text-center">
                        <div class="inline-block w-12 h-12 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
                        <p class="mt-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Carregando Escrituras...</p>
                    </div>

                    <!-- Error State -->
                    <div x-show="bibleModal.error && !bibleModal.loading" class="py-12 text-center bg-red-50 dark:bg-red-900/20 rounded-3xl border border-red-100 dark:border-red-900/30">
                        <x-icon name="circle-exclamation" style="duotone" class="w-12 h-12 mx-auto text-red-500 mb-4" />
                        <h4 class="text-sm font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-2" x-text="bibleModal.error"></h4>
                        <p class="text-xs text-red-500/70 font-medium">Tente buscar outra referência.</p>
                    </div>

                    <!-- Content -->
                    <div x-show="!bibleModal.loading && !bibleModal.error" class="space-y-6">
                        <template x-for="verse in bibleModal.verses" :key="verse.verse_number">
                            <div class="flex gap-4">
                                <span class="text-xs font-black text-indigo-500/50 mt-1.5" x-text="verse.verse_number"></span>
                                <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed font-serif" x-text="verse.text"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between">
                    <a :href="bibleModal.fullUrl" class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                        <x-icon name="book-open" style="duotone" class="w-4 h-4" />
                        Ler Capítulo Completo
                    </a>
                    <button @click="bibleModal.isOpen = false" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all">
                        Amém
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('prayerTimer', () => ({
            timer: 0,
            isPraying: false,
            interval: null,

            init() {
                document.addEventListener('insert-verse', (e) => {
                    const textarea = document.getElementById('interaction_body');
                    if (textarea) {
                        const reference = '@' + e.detail;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const text = textarea.value;
                        textarea.value = text.substring(0, start) + reference + text.substring(end);
                        // Trigger input event for bound models if any
                        textarea.dispatchEvent(new Event('input'));
                    }
                });
            },

            // Bible Modal Logic
            bibleModal: {
                isOpen: false,
                loading: false,
                reference: '',
                verses: [],
                fullUrl: '',
                error: null
            },

            openBibleModal(ref) {
                this.bibleModal.isOpen = true;
                this.bibleModal.loading = true;
                this.bibleModal.reference = ref;
                this.bibleModal.verses = [];
                this.bibleModal.error = null;

                fetch(`/api/v1/bible/find?ref=${encodeURIComponent(ref)}`)
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.data) {
                            this.bibleModal.verses = resp.data.verses || [];
                            this.bibleModal.fullUrl = resp.data.full_chapter_url || '';
                        } else {
                            this.bibleModal.error = resp.message || 'Referência não encontrada.';
                        }
                    })
                    .catch(err => {
                        this.bibleModal.error = "Erro ao carregar versículo.";
                    })
                    .finally(() => {
                        this.bibleModal.loading = false;
                    });
            },

            startPrayer() {
                // Perform AJAX request to register commitment
                fetch('{{ route('member.intercessor.room.commit', $request) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (response.ok) {
                        this.isPraying = true;
                        this.interval = setInterval(() => {
                            this.timer++;
                        }, 1000);
                        // Optional: Show success toast if needed
                    }
                })
                .catch(error => {
                    console.error('Erro ao iniciar intercessão:', error);
                });
            },

            endPrayer() {
                this.isPraying = false;
                clearInterval(this.interval);

                // Send duration to backend
                fetch('{{ route('member.intercessor.room.finish', $request) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        duration: this.timer
                    })
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const error = (data && data.message) || response.statusText;
                        throw new Error(error);
                    }

                    if(data && data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erro detalhado ao finalizar:', error);
                    alert('Erro ao salvar tempo de oração: ' + error.message);
                });
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
        }))
    })

    function bibleSelector() {
        return {
            versions: [],
            books: [],
            chapters: [],
            allChapterVerses: [],

            selected: {
                versionId: null,
                bookId: null,
                chapterId: null,
                chapterNumber: null,
                verseRange: ''
            },

            previewText: '',
            referenceString: '',

            init() {
                fetch('/api/v1/bible/versions', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(resp => {
                    const data = resp.data || [];
                    this.versions = data;
                    if (this.versions.length > 0) {
                        this.selected.versionId = this.versions[0].id;
                        this.fetchBooks();
                    }
                });
            },

            fetchBooks() {
                if (!this.selected.versionId) return;
                fetch(`/api/v1/bible/books?version_id=${this.selected.versionId}`)
                    .then(res => res.json())
                    .then(resp => { const data = resp.data || []; this.books = data; this.selected.bookId = ''; this.chapters = []; });
            },

            fetchChapters() {
                if (!this.selected.bookId) return;
                fetch(`/api/v1/bible/chapters?book_id=${this.selected.bookId}`)
                    .then(res => res.json())
                    .then(resp => { const data = resp.data || []; this.chapters = data; this.selected.chapterId = ''; this.allChapterVerses = []; });
            },

            fetchVerses() {
                if (!this.selected.chapterId) return;
                let chapter = this.chapters.find(c => c.id == this.selected.chapterId);
                this.selected.chapterNumber = chapter ? chapter.chapter_number : '?';
                fetch(`/api/v1/bible/verses?chapter_id=${this.selected.chapterId}`)
                    .then(res => res.json())
                    .then(resp => { this.allChapterVerses = resp.data || []; this.generatePreview(); });
            },

            generatePreview() {
                if (!this.selected.chapterId || this.allChapterVerses.length === 0) {
                    this.previewText = '';
                    this.referenceString = '';
                    return;
                }
                let book = this.books.find(b => b.id == this.selected.bookId);
                let bookName = book ? book.name : '';
                this.referenceString = `${bookName} ${this.selected.chapterNumber}:${this.selected.verseRange || '1-' + this.allChapterVerses.length}`;
            },

            appendCitation() {
                if (!this.referenceString) return;
                document.dispatchEvent(new CustomEvent('insert-verse', { detail: this.referenceString }));
            }
        }
    }
</script>
@endpush
@endsection
