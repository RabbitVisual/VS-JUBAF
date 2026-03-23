<template>
    <div class="flex h-screen bg-slate-900 text-white overflow-hidden font-sans" :class="{ 'console-compact': isCompact }">
        <!-- Main Content Area (Quelea layout: left + center from WorshipTab, no tab sidebar) -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-900 overflow-hidden">
            <header class="h-14 bg-slate-950 border-b border-slate-800 flex items-center px-6 justify-between shrink-0">
                <div class="flex items-center gap-4">
                    <a :href="dashboardUrl" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:text-white hover:bg-slate-800 transition shrink-0" title="Voltar ao Painel">
                        <i class="fa-duotone fa-arrow-left text-lg"></i>
                    </a>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-indigo-500 uppercase tracking-[0.4em] leading-none mb-0.5">Vertex System Control</span>
                        <h2 class="text-[10px] font-black tracking-widest uppercase text-white opacity-40">
                            {{ consoleTitle }}
                        </h2>
                    </div>

                    <!-- Master Actions -->
                    <div class="flex items-center gap-1.5 bg-slate-900 p-1 rounded-xl border border-white/5 mr-4">
                        <button @click="liveState.isClear ? updateLiveState({ isClear: false }) : clearScreen()"
                                :class="['w-9 h-9 rounded-lg flex items-center justify-center transition-all', liveState.isClear ? 'bg-red-600 text-white shadow-lg shadow-red-900/50' : 'bg-slate-800 text-slate-400 hover:text-white']"
                                title="Limpar Tela">
                            <i class="fa-duotone fa-eraser text-sm"></i>
                        </button>
                        <button @click="updateLiveState({ isBlackout: !liveState.isBlackout })"
                                :class="['w-9 h-9 rounded-lg flex items-center justify-center transition-all', liveState.isBlackout ? 'bg-black text-red-500 border border-red-900/50' : 'bg-slate-800 text-slate-400 hover:text-white']"
                                title="Blackout">
                            <i class="fa-duotone fa-power-off text-sm"></i>
                        </button>
                        <button @click="goLive({ type: 'logo', content: '', footer: '', logoOption: liveState.logoOption || 'verse', logoMessage: liveState.logoMessage || '' })"
                                :class="['px-3 h-9 rounded-lg flex items-center justify-center gap-2 transition-all font-black uppercase text-[9px] tracking-widest', liveState.type === 'logo' && !liveState.isClear ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white']"
                                title="Logo (versículo ou mensagem atual)">
                            <i class="fa-duotone fa-church text-sm"></i>
                            <span class="hidden xl:inline">Logo</span>
                        </button>
                        <button @click="goLive({ type: 'logo', content: '', footer: '', logoOption: 'message', logoMessage: liveState.logoMessage || 'Sejam Bem Vindos!' })"
                                :class="['px-3 h-9 rounded-lg flex items-center justify-center gap-2 transition-all font-black uppercase text-[9px] tracking-widest', liveState.type === 'logo' && !liveState.isClear && liveState.logoOption === 'message' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white']"
                                title="Logo + Mensagem (ex.: Sejam Bem Vindos)">
                            <i class="fa-duotone fa-message text-sm"></i>
                            <span class="hidden xl:inline">Logo + Msg</span>
                        </button>
                        <button @click="resetSystem"
                                class="w-9 h-9 xl:w-auto xl:px-2 rounded-lg flex items-center justify-center bg-slate-800 text-red-500 hover:bg-red-600 hover:text-white transition-all font-black text-[8px] tracking-widest border border-red-900/20"
                                title="Resetar Todo o Sistema">
                            <i class="fa-duotone fa-trash-can-list text-sm"></i>
                            <span class="ml-1 hidden xl:inline">RESET</span>
                        </button>
                    </div>

                    <!-- Auto-Mode Control -->
                    <div class="flex items-center gap-2.5 bg-slate-900 px-4 py-1.5 rounded-full border border-white/5 ml-2 shadow-inner">
                        <span class="text-[9px] font-black uppercase text-slate-500 tracking-wider">Auto</span>
                        <div class="flex items-center bg-black/60 px-1.5 rounded-lg border border-white/5">
                            <input type="number" v-model="autoTimer"
                                   class="w-12 bg-transparent text-[11px] text-indigo-400 font-black outline-none text-center py-1 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   min="1">
                        </div>
                        <button @click="isAutoMode = !isAutoMode"
                                :class="['w-7 h-7 rounded-full flex items-center justify-center transition-all shadow-lg', isAutoMode ? 'bg-indigo-600 text-white scale-95 shadow-indigo-900/50' : 'bg-slate-800 text-slate-500 hover:text-white shadow-black/20']">
                            <i :class="['fa-duotone', isAutoMode ? 'fa-pause text-[10px]' : 'fa-play text-[9px] ml-0.5']"></i>
                        </button>
                        <div v-if="isAutoMode" class="flex items-center gap-1.5 min-w-[30px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span>
                            <span class="text-[10px] font-black text-indigo-400 whitespace-nowrap">{{ autoCounter }}s</span>
                        </div>
                    </div>
                </div>

                <!-- Alert Input -->
                <div class="flex-1 max-w-md mx-8 flex items-center gap-2">
                    <form @submit.prevent="sendAlert" class="relative group flex-1 h-10">
                        <input type="text" v-model="alertMessage"
                               class="w-full h-full bg-black/60 border border-slate-700 rounded-xl pl-10 pr-4 text-xs text-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 outline-none transition-all placeholder-slate-600 shadow-inner"
                                placeholder="Transmitir alerta rápido...">

                        <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center justify-center text-slate-500 group-focus-within:text-red-500 transition-colors pointer-events-none">
                            <i class="fa-duotone fa-triangle-exclamation text-sm"></i>
                        </div>

                        <!-- Active Indicator Dot -->
                        <div v-if="alertActive" class="absolute right-10 top-1/2 -translate-y-1/2 flex items-center gap-1.5 animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="text-[8px] font-black text-red-500 uppercase">Live</span>
                        </div>

                        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-red-600/20 text-red-500 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-lg active:scale-95">
                            <i class="fa-duotone fa-paper-plane-top text-[10px] pr-0.5"></i>
                        </button>
                    </form>

                    <button v-if="alertActive" @click="clearAlert"
                            class="h-10 w-10 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-all shadow-lg flex items-center justify-center"
                            title="Limpar Alerta Agora">
                        <i class="fa-duotone fa-eraser text-lg"></i>
                    </button>
                </div>

                <div class="flex items-center gap-6">
                    <button @click="isCompact = !isCompact" :class="['w-9 h-9 rounded-lg flex items-center justify-center border border-white/5 transition', isCompact ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white']" :title="isCompact ? 'Mostrar painel direito' : 'Modo operador (ocultar painel direito)'">
                        <i class="fa-duotone fa-compress text-sm"></i>
                    </button>
                    <button @click="showShortcutHelp = !showShortcutHelp" class="w-9 h-9 rounded-lg flex items-center justify-center bg-slate-800 text-slate-400 hover:text-white border border-white/5 transition" title="Atalhos de teclado">
                        <span class="text-sm font-black">?</span>
                    </button>
                     <div class="flex flex-col items-end border-r border-slate-800 pr-6 mr-2">
                         <span class="text-sm font-black text-white leading-none font-mono tracking-tighter">{{ currentTime }}</span>
                         <span class="text-[8px] font-black text-indigo-500 uppercase tracking-widest mt-0.5 animate-pulse">Server Active</span>
                     </div>
                </div>
            </header>

            <!-- Keyboard shortcut help overlay -->
            <div v-if="showShortcutHelp" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showShortcutHelp = false">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-black text-indigo-400 uppercase tracking-widest">Atalhos de teclado</h3>
                        <button @click="showShortcutHelp = false" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center"><i class="fa-solid fa-times text-xs"></i></button>
                    </div>
                    <ul class="space-y-2 text-xs text-slate-300">
                        <li class="flex justify-between gap-4 py-1.5 border-b border-slate-800"><span>Próximo slide</span><kbd class="px-2 py-0.5 rounded bg-slate-800 font-mono">→</kbd></li>
                        <li class="flex justify-between gap-4 py-1.5 border-b border-slate-800"><span>Slide anterior</span><kbd class="px-2 py-0.5 rounded bg-slate-800 font-mono">←</kbd></li>
                        <li class="flex justify-between gap-4 py-1.5 border-b border-slate-800"><span>Próximo item (ordem)</span><kbd class="px-2 py-0.5 rounded bg-slate-800 font-mono">Ctrl + →</kbd></li>
                        <li class="flex justify-between gap-4 py-1.5 border-b border-slate-800"><span>Item anterior</span><kbd class="px-2 py-0.5 rounded bg-slate-800 font-mono">Ctrl + ←</kbd></li>
                        <li class="flex justify-between gap-4 py-1.5 border-b border-slate-800"><span>Mostrar/ocultar esta ajuda</span><kbd class="px-2 py-0.5 rounded bg-slate-800 font-mono">?</kbd></li>
                    </ul>
                </div>
            </div>

            <div v-if="apiError" class="mx-4 mt-2 py-2 px-4 rounded-xl bg-red-500/20 border border-red-500/50 text-red-400 text-xs font-bold flex items-center justify-between gap-2">
                <span>{{ apiError }}</span>
                <button @click="apiError = ''" class="p-1 rounded hover:bg-red-500/20"><i class="fa-duotone fa-times text-sm"></i></button>
            </div>

            <div class="flex-1 overflow-hidden relative p-4">
                <WorshipTab :state="liveState" :initial-setlist-id="initialSetlistId" ref="tabRef" @preview="setPreview" @go-live="goLive" />
            </div>
        </main>

        <!-- Right Panel: Preview & Live Deck (oculto em modo compacto) -->
        <aside v-show="!isCompact" class="w-80 bg-slate-950 border-l border-slate-800 flex flex-col shrink-0 transition-[margin] duration-200">
            <!-- Preview Window -->
            <div class="h-[28%] p-4 border-b border-slate-800 flex flex-col">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Preview</h3>
                <div class="flex-1 bg-black rounded-lg border border-slate-800 flex items-center justify-center relative overflow-hidden shadow-inner">
                    <div v-if="previewState" class="text-center w-full h-full flex items-center justify-center bg-slate-900/20">
                        <div v-if="previewState.type === 'slide'" v-html="previewState.content" class="text-white text-xs p-4 overflow-hidden"></div>
                        <img v-else-if="previewState.type === 'image'" :src="previewAssetUrl(previewState.url)" class="max-h-full max-w-full object-contain">
                        <video v-else-if="previewState.type === 'video'" :src="previewAssetUrl(previewState.url)" class="max-h-full max-w-full object-contain"></video>
                    </div>
                    <div v-else class="text-slate-700 text-[10px] font-black uppercase">Nada Selecionado</div>
                </div>
                <button @click="pushPreviewToLive" :disabled="!previewState" class="mt-3 w-full py-2.5 bg-green-600 hover:bg-green-500 disabled:opacity-20 disabled:grayscale text-white text-xs font-black rounded-xl shadow-lg transition-all active:scale-95 uppercase tracking-widest">
                    <i class="fa-duotone fa-circle-arrow-up mr-2"></i> Projetar agora
                </button>
            </div>

            <!-- Live Window -->
             <div class="h-[28%] p-4 border-b border-slate-800 flex flex-col bg-slate-900/10">
                  <h3 class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 flex justify-between items-center">
                     <span>Live Output</span>
                     <span class="animate-pulse w-2 h-2 bg-red-500 rounded-full shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                  </h3>
                  <div class="flex-1 bg-black rounded-lg border border-red-900/30 flex items-center justify-center relative overflow-hidden shadow-2xl">
                      <div v-if="liveState && !liveState.isBlackout && !liveState.isClear" class="w-full h-full flex items-center justify-center px-4">
                          <div v-if="liveState.type === 'slide'" v-html="liveState.content" class="text-white text-[10px] font-bold text-center leading-tight scale-75 origin-center"></div>
                          <img v-else-if="liveState.type === 'image'" :src="previewAssetUrl(liveState.url)" class="max-h-full max-w-full object-contain">
                          <div v-else-if="liveState.type === 'logo'" class="flex flex-col items-center gap-1 p-2">
                              <img src="/storage/image/logo_icon.png" class="w-8 h-auto opacity-80">
                              <div class="text-[6px] text-indigo-300 font-bold text-center leading-tight uppercase max-w-full" v-html="liveState.content"></div>
                          </div>
                          <video v-else-if="liveState.type === 'video'" :src="previewAssetUrl(liveState.url)" class="max-h-full max-w-full object-contain"></video>
                      </div>
                      <div v-else-if="liveState.isBlackout" class="text-slate-800 text-[10px] uppercase font-black tracking-tighter">Blackout Active</div>
                      <div v-else-if="liveState.isClear" class="text-slate-800 text-[10px] uppercase font-black tracking-tighter">Clear Screen</div>
                  </div>
             </div>

            <!-- ATUAL / PRÓXIMO (resumo no painel direito) -->
            <div class="p-4 border-b border-slate-800 flex flex-col gap-2">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ao vivo / Próximo</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-lg border-2 border-red-500/30 bg-slate-900/50 p-2 min-h-[60px] flex flex-col">
                        <span class="text-[9px] font-black text-red-500 uppercase mb-1">ATUAL</span>
                        <div v-if="liveState && !liveState.isClear && !liveState.isBlackout" class="flex-1 text-[9px] text-slate-300 overflow-hidden line-clamp-3" v-html="liveState.type === 'slide' ? (liveState.content || '').replace(/<[^>]+>/g,' ').slice(0,80) : liveState.type"></div>
                        <div v-else class="text-slate-600 text-[9px]">—</div>
                    </div>
                    <div class="rounded-lg border border-slate-700 bg-slate-900/50 p-2 min-h-[60px] flex flex-col">
                        <span class="text-[9px] font-black text-amber-500 uppercase mb-1">PRÓXIMO</span>
                        <div v-if="nextSlidePreview" class="flex-1 text-[9px] text-slate-400 overflow-hidden line-clamp-3" v-html="(nextSlidePreview.content || nextSlidePreview.title || '').toString().replace(/<[^>]+>/g,' ').slice(0,80)"></div>
                        <div v-else class="text-slate-600 text-[9px]">—</div>
                    </div>
                </div>
            </div>

            <!-- Live Controls (Controles de tela, Temas, Animação) -->
            <div class="flex-1 p-4 bg-slate-950 overflow-y-auto">
                <LiveDeck :state="liveState" @update="updateLiveState" />
            </div>
        </aside>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import WorshipTab from './tabs/WorshipTab.vue';
import LiveDeck from './LiveDeck.vue';

const el = document.getElementById('projection-app');
const dashboardUrl = el?.dataset.dashboardUrl || '/admin/projection';
const isMemberContext = (el?.dataset.context || '').toLowerCase() === 'member';
const initialSetlistId = (el?.dataset.initialSetlistId || '').trim() || null;
const consoleTitle = isMemberContext ? 'Projeção – Painel do Membro' : 'Ordem do culto';
const previewState = ref(null);
const liveState = ref({
    type: 'clear',
    content: '',
    isBlackout: false,
    isClear: true,
    url: '',
    theme: 'black',
    bgOpacity: 1,
    bgBlur: 0,
    logoOption: 'verse', // verse, message, none
    logoMessage: 'Sejam Bem Vindos!',
    worshipFx: true
});

const alertMessage = ref('');
const alertActive = ref(false);
let alertTimeout = null;

const tabRef = ref(null);
const showShortcutHelp = ref(false);
const isCompact = ref(false);
const isAutoMode = ref(false);
const nextSlidePreview = computed(() => tabRef.value?.nextSlidePreview ?? null);
const autoTimer = ref(10);
const autoCounter = ref(10);
let autoInterval = null;
const currentTime = ref('');
const apiError = ref('');

// Auth headers for Axios
const apiToken = el?.dataset.apiToken;
if (apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
}

// Keyboard Navigation (Quelea-style: setas = slide, Ctrl+setas = item)
const handleKeyDown = (e) => {
    if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;

    if (e.key === '?') {
        showShortcutHelp.value = !showShortcutHelp.value;
        return;
    }
    if (e.key === 'ArrowRight') {
        if (e.ctrlKey) tabRef.value?.nextItem?.();
        else tabRef.value?.next?.();
    } else if (e.key === 'ArrowLeft') {
        if (e.ctrlKey) tabRef.value?.prevItem?.();
        else tabRef.value?.prev?.();
    }
};

// Actions
const setPreview = (content) => {
    previewState.value = content;
};

const goLive = (content) => {
    // Toggle logic: only clear if we are projecting the EXACT SAME logo mode and it's NOT already cleared.
    if (content.type === 'logo' &&
        liveState.value.type === 'logo' &&
        content.logoOption === liveState.value.logoOption &&
        !liveState.value.isClear) {
        updateLiveState({ isClear: true, type: 'clear', content: '', footer: '', url: '' });
        return;
    }

    const payload = { ...content };
    payload.isClear = false;
    payload.isBlackout = false;

    if (!payload.type) {
        if (payload.url) payload.type = 'image';
        if (payload.html || payload.content) payload.type = 'slide';
    }

    updateLiveState(payload);
};

const pushPreviewToLive = () => {
    if (previewState.value) {
        goLive(previewState.value);
    }
};

/** Normalize projection asset URL so /storage/projection_assets/ is served via API (avoids 403). */
const previewAssetUrl = (url) => {
    if (!url || typeof url !== 'string') return url;
    if (url.startsWith('/storage/projection_assets/')) return '/api/v1/projection/assets/serve/' + url.split('/').pop();
    return url;
};

const updateLiveState = (newState) => {
    // Merge full state to ensure reactivity and consistency
    const updatedState = { ...liveState.value, ...newState };
    liveState.value = updatedState;

    // Always send the full state to ensure synchronization is 100% reliable
    axios.post('/api/v1/projection/state', updatedState)
        .then(() => { apiError.value = ''; })
        .catch(err => {
            apiError.value = 'Falha ao sincronizar estado.';
            console.error('Failed to sync state', err);
        });
};

const clearScreen = () => {
    updateLiveState({
        isClear: true,
        type: 'clear',
        content: '',
        footer: '',
        url: ''
    });
};

const resetSystem = () => {
    if (!confirm('DESEJA RESETAR TODO O SISTEMA? Isso limpará conteúdo, fundo, temas e alertas.')) return;

    updateLiveState({
        type: 'clear',
        content: '',
        footer: '',
        url: '',
        isClear: true,
        isBlackout: false,
        theme: 'black',
        bgUrl: '',
        bgType: 'image',
        bgOpacity: 1,
        bgBlur: 0,
        alertMessage: ''
    });
};

const sendAlert = () => {
    if (!alertMessage.value) return;
    const msg = alertMessage.value;
    alertActive.value = true;
    updateLiveState({ alertMessage: msg, stage_alert: msg });
    alertMessage.value = '';

    if (alertTimeout) clearTimeout(alertTimeout);
    alertTimeout = setTimeout(() => {
        clearAlert();
    }, 7000);
};

const clearAlert = () => {
    alertActive.value = false;
    if (alertTimeout) clearTimeout(alertTimeout);
    updateLiveState({ alertMessage: '', stage_alert: '' });
};

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

watch(isAutoMode, (active) => {
    if (active) {
        autoCounter.value = autoTimer.value;
        autoInterval = setInterval(() => {
            autoCounter.value--;
            if (autoCounter.value <= 0) {
                if (tabRef.value && typeof tabRef.value.next === 'function') {
                    tabRef.value.next();
                }
                autoCounter.value = autoTimer.value;
            }
        }, 1000);
    } else {
        if (autoInterval) clearInterval(autoInterval);
        autoCounter.value = autoTimer.value;
    }
});

onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);

    // Keyboard Navigation
    window.addEventListener('keydown', handleKeyDown);

    // Initial fetch
    axios.get('/api/v1/projection/state')
        .then(res => {
            const data = res.data?.data ?? res.data;
            liveState.value = { ...liveState.value, ...data };
            apiError.value = '';
        })
        .catch(err => {
            apiError.value = 'Falha ao carregar estado inicial.';
            console.error(err);
        });

    // Zero-Delay Offline-First Engine (Aggressive Polling)
    // Since the backend API serves strictly from Cache now, this puts ZERO stress on MySQL.
    setInterval(() => {
        axios.get('/api/v1/projection/state', { timeout: 1500 }) // low timeout to prevent lockups on fail
            .then(res => {
                const data = res.data?.data ?? res.data;
                // Only merge what changed to prevent unnecessary re-renders in Vue
                if(JSON.stringify(liveState.value) !== JSON.stringify(data)) {
                     liveState.value = { ...liveState.value, ...data };
                }
            })
            .catch(() => {
                // Ignore silent network drops; the projection engine will self-heal on the next 250ms tick
            });
    }, 250);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>
