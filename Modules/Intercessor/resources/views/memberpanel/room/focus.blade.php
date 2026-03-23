@extends('memberpanel::components.layouts.master')

@section('page-title', (\Modules\Intercessor\App\Services\IntercessorSettings::get('room_label') ?? 'Sala de Oração') . ' - ' . $request->title)
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
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
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Intercessão focada e acompanhamento espiritual.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('member.intercessor.room.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Voltar ao Mural
             </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Request Detail Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-8 md:p-10">
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-indigo-100/50">
                            {{ $request->category->name }}
                        </span>
                        @if($request->urgency_level === 'critical')
                            <span class="px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-100/50 animate-pulse">
                                Urgente
                            </span>
                        @endif
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-auto">Publicado {{ $request->created_at->diffForHumans() }}</span>
                    </div>

                    <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight uppercase">
                        {{ $request->title }}
                    </h2>

                    <div class="prose dark:prose-invert prose-lg max-w-none text-gray-600 dark:text-gray-300 leading-relaxed font-medium bg-gray-50 dark:bg-gray-900/40 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 italic mb-10">
                         {!! nl2br(\Modules\Intercessor\App\Services\BibleParser::parse($request->description)) !!}
                    </div>

                    <!-- Testimony (if exists) -->
                    @if($request->status === 'answered' && $request->testimony)
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-3xl border border-emerald-100 dark:border-emerald-800 p-8 mb-10 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-6 opacity-10">
                                <x-icon name="sparkles" class="w-20 h-20 text-emerald-500" />
                            </div>
                            <h3 class="text-sm font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" /> Relato da Vitória
                            </h3>
                            <div class="text-emerald-900 dark:text-emerald-100 font-bold italic leading-relaxed">
                                "{!! nl2br(e($request->testimony)) !!}"
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4 pt-8 border-t border-gray-50 dark:border-gray-700/50">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black overflow-hidden shadow-inner">
                            @if(!$request->is_anonymous && $request->user && $request->user->photo)
                                <img src="{{ Storage::url($request->user->photo) }}" class="h-full w-full object-cover">
                            @else
                                {{ substr($request->is_anonymous ? 'A' : ($request->user->name ?? 'A'), 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Solicitante</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ $request->is_anonymous ? 'Anônimo' : $request->user->name ?? 'Usuário' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactions Section -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-10">
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-3">
                    <x-icon name="chat" class="w-6 h-6 text-gray-400" />
                    Interações
                    <span class="px-2.5 py-1 text-[10px] font-black bg-gray-100 dark:bg-gray-700 rounded-full text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ $request->interactions->count() }}</span>
                </h3>

                <!-- Interaction Form -->
                @if(\Modules\Intercessor\App\Services\IntercessorSettings::get('allow_comments'))
                    @if(Auth::id() !== $request->user_id)
                        <div class="mb-12 bg-gray-50 dark:bg-gray-900/30 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50" x-data="bibleSelector()">
                            <h4 class="text-[10px] font-black text-gray-900 dark:text-white mb-6 uppercase tracking-widest text-center">Deixe uma palavra de ânimo</h4>

                            @include('intercessor::components.bible-selector')

                            <form action="{{ route('member.intercessor.room.interact', $request) }}" method="POST" class="mt-6">
                                @csrf
                                <input type="hidden" name="type" value="comment">
                                <textarea name="body" x-model="commentBody" rows="4"
                                    class="w-full px-6 py-5 border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium leading-relaxed resize-none"
                                    placeholder="Escreva sua oração ou promessa bíblica..." required></textarea>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] rounded-xl transition-all shadow-lg shadow-indigo-600/20 uppercase tracking-widest">
                                        Enviar Mensagem
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endif

                <!-- Threaded Comments -->
                <div class="space-y-10">
                    @forelse($request->interactions->where('type', 'comment')->whereNull('parent_id')->sortByDesc('created_at') as $interaction)
                        <div class="flex flex-col md:flex-row gap-6 group" x-data="{ replyOpen: false }">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-400 font-black shadow-inner shrink-0 overflow-hidden">
                                @if($interaction->user->photo)
                                    <img src="{{ Storage::url($interaction->user->photo) }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($interaction->user->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="flex-1 w-full space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $interaction->user->name }}</span>
                                        @if($interaction->user_id === $request->user_id)
                                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[8px] rounded uppercase font-black">Autor</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $interaction->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-3xl rounded-tl-none p-6 border border-gray-100 dark:border-gray-700/50 text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                    {!! \Modules\Intercessor\App\Services\BibleParser::parse($interaction->body) !!}
                                </div>

                                <div class="flex items-center gap-4 pl-2">
                                    <button @click="replyOpen = !replyOpen" class="text-[10px] font-black text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center gap-1">
                                        <x-icon name="reply" class="w-3 h-3" /> Responder
                                    </button>
                                </div>

                                <!-- Reply Form -->
                                <div x-show="replyOpen" x-cloak x-transition class="mt-4">
                                    <form action="{{ route('member.intercessor.room.interact', $request) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="comment">
                                        <input type="hidden" name="parent_id" value="{{ $interaction->id }}">
                                        <div class="flex gap-2">
                                            <input type="text" name="body" required class="flex-1 px-4 py-3 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 font-medium" placeholder="Ecreva sua resposta...">
                                            <button type="submit" class="px-5 py-3 bg-indigo-600 text-white text-[10px] font-black rounded-xl hover:bg-indigo-700 transition-all uppercase">OK</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Replies -->
                                @if($interaction->replies->count() > 0)
                                    <div class="mt-6 space-y-6 pt-6 border-t border-gray-50 dark:border-gray-800/50">
                                        @foreach($interaction->replies as $reply)
                                            <div class="flex items-start gap-4">
                                                <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-[10px] font-black text-gray-400 overflow-hidden">
                                                    @if($reply->user->photo)
                                                        <img src="{{ Storage::url($reply->user->photo) }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $reply->user->name }}</span>
                                                        @if($reply->user_id === $request->user_id)
                                                            <span class="text-[8px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded uppercase font-black">Autor</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-900/40 p-4 rounded-2xl rounded-tl-none inline-block border border-gray-100 dark:border-gray-700/50 font-medium">
                                                        {!! \Modules\Intercessor\App\Services\BibleParser::parse($reply->body) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400 font-medium italic">
                            Nenhuma interação registrada ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Commitment Card -->
            <div class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-600/20">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <x-icon name="heart" class="w-48 h-48 -right-12 -top-12 rotate-12" />
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6 border border-white/20">
                        <x-icon name="hand" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-tight mb-2">Clamor Ativo</h3>
                    <p class="text-indigo-100 text-sm font-medium mb-8">Junte-se a {{ $request->commitments->count() }} intercessores clamando por este pedido.</p>

                    @if(auth()->id() == $request->user_id)
                        <div class="bg-white/10 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest">
                            Seu Pedido
                        </div>
                    @elseif($request->commitments->where('user_id', Auth::id())->count() > 0)
                        <div class="bg-emerald-500 py-4 rounded-2xl font-black text-xs flex items-center justify-center gap-2 uppercase tracking-widest shadow-lg">
                            <x-icon name="check-circle" class="w-5 h-5" /> Você está orando
                        </div>
                    @else
                        <form action="{{ route('member.intercessor.room.commit', $request) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-white text-indigo-600 font-black rounded-2xl transition-all hover:scale-105 active:scale-95 uppercase tracking-widest text-xs shadow-xl">
                                Entrar em Clamor
                            </button>
                        </form>
                    @endif

                    <div class="mt-8 flex justify-center -space-x-3 overflow-hidden">
                        @foreach($request->commitments->take(8) as $commitment)
                            <div class="h-10 w-10 rounded-xl ring-4 ring-indigo-600 bg-white/20 flex items-center justify-center text-[10px] font-black text-white overflow-hidden shadow-sm" title="{{ $commitment->user->name }}">
                                @if($commitment->user->photo)
                                    <img src="{{ Storage::url($commitment->user->photo) }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($commitment->user->name, 0, 1) }}
                                @endif
                            </div>
                        @endforeach
                        @if($request->commitments->count() > 8)
                            <div class="h-10 w-10 rounded-xl ring-4 ring-indigo-600 bg-white/10 backdrop-blur-md flex items-center justify-center text-[10px] font-black text-white">
                                +{{ $request->commitments->count() - 8 }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Devotion Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="absolute inset-0 bg-indigo-50 dark:bg-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center mb-6 text-gray-400">
                        <x-icon name="book-open" class="w-5 h-5" />
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic leading-relaxed mb-4">
                        "Clama a mim, e responder-te-ei, e anunciar-te-ei coisas grandes e firmes que não sabes."
                    </p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jeremias 33:3</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            commentBody: '',

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
                this.books = [];
                this.chapters = [];
                this.selected.bookId = null;
                this.selected.chapterId = null;
                this.previewText = '';

                fetch(`/api/v1/bible/books?version_id=${this.selected.versionId}`)
                    .then(res => res.json())
                    .then(resp => { this.books = resp.data || []; });
            },

            fetchChapters() {
                if (!this.selected.bookId) return;
                this.chapters = [];
                this.selected.chapterId = null;
                this.previewText = '';

                fetch(`/api/v1/bible/chapters?book_id=${this.selected.bookId}`)
                    .then(res => res.json())
                    .then(resp => { this.chapters = resp.data || []; });
            },

            fetchVerses() {
                if (!this.selected.chapterId) return;

                let chapter = this.chapters.find(c => c.id == this.selected.chapterId);
                this.selected.chapterNumber = chapter ? chapter.chapter_number : '?';

                fetch(`/api/v1/bible/verses?chapter_id=${this.selected.chapterId}`)
                    .then(res => res.json())
                    .then(resp => {
                        this.allChapterVerses = resp.data || [];
                        this.selected.verseRange = '';
                        this.generatePreview();
                    });
            },

            generatePreview() {
                if (!this.selected.chapterId || this.allChapterVerses.length === 0) {
                    this.previewText = '';
                    return;
                }

                let versesToUse = [];
                let range = this.selected.verseRange.trim();

                if (!range) {
                    versesToUse = this.allChapterVerses;
                } else {
                    let match = range.match(/^(\d+)(?:-(\d+))?$/);
                    if (match) {
                        let start = parseInt(match[1]);
                        let end = match[2] ? parseInt(match[2]) : start;

                        versesToUse = this.allChapterVerses.filter(v =>
                            v.verse_number >= start && v.verse_number <= end
                        );
                    } else {
                        versesToUse = this.allChapterVerses;
                    }
                }

                this.previewText = versesToUse.map(v => v.text).join(' ');

                let book = this.books.find(b => b.id == this.selected.bookId);
                let bookName = book ? book.name : '';
                let refVerses = range ? range : '1-' + this.allChapterVerses.length;

                this.referenceString = `${bookName} ${this.selected.chapterNumber}:${refVerses}`;
            },

            appendCitation() {
                if (!this.previewText) return;

                // Insert only the reference shortcode
                let citation = `@${this.referenceString}`;

                if (this.commentBody.trim() !== '') {
                    this.commentBody += ' ' + citation;
                } else {
                    this.commentBody = citation;
                }
            }
        }
    }
</script>
@endsection

