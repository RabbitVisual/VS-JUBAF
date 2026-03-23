@extends('memberpanel::components.layouts.master')

@section('title', 'Aula: ' . $lesson->title)

@section('content')
@php $lessonStates = $lessonStates ?? []; @endphp
<!-- Focus Mode Layout: Full Screen Overlay -->
<div class="fixed inset-0 bg-slate-950 flex flex-col z-[60] overflow-hidden" x-data="{ ...lmsPlayer(), mobileDrawerOpen: false, ...eliasChat() }">

    <!-- Premium Header -->
    <header class="h-16 sm:h-20 bg-slate-900/80 backdrop-blur-xl border-b border-white/5 flex items-center justify-between px-4 sm:px-6 md:px-8 lg:px-12 shrink-0 z-30 shadow-2xl safe-area-inset-top">
        <div class="flex items-center gap-3 sm:gap-6">
            <a href="{{ route('memberpanel.ebd.student.index') }}"
               class="group w-10 h-10 bg-white/5 hover:bg-white/10 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all border border-white/5 touch-manipulation active:scale-95 shrink-0">
                <x-icon name="arrow-left-to-line" style="duotone" class="w-4 h-4 font-black" />
            </a>
            <div class="h-8 w-px bg-white/10 hidden md:block"></div>
            <!-- Mobile: menu button to open curriculum + materials -->
            <button type="button" @click="mobileDrawerOpen = true" class="xl:hidden flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 border border-white/5 text-gray-300 hover:text-white hover:bg-white/10 transition-all touch-manipulation">
                <x-icon name="bars" style="duotone" class="w-4 h-4" />
                <span class="text-xs font-bold uppercase tracking-wider">Aulas e Materiais</span>
            </button>
            <div class="hidden md:block min-w-0">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-amber-500/60 leading-none mb-1">Módulo EBD</p>
                <h1 class="text-white font-black text-lg tracking-tight italic truncate max-w-sm">{{ $lesson->title }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-4 sm:gap-6 shrink-0">
             <!-- Completion Status: habilitado só após tempo mínimo na aula -->
             <button type="button" @click="canComplete && !isCompleted && markAsComplete()"
                :disabled="!canComplete || isCompleted"
                class="flex items-center gap-2 sm:gap-3 px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-2xl active:scale-[0.98] border touch-manipulation focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-70 disabled:cursor-not-allowed"
                :class="isCompleted ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20 cursor-default' : (canComplete ? 'bg-amber-600 text-white border-amber-500 hover:bg-amber-500 shadow-amber-600/20' : 'bg-slate-700 text-slate-400 border-slate-600 cursor-not-allowed')">
                <template x-if="isCompleted">
                    <span class="flex items-center gap-2"><x-icon name="shield-check" style="duotone" class="w-4 h-4" /> Concluída</span>
                </template>
                <template x-if="!isCompleted && canComplete">
                    <span class="flex items-center gap-2 font-black">Finalizar Aula <x-icon name="flag-checkered" style="duotone" class="w-4 h-4" /></span>
                </template>
                <template x-if="!isCompleted && !canComplete">
                    <span class="flex items-center gap-2">Disponível em <span x-text="countdownLabel"></span></span>
                </template>
             </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <!-- Left Column: Curriculum (Desktop) -->
        <aside class="w-96 bg-slate-900 border-r border-white/5 flex flex-col shrink-0 overflow-hidden hidden xl:flex">
            <div class="p-8 border-b border-white/5 bg-slate-950/30">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-500 block mb-2">Classe Atual</span>
                <h2 class="text-white font-black text-xl leading-none italic tracking-tighter">{{ $lesson->ebdClass?->name ?? 'EBD' }}</h2>
                @if(isset($courseProgressPercent) && $courseProgressPercent >= 0)
                    <div class="mt-4">
                        <div class="flex justify-between text-[10px] font-bold text-gray-400 mb-1">
                            <span>Progresso do curso</span>
                            <span>{{ $courseProgressPercent }}%</span>
                        </div>
                        <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: {{ $courseProgressPercent }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-3">
                @foreach($classLessons as $l)
                @php $state = $lessonStates[$l->id] ?? []; $canAccess = $state['can_access'] ?? false; $isCompleted = $state['is_completed'] ?? false; $isPending = $state['is_pending'] ?? false; $isCurrent = $state['is_current'] ?? false; $availableByDate = $state['available_by_date'] ?? false; @endphp
                @if($canAccess)
                <a href="{{ route('memberpanel.ebd.student.classroom.player', $l->id) }}"
                   class="relative group block p-5 rounded-3xl border-2 transition-all {{ $isCurrent ? 'bg-amber-600/10 border-amber-600/30' : 'bg-white/5 border-transparent hover:bg-white/10' }}">

                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Aula {{ $loop->iteration }}</span>
                        @if($isCompleted)
                        <div class="w-5 h-5 bg-emerald-500/20 rounded-full flex items-center justify-center">
                            <x-icon name="circle-check" class="w-3 h-3 text-emerald-500" />
                        </div>
                        @elseif($isPending)
                        <span class="text-[8px] font-black text-amber-500 uppercase tracking-wider">Pendente</span>
                        @else
                        <div class="w-5 h-5 bg-white/5 rounded-full flex items-center justify-center">
                            <x-icon name="circle" class="w-3 h-3 text-gray-700" />
                        </div>
                        @endif
                    </div>

                    <h4 class="text-sm font-black tracking-tight {{ $isCurrent ? 'text-amber-500' : 'text-gray-300 group-hover:text-white' }}">{{ $l->title }}</h4>

                    @if($isCurrent)
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-amber-600 rounded-r-full shadow-[0_0_15px_rgba(245,158,11,0.5)]"></div>
                    @endif
                </a>
                @else
                <div class="relative block p-5 rounded-3xl border-2 border-white/5 bg-white/[0.02] cursor-not-allowed">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Aula {{ $loop->iteration }}</span>
                        @if($availableByDate)
                        <span class="text-[8px] font-black text-amber-500/80 uppercase tracking-wider">Conclua a anterior</span>
                        @else
                        <x-icon name="lock" class="w-3 h-3 text-gray-600" />
                        @endif
                    </div>
                    <h4 class="text-sm font-black text-gray-600 truncate">{{ $l->title }}</h4>
                    @if(!$availableByDate)
                    <p class="text-[9px] text-gray-600 mt-1">Em breve</p>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </aside>

        <!-- Main Content: Player & Article -->
        <main class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden bg-slate-950 relative custom-scrollbar">
            <div class="max-w-5xl mx-auto p-4 sm:p-6 md:p-8 lg:p-14 xl:p-20 space-y-10 sm:space-y-16 overflow-x-hidden">

                <!-- Video Player Container: YouTube/Vimeo embed or local video -->
                @if($lesson->video_embed_url)
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-linear-to-tr from-amber-600/30 to-blue-600/30 rounded-[3rem] blur-2xl group-hover:blur-3xl transition-all opacity-50"></div>
                        <div class="relative aspect-video bg-black rounded-[2.5rem] overflow-hidden shadow-[0_45px_100px_rgba(0,0,0,0.8)] border border-white/10 ring-1 ring-white/10">
                            <iframe src="{{ $lesson->video_embed_url }}" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Vídeo da aula"></iframe>
                        </div>
                    </div>
                @elseif($lesson->media && $lesson->media->where('type', 'local_video')->count() > 0)
                    @php $video = $lesson->media->where('type', 'local_video')->first(); @endphp
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-linear-to-tr from-amber-600/30 to-blue-600/30 rounded-[3rem] blur-2xl group-hover:blur-3xl transition-all opacity-50"></div>
                        <div class="relative aspect-video bg-black rounded-[2.5rem] overflow-hidden shadow-[0_45px_100px_rgba(0,0,0,0.8)] border border-white/10 ring-1 ring-white/10">
                            <video controls class="w-full h-full" poster="{{ $lesson->thumbnail_url }}">
                                <source src="{{ Storage::url($video->path) }}" type="video/mp4">
                                Seu navegador não suporta vídeos.
                            </video>
                        </div>
                    </div>
                @elseif($lesson->thumbnail_url)
                    <div class="relative rounded-[2.5rem] overflow-hidden h-96 group shadow-2xl">
                         <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-950/40 to-transparent z-10"></div>
                         <img src="{{ $lesson->thumbnail_url ?? asset('storage/image/default_hero.jpg') }}" class="w-full h-full object-cover grayscale opacity-40 group-hover:scale-105 transition-transform duration-1000" alt="">
                         <div class="absolute bottom-10 left-10 z-20 space-y-3">
                             <div class="px-3 py-1 bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest w-fit rounded-lg shadow-xl">Conteúdo Digital</div>
                             <h2 class="text-4xl font-black text-white italic tracking-tighter">{{ $lesson->title }}</h2>
                         </div>
                    </div>
                @endif

                <!-- Content Article -->
                <article class="prose prose-invert prose-amber max-w-none overflow-hidden prose-p:leading-relaxed prose-p:mb-4 prose-headings:mb-4 [&>*]:space-y-4 lms-lesson-content">
                    <div class="flex flex-col gap-8">
                        <div class="space-y-4 mb-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500/80 italic">Descrição da Lição</span>
                            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight m-0">
                                {{ $lesson->title }}
                            </h2>
                        </div>

                        @if($lesson->bible_book)
                            <section class="space-y-4">
                                <div class="relative bg-slate-900/50 border border-white/5 rounded-4xl p-10 not-prose overflow-hidden group">
                                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-600/10 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                                    <div class="relative z-10 flex flex-col md:flex-row gap-8">
                                        <div class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center text-white shadow-2xl shadow-amber-600/20 shrink-0">
                                            <x-icon name="book-bible" style="duotone" class="w-8 h-8" />
                                        </div>
                                        <div class="space-y-4">
                                            <h4 class="text-amber-500 font-black uppercase text-[10px] tracking-widest m-0 leading-none">
                                                Texto Bíblico Central
                                            </h4>
                                            <p class="text-white font-serif text-2xl md:text-3xl italic leading-relaxed tracking-tight m-0">
                                                {{ $lesson->bible_reference }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif

                        @if($lesson->introduction)
                            <section class="space-y-3">
                                <h3 class="text-xl md:text-2xl font-black text-white m-0">Introdução</h3>
                                <div class="text-gray-400 font-medium text-lg leading-relaxed space-y-4 [&>p]:mb-4">
                                    {!! nl2br(e($lesson->introduction)) !!}
                                </div>
                            </section>
                        @endif

                        @php
                            $developmentContent = $lesson->development ?: $lesson->description;
                        @endphp
                        @if($developmentContent)
                            <section class="space-y-3 overflow-hidden">
                                <h3 class="text-xl md:text-2xl font-black text-white m-0">Desenvolvimento</h3>
                                <div class="lms-development-content text-gray-400 font-medium text-lg leading-relaxed space-y-4 min-w-0">
                                    {!! $developmentContent !!}
                                </div>
                            </section>
                        @endif

                        @if($lesson->application)
                            <section class="space-y-3">
                                <h3 class="text-xl md:text-2xl font-black text-white m-0">Aplicação</h3>
                                <div class="text-gray-400 font-medium text-lg leading-relaxed space-y-4 [&>p]:mb-4">
                                    {!! nl2br(e($lesson->application)) !!}
                                </div>
                            </section>
                        @endif

                        @if($lesson->conclusion)
                            <section class="space-y-3">
                                <h3 class="text-xl md:text-2xl font-black text-white m-0">Conclusão</h3>
                                <div class="text-gray-400 font-medium text-lg leading-relaxed space-y-4 [&>p]:mb-4">
                                    {!! nl2br(e($lesson->conclusion)) !!}
                                </div>
                            </section>
                        @endif
                    </div>
                </article>
            </div>
        </main>

        <!-- Right Column: Tools & Navigation (Desktop) -->
        <aside class="w-96 bg-slate-900 border-l border-white/5 flex flex-col shrink-0 hidden lg:flex" x-data="{ tab: 'materials' }">
            <div class="flex p-2 bg-slate-950/50 rounded-b-[2rem] mx-4 mb-4 border-b border-white/5">
                <button @click="tab = 'materials'"
                    class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all"
                    :class="tab === 'materials' ? 'bg-amber-600 text-white shadow-xl shadow-amber-600/20' : 'text-gray-500 hover:text-white'">
                    Materiais
                </button>
                <button @click="tab = 'notes'"
                    class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all"
                    :class="tab === 'notes' ? 'bg-amber-600 text-white shadow-xl shadow-amber-600/20' : 'text-gray-500 hover:text-white'">
                    Anotações
                </button>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col">
                <!-- Tab Content: Materials -->
                <div x-show="tab === 'materials'" x-transition class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                    @forelse($lesson->materials as $material)
                    <a href="{{ Storage::url($material->file_path) }}" target="_blank"
                       class="group flex flex-col p-6 bg-white/5 border border-white/5 rounded-4xl hover:bg-amber-600/10 hover:border-amber-600/20 transition-all duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-gray-400 group-hover:text-amber-500 transition-colors">
                                @if(Str::contains($material->file_path, '.pdf'))
                                    <x-icon name="file-pdf" style="duotone" class="w-6 h-6" />
                                @else
                                    <x-icon name="file-arrow-down" style="duotone" class="w-6 h-6" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-sm font-black text-white group-hover:text-amber-500 transition-all truncate">{{ $material->title }}</h5>
                                <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Documento Digital</span>
                            </div>
                        </div>
                        <button class="w-full py-3 bg-white/5 text-[9px] font-black uppercase tracking-widest text-white rounded-xl group-hover:bg-amber-600 transition-all">Download</button>
                    </a>
                    @empty
                    <div class="text-center py-20 px-8">
                        <x-icon name="folder-open" style="duotone" class="w-12 h-12 text-gray-800 mx-auto mb-4" />
                        <p class="text-gray-600 font-bold text-[10px] uppercase tracking-widest">Sem materiais para esta aula</p>
                    </div>
                    @endforelse
                </div>

                <!-- Tab Content: Notes -->
                <div x-show="tab === 'notes'" x-transition class="flex-1 flex flex-col p-6">
                    <div class="bg-slate-950/50 rounded-[2.5rem] p-8 flex-1 flex flex-col border border-white/5 shadow-inner">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-[9px] font-black uppercase tracking-widest text-amber-500/60 flex items-center gap-2">
                                <x-icon name="pen-nib" class="w-3 h-3 text-amber-500" /> Bloco de Notas
                            </span>
                            <div class="flex items-center gap-2" x-show="savedStatus !== 'Salvo'">
                                <span class="text-[8px] font-black text-amber-500 uppercase animate-pulse" x-text="savedStatus"></span>
                            </div>
                        </div>
                        <textarea
                            x-model="notes"
                            @input.debounce.1000ms="saveNotes()"
                            class="flex-1 w-full bg-transparent border-0 text-white p-0 focus:ring-0 resize-none placeholder-gray-700 text-base leading-relaxed font-medium appearance-none"
                            placeholder="Escreva aqui seus insights sobre esta aula..."></textarea>

                        <div class="mt-6 pt-6 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[9px] font-black uppercase text-gray-600 tracking-widest">Auto-save Habilitado</span>
                            <x-icon name="cloud-check" style="duotone" class="text-gray-700 w-4 h-4" />
                        </div>
                    </div>
                </div>
            </div>
        </aside>

    </div>

    <!-- Pergunte ao Elias: floating button + chat panel -->
    <div class="fixed bottom-6 right-6 z-40">
        <button type="button" @click="eliasOpen = !eliasOpen"
            class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white shadow-xl shadow-amber-600/30 flex items-center justify-center border border-amber-500/30 transition-all active:scale-95 touch-manipulation"
            title="Pergunte ao Elias">
            <img src="{{ asset('images/CBAVBOT.png') }}" alt="Elias" class="w-8 h-8 sm:w-10 sm:h-10 object-contain object-bottom" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
            <x-icon name="book-bible" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 hidden" />
        </button>
    </div>
    <div x-show="eliasOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
        class="fixed top-20 right-6 bottom-6 z-50 w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        <div class="p-4 border-b border-white/10 flex items-center justify-between shrink-0 bg-slate-800/50">
            <div class="flex items-center gap-2">
                <span class="text-amber-400 font-black text-sm">Elias</span>
                <span class="text-[10px] text-gray-500 uppercase tracking-wider">Pergunte sobre a lição</span>
            </div>
            <button type="button" @click="eliasOpen = false" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400">
                <x-icon name="xmark" class="w-5 h-5" />
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" x-ref="eliasMessages">
            <template x-for="(m, i) in eliasMessages" :key="i">
                <div :class="m.role === 'user' ? 'text-right' : 'text-left'">
                    <div :class="m.role === 'user' ? 'inline-block rounded-2xl rounded-br-md bg-amber-600/20 text-white px-4 py-2 text-sm max-w-[85%]' : 'inline-block rounded-2xl rounded-bl-md bg-white/10 text-gray-200 px-4 py-2 text-sm max-w-[85%]'"
                        x-text="m.text"></div>
                </div>
            </template>
            <div x-show="eliasLoading" class="text-left">
                <div class="inline-block rounded-2xl rounded-bl-md bg-white/10 text-gray-400 px-4 py-2 text-sm">Elias está pensando...</div>
            </div>
        </div>
        <form @submit.prevent.stop="sendElias()" class="p-4 border-t border-white/10 shrink-0 flex gap-2">
            <input type="text" x-model="eliasInput" placeholder="Sua dúvida sobre a lição..."
                class="flex-1 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500/50">
            <button type="submit" :disabled="eliasLoading || !eliasInput.trim()"
                class="px-4 py-3 rounded-xl bg-amber-600 text-white font-bold text-sm hover:bg-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all touch-manipulation">
                <x-icon name="paper-plane" style="duotone" class="w-5 h-5" />
            </button>
        </form>
    </div>

    <!-- Mobile drawer: curriculum + materials/notes (visible below xl) -->
    <div x-show="mobileDrawerOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] xl:hidden">
        <div @click="mobileDrawerOpen = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-slate-900 border-l border-white/5 shadow-2xl flex flex-col overflow-hidden"
             @click.outside="mobileDrawerOpen = false">
            <div class="p-4 border-b border-white/5 flex items-center justify-between shrink-0">
                <span class="text-[10px] font-black uppercase tracking-widest text-amber-500/80">Menu</span>
                <button type="button" @click="mobileDrawerOpen = false" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-gray-400 hover:text-white">
                    <x-icon name="xmark" class="w-5 h-5" />
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-6">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Classe</p>
                    <p class="text-white font-black">{{ $lesson->ebdClass->name }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-500 mb-3">Aulas</p>
                    <div class="space-y-2">
                        @foreach($classLessons as $l)
                        @php $st = $lessonStates[$l->id] ?? []; $canAcc = $st['can_access'] ?? false; $isCur = ($l->id === $lesson->id); @endphp
                        @if($canAcc)
                        <a href="{{ route('memberpanel.ebd.student.classroom.player', $l->id) }}" class="block p-3 rounded-2xl {{ $isCur ? 'bg-amber-600/20 border border-amber-500/30' : 'bg-white/5 hover:bg-white/10' }}">
                            <span class="text-sm font-bold {{ $isCur ? 'text-amber-500' : 'text-gray-300' }}">{{ $l->title }}</span>
                            @if($st['is_completed'] ?? false)<x-icon name="circle-check" class="w-4 h-4 text-emerald-500 inline-block ml-1" />@endif
                        </a>
                        @else
                        <div class="block p-3 rounded-2xl bg-white/[0.02] text-gray-600">
                            <span class="text-sm">{{ $l->title }}</span>
                            <span class="text-[9px] block mt-0.5">{{ ($st['available_by_date'] ?? false) ? 'Conclua a anterior' : 'Em breve' }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                <div x-data="{ tab: 'materials' }">
                    <div class="flex rounded-xl bg-slate-950/50 p-1 border border-white/5 mb-3">
                        <button @click="tab = 'materials'" class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg" :class="tab === 'materials' ? 'bg-amber-600 text-white' : 'text-gray-500'">Materiais</button>
                        <button @click="tab = 'notes'" class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg" :class="tab === 'notes' ? 'bg-amber-600 text-white' : 'text-gray-500'">Anotações</button>
                    </div>
                    <div x-show="tab === 'materials'" class="space-y-3">
                        @forelse($lesson->materials as $material)
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-white/5 rounded-2xl hover:bg-amber-600/10">
                            <x-icon name="file-pdf" style="duotone" class="w-5 h-5 text-amber-500" />
                            <span class="text-sm font-bold text-white truncate">{{ $material->title }}</span>
                        </a>
                        @empty
                        <p class="text-gray-500 text-sm">Sem materiais.</p>
                        @endforelse
                    </div>
                    <div x-show="tab === 'notes'" class="pt-2">
                        <textarea x-model="notes" @input.debounce.1000ms="saveNotes()" class="w-full h-32 bg-white/5 rounded-2xl border border-white/5 px-4 py-3 text-white placeholder-gray-600 text-sm resize-none" placeholder="Suas anotações..."></textarea>
                        <p class="text-[9px] text-gray-500 mt-1" x-text="'Status: ' + savedStatus"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    /* Normalizar conteúdo da lição: evitar letras sobrepostas e garantir espaçamento */
    .lms-lesson-content .lms-development-content {
        line-height: 1.75 !important;
        word-spacing: normal !important;
    }
    .lms-lesson-content .lms-development-content p,
    .lms-lesson-content .lms-development-content > * {
        line-height: 1.75 !important;
        margin-bottom: 1rem !important;
    }
    .lms-lesson-content .lms-development-content span,
    .lms-lesson-content .lms-development-content em,
    .lms-lesson-content .lms-development-content strong {
        position: relative !important;
    }
</style>

@push('scripts')
<script>
function eliasChat() {
    const chatUrl = '{{ route("memberpanel.cbav-bot.chat") }}';
    const lessonId = {{ $lesson->id }};
    const csrf = '{{ csrf_token() }}';
    return {
        eliasOpen: false,
        eliasMessages: [],
        eliasInput: '',
        eliasLoading: false,
        sendElias() {
            const msg = (this.eliasInput || '').trim();
            if (!msg || this.eliasLoading) return;
            this.eliasMessages.push({ role: 'user', text: msg });
            this.eliasInput = '';
            this.eliasLoading = true;
            fetch(chatUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ message: msg, lesson_id: lessonId })
            })
            .then(r => r.json())
            .then(data => {
                this.eliasLoading = false;
                this.eliasMessages.push({ role: 'assistant', text: data.reply || 'Posso te ajudar com a lição e com versículos. Consulte a Bíblia como base.' });
                this.$nextTick(() => { const el = this.$refs.eliasMessages; if (el) el.scrollTop = el.scrollHeight; });
            })
            .catch(() => {
                this.eliasLoading = false;
                this.eliasMessages.push({ role: 'assistant', text: 'Desculpe, não consegui responder agora. Tente de novo ou consulte seu professor.' });
            });
        }
    };
}
function lmsPlayer() {
    const minSeconds = {{ (int) ($minSecondsToComplete ?? 90) }};
    const startedAt = '{{ $progress->created_at->toIso8601String() }}';
    const startedAtSec = startedAt ? Math.floor(new Date(startedAt).getTime() / 1000) : 0;
    const nowSec = Math.floor(Date.now() / 1000);
    const elapsed = Math.max(0, nowSec - startedAtSec);
    const initialRemaining = Math.max(0, minSeconds - elapsed);

    const alreadyCompleted = {{ $progress && $progress->status === 'completed' ? 'true' : 'false' }};
    return {
        isCompleted: alreadyCompleted,
        notes: `{!! addslashes($progress->notes ?? '') !!}`,
        savedStatus: 'Salvo',
        canComplete: alreadyCompleted || initialRemaining <= 0,
        secondsRemaining: initialRemaining,
        countdownLabel: '0:00',
        _countdownInterval: null,

        init() {
            const self = this;
            if (self.isCompleted) {
                self.canComplete = true;
                return;
            }
            const updateCountdown = () => {
                if (self.secondsRemaining <= 0) {
                    self.canComplete = true;
                    if (self._countdownInterval) {
                        clearInterval(self._countdownInterval);
                        self._countdownInterval = null;
                    }
                    return;
                }
                self.secondsRemaining--;
                const m = Math.floor(self.secondsRemaining / 60);
                const s = self.secondsRemaining % 60;
                self.countdownLabel = m + ':' + (s < 10 ? '0' : '') + s;
            };
            const m0 = Math.floor(self.secondsRemaining / 60);
            const s0 = self.secondsRemaining % 60;
            self.countdownLabel = m0 + ':' + (s0 < 10 ? '0' : '') + s0;
            if (self.secondsRemaining > 0) {
                self._countdownInterval = setInterval(updateCountdown, 1000);
            } else {
                self.canComplete = true;
            }
        },

        markAsComplete() {
            if (this.isCompleted) return;
            window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Finalizando aula...' } }));

            fetch('{{ route("memberpanel.ebd.student.classroom.complete", $lesson->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                if (data.success) {
                    this.isCompleted = true;
                    if (typeof window.triggerEbdCelebration === 'function') window.triggerEbdCelebration();
                    if (window.Toast) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                    }
                } else {
                    if (window.Toast) {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Não foi possível concluir a aula.'
                        });
                    }
                }
            })
            .catch(() => {
                window.dispatchEvent(new CustomEvent('stop-loading'));
                if (window.Toast) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Erro de conexão ao concluir a aula.'
                    });
                }
            });
        },

        saveNotes() {
            this.savedStatus = 'Salvando...';

            fetch('{{ route("memberpanel.ebd.student.classroom.notes", $lesson->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notes: this.notes })
            })
            .then(r => r.json())
            .then(data => {
                this.savedStatus = data.success ? 'Salvo' : 'Erro';
            })
            .catch(() => {
                this.savedStatus = 'Erro de Conexão';
            });
        }
    }
}
window.triggerEbdCelebration = function() {
    const colors = ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];
    const container = document.createElement('div');
    container.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:100;overflow:hidden';
    document.body.appendChild(container);
    for (let i = 0; i < 55; i++) {
        const el = document.createElement('div');
        el.style.cssText = 'position:absolute;width:10px;height:10px;border-radius:2px;left:' + (Math.random() * 100) + 'vw;top:-20px;animation:ebd-confetti-fall 2.5s ease-out forwards;animation-delay:' + (Math.random() * 0.5) + 's';
        el.style.background = colors[Math.floor(Math.random() * colors.length)];
        el.style.setProperty('--tx', (Math.random() - 0.5) * 200 + 'px');
        container.appendChild(el);
    }
    const style = document.createElement('style');
    style.textContent = '@keyframes ebd-confetti-fall { to { transform: translate(var(--tx, 0), 100vh) rotate(720deg); opacity: 0; } }';
    document.head.appendChild(style);
    setTimeout(function() { container.remove(); style.remove(); }, 3000);
};
</script>
@endpush
@endsection
