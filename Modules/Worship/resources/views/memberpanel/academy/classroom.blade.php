@extends('memberpanel::components.layouts.master')

@section('page-title', $course->title . ' - Academia de Louvor')

@section('content')
<!-- Sala de Aula - Academia de Louvor (tela cheia, responsiva) -->
<div class="fixed inset-0 w-full h-full min-h-[100dvh] bg-gray-950 flex flex-col z-[60] overflow-hidden font-sans" x-data="worshipPlayer({{ $course->id }})" x-init="initPlayer()">

    <!-- Header fixo: nunca sobrepõe o conteúdo -->
    <header class="shrink-0 min-h-[56px] sm:min-h-[64px] md:min-h-[72px] bg-gray-900/98 backdrop-blur-xl border-b border-white/10 flex items-center justify-between gap-3 px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4 z-40 shadow-xl safe-area-inset-top">
        <div class="flex items-center gap-2 sm:gap-4 min-w-0 flex-1">
            <a href="{{ route('worship.member.academy.index') }}" class="shrink-0 w-10 h-10 bg-white/5 hover:bg-white/10 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all border border-white/10" aria-label="Voltar para Academia">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="h-6 sm:h-8 w-px bg-white/10 hidden sm:block shrink-0"></div>
            <button type="button" @click="mobileDrawerOpen = true" class="sm:hidden shrink-0 flex items-center gap-2 px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-gray-300 hover:text-white transition-all">
                <x-icon name="bars-staggered" class="w-4 h-4" />
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Conteúdo</span>
            </button>
            <div class="min-w-0 flex-1 sm:flex-initial">
                <p class="text-[10px] sm:text-[9px] font-bold uppercase tracking-widest text-purple-400 leading-tight mb-0.5">Academia de Louvor</p>
                <h1 class="text-white font-bold sm:font-black text-base sm:text-lg tracking-tight truncate max-w-[200px] sm:max-w-md" x-text="course.title || 'Carregando…'">Carregando…</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-4 shrink-0">
            <template x-if="currentLesson">
                <div class="flex flex-col items-end gap-1.5">
                    <template x-if="!isCompleted(currentLesson.id) && currentLesson.video_url && !videoWatchedToEnd">
                        <p class="text-[10px] text-amber-400 font-semibold uppercase tracking-wider text-right max-w-[120px] sm:max-w-none">Assista o vídeo até o fim para finalizar</p>
                    </template>
                    <button type="button" @click="markAsComplete()"
                        class="flex items-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl text-[10px] sm:text-xs font-bold uppercase tracking-widest transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px]"
                        :disabled="!canFinalizeLesson"
                        :class="isCompleted(currentLesson.id) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 cursor-default' : (canFinalizeLesson ? 'bg-blue-600 text-white hover:bg-blue-500 hover:shadow-blue-500/20' : 'bg-gray-600 text-gray-400 cursor-not-allowed')">
                        <template x-if="isCompleted(currentLesson.id)">
                            <span class="flex items-center gap-2"><x-icon name="check-circle" class="w-4 h-4" /> Concluída</span>
                        </template>
                        <template x-if="!isCompleted(currentLesson.id)">
                            <span class="flex items-center gap-2">Finalizar Aula <x-icon name="flag" class="w-4 h-4" /></span>
                        </template>
                    </button>
                </div>
            </template>
        </div>
    </header>

    <div class="flex flex-1 min-h-0 overflow-hidden relative">
        <!-- Sidebar - Curriculum -->
        <aside class="w-72 xl:w-80 bg-gray-900 border-r border-white/5 flex flex-col shrink-0 hidden lg:flex relative z-20 shadow-2xl shadow-black min-h-0">
            <div class="p-4 sm:p-5 border-b border-white/5 bg-gray-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0">
                        <x-icon name="list-music" class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block mb-0.5">Conteúdo</span>
                        <h2 class="text-white font-bold text-base truncate" x-text="(course.modules?.length || 0) + ' Módulos'">0 Módulos</h2>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-xs font-semibold text-gray-400 mb-2">
                        <span>Progresso</span>
                        <span x-text="progressPercent + '%'">0%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-500 rounded-full" :style="`width: ${progressPercent}%`"></div>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-3 sm:p-4 space-y-1">
                <template x-if="isLoading">
                    <div class="flex items-center justify-center h-32">
                        <x-icon name="spinner-third" class="w-8 h-8 text-blue-500 animate-spin" />
                    </div>
                </template>
                <template x-for="(moduleItem, mIndex) in course.modules" :key="moduleItem.id">
                    <div class="mb-4">
                        <div class="px-3 py-2 flex items-center justify-between group cursor-pointer" @@click="moduleItem.open = !moduleItem.open">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-500" x-text="`Módulo ${mIndex + 1}`"></span>
                                <h4 class="text-sm font-bold text-gray-200 group-hover:text-white transition-colors" x-text="moduleItem.title"></h4>
                            </div>
                            <span :class="moduleItem.open !== false ? 'rotate-180 inline-block' : ''">
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-500 transition-transform" />
                            </span>
                        </div>

                        <div x-show="moduleItem.open !== false" x-collapse>
                            <div class="space-y-1 mt-1 pl-2">
                                <template x-for="(lesson, lIndex) in moduleItem.lessons" :key="lesson.id">
                                    <button @@click="canSelectLesson(lesson) && selectLesson(lesson)"
                                       type="button"
                                       class="w-full text-left relative group flex flex-col p-3 rounded-xl border border-transparent transition-all touch-manipulation"
                                       :class="!canSelectLesson(lesson) ? 'opacity-60 cursor-not-allowed' : (currentLesson?.id === lesson.id ? 'bg-blue-600/10 border border-blue-500/20' : 'hover:bg-white/5')"
                                       :title="!canSelectLesson(lesson) ? 'Conclua a aula anterior para desbloquear' : ''">

                                        <div class="flex justify-between items-start gap-2 mb-1">
                                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1 shrink-0">
                                                <x-icon name="play" class="w-2.5 h-2.5" /> Aula <span x-text="lIndex + 1"></span>
                                            </span>
                                            <template x-if="isCompleted(lesson.id)">
                                                <div class="w-4 h-4 bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0">
                                                    <x-icon name="check" class="w-2.5 h-2.5 text-emerald-500" />
                                                </div>
                                            </template>
                                            <template x-if="!isCompleted(lesson.id)">
                                                <div class="w-4 h-4 bg-gray-800 rounded-full shrink-0"></div>
                                            </template>
                                        </div>
                                        <h5 class="text-xs font-semibold leading-snug break-words" :class="currentLesson?.id === lesson.id ? 'text-blue-400' : 'text-gray-400 group-hover:text-gray-200'" x-text="lesson.title"></h5>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </aside>

        <!-- Área principal: vídeo + conteúdo (scrollável, nunca cortado pelo header) -->
        <main class="flex-1 min-h-0 w-full bg-gray-950 relative flex flex-col overflow-y-auto overflow-x-hidden">
            <template x-if="isLoading && !course.modules?.length && !loadError">
                <div class="flex flex-col items-center justify-center flex-1 w-full p-6 sm:p-8 text-center">
                    <x-icon name="spinner-third" class="w-10 h-10 text-blue-500 animate-spin mb-4" />
                    <p class="text-gray-400 font-semibold">Carregando curso…</p>
                    <p class="text-gray-500 text-sm mt-1">Aguarde um momento.</p>
                </div>
            </template>

            <template x-if="loadError">
                <div class="flex flex-col items-center justify-center flex-1 w-full p-6 sm:p-10 text-center max-w-md">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-red-500/10 rounded-2xl flex items-center justify-center text-red-400 mb-6">
                        <x-icon name="triangle-exclamation" class="w-8 h-8 sm:w-10 sm:h-10" />
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Não foi possível carregar o curso</h2>
                    <p class="text-gray-400 text-sm sm:text-base leading-relaxed mb-6">Verifique sua conexão e tente novamente.</p>
                    <a href="{{ route('worship.member.academy.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-500 transition-colors">
                        <x-icon name="arrow-left" class="w-4 h-4" /> Voltar para Academia
                    </a>
                    <button type="button" @click="loadError = false; initPlayer()" class="mt-4 text-gray-400 hover:text-white text-sm font-semibold">Tentar novamente</button>
                </div>
            </template>

            <template x-if="!currentLesson && !isLoading && course.modules?.length && !loadError">
                <div class="flex flex-col items-center justify-center flex-1 w-full p-6 sm:p-10 text-center max-w-md">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-800/80 rounded-2xl flex items-center justify-center text-gray-500 mb-6">
                        <x-icon name="play" class="w-10 h-10 sm:w-12 sm:h-12 ml-1" />
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white mb-3">Selecione uma aula</h2>
                    <p class="text-gray-400 text-sm sm:text-base leading-relaxed">Use o menu <strong class="text-gray-300">Conteúdo</strong> ao lado (ou no canto superior) para escolher a primeira aula e começar.</p>
                </div>
            </template>

            <template x-if="currentLesson">
                <div class="w-full max-w-[1200px] mx-auto flex flex-col items-stretch py-4 sm:py-6 md:py-8 px-4 sm:px-6 md:px-8 pb-20">

                    <!-- Contexto: Módulo e aula -->
                    <div class="w-full mb-3 sm:mb-4 flex items-center gap-2 text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <span x-text="currentLesson ? (allLessons.findIndex(l => l.id === currentLesson.id) + 1) + ' de ' + allLessons.length + ' aulas' : ''"></span>
                    </div>

                    <!-- Bloco de vídeo: só quando há video_url (evita caixa vazia e cortes) -->
                    <div class="w-full relative" x-show="currentLesson.video_url">
                        <div class="relative w-full rounded-xl sm:rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-black" style="aspect-ratio: 16/9;">
                            <template x-if="currentLesson.multicam_video_url">
                                <div class="absolute top-3 left-3 z-20">
                                    <button type="button" class="px-3 py-1.5 bg-black/60 backdrop-blur border border-white/10 rounded-lg text-xs font-bold text-white flex items-center gap-2 hover:bg-white/10 transition-colors">
                                        <x-icon name="camera" class="w-3 h-3" />
                                        Mudar Ângulo
                                    </button>
                                </div>
                            </template>
                            <template x-if="currentLesson.video_url && isYoutubeUrl(currentLesson.video_url)">
                                <div class="absolute inset-0 w-full h-full">
                                    <div id="academy-yt-player" class="absolute inset-0 w-full h-full" x-ref="ytContainer"></div>
                                    <div class="absolute inset-0 flex items-center justify-center z-10 cursor-pointer" @click="toggleYtPlayPause()" x-show="ytPlayerReady">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-black/70 flex items-center justify-center text-white transition-opacity hover:bg-black/80" x-show="!ytPlaying">
                                            <x-icon name="play" class="w-8 h-8 sm:w-10 sm:h-10 ml-1" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="currentLesson.video_url && isVimeoUrl(currentLesson.video_url)">
                                <div class="absolute inset-0 w-full h-full">
                                    <iframe class="absolute inset-0 w-full h-full object-contain bg-black" :src="getVideoEmbedUrl(currentLesson.video_url)" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen x-ref="vimeoIframe"></iframe>
                                    <div class="absolute inset-0 flex items-center justify-center z-10 cursor-pointer" @click="toggleVimeoPlayPause()">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-black/70 flex items-center justify-center text-white" x-show="!vimeoPlaying">
                                            <x-icon name="play" class="w-8 h-8 sm:w-10 sm:h-10 ml-1" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="currentLesson.video_url && !getVideoEmbedUrl(currentLesson.video_url)">
                                <div class="absolute inset-0 w-full h-full">
                                    <video class="absolute inset-0 w-full h-full object-contain bg-black" :src="currentLesson.video_url" x-ref="nativeVideo" @ended="videoWatchedToEnd = true" @play="nativeVideoPlaying = true" @pause="nativeVideoPlaying = false"></video>
                                    <div class="absolute inset-0 flex items-center justify-center z-10 cursor-pointer" @click="toggleNativeVideoPlayPause()">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-black/70 flex items-center justify-center text-white" x-show="!nativeVideoPlaying">
                                            <x-icon name="play" class="w-8 h-8 sm:w-10 sm:h-10 ml-1" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <p class="mt-2 sm:mt-3 text-[10px] sm:text-xs text-amber-400/90 text-center" x-show="currentLesson.video_url && !videoWatchedToEnd && !isCompleted(currentLesson.id)">
                            Assista até o fim para poder finalizar esta aula.
                        </p>
                    </div>

                    <!-- Aulas sem vídeo (cifra, devocional, material): destaque direto para o conteúdo -->
                    <div class="w-full" x-show="!currentLesson.video_url && (currentLesson.type === 'chordpro' || currentLesson.type === 'devotional' || currentLesson.type === 'material')">
                        <div class="rounded-xl sm:rounded-2xl border border-white/10 bg-gray-900/60 p-4 sm:p-6 flex items-center gap-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center shrink-0" :class="currentLesson.type === 'chordpro' ? 'bg-indigo-500/20 text-indigo-400' : (currentLesson.type === 'devotional' ? 'bg-amber-500/20 text-amber-400' : 'bg-blue-500/20 text-blue-400')">
                                <x-icon name="file-lines" class="w-6 h-6 sm:w-7 sm:h-7" x-show="currentLesson.type === 'chordpro'" />
                                <x-icon name="book-bible" class="w-6 h-6 sm:w-7 sm:h-7" x-show="currentLesson.type === 'devotional'" />
                                <x-icon name="file-arrow-down" class="w-6 h-6 sm:w-7 sm:h-7" x-show="currentLesson.type === 'material'" />
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-500" x-text="currentLesson.type === 'chordpro' ? 'Cifra / ChordPro' : (currentLesson.type === 'devotional' ? 'Devocional' : 'Material')"></p>
                                <p class="text-sm sm:text-base text-gray-300 mt-0.5">Use as abas abaixo para ver a descrição, downloads e dicas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navegação: Anterior / Próximo -->
                    <div class="w-full flex justify-between items-center gap-4 mt-4 sm:mt-6">
                        <button type="button" @@click="goPrevLesson()" x-show="allLessons.length && currentLessonIndex > 0"
                            class="flex items-center gap-2 px-4 sm:px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-colors text-sm font-bold min-h-[48px] touch-manipulation">
                            <x-icon name="arrow-left" class="w-4 h-4 shrink-0" /> <span class="hidden sm:inline">Anterior</span>
                        </button>
                        <span class="text-gray-500 text-xs sm:text-sm font-medium shrink-0" x-show="allLessons.length" x-text="(currentLessonIndex + 1) + ' / ' + allLessons.length"></span>
                        <button type="button" @@click="goNextLesson()"
                            x-show="allLessons.length && currentLessonIndex < allLessons.length - 1"
                            :disabled="!canGoToNextLesson"
                            :class="!canGoToNextLesson ? 'opacity-50 cursor-not-allowed' : ''"
                            class="flex items-center gap-2 px-4 sm:px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-colors text-sm font-bold min-h-[48px] touch-manipulation disabled:cursor-not-allowed"
                            :title="!canGoToNextLesson ? 'Conclua esta aula para avançar' : ''">
                            <span class="hidden sm:inline">Próximo</span> <x-icon name="arrow-right" class="w-4 h-4 shrink-0" />
                        </button>
                    </div>

                    <!-- Abas: Descrição | Downloads | Dicas -->
                    <div class="w-full mt-6 sm:mt-8 bg-gray-900/80 border border-white/5 rounded-2xl sm:rounded-3xl overflow-hidden">
                        <div class="flex overflow-x-auto border-b border-white/10 custom-scrollbar snap-x snap-mandatory scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                            <button type="button" @@click="lessonTab = 'descricao'"
                                class="shrink-0 px-4 sm:px-6 py-3.5 sm:py-4 text-sm font-bold transition-colors min-h-[48px] flex items-center gap-2 snap-start touch-manipulation"
                                :class="lessonTab === 'descricao' ? 'text-blue-400 border-b-2 border-blue-500 bg-white/5' : 'text-gray-500 hover:text-white'">
                                <x-icon name="file-lines" class="w-4 h-4 shrink-0" />
                                <span>Descrição</span>
                            </button>
                            <button type="button" @@click="lessonTab = 'downloads'"
                                class="shrink-0 px-4 sm:px-6 py-3.5 sm:py-4 text-sm font-bold transition-colors min-h-[48px] flex items-center gap-2 snap-start touch-manipulation"
                                :class="lessonTab === 'downloads' ? 'text-blue-400 border-b-2 border-blue-500 bg-white/5' : 'text-gray-500 hover:text-white'">
                                <x-icon name="download" class="w-4 h-4 shrink-0" />
                                <span>Downloads</span>
                            </button>
                            <button type="button" @@click="lessonTab = 'dicas'"
                                class="shrink-0 px-4 sm:px-6 py-3.5 sm:py-4 text-sm font-bold transition-colors min-h-[48px] flex items-center gap-2 snap-start touch-manipulation"
                                :class="lessonTab === 'dicas' ? 'text-blue-400 border-b-2 border-blue-500 bg-white/5' : 'text-gray-500 hover:text-white'">
                                <x-icon name="chalkboard-user" class="w-4 h-4 shrink-0" />
                                <span>Dicas do Professor</span>
                            </button>
                        </div>
                        <div class="p-4 sm:p-6 lg:p-8">
                            <div x-show="lessonTab === 'descricao'" class="academy-prose">
                                <h2 class="text-lg sm:text-xl font-bold text-white mb-4 sm:mb-5" x-text="currentLesson.title"></h2>
                                <template x-if="currentLesson.type === 'chordpro'">
                                    <div class="academy-cifra-content text-gray-300 text-sm sm:text-base leading-relaxed space-y-4" x-html="renderChordProCifra(currentLesson.content)"></div>
                                </template>
                                <template x-if="currentLesson.type === 'devotional'">
                                    <div class="academy-devotional space-y-6">
                                        <template x-if="currentLesson.bible_reference">
                                            <div class="p-4 sm:p-5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                                                <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-2">Referência bíblica</p>
                                                <p class="text-amber-100 font-medium" x-text="currentLesson.bible_reference"></p>
                                            </div>
                                        </template>
                                        <div class="academy-prose-content text-gray-300 text-sm sm:text-base leading-relaxed" x-html="currentLesson.content || '<p class=\'text-gray-500\'>Nenhum conteúdo.</p>'"></div>
                                    </div>
                                </template>
                                <template x-if="currentLesson.type !== 'chordpro' && currentLesson.type !== 'devotional'">
                                    <div class="academy-prose-content text-gray-300 text-sm sm:text-base leading-relaxed" x-html="currentLesson.content || '<p class=\'text-gray-500\'>Nenhuma descrição adicionada.</p>'"></div>
                                </template>
                                <template x-if="course.biblical_reflection">
                                    <div class="mt-6 sm:mt-8 p-4 sm:p-5 rounded-xl sm:rounded-2xl bg-white/5 border border-white/10">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">Reflexão bíblica</p>
                                        <p class="text-gray-300 text-sm sm:text-base leading-relaxed" x-text="course.biblical_reflection"></p>
                                    </div>
                                </template>
                            </div>
                            <div x-show="lessonTab === 'downloads'" style="display: none;">
                                <div class="space-y-3 sm:space-y-4">
                                    <template x-if="(currentLesson.materials && currentLesson.materials.length) || currentLesson.sheet_music_pdf || currentLesson.pdf_path">
                                        <div class="space-y-3">
                                            <template x-for="mat in (currentLesson.materials || [])" :key="mat.id">
                                                <a :href="mat.file_path" target="_blank" rel="noopener" class="flex items-center gap-3 p-4 sm:p-5 bg-white/5 rounded-xl sm:rounded-2xl hover:bg-white/10 transition-colors border border-white/5 group">
                                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0">
                                                        <x-icon name="file-arrow-down" class="w-5 h-5" />
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-bold text-sm sm:text-base text-white truncate" x-text="mat.label"></p>
                                                        <p class="text-[10px] uppercase text-gray-500 mt-0.5" x-text="mat.type"></p>
                                                    </div>
                                                    <x-icon name="download" class="w-4 h-4 text-gray-500 shrink-0" />
                                                </a>
                                            </template>
                                            <template x-if="currentLesson.sheet_music_pdf">
                                                <a :href="currentLesson.sheet_music_pdf" target="_blank" rel="noopener" class="flex items-center gap-3 p-4 sm:p-5 bg-white/5 rounded-xl sm:rounded-2xl hover:bg-white/10 border border-white/5 group">
                                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0"><x-icon name="file-music" class="w-5 h-5" /></div>
                                                    <div class="flex-1 min-w-0"><p class="font-bold text-sm text-white">Partitura</p><p class="text-[10px] text-gray-500">PDF</p></div>
                                                    <x-icon name="download" class="w-4 h-4 text-gray-500 shrink-0" />
                                                </a>
                                            </template>
                                            <template x-if="currentLesson.pdf_path">
                                                <a :href="currentLesson.pdf_path" target="_blank" rel="noopener" class="flex items-center gap-3 p-4 sm:p-5 bg-white/5 rounded-xl sm:rounded-2xl hover:bg-white/10 border border-white/5 group">
                                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center shrink-0"><x-icon name="file-pdf" class="w-5 h-5" /></div>
                                                    <div class="flex-1 min-w-0"><p class="font-bold text-sm text-white">Guia de Estudo</p><p class="text-[10px] text-gray-500">PDF</p></div>
                                                    <x-icon name="download" class="w-4 h-4 text-gray-500 shrink-0" />
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="(!currentLesson.materials || !currentLesson.materials.length) && !currentLesson.sheet_music_pdf && !currentLesson.pdf_path">
                                        <div class="p-6 sm:p-8 text-center border border-dashed border-white/10 rounded-xl sm:rounded-2xl bg-white/5">
                                            <x-icon name="folder-open" class="w-10 h-10 sm:w-12 sm:h-12 text-gray-500 mx-auto mb-3" />
                                            <p class="text-sm text-gray-400 font-semibold">Nenhum material para download nesta aula</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div x-show="lessonTab === 'dicas'" style="display: none;" class="academy-prose">
                                <template x-if="currentLesson.teacher_tips">
                                    <div class="academy-prose-content text-gray-300 text-sm sm:text-base leading-relaxed whitespace-pre-wrap" x-html="currentLesson.teacher_tips"></div>
                                </template>
                                <template x-if="!currentLesson.teacher_tips">
                                    <div class="p-6 sm:p-8 text-center border border-dashed border-white/10 rounded-xl sm:rounded-2xl bg-white/5">
                                        <x-icon name="chalkboard-user" class="w-10 h-10 sm:w-12 sm:h-12 text-gray-500 mx-auto mb-3" />
                                        <p class="text-sm text-gray-400 font-semibold">Nenhuma dica registrada para esta aula</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="h-16 sm:h-24" aria-hidden="true"></div>
                </div>
            </template>
        </main>
    </div>

    <!-- Mobile Drawer for Modules -->
    <div x-show="mobileDrawerOpen" style="display: none;" class="fixed inset-0 z-[70] lg:hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @@click="mobileDrawerOpen = false"></div>
        <div class="absolute left-0 top-0 bottom-0 w-80 bg-gray-900 shadow-2xl flex flex-col border-r border-white/10"
             x-transition:enter="transition-transform duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition-transform duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">

            <div class="p-4 border-b border-white/10 flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Módulos do Curso</span>
                <button type="button" @@click="mobileDrawerOpen = false" class="text-white p-2">
                    <x-icon name="xmark" class="w-5 h-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-2">
                 <template x-for="(moduleItem, mIndex) in course.modules" :key="'mob-'+moduleItem.id">
                     <div class="mb-4">
                        <div class="px-2 py-2 flex items-center justify-between group cursor-pointer" @@click="moduleItem.open = !moduleItem.open">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-500" x-text="`Módulo ${mIndex + 1}`"></span>
                                <h4 class="text-sm font-bold text-gray-200" x-text="moduleItem.title"></h4>
                            </div>
                            <span :class="moduleItem.open !== false ? 'rotate-180 inline-block' : ''">
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-500 transition-transform" />
                            </span>
                        </div>

                        <div x-show="moduleItem.open !== false">
                            <div class="space-y-1 mt-1 pl-2 border-l border-white/10 ml-2">
                                <template x-for="(lesson, lIndex) in moduleItem.lessons" :key="'mob-l-'+lesson.id">
                                    <button type="button"
                                       @@click="canSelectLesson(lesson) && (selectLesson(lesson), mobileDrawerOpen = false)"
                                       class="w-full text-left flex flex-col p-3 rounded-xl transition-all min-h-[52px] touch-manipulation"
                                       :class="!canSelectLesson(lesson) ? 'opacity-60 cursor-not-allowed' : (currentLesson?.id === lesson.id ? 'bg-blue-600/20 border border-blue-500/30 text-white' : 'hover:bg-white/5 text-gray-400')"
                                       :title="!canSelectLesson(lesson) ? 'Conclua a aula anterior para desbloquear' : ''">

                                        <div class="flex justify-between items-center w-full mb-1">
                                            <span class="text-[10px] uppercase font-bold" :class="currentLesson?.id === lesson.id ? 'text-blue-200' : 'text-gray-500'" x-text="`Aula ${lIndex + 1}`"></span>
                                            <template x-if="isCompleted(lesson.id)">
                                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-400 shrink-0" />
                                            </template>
                                            <template x-if="!isCompleted(lesson.id)">
                                                <div class="w-4 h-4 rounded-full bg-gray-700 shrink-0"></div>
                                            </template>
                                        </div>
                                        <h5 class="text-xs font-bold leading-snug" x-text="lesson.title"></h5>
                                    </button>
                                </template>
                            </div>
                        </div>
                     </div>
                 </template>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }

    /* Prose: descrição, devocional, conteúdo */
    .academy-prose-content p { margin-bottom: 1rem; }
    .academy-prose-content p:last-child { margin-bottom: 0; }
    .academy-prose-content h1, .academy-prose-content h2, .academy-prose-content h3 { margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; color: #f3f4f6; }
    .academy-prose-content h1:first-child, .academy-prose-content h2:first-child, .academy-prose-content h3:first-child { margin-top: 0; }
    .academy-prose-content ul, .academy-prose-content ol { margin: 1rem 0; padding-left: 1.5rem; }
    .academy-prose-content li { margin-bottom: 0.5rem; }
    .academy-prose-content strong { font-weight: 700; color: #e5e7eb; }
    .academy-prose-content a { color: #60a5fa; text-decoration: underline; }
    .academy-prose-content a:hover { color: #93c5fd; }

    /* Cifra (ChordPro): seções, tablatura, acordes */
    .academy-cifra-content .cifra-section-header { margin-top: 1.25rem; margin-bottom: 0.5rem; }
    .academy-cifra-content .cifra-tab-block { line-height: 1.5; margin: 0.75rem 0; }
    .academy-cifra-content .cifra-lyric-line { margin-bottom: 0.75rem; }
    .academy-cifra-content .cifra-chord { margin-right: 0.25rem; }
    .academy-cifra-content .cifra-plain { margin-bottom: 0.5rem; }

    /* Devocional: referência bíblica já estilizada no markup */
    .academy-devotional .academy-prose-content { margin-top: 0.5rem; }
</style>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('worshipPlayer', (courseId) => ({
        isLoading: true,
        loadError: false,
        course: {},
        completedLessons: [],
        currentLesson: null,
        mobileDrawerOpen: false,
        lessonTab: 'descricao',
        videoWatchedToEnd: false,
        ytPlayer: null,
        vimeoPlayer: null,
        pendingYtVideoId: null,
        ytPlaying: false,
        ytPlayerReady: false,
        vimeoPlaying: false,
        nativeVideoPlaying: false,

        get canFinalizeLesson() {
            if (!this.currentLesson) return false;
            if (this.isCompleted(this.currentLesson.id)) return true;
            if (!this.currentLesson.video_url) return true;
            return this.videoWatchedToEnd;
        },

        get firstUncompletedLessonIndex() {
            if (!this.allLessons.length) return -1;
            const idx = this.allLessons.findIndex(l => !this.isCompleted(l.id));
            return idx === -1 ? this.allLessons.length : idx;
        },

        canSelectLesson(lesson) {
            if (!lesson || !this.allLessons.length) return true;
            const lessonIdx = this.allLessons.findIndex(l => l.id === lesson.id);
            if (lessonIdx === -1) return true;
            return lessonIdx <= this.firstUncompletedLessonIndex;
        },

        get canGoToNextLesson() {
            return this.currentLesson && this.isCompleted(this.currentLesson.id);
        },

        getVideoEmbedUrl(url) {
            if (!url) return '';
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                const id = url.split('v=')[1]?.split('&')[0] || url.split('/').pop()?.split('?')[0];
                return id ? `https://www.youtube.com/embed/${id}?enablejsapi=1` : '';
            }
            if (url.includes('vimeo.com')) {
                const m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
                return m ? `https://player.vimeo.com/video/${m[1]}?controls=0` : '';
            }
            return '';
        },

        isYoutubeUrl(url) {
            return url && (url.includes('youtube.com') || url.includes('youtu.be'));
        },

        isVimeoUrl(url) {
            return url && url.includes('vimeo.com');
        },

        getYoutubeVideoId(url) {
            if (!url) return null;
            if (url.includes('v=')) return url.split('v=')[1].split('&')[0];
            return url.split('/').pop()?.split('?')[0] || null;
        },

        renderChordProCifra(content) {
            if (!content || !content.trim()) return '<p class="text-gray-500">Nenhuma cifra adicionada.</p>';
            const lines = content.split('\n');
            const tabRegex = /^[EADGB]\|.+/;
            let html = '';
            let i = 0;
            const esc = (s) => {
                const d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            };
            while (i < lines.length) {
                const line = lines[i];
                const t = line.trim();
                if (t.startsWith('{') && t.endsWith('}') || t.startsWith('#')) { i++; continue; }
                if (/^\[([^\]]+)\]$/.test(t)) {
                    const title = esc(t.slice(1, -1));
                    html += `<div class="cifra-section-header text-indigo-400 font-bold text-sm sm:text-base uppercase tracking-wider mt-4 first:mt-0">${title}</div>`;
                    i++;
                    continue;
                }
                if (tabRegex.test(t)) {
                    const tabLines = [];
                    while (i < lines.length && tabRegex.test(lines[i].trim())) tabLines.push(esc(lines[i])), i++;
                    html += `<pre class="cifra-tab-block overflow-x-auto text-xs sm:text-sm font-mono text-gray-300 bg-white/5 rounded-lg p-4 border border-white/10 whitespace-pre my-3">${tabLines.join('\n')}</pre>`;
                    continue;
                }
                if (t.includes('[') && t.includes(']')) {
                    const parts = line.split(/(\[.*?\])/);
                    let row = '<div class="cifra-lyric-line flex flex-wrap items-end leading-relaxed mb-3">';
                    for (let j = 0; j < parts.length; j++) {
                        const p = parts[j];
                        if (p.startsWith('[') && p.endsWith(']')) {
                            row += `<span class="cifra-chord text-indigo-400 font-bold text-xs mr-1">${esc(p.slice(1, -1))}</span>`;
                        } else if (p.length) {
                            row += `<span class="cifra-lyric text-gray-200 whitespace-pre">${esc(p)}</span>`;
                        }
                    }
                    row += '</div>';
                    html += row;
                    i++;
                    continue;
                }
                if (t.length === 0) { i++; continue; }
                html += `<div class="cifra-plain text-gray-400 text-sm whitespace-pre-wrap mb-2">${esc(line)}</div>`;
                i++;
            }
            return html || '<p class="text-gray-500">Nenhuma cifra adicionada.</p>';
        },

        async initPlayer() {
            try {
                // Fetch course structure via v1 api
                const response = await fetch(`/api/v1/worship/academy/courses/${courseId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Sanctum web cookie takes care of auth usually, but providing Accept is good
                    }
                });
                const res = await response.json();
                this.course = res.data.course || {};
                this.completedLessons = res.data.completed_lessons || [];

                // Select first lesson by default if available
                if (this.course.modules && this.course.modules.length > 0) {
                    const firstMod = this.course.modules[0];
                    if (firstMod.lessons && firstMod.lessons.length > 0) {
                        this.selectLesson(firstMod.lessons[0]);
                    }
                }
            } catch (err) {
                console.error("Failed to load course", err);
                this.loadError = true;
                if(window.Toast) Toast.fire({icon: 'error', title: 'Erro ao carregar o curso.'});
            } finally {
                this.isLoading = false;
            }
        },

        get progressPercent() {
            if (!this.course.modules) return 0;
            let total = 0;
            this.course.modules.forEach(m => total += m.lessons?.length || 0);
            if (total === 0) return 0;
            return Math.round((this.completedLessons.length / total) * 100);
        },

        get allLessons() {
            if (!this.course.modules) return [];
            return this.course.modules.reduce((acc, m) => acc.concat(m.lessons || []), []);
        },

        get currentLessonIndex() {
            if (!this.currentLesson || !this.allLessons.length) return -1;
            return this.allLessons.findIndex(l => l.id === this.currentLesson.id);
        },

        goPrevLesson() {
            if (this.currentLessonIndex <= 0) return;
            this.selectLesson(this.allLessons[this.currentLessonIndex - 1]);
        },

        goNextLesson() {
            if (!this.canGoToNextLesson || this.currentLessonIndex < 0 || this.currentLessonIndex >= this.allLessons.length - 1) return;
            this.selectLesson(this.allLessons[this.currentLessonIndex + 1]);
        },

        selectLesson(lesson) {
            this.destroyVideoPlayers();
            this.currentLesson = lesson;
            this.lessonTab = 'descricao';
            this.videoWatchedToEnd = !lesson.video_url;
            this.$nextTick(() => this.initVideoPlayer());
        },

        destroyVideoPlayers() {
            if (this.ytPlayer && typeof this.ytPlayer.destroy === 'function') {
                this.ytPlayer.destroy();
                this.ytPlayer = null;
            }
            this.ytPlaying = false;
            this.ytPlayerReady = false;
            this.vimeoPlayer = null;
            this.vimeoPlaying = false;
            this.nativeVideoPlaying = false;
        },

        toggleYtPlayPause() {
            if (!this.ytPlayer || typeof this.ytPlayer.getPlayerState !== 'function') return;
            const state = this.ytPlayer.getPlayerState();
            if (state === YT.PlayerState.PLAYING) this.ytPlayer.pauseVideo();
            else this.ytPlayer.playVideo();
        },

        toggleVimeoPlayPause() {
            if (!this.vimeoPlayer) return;
            this.vimeoPlayer.getPaused().then(paused => {
                if (paused) this.vimeoPlayer.play();
                else this.vimeoPlayer.pause();
            }).catch(() => {});
        },

        toggleNativeVideoPlayPause() {
            const el = this.$refs.nativeVideo;
            if (!el) return;
            if (el.paused) el.play();
            else el.pause();
        },

        initVideoPlayer() {
            if (!this.currentLesson || !this.currentLesson.video_url) return;
            const url = this.currentLesson.video_url;
            if (this.isYoutubeUrl(url)) {
                const videoId = this.getYoutubeVideoId(url);
                if (!videoId) return;
                if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                    const self = this;
                    this.pendingYtVideoId = videoId;
                    window.onYouTubeIframeAPIReady = function() {
                        if (self.pendingYtVideoId) {
                            self.createYtPlayer(self.pendingYtVideoId);
                            self.pendingYtVideoId = null;
                        }
                    };
                    return;
                }
                this.createYtPlayer(videoId);
            } else if (this.isVimeoUrl(url)) {
                this.$nextTick(() => {
                    const iframe = this.$refs.vimeoIframe;
                    if (iframe && typeof Vimeo !== 'undefined') {
                        this.vimeoPlayer = new Vimeo.Player(iframe);
                        this.vimeoPlayer.on('ended', () => { this.videoWatchedToEnd = true; });
                        this.vimeoPlayer.on('play', () => { this.vimeoPlaying = true; });
                        this.vimeoPlayer.on('pause', () => { this.vimeoPlaying = false; });
                    }
                });
            }
        },

        createYtPlayer(videoId) {
            const container = document.getElementById('academy-yt-player');
            if (!container) return;
            const self = this;
            this.ytPlayer = new YT.Player(container, {
                videoId: videoId,
                width: '100%',
                height: '100%',
                playerVars: { enablejsapi: 1, controls: 0 },
                events: {
                    onReady: function() {
                        self.ytPlayerReady = true;
                    },
                    onStateChange: function(e) {
                        self.ytPlaying = (e.data === YT.PlayerState.PLAYING);
                        if (e.data === YT.PlayerState.ENDED) self.videoWatchedToEnd = true;
                    }
                }
            });
        },

        isCompleted(id) {
            return this.completedLessons.includes(id);
        },

        async markAsComplete() {
            if (!this.currentLesson || this.isCompleted(this.currentLesson.id)) return;

            try {
                const response = await fetch(`/api/v1/worship/academy/lessons/${this.currentLesson.id}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const res = await response.json();

                if (response.ok) {
                    this.completedLessons.push(this.currentLesson.id);
                    if(window.Toast) Toast.fire({icon: 'success', title: 'Aula concluída!'});
                }
            } catch (err) {
                if(window.Toast) Toast.fire({icon: 'error', title: 'Erro ao registrar progresso.'});
            }
        }
    }));
});
</script>
<script src="https://www.youtube.com/iframe_api" async></script>
<script src="https://player.vimeo.com/api/player.js" async></script>
@endpush
@endsection
