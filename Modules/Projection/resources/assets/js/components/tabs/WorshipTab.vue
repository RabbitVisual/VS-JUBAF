<template>
    <div class="worship-tab-root flex h-full w-full">
    <div class="flex h-full bg-slate-950 flex-1 min-w-0">
        <!-- ========== LEFT COLUMN: Order of Service + Library ========== -->
        <div class="w-full min-w-[280px] max-w-[360px] md:w-[360px] border-r border-slate-800 flex flex-col shrink-0 overflow-hidden">
            <!-- Order of Service (Timeline) -->
            <div class="p-4 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <h3 class="text-sm font-black text-indigo-400 uppercase tracking-[0.15em] flex items-center gap-2 shrink-0">
                        <i class="fa-duotone fa-film"></i> Ordem do culto
                    </h3>
                    <span v-if="selectedSetlist && displayTimelineItems.length" class="text-[10px] font-bold text-slate-500 tabular-nums shrink-0">{{ displayTimelineItems.length }} itens</span>
                </div>
                <button @click="showAddItemModal = true" :disabled="!selectedSetlist" class="w-6 h-6 rounded bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center transition shadow-lg shadow-indigo-900/50 disabled:opacity-50 disabled:cursor-not-allowed" title="Adicionar (Card, Contagem, Seção, Evento, Slide)">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar min-h-0" ref="timelineListRef">
                <p v-if="!selectedSetlist && setlists.length" class="text-[10px] text-slate-500 text-center py-4">Selecione um setlist abaixo para ver a ordem do culto.</p>
                <div v-for="(item, index) in displayTimelineItems" :key="item.id" :data-id="item.id"
                     @click="selectTimelineItem(index)"
                     :class="['group p-3 rounded-xl cursor-pointer border-2 transition-all duration-300 relative overflow-hidden',
                     currentItemIndex === index ? 'bg-indigo-600 border-red-500 text-white shadow-lg shadow-indigo-900/40 ring-2 ring-red-500/50' : 'bg-slate-900/40 border-white/5 text-slate-400 hover:border-slate-700 hover:bg-slate-800/60']">

                    <div class="absolute top-1 right-1 flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition z-20">
                        <span class="drag-handle w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:bg-slate-600 cursor-grab active:cursor-grabbing" title="Arrastar para reordenar">
                            <i class="fa-duotone fa-grip-vertical text-[10px]"></i>
                        </span>
                        <button @click.stop="moveTimelineItemUp(index)" :disabled="index === 0" title="Subir na lista"
                            class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:bg-slate-600 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        </button>
                        <button @click.stop="moveTimelineItemDown(index)" :disabled="index === displayTimelineItems.length - 1" title="Descer na lista"
                            class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:bg-slate-600 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-arrow-down text-[10px]"></i>
                        </button>
                        <button @click.stop="confirmDeleteItem(item)" title="Remover"
                            class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-red-500/10 hover:text-red-500">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-3 relative z-10">
                        <div :class="['w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0',
                             currentItemIndex === index ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-500']">
                            {{ index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-0.5 flex items-center gap-1">
                                <i :class="typeIcon(item.type)" class="text-[10px]"></i>
                                {{ typeLabel(item.type) }}
                            </div>
                            <div class="font-bold text-xs truncate">{{ item.title }}</div>
                        </div>
                        <span v-if="currentItemIndex === index" class="flex items-center gap-1 px-1.5 py-0.5 rounded bg-red-500/20 border border-red-500/50 text-[8px] font-black text-red-400 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Ao vivo
                        </span>
                    </div>

                    <div v-if="currentItemIndex === index" class="absolute bottom-0 left-0 h-0.5 bg-red-500/60 w-full"></div>
                </div>
            </div>

            <!-- Setlist selector — timeline só aparece após selecionar um setlist aqui -->
            <div class="p-4 border-t border-slate-800 bg-slate-900/50 shrink-0">
                <select :value="selectedSetlist?.id ?? ''" @change="onSetlistChange" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs font-bold text-slate-300 py-2 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <option value="">Trocar setlist...</option>
                    <option v-for="s in setlists" :key="s.id" :value="s.id">{{ new Date(s.scheduled_at).toLocaleDateString() }} - {{ s.title }}</option>
                </select>
            </div>

            <!-- Library: Músicas | Bíblia | Imagens -->
            <div class="border-t border-slate-800 flex flex-col shrink-0" style="min-height: 220px;">
                <div class="flex border-b border-slate-800 bg-slate-950">
                    <button @click="libraryTab = 'songs'" :class="['flex-1 py-2.5 text-[10px] font-black uppercase tracking-wider border-b-2 transition', libraryTab === 'songs' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                        <i class="fa-duotone fa-music mr-1.5"></i> Músicas
                    </button>
                    <button @click="libraryTab = 'bible'" :class="['flex-1 py-2.5 text-[10px] font-black uppercase tracking-wider border-b-2 transition', libraryTab === 'bible' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                        <i class="fa-duotone fa-book-bible mr-1.5"></i> Bíblia
                    </button>
                    <button @click="libraryTab = 'media'" :class="['flex-1 py-2.5 text-[10px] font-black uppercase tracking-wider border-b-2 transition', libraryTab === 'media' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                        <i class="fa-duotone fa-images mr-1.5"></i> Imagens
                    </button>
                    <button @click="libraryTab = 'customSlides'" :class="['flex-1 py-2.5 text-[10px] font-black uppercase tracking-wider border-b-2 transition', libraryTab === 'customSlides' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                        <i class="fa-duotone fa-rectangle-slides mr-1.5"></i> Slides
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-3 custom-scrollbar min-h-0">
                    <!-- Músicas -->
                    <div v-if="libraryTab === 'songs'" class="space-y-3">
                        <div class="flex gap-2">
                            <input v-model="songSearchQuery" @keyup.enter="searchSongs" placeholder="Buscar no repertório (músicas já cadastradas)"
                                class="flex-1 bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 focus:ring-1 focus:ring-indigo-500 outline-none placeholder-slate-500">
                            <button @click="searchSongs" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 rounded-lg font-bold text-[10px] uppercase transition shrink-0">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </div>
                        <button @click="showPasteLyrics = !showPasteLyrics" :class="['w-full py-2 rounded-lg text-[10px] font-black uppercase tracking-wider transition flex items-center justify-center gap-2', showPasteLyrics ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white']">
                            <i class="fa-duotone fa-paste"></i> Colar letra
                        </button>
                        <p class="text-[9px] text-slate-500 text-center">Trazer letra de qualquer site ou app (cole no campo abaixo)</p>
                        <div v-if="showPasteLyrics" class="space-y-2 p-2 bg-slate-900/50 rounded-xl border border-slate-700">
                            <input v-model="manualTitle" placeholder="Título" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                            <input v-model="manualArtist" placeholder="Artista" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                            <textarea v-model="manualLyrics" rows="4" placeholder="Cole a letra (linha vazia = novo slide)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500 font-mono placeholder-slate-500 resize-none"></textarea>
                            <button @click="addManualSongFromLibrary" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2 rounded-lg font-bold text-[10px] uppercase transition">
                                Adicionar à ordem
                            </button>
                        </div>
                        <div v-if="isSearching" class="text-center py-4 text-slate-500">
                            <i class="fa-duotone fa-spinner-third fa-spin text-xl"></i>
                        </div>
                        <div v-else-if="hasSearchedSongs && onlineResults.length === 0" class="py-3 px-2 rounded-lg bg-slate-800/50 border border-slate-700 text-[10px] text-slate-400 text-center">
                            Nenhuma música encontrada no repertório. Use <strong class="text-indigo-400">Colar letra</strong> para adicionar a partir de qualquer site ou app.
                        </div>
                        <div v-else class="space-y-1.5">
                            <div v-if="onlineResults.length > 0" class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Resultados</div>
                            <div v-for="res in onlineResults" :key="res.id" class="flex justify-between items-center p-2.5 bg-slate-800/50 rounded-lg border border-slate-700 hover:border-indigo-500/50 transition group">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs text-white truncate">{{ res.title }}</div>
                                    <div class="text-[10px] text-slate-400 truncate">{{ res.artist }}</div>
                                </div>
                                <button @click="addLocalSong(res)" class="text-[10px] font-bold bg-slate-700 hover:bg-indigo-600 text-white px-2.5 py-1 rounded-md uppercase transition shrink-0">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bíblia -->
                    <div v-if="libraryTab === 'bible'" class="space-y-3">
                        <input v-model="bibleRef" placeholder="Ex: João 3:16" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-500">
                        <textarea v-model="bibleText" rows="4" placeholder="Texto (cole ou digite; quebras = slides)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-500 resize-none"></textarea>
                        <button @click="addBibleItem" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2 rounded-lg font-bold text-[10px] uppercase transition">
                            Adicionar à ordem
                        </button>
                    </div>

                    <!-- Imagens / Mídia -->
                    <div v-if="libraryTab === 'media'" class="space-y-3">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Enviar do seu PC</label>
                            <div class="flex gap-2">
                                <input type="file" accept="image/*,video/*" ref="mediaFileInput" @change="onMediaFileSelect"
                                    class="flex-1 text-[10px] text-slate-300 border border-slate-600 rounded-lg cursor-pointer bg-slate-800 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-indigo-600 file:text-white">
                                <button @click="uploadMediaFile" :disabled="!selectedMediaFile || uploadingMedia"
                                    class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-[10px] font-bold uppercase transition shrink-0">
                                    <i v-if="uploadingMedia" class="fa-duotone fa-spinner-third fa-spin"></i>
                                    <i v-else class="fa-duotone fa-upload"></i>
                                    <span class="ml-1">Enviar</span>
                                </button>
                            </div>
                            <p v-if="uploadMediaError" class="text-[10px] text-red-400">{{ uploadMediaError }}</p>
                            <p v-else-if="uploadMediaSuccess" class="text-[10px] text-green-400">Enviado.</p>
                        </div>
                        <div v-if="mediaAssets.length === 0" class="text-slate-500 text-xs py-3 text-center">Nenhum arquivo. Use o envio acima ou atualize a lista.</div>
                        <div v-else class="space-y-1.5">
                            <div v-for="a in mediaAssets" :key="a.id"
                                class="w-full p-2.5 rounded-lg border border-slate-700 bg-slate-800/50 hover:border-slate-600 flex items-center justify-between gap-2 group">
                                <button @click="addMediaItem(a)" class="flex-1 min-w-0 text-left">
                                    <span class="text-xs font-bold text-white truncate block">{{ a.title }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase">{{ a.type }}</span>
                                </button>
                                <button @click.stop="deleteMediaAsset(a)" class="p-1.5 rounded text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition shrink-0" title="Excluir do sistema">
                                    <i class="fa-duotone fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <button @click="loadMediaAssets" class="w-full py-2 text-slate-400 hover:text-white text-[10px] font-bold uppercase transition">
                            <i class="fa-duotone fa-arrows-rotate mr-1"></i> Atualizar lista
                        </button>
                    </div>

                    <!-- Slides customizados -->
                    <div v-if="libraryTab === 'customSlides'" class="space-y-3">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Novo slide</label>
                            <input v-model="customSlideTitle" placeholder="Título" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-500">
                            <textarea v-model="customSlideContent" rows="3" placeholder="Conteúdo (HTML ou texto; linha vazia = novo slide)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-500 resize-none"></textarea>
                            <button @click="createCustomSlide" :disabled="!customSlideTitle.trim() || !customSlideContent.trim() || creatingCustomSlide"
                                class="w-full py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-[10px] font-bold uppercase transition flex items-center justify-center gap-2">
                                <i v-if="creatingCustomSlide" class="fa-duotone fa-spinner-third fa-spin"></i>
                                <i v-else class="fa-duotone fa-plus"></i>
                                Criar slide
                            </button>
                            <p v-if="customSlideError" class="text-[10px] text-red-400">{{ customSlideError }}</p>
                        </div>
                        <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Slides salvos</div>
                        <div v-if="customSlides.length === 0" class="text-slate-500 text-xs py-3 text-center">Nenhum slide customizado. Crie um acima.</div>
                        <div v-else class="space-y-1.5">
                            <div v-for="s in customSlides" :key="s.id"
                                class="p-2.5 rounded-lg border border-slate-700 bg-slate-800/50 hover:border-slate-600 flex items-center justify-between gap-2 group">
                                <span class="text-xs font-bold text-white truncate flex-1 min-w-0">{{ s.title }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button @click="openEditSlide(s)" class="px-2.5 py-1 rounded-md bg-slate-700 hover:bg-amber-600/30 text-amber-400 hover:text-amber-300 text-[10px] font-bold uppercase transition flex items-center gap-1.5" title="Editar slide">
                                        <i class="fa-duotone fa-pen text-[10px]"></i>
                                        Editar
                                    </button>
                                    <button @click="addCustomSlideToTimeline(s)" class="px-2.5 py-1 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold uppercase transition">
                                        À ordem
                                    </button>
                                    <button @click="goLiveCustomSlide(s)" class="px-2.5 py-1 rounded-md bg-slate-700 hover:bg-indigo-500/30 text-indigo-400 text-[10px] font-bold uppercase transition">
                                        Ir ao vivo
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button @click="loadCustomSlides" class="w-full py-2 text-slate-400 hover:text-white text-[10px] font-bold uppercase transition">
                            <i class="fa-duotone fa-arrows-rotate mr-1"></i> Atualizar lista
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Editar slide customizado -->
            <div v-if="editingSlide" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="editingSlide = null">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-lg p-5">
                    <h3 class="text-sm font-black text-indigo-400 uppercase tracking-widest mb-3">Editar slide</h3>
                    <input v-model="editSlideTitle" placeholder="Título" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 mb-2 outline-none focus:ring-1 focus:ring-indigo-500">
                    <textarea v-model="editSlideContent" rows="4" placeholder="Conteúdo (HTML ou texto)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-xs text-white px-3 py-2 mb-3 outline-none focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                    <div class="flex gap-2 justify-end">
                        <button @click="editingSlide = null" class="px-4 py-2 rounded-lg bg-slate-700 text-slate-300 text-xs font-bold uppercase transition hover:bg-slate-600">Cancelar</button>
                        <button @click="saveEditSlide" :disabled="savingEditSlide || !editSlideTitle.trim() || !editSlideContent.trim()"
                            class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-xs font-bold uppercase transition flex items-center gap-2">
                            <i v-if="savingEditSlide" class="fa-duotone fa-spinner-third fa-spin"></i>
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== CENTER: Preview (full lyrics by section + Projetar agora + mini preview + slides) ========== -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-900">
            <!-- Item header -->
            <div v-if="selectedItem" class="h-14 border-b border-slate-800 flex items-center px-4 md:px-6 justify-between bg-slate-950 shrink-0 flex-wrap gap-2">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-[10px] font-black text-indigo-400 uppercase tracking-widest">
                            EM EXECUÇÃO
                        </div>
                        <div v-if="currentStanzaIndex >= 0" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500/20 border border-red-500/50 shadow-[0_0_12px_rgba(239,68,68,0.3)]">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-[9px] font-black text-red-400 uppercase tracking-wider">Ao vivo</span>
                        </div>
                    </div>
                    <div class="font-black text-white tracking-tight truncate">{{ selectedItem.title }}</div>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                    <span class="text-indigo-400">{{ currentStanzaIndex + 1 }}</span> / {{ selectedItem.slides.length }} slides
                </div>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col min-h-0 p-4 gap-4">
                <!-- Grade de slides (clickable cards) + Full lyrics -->
                <div v-if="selectedItem && selectedItem.slides?.length" class="flex-1 min-h-0 flex flex-col gap-4">
                    <!-- Grade de slides: cards clicáveis -->
                    <div class="shrink-0">
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Pré-visualização — slides (clique para projetar)</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 max-h-40 overflow-y-auto custom-scrollbar">
                            <div v-for="(stanza, index) in selectedItem.slides" :key="index"
                                 @click="goLiveStanza(index)"
                                 :class="['p-3 rounded-xl border cursor-pointer transition-all duration-200 min-h-[72px] flex flex-col justify-between',
                                 currentStanzaIndex === index ? 'border-indigo-500 bg-indigo-600/80 text-white shadow-lg shadow-indigo-900/30 ring-2 ring-red-500/50' : 'border-slate-700 bg-slate-800/50 hover:border-slate-600 hover:bg-slate-800 text-slate-300']">
                                <span class="text-[9px] font-black uppercase tracking-wider opacity-80">{{ stanza.label || 'Slide ' + (index + 1) }}</span>
                                <div class="text-[10px] font-bold leading-tight line-clamp-2" v-html="stanza.html"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Full lyrics by section (readable block) -->
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-5 overflow-y-auto custom-scrollbar flex-1 min-h-0">
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Letras completas</h4>
                        <div class="space-y-4 text-slate-200">
                            <template v-for="(stanza, index) in selectedItem.slides" :key="index">
                                <div class="border-l-2 border-indigo-500/50 pl-4 py-1">
                                    <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">{{ stanza.label || 'Slide ' + (index + 1) }}</div>
                                    <div class="text-sm font-bold leading-relaxed" v-html="stanza.html"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Projetar agora + Ao vivo / Próximo -->
                    <div class="flex gap-4 items-stretch shrink-0">
                        <div class="flex-1 flex items-center">
                            <button @click="pushCurrentToLive" :disabled="!selectedItem || !selectedItem.slides?.length"
                                class="w-full max-w-xs py-3 bg-green-600 hover:bg-green-500 disabled:opacity-30 disabled:cursor-not-allowed text-white text-xs font-black rounded-xl shadow-lg transition-all active:scale-95 uppercase tracking-widest flex items-center justify-center gap-2">
                                <i class="fa-duotone fa-circle-arrow-up"></i> Projetar agora
                            </button>
                        </div>
                        <!-- Ao vivo: mini preview do slide atual na tela -->
                        <div class="w-40 h-24 rounded-xl border-2 border-red-500/40 bg-black overflow-hidden flex flex-col shrink-0 shadow-inner">
                            <span class="text-[8px] font-black text-red-500 uppercase tracking-wider px-2 py-0.5 bg-red-500/10 border-b border-red-500/20">Ao vivo</span>
                            <div class="flex-1 flex items-center justify-center min-h-0">
                                <div v-if="currentSlideContent" v-html="currentSlideContent" class="text-white text-[9px] font-bold text-center leading-tight p-1.5 scale-90 origin-center w-full"></div>
                                <div v-else class="text-slate-600 text-[8px] font-black uppercase">—</div>
                            </div>
                        </div>
                        <!-- Próximo: minipreview do próximo slide -->
                        <div class="w-40 h-24 rounded-xl border border-slate-700 bg-slate-900/80 overflow-hidden flex flex-col shrink-0">
                            <span class="text-[8px] font-black text-amber-500 uppercase tracking-wider px-2 py-0.5 bg-amber-500/10 border-b border-slate-700">Próximo</span>
                            <div class="flex-1 flex items-center justify-center min-h-0 p-1.5">
                                <div v-if="nextSlidePreview && nextSlidePreview.content" class="text-slate-400 text-[8px] font-bold text-center leading-tight line-clamp-3" v-html="(typeof nextSlidePreview.content === 'string' ? nextSlidePreview.content : '').replace(/<[^>]+>/g,' ').slice(0,120)"></div>
                                <div v-else class="text-slate-600 text-[8px] font-black uppercase">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- Clickable slides (footer) -->
                    <div class="flex items-center gap-2 flex-wrap shrink-0">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">Ir para slide:</span>
                        <button v-for="(_, idx) in selectedItem.slides" :key="idx"
                                @click="goLiveStanza(idx)"
                                :class="['w-8 h-8 rounded-lg font-black text-xs transition-all',
                                currentStanzaIndex === idx ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50 ring-2 ring-red-500 ring-offset-2 ring-offset-slate-900' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white border border-slate-700']">
                            {{ idx + 1 }}
                        </button>
                    </div>
                </div>

                <!-- Zero state -->
                <div v-else class="flex-1 flex flex-col items-center justify-center text-slate-600 gap-4">
                    <i class="fa-duotone fa-clapperboard-play text-6xl opacity-20"></i>
                    <div class="text-xs font-black uppercase tracking-[0.3em] opacity-40">Selecione um item da Ordem do culto</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Card, Countdown, Section, Event only -->
    <div v-if="showAddItemModal" class="fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative bg-slate-900 border border-slate-700 w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh]">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-950 rounded-t-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-duotone fa-plus-circle text-indigo-500"></i> Adicionar à ordem
                </h3>
                <button @click="closeModal" class="text-slate-500 hover:text-white transition"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="flex border-b border-slate-800 bg-slate-950">
                <button @click="modalTab = 'card'" :class="['flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition', modalTab === 'card' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                    <i class="fa-duotone fa-message-captions mr-2"></i> Card
                </button>
                <button @click="modalTab = 'countdown'" :class="['flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition', modalTab === 'countdown' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                    <i class="fa-duotone fa-timer mr-2"></i> Contagem
                </button>
                <button @click="modalTab = 'section'" :class="['flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition', modalTab === 'section' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                    <i class="fa-duotone fa-heading mr-2"></i> Seção
                </button>
                <button @click="modalTab = 'event'" :class="['flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition', modalTab === 'event' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                    <i class="fa-duotone fa-calendar-star mr-2"></i> Evento
                </button>
                <button @click="onModalTabSlide" :class="['flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition', modalTab === 'slide' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-transparent text-slate-500 hover:text-slate-300']">
                    <i class="fa-duotone fa-rectangle-slides mr-2"></i> Slide
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                <div v-if="modalTab === 'card'" class="space-y-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Banner / Card na ordem de culto</p>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Template (opcional)</label>
                        <select v-model="cardTemplateId" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                            <option :value="null">Nenhum (estilo padrão)</option>
                            <option v-for="t in cardTemplates" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Título</label>
                        <input v-model="cardTitle" placeholder="Ex: MOMENTO DE ORAÇÃO" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            <button v-for="s in CARD_SIZE_OPTIONS" :key="'title-'+s" @click="cardTitleSize = s" :class="cardTitleSize === s ? 'bg-indigo-600 text-white' : 'bg-slate-700 text-slate-400 hover:bg-slate-600'" class="px-2 py-1 rounded text-[10px] font-bold uppercase">{{ s }}</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Subtítulo</label>
                        <input v-model="cardSubtitle" placeholder="Ex: Salmo 145:8-9 e 16" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            <button v-for="s in CARD_SIZE_OPTIONS" :key="'sub-'+s" @click="cardSubtitleSize = s" :class="cardSubtitleSize === s ? 'bg-indigo-600 text-white' : 'bg-slate-700 text-slate-400 hover:bg-slate-600'" class="px-2 py-1 rounded text-[10px] font-bold uppercase">{{ s }}</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Descrição / texto</label>
                        <textarea v-model="cardText" rows="4" placeholder="Texto adicional (opcional). Ex: citação, aviso." class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            <button v-for="s in CARD_SIZE_OPTIONS" :key="'desc-'+s" @click="cardDescriptionSize = s" :class="cardDescriptionSize === s ? 'bg-indigo-600 text-white' : 'bg-slate-700 text-slate-400 hover:bg-slate-600'" class="px-2 py-1 rounded text-[10px] font-bold uppercase">{{ s }}</button>
                        </div>
                    </div>
                    <button @click="addCardItem" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-lg font-bold text-xs uppercase tracking-widest transition">Adicionar à timeline</button>
                </div>
                <div v-if="modalTab === 'countdown'" class="space-y-4">
                    <input v-model="countdownLabel" placeholder="Label (ex: Retorno em 5 min)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                    <input v-model.number="countdownSeconds" type="number" min="1" placeholder="Segundos (ex: 300)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                    <button @click="addCountdownItem" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-lg font-bold text-xs uppercase tracking-widest transition">Adicionar Contagem</button>
                </div>
                <div v-if="modalTab === 'section'" class="space-y-4">
                    <input v-model="sectionTitle" placeholder="Título da seção (ex: Louvor)" class="w-full bg-slate-800 border-slate-700 rounded-lg text-sm text-white px-4 py-2 outline-none focus:ring-1 focus:ring-indigo-500">
                    <button @click="addSectionItem" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-lg font-bold text-xs uppercase tracking-widest transition">Adicionar Seção</button>
                </div>
                <div v-if="modalTab === 'event'" class="space-y-4">
                    <div v-if="upcomingEvents.length === 0" class="text-slate-500 text-sm py-4">Nenhum evento próximo.</div>
                    <div v-else class="space-y-2">
                        <button v-for="ev in upcomingEvents" :key="ev.id" @click="addEventSpotlightItem(ev)"
                            class="w-full p-3 rounded-lg border border-slate-700 bg-slate-800/50 hover:border-indigo-500 text-left">
                            <span class="text-sm font-bold text-white block">{{ ev.title }}</span>
                            <span class="text-xs text-slate-400">{{ ev.start_date ? new Date(ev.start_date).toLocaleString('pt-BR') : '' }}</span>
                        </button>
                    </div>
                </div>
                <div v-if="modalTab === 'slide'" class="space-y-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Slide customizado na ordem do culto</p>
                    <div v-if="modalCustomSlides.length === 0" class="text-slate-500 text-sm py-4">Nenhum slide salvo. Crie um na aba <strong>Slides</strong> da biblioteca.</div>
                    <div v-else class="space-y-2">
                        <button v-for="s in modalCustomSlides" :key="s.id" @click="addCustomSlideToTimeline(s)"
                            class="w-full p-3 rounded-lg border border-slate-700 bg-slate-800/50 hover:border-indigo-500 text-left flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-white truncate">{{ s.title }}</span>
                            <span class="text-[10px] font-bold text-indigo-400 uppercase shrink-0">Adicionar à ordem</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';
import Sortable from 'sortablejs';

const props = defineProps({
    state: { type: Object, default: null },
    initialSetlistId: { type: [String, Number], default: null }
});

const WORSHIP_API = '/api/v1/worship';
const emit = defineEmits(['preview', 'go-live']);

const showAddItemModal = ref(false);
const modalTab = ref('card');
const libraryTab = ref('songs');
const showPasteLyrics = ref(false);
const isSearching = ref(false);

const songSearchQuery = ref('');
const onlineResults = ref([]);
const hasSearchedSongs = ref(false);
const bibleRef = ref('');
const bibleText = ref('');
const CARD_SIZE_OPTIONS = ['xs', 'sm', 'md', 'lg', 'xl'];
const cardTitle = ref('');
const cardSubtitle = ref('');
const cardText = ref('');
const cardTitleSize = ref('md');
const cardSubtitleSize = ref('md');
const cardDescriptionSize = ref('md');
const cardTemplateId = ref(null);
const cardTemplates = ref([]);
const manualTitle = ref('');
const manualArtist = ref('');
const manualLyrics = ref('');
const mediaAssets = ref([]);
const mediaFileInput = ref(null);
const selectedMediaFile = ref(null);
const uploadingMedia = ref(false);
const uploadMediaError = ref('');
const uploadMediaSuccess = ref(false);
const countdownLabel = ref('');
const countdownSeconds = ref(300);
const sectionTitle = ref('');
const upcomingEvents = ref([]);

const customSlides = ref([]);
const customSlideTitle = ref('');
const customSlideContent = ref('');
const creatingCustomSlide = ref(false);
const customSlideError = ref('');
const editingSlide = ref(null);
const editSlideTitle = ref('');
const editSlideContent = ref('');
const savingEditSlide = ref(false);

const selectedSetlist = ref(null);
const setlists = ref([]);
const timelineItems = ref([]);
const timelineListRef = ref(null);
let sortableInstance = null;
const currentItemIndex = ref(-1);
const currentStanzaIndex = ref(-1);

/** Timeline única: só itens do setlist (API), incluindo tipo custom_slide */
const displayTimelineItems = computed(() => timelineItems.value || []);

/** Slides customizados para o modal "Adicionar > Slide" */
const modalCustomSlides = computed(() => customSlides.value || []);

const selectedItem = computed(() => {
    const items = displayTimelineItems.value;
    if (currentItemIndex.value === -1 || !items.length) return null;
    return items[currentItemIndex.value] ?? null;
});

const currentSlideContent = computed(() => {
    if (!selectedItem.value?.slides?.length || currentStanzaIndex.value < 0) return null;
    const stanza = selectedItem.value.slides[currentStanzaIndex.value];
    return formatStanzaContent(stanza);
});

const nextSlidePreview = computed(() => {
    const items = displayTimelineItems.value;
    if (!items.length) return null;
    let itemIdx = currentItemIndex.value;
    let slideIdx = currentStanzaIndex.value + 1;
    if (itemIdx < 0) {
        itemIdx = 0;
        slideIdx = 0;
    }
    const item = items[itemIdx];
    if (!item?.slides?.length) {
        if (itemIdx < items.length - 1) {
            const nextItem = items[itemIdx + 1];
            if (nextItem?.slides?.length) return { type: 'slide', content: formatStanzaContent(nextItem.slides[0]), title: nextItem.title };
        }
        return null;
    }
    if (slideIdx < item.slides.length) {
        const stanza = item.slides[slideIdx];
        if (stanza.media_type && stanza.url) return { type: stanza.media_type, url: projectionAssetUrl(stanza.url), content: '', title: item.title };
        if (stanza.countdown_seconds !== undefined) return { type: 'countdown', content: '', countdown_seconds: stanza.countdown_seconds, title: stanza.label || item.title };
        return { type: 'slide', content: formatStanzaContent(stanza), title: item.title };
    }
    if (itemIdx < items.length - 1) {
        const nextItem = items[itemIdx + 1];
        if (nextItem?.slides?.length) return { type: 'slide', content: formatStanzaContent(nextItem.slides[0]), title: nextItem.title };
    }
    return null;
});


function typeIcon(type) {
    const map = {
        song: 'fa-duotone fa-music',
        bible: 'fa-duotone fa-book-bible',
        card: 'fa-duotone fa-message-captions',
        video: 'fa-duotone fa-video',
        audio: 'fa-duotone fa-volume-high',
        image: 'fa-duotone fa-image',
        countdown: 'fa-duotone fa-timer',
        section_header: 'fa-duotone fa-heading',
        event_spotlight: 'fa-duotone fa-calendar-star',
        custom_slide: 'fa-duotone fa-rectangle-slides'
    };
    return map[type] || 'fa-duotone fa-circle';
}

function typeLabel(type) {
    const map = {
        song: 'Louvor',
        bible: 'Bíblia',
        card: 'Card',
        video: 'Vídeo',
        audio: 'Áudio',
        image: 'Imagem',
        countdown: 'Contagem',
        section_header: 'Seção',
        event_spotlight: 'Evento',
        custom_slide: 'Slide'
    };
    return map[type] || (type ? String(type) : '');
}

const fetchSetlists = () => {
    axios.get(`${WORSHIP_API}/setlists`, { params: { limit: 50 } })
        .then(res => {
            setlists.value = res.data?.data ?? [];
            // Só preenche a timeline se a URL tiver setlist (ex: /console/1). Caso contrário fica vazio até o usuário escolher em "Trocar setlist..."
            if (setlists.value.length > 0 && currentItemIndex.value === -1 && !selectedSetlist.value) {
                const id = props.initialSetlistId != null ? String(props.initialSetlistId) : null;
                if (id) {
                    const setlist = setlists.value.find(s => String(s.id) === id);
                    selectSetlist(setlist || setlists.value[0]);
                }
            }
        })
        .catch(() => { setlists.value = []; });
};

const onSetlistChange = (e) => {
    const id = e.target.value;
    if (!id) return;
    const setlist = setlists.value.find(s => s.id == id);
    if (setlist) selectSetlist(setlist);
};

const selectSetlist = (setlist) => {
    selectedSetlist.value = setlist;
    axios.get(`${WORSHIP_API}/setlists/${setlist.id}`)
        .then(res => {
            const payload = res.data?.data;
            timelineItems.value = Array.isArray(payload?.items) ? payload.items : [];
            currentItemIndex.value = timelineItems.value.length > 0 ? 0 : -1;
            currentStanzaIndex.value = -1;
        })
        .catch(() => {
            timelineItems.value = [];
            currentItemIndex.value = -1;
            currentStanzaIndex.value = -1;
        });
};

const loadMediaAssets = () => {
    axios.get('/api/v1/projection/assets').then(res => {
        mediaAssets.value = res.data?.data ?? res.data ?? [];
    }).catch(() => { mediaAssets.value = []; });
};

const onMediaFileSelect = (e) => {
    selectedMediaFile.value = e.target.files?.[0] ?? null;
    uploadMediaError.value = '';
    uploadMediaSuccess.value = false;
};

const uploadMediaFile = () => {
    if (!selectedMediaFile.value) return;
    uploadingMedia.value = true;
    uploadMediaError.value = '';
    uploadMediaSuccess.value = false;
    const formData = new FormData();
    formData.append('file', selectedMediaFile.value);
    axios.post('/api/v1/projection/assets', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => {
        const asset = res.data?.data ?? res.data;
        if (asset) mediaAssets.value.unshift(asset);
        selectedMediaFile.value = null;
        if (mediaFileInput.value) mediaFileInput.value.value = '';
        uploadMediaSuccess.value = true;
        setTimeout(() => { uploadMediaSuccess.value = false; }, 3000);
    }).catch(() => {
        uploadMediaError.value = 'Falha no envio.';
    }).finally(() => {
        uploadingMedia.value = false;
    });
};

const deleteMediaAsset = (asset) => {
    if (!asset?.id) return;
    if (!confirm('Excluir esta mídia do sistema? O arquivo será removido permanentemente.')) return;
    axios.delete(`/api/v1/projection/assets/${asset.id}`)
        .then(() => {
            mediaAssets.value = mediaAssets.value.filter(a => a.id !== asset.id);
        })
        .catch(() => {
            alert('Não foi possível excluir. Tente novamente.');
        });
};

const loadUpcomingEvents = () => {
    axios.get('/api/v1/projection/events/upcoming', { params: { limit: 20 } }).then(res => {
        upcomingEvents.value = res.data?.data ?? res.data ?? [];
    }).catch(() => { upcomingEvents.value = []; });
};

const loadCustomSlides = () => {
    axios.get('/api/v1/projection/slides').then(res => {
        const list = res.data?.data ?? res.data ?? [];
        customSlides.value = list;
    }).catch(() => { customSlides.value = []; });
};

const createCustomSlide = () => {
    const title = (customSlideTitle.value || '').trim();
    const content = (customSlideContent.value || '').trim();
    if (!title || !content) return;
    creatingCustomSlide.value = true;
    customSlideError.value = '';
    axios.post('/api/v1/projection/slides', { title, content })
        .then(res => {
            const slide = res.data?.data ?? res.data;
            if (slide) customSlides.value = [slide, ...customSlides.value];
            customSlideTitle.value = '';
            customSlideContent.value = '';
        })
        .catch(() => {
            customSlideError.value = 'Falha ao criar slide.';
        })
        .finally(() => { creatingCustomSlide.value = false; });
};

const goLiveCustomSlide = (slide) => {
    const content = (slide.content || '').trim();
    const html = content.includes('<') ? content : '<p>' + content.replace(/\n/g, '</p><p>') + '</p>';
    emit('go-live', { type: 'slide', content: html, footer: slide.title || null });
};

const openEditSlide = (s) => {
    editingSlide.value = { id: s.id, title: s.title, content: s.content };
    editSlideTitle.value = s.title || '';
    editSlideContent.value = s.content || '';
};

const saveEditSlide = () => {
    if (!editingSlide.value?.id || !editSlideTitle.value?.trim() || !editSlideContent.value?.trim()) return;
    savingEditSlide.value = true;
    axios.put(`/api/v1/projection/slides/${editingSlide.value.id}`, {
        title: editSlideTitle.value.trim(),
        content: editSlideContent.value.trim()
    }).then(res => {
        const updated = res.data?.data ?? res.data;
        if (updated) {
            const idx = customSlides.value.findIndex(x => x.id === updated.id);
            if (idx >= 0) customSlides.value[idx] = updated;
        }
        editingSlide.value = null;
    }).catch(() => {
        alert('Não foi possível salvar. Tente novamente.');
    }).finally(() => { savingEditSlide.value = false; });
};

watch(libraryTab, (tab) => {
    if (tab === 'media') loadMediaAssets();
    if (tab === 'customSlides') loadCustomSlides();
});
watch(showAddItemModal, (open) => {
    if (open && modalTab.value === 'event') loadUpcomingEvents();
    if (open && modalTab.value === 'slide') loadCustomSlides();
});

function onModalTabSlide() {
    modalTab.value = 'slide';
    loadCustomSlides();
}

function addCustomSlideToTimeline(slide) {
    addItemToTimeline({ type: 'custom_slide', custom_slide_id: slide.id });
    closeModal();
}

const closeModal = () => {
    showAddItemModal.value = false;
    cardTitle.value = '';
    cardSubtitle.value = '';
    cardText.value = '';
    cardTitleSize.value = 'md';
    cardSubtitleSize.value = 'md';
    cardDescriptionSize.value = 'md';
    cardTemplateId.value = null;
    countdownLabel.value = '';
    countdownSeconds.value = 300;
    sectionTitle.value = '';
};

const searchSongs = () => {
    if (!songSearchQuery.value || songSearchQuery.value.length < 2) return;
    isSearching.value = true;
    hasSearchedSongs.value = true;
    axios.get(`${WORSHIP_API}/songs`, { params: { q: songSearchQuery.value, limit: 30 } })
        .then(res => {
            onlineResults.value = res.data?.data ?? res.data ?? [];
        })
        .finally(() => { isSearching.value = false });
};

const addLocalSong = (result) => {
    addItemToTimeline({
        type: 'song',
        title: result.title,
        artist: result.artist,
        song_id: result.id
    });
};

const addBibleItem = () => {
    if (!bibleText.value) return;
    const lines = bibleText.value.split('\n').filter(l => l.trim());
    const chunks = [];
    for (let i = 0; i < lines.length; i += 2) {
        chunks.push(lines.slice(i, i + 2).join('<br>'));
    }
    const slides = chunks.map((html, idx) => ({
        label: `${bibleRef.value} (${idx+1}/${chunks.length})`,
        html: html
    }));
    addItemToTimeline({
        type: 'bible',
        title: bibleRef.value || 'Leitura Bíblica',
        reference: bibleRef.value,
        slides: slides
    });
    bibleRef.value = '';
    bibleText.value = '';
};

const addCardItem = () => {
    const title = (cardTitle.value || '').trim() || 'Banner';
    const parts = [cardSubtitle.value, cardText.value].map(s => (s || '').trim()).filter(Boolean);
    const text = parts.join('\n') || title;
    addItemToTimeline({
        type: 'card',
        title,
        text,
        title_size: cardTitleSize.value || 'md',
        subtitle_size: cardSubtitleSize.value || 'md',
        description_size: cardDescriptionSize.value || 'md',
        card_template_id: cardTemplateId.value || undefined
    });
    closeModal();
};

const addMediaItem = (asset) => {
    addItemToTimeline({
        type: asset.type,
        title: asset.title,
        asset_id: asset.id,
        url: asset.url || asset.file_path
    });
};

const addCountdownItem = () => {
    addItemToTimeline({
        type: 'countdown',
        title: countdownLabel.value || 'Contagem',
        duration_seconds: countdownSeconds.value || 300,
        label: countdownLabel.value || ''
    });
    closeModal();
};

const addSectionItem = () => {
    if (!sectionTitle.value) return;
    addItemToTimeline({ type: 'section_header', title: sectionTitle.value });
    sectionTitle.value = '';
    closeModal();
};

const addEventSpotlightItem = (ev) => {
    addItemToTimeline({ type: 'event_spotlight', title: ev.title, event_id: ev.id });
    closeModal();
};

const addManualSongFromLibrary = () => {
    if (!manualTitle.value || !manualLyrics.value) {
        alert('Título e letra são obrigatórios.');
        return;
    }
    addItemToTimeline({
        type: 'song',
        title: manualTitle.value,
        artist: manualArtist.value || 'Desconhecido',
        content: manualLyrics.value
    });
    manualTitle.value = '';
    manualArtist.value = '';
    manualLyrics.value = '';
    showPasteLyrics.value = false;
};

const addItemToTimeline = (data) => {
    if (!selectedSetlist.value) return;
    axios.post('/api/v1/projection/timeline/items', {
        setlist_id: selectedSetlist.value.id,
        ...data
    }).then(() => {
        selectSetlist(selectedSetlist.value);
    }).catch(err => {
        console.error(err);
        alert('Erro ao adicionar item: ' + (err.response?.data?.error || err.message));
    });
};

const selectTimelineItem = (index) => {
    currentItemIndex.value = index;
    currentStanzaIndex.value = -1;
};

const formatStanzaContent = (stanza) => {
    return `<div class="lyric-slide"><div class="lyric-text">${stanza.html}</div></div>`;
};

const baseLivePayload = () => ({
    current_setlist_id: selectedSetlist.value?.id ?? null,
    current_item_id: selectedItem.value?.id ?? null,
    current_slide_index: currentStanzaIndex.value,
    current_item_title: selectedItem.value?.title ?? null
});

/** Normalize projection asset URL so /storage/projection_assets/xxx is served via API (avoids 403). */
const projectionAssetUrl = (url) => {
    if (!url || typeof url !== 'string') return url;
    if (url.startsWith('/storage/projection_assets/')) return '/api/v1/projection/assets/serve/' + url.split('/').pop();
    return url;
};

const goLiveStanza = (index) => {
    currentStanzaIndex.value = index;
    const stanza = selectedItem.value.slides[index];
    const base = { ...baseLivePayload(), current_slide_index: index };
    let payload = { type: 'slide', content: formatStanzaContent(stanza), footer: selectedItem.value.type === 'bible' ? selectedItem.value.title : null };
    if (stanza.media_type && stanza.url) {
        payload = { ...base, type: stanza.media_type, url: projectionAssetUrl(stanza.url), footer: selectedItem.value.title || null };
    } else if (stanza.countdown_seconds !== undefined) {
        payload = { ...base, type: 'countdown', content: '', countdown_seconds: stanza.countdown_seconds, footer: stanza.label || null };
    } else if (selectedItem.value.type === 'event_spotlight' && stanza.event) {
        payload = { ...base, type: 'slide', content: stanza.html || formatStanzaContent(stanza), footer: stanza.event.title || null };
    } else {
        payload = { ...base, ...payload };
        if (selectedItem.value.type === 'card' && selectedItem.value.content?.card_template_id) {
            const tid = selectedItem.value.content.card_template_id;
            const t = cardTemplates.value.find(x => x.id === tid);
            if (t) {
                const style = buildCardWrapperStyle(t);
                if (style) payload.content = `<div style="${style.replace(/"/g, '&quot;')}">${payload.content}</div>`;
            }
        }
    }
    emit('go-live', payload);
    emit('preview', { type: payload.type, content: payload.content, url: payload.url });
};

const pushCurrentToLive = () => {
    if (!selectedItem.value?.slides?.length) return;
    const idx = currentStanzaIndex.value >= 0 ? currentStanzaIndex.value : 0;
    currentStanzaIndex.value = idx;
    goLiveStanza(idx);
};

const confirmDeleteItem = (item) => {
    if (confirm(`Remover "${item.title}" da ordem?`)) {
        axios.delete(`/api/v1/projection/timeline/items/${item.id}`)
            .then(() => selectSetlist(selectedSetlist.value))
            .catch(() => alert('Não foi possível remover. Tente novamente.'));
    }
};

const moveTimelineItemUp = (index) => {
    if (index <= 0 || !selectedSetlist.value) return;
    const items = [...timelineItems.value];
    [items[index - 1], items[index]] = [items[index], items[index - 1]];
    const payload = items.map((it, i) => ({ id: it.id, order: i }));
    axios.post('/api/v1/projection/timeline/reorder', { items: payload })
        .then(() => selectSetlist(selectedSetlist.value))
        .catch(err => { console.error(err); alert('Erro ao reordenar.'); });
};

const moveTimelineItemDown = (index) => {
    if (index < 0 || index >= timelineItems.value.length - 1 || !selectedSetlist.value) return;
    const items = [...timelineItems.value];
    [items[index], items[index + 1]] = [items[index + 1], items[index]];
    const payload = items.map((it, i) => ({ id: it.id, order: i }));
    axios.post('/api/v1/projection/timeline/reorder', { items: payload })
        .then(() => selectSetlist(selectedSetlist.value))
        .catch(err => { console.error(err); alert('Erro ao reordenar.'); });
};

const next = () => {
    if (!selectedItem.value) return;
    if (currentStanzaIndex.value < selectedItem.value.slides.length - 1) {
        goLiveStanza(currentStanzaIndex.value + 1);
    } else if (currentItemIndex.value < displayTimelineItems.value.length - 1) {
        currentItemIndex.value++;
        goLiveStanza(0);
    }
};

const prev = () => {
    if (!selectedItem.value) return;
    if (currentStanzaIndex.value > 0) {
        goLiveStanza(currentStanzaIndex.value - 1);
    } else if (currentItemIndex.value > 0) {
        currentItemIndex.value--;
        const prevItem = displayTimelineItems.value[currentItemIndex.value];
        if (prevItem?.slides?.length) goLiveStanza(prevItem.slides.length - 1);
    }
};

const nextItem = () => {
    if (!selectedItem.value || currentItemIndex.value >= displayTimelineItems.value.length - 1) return;
    currentItemIndex.value++;
    goLiveStanza(0);
};

const prevItem = () => {
    if (!selectedItem.value || currentItemIndex.value <= 0) return;
    currentItemIndex.value--;
    const prevItem = displayTimelineItems.value[currentItemIndex.value];
    goLiveStanza(prevItem.slides.length - 1);
};

defineExpose({ next, prev, nextItem, prevItem, nextSlidePreview });

const buildCardWrapperStyle = (template) => {
    if (!template || typeof template !== 'object') return '';
    const parts = [];
    if (template.background_type === 'solid' && template.background_value) {
        parts.push('background:' + template.background_value);
    } else if (template.background_type === 'gradient' && template.background_value) {
        parts.push('background:' + template.background_value);
    } else if (template.background_type === 'image' && template.background_value) {
        parts.push('background-image:url(' + template.background_value + ')');
        parts.push('background-size:cover');
        parts.push('background-position:center');
    }
    if (template.font_family) parts.push('font-family:' + template.font_family);
    return parts.length ? parts.join(';') : '';
};

function initSortable() {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
    const el = timelineListRef.value;
    if (!el || !selectedSetlist.value || displayTimelineItems.value.length === 0) return;
    const itemNodes = el.querySelectorAll('[data-id]');
    if (itemNodes.length === 0) return;
    sortableInstance = Sortable.create(el, {
        draggable: '[data-id]',
        handle: '.drag-handle',
        animation: 150,
        onEnd(evt) {
            const parent = timelineListRef.value;
            if (!parent || !selectedSetlist.value) return;
            const children = [...parent.querySelectorAll('[data-id]')];
            const payload = children.map((node, i) => {
                const id = parseInt(node.getAttribute('data-id'), 10);
                return { id: isNaN(id) ? null : id, order: i };
            }).filter(p => p.id != null);
            if (payload.length === 0) return;
            axios.post('/api/v1/projection/timeline/reorder', { items: payload })
                .then(() => selectSetlist(selectedSetlist.value))
                .catch(err => { console.error(err); alert('Erro ao reordenar.'); });
        }
    });
}

watch([selectedSetlist, displayTimelineItems], () => {
    nextTick(() => initSortable());
}, { deep: true });

onMounted(() => {
    fetchSetlists();
    loadCustomSlides();
    axios.get('/api/v1/projection/card-templates').then(res => {
        cardTemplates.value = res.data?.data ?? res.data ?? [];
    }).catch(() => {});
});
</script>
