<template>
    <div class="flex flex-col gap-4">
        <!-- Controles de tela -->
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-0 flex items-center gap-2">
            <i class="fa-duotone fa-tv"></i> Controles de tela
        </h3>
        <div class="grid grid-cols-3 gap-2">
            <button @click="toggleClear"
                    :class="['h-20 rounded-xl flex flex-col items-center justify-center font-black transition-all duration-300 border border-white/5',
                    state.isClear ? 'bg-red-600 text-white shadow-lg shadow-red-900/50 scale-95' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white']">
                <i class="fa-duotone fa-eraser text-xl mb-1"></i>
                <span class="text-[8px] uppercase tracking-widest text-center">Clear<br><span class="opacity-50">(Limpar)</span></span>
            </button>

            <button @click="toggleBlackout"
                    :class="['h-20 rounded-xl flex flex-col items-center justify-center font-black transition-all duration-300 border border-white/5',
                    state.isBlackout ? 'bg-black text-red-500 shadow-lg shadow-black/50 scale-95 border-red-900/50' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white']">
                <i class="fa-duotone fa-power-off text-xl mb-1"></i>
                <span class="text-[8px] uppercase tracking-widest text-center">Blackout<br><span class="opacity-50">(Apagar)</span></span>
            </button>

            <button @click="triggerLogo"
                    :class="['h-20 rounded-xl flex flex-col items-center justify-center font-black transition-all duration-300 border border-white/5',
                    state.type === 'logo' && !state.isClear ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50 scale-95' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white']">
                <i class="fa-duotone fa-church text-xl mb-1"></i>
                <span class="text-[8px] uppercase tracking-widest text-center">Logo +<br>Verso</span>
            </button>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-2">
            <button @click="toggleBlur"
                    :class="['h-8 rounded-lg flex items-center justify-center gap-2 font-black transition-all border border-white/5',
                    state.bgBlur > 0 ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700']"
                    title="Desfocar fundo">
                <i class="fa-duotone fa-droplet text-[10px]"></i>
                <span class="text-[7px] uppercase tracking-tighter">Blur</span>
            </button>
            <button @click="toggleDim"
                    :class="['h-8 rounded-lg flex items-center justify-center gap-2 font-black transition-all border border-white/5',
                    state.bgOpacity < 1 ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700']"
                    title="Escurecer fundo">
                <i class="fa-duotone fa-sun text-[10px]"></i>
                <span class="text-[7px] uppercase tracking-tighter">Dim</span>
            </button>
            <button @click="toggleWorshipFx"
                    :class="['h-8 rounded-lg flex items-center justify-center gap-2 font-black transition-all border border-white/5',
                    state.worshipFx ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-900/40' : 'bg-slate-800 text-slate-400 hover:bg-slate-700']"
                    title="Alternar Animações (Notas Musicais)">
                <i class="fa-duotone fa-sparkles text-[10px]"></i>
                <span class="text-[7px] uppercase tracking-tighter">Worship FX</span>
            </button>
        </div>

        <!-- Animação -->
        <div class="mt-2">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-duotone fa-wand-magic-sparkles"></i> Animação
            </h3>
            <div class="flex gap-2">
                <button v-for="opt in animationOptions" :key="opt.value"
                        @click="emit('update', { animation_style: opt.value })"
                        :class="['px-2 py-1 rounded text-[9px] font-bold uppercase border transition-all',
                        (state.animation_style || 'none') === opt.value ? 'bg-amber-600/30 border-amber-500 text-amber-400' : 'bg-slate-800/40 border-white/5 text-slate-400 hover:bg-slate-700']">
                    {{ opt.label }}
                </button>
            </div>
        </div>

        <!-- Temas -->
        <div class="mt-2">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                <i class="fa-duotone fa-palette"></i> Temas
            </h3>
            <div class="grid grid-cols-2 gap-2">
                <button v-for="t in themes" :key="t.id"
                        @click="setTheme(t)"
                        :class="['px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-left transition-all border',
                        (state.theme === (t.slug ?? t.id) || state.theme_id === t.id) ? 'bg-indigo-600/20 border-indigo-500 text-indigo-400' : 'bg-slate-800/40 border-white/5 text-slate-400 hover:bg-slate-800']">
                    {{ t.name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps(['state']);
const emit = defineEmits(['update']);

const animationOptions = [
    { value: 'none', label: 'Nenhuma' },
    { value: 'line', label: 'Por linha' },
    { value: 'stanza', label: 'Por estrofe' }
];

const builtinThemes = [
    { id: 'black', slug: 'black', name: 'Original Black' },
    { id: 'dark', slug: 'dark', name: 'Soft Dark' },
    { id: 'modern-dark', slug: 'modern-dark', name: 'Slate Blue' },
    { id: 'paper', slug: 'paper', name: 'Sepia Paper' },
    { id: 'ethereal', slug: 'ethereal', name: 'Ethereal Blue' },
    { id: 'vibrant-worship', slug: 'vibrant-worship', name: 'Worship Green' },
    { id: 'classic-bible', slug: 'classic-bible', name: 'Classic Brown' },
    { id: 'solid-blue', slug: 'solid-blue', name: 'Solid Blue' },
    { id: 'solid-red', slug: 'solid-red', name: 'Solid Red' },
    { id: 'solid-purple', slug: 'solid-purple', name: 'Solid Purple' }
];

const apiThemes = ref([]);
const themes = ref([...builtinThemes]);

onMounted(() => {
    axios.get('/api/v1/projection/themes').then(res => {
        const list = res.data?.data ?? res.data ?? [];
        apiThemes.value = list.map(t => ({ id: t.id, slug: t.slug || 'theme-' + t.id, name: t.name }));
        themes.value = [...builtinThemes, ...apiThemes.value];
    }).catch(() => {});
});

const toggleClear = () => {
    if (props.state.isClear) {
        emit('update', { isClear: false });
    } else {
        emit('update', {
            isClear: true,
            type: 'clear',
            content: '',
            footer: '',
            url: ''
        });
    }
};

const toggleBlackout = () => emit('update', { isBlackout: !props.state.isBlackout });

const triggerLogo = () => {
    // If we are already in Logo mode and NOT cleared, then "turning it off" means clearing the screen.
    if (props.state.type === 'logo' && !props.state.isClear && !props.state.isBlackout) {
        emit('update', { isClear: true, type: 'clear', content: '', footer: '', url: '' });
    } else {
        emit('update', {
            type: 'logo',
            isClear: false,
            isBlackout: false,
            content: '',
            footer: ''
        });
    }
};

const toggleBlur = () => emit('update', { bgBlur: props.state.bgBlur === 0 ? 15 : 0 });
const toggleDim = () => emit('update', { bgOpacity: props.state.bgOpacity === 1 ? 0.4 : 1 });
const toggleWorshipFx = () => emit('update', { worshipFx: !props.state.worshipFx });
const setTheme = (t) => {
    const value = typeof t === 'object' ? (t.slug ?? t.id) : t;
    emit('update', { theme: value, theme_id: typeof t === 'object' && typeof t.id === 'number' ? t.id : null });
};
</script>
