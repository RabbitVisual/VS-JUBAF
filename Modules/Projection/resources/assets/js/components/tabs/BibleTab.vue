<template>
    <div class="flex h-full">
        <!-- Navigation -->
        <div class="w-1/3 border-r border-slate-800 p-4 flex flex-col gap-4 overflow-y-auto">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-duotone fa-compass"></i> Navegação
            </h3>

            <!-- Version -->
            <div>
                <label class="block text-[9px] font-black text-slate-500 uppercase mb-2 tracking-tighter">Versão da Bíblia</label>
                <select v-model="selectedVersion" @change="fetchBooks" class="w-full bg-slate-800/50 border-white/5 text-white rounded-xl text-xs p-3 outline-none focus:border-indigo-500 transition-all cursor-pointer">
                    <option v-for="v in versions" :key="v.id" :value="v.id">{{ v.abbreviation }} - {{ v.name }}</option>
                </select>
            </div>

            <!-- Book -->
            <div>
                <label class="block text-[9px] font-black text-slate-500 uppercase mb-2 tracking-tighter">Livro</label>
                <select v-model="selectedBook" @change="fetchChapters" class="w-full bg-slate-800/50 border-white/5 text-white rounded-xl text-xs p-3 outline-none focus:border-indigo-500 transition-all cursor-pointer">
                    <option v-for="b in books" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
            </div>

            <!-- Chapter -->
            <div v-if="selectedBook">
                <label class="block text-[9px] font-black text-slate-500 uppercase mb-2 tracking-tighter">Capítulo</label>
                <div class="grid grid-cols-5 gap-2">
                    <button v-for="c in chapters" :key="c.id"
                            @click="selectChapter(c)"
                            :class="['h-9 rounded-lg text-center text-xs font-black transition-all duration-200 border', selectedChapter && selectedChapter.id === c.id ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-900/50' : 'bg-slate-800/40 border-white/5 text-slate-400 hover:border-slate-500 hover:text-white']">
                        {{ c.number }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Verses -->
        <div class="flex-1 p-4 overflow-y-auto bg-slate-900">
            <div v-if="verses.length > 0" class="space-y-3">
                <div v-for="(v, index) in verses" :key="v.id"
                     @click="goLiveVerse(v, index)"
                     :class="['p-4 rounded-xl border transition-all duration-300 group relative cursor-pointer',
                     currentVerseIndex === index ? 'border-indigo-500 bg-indigo-600/10 shadow-lg shadow-indigo-900/20' : 'border-slate-800 bg-slate-800/20 hover:border-slate-600 hover:bg-slate-800/40']">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Versículo {{ v.number }}</span>
                        <div class="opacity-0 group-hover:opacity-100 flex gap-2 transition-opacity">
                            <button @click.stop="previewVerse(v)" class="text-[8px] font-black uppercase bg-slate-700 hover:bg-slate-600 text-white px-2 py-1 rounded-lg">PREVIEW</button>
                            <button @click.stop="goLiveVerse(v)" class="text-[8px] font-black uppercase bg-red-600 hover:bg-red-500 text-white px-2 py-1 rounded-lg">LIVE</button>
                        </div>
                    </div>
                    <p class="text-sm font-bold leading-relaxed text-slate-200">{{ v.text }}</p>
                </div>
            </div>
            <div v-else class="h-full flex flex-col items-center justify-center text-slate-600 gap-4 opacity-70">
                <i class="fa-duotone fa-book-open text-6xl text-slate-800"></i>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-700">Selecione um capítulo</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

defineProps({
    state: { type: Object, default: null }
});

const emit = defineEmits(['preview', 'go-live']);

const versions = ref([]);
const books = ref([]);
const chapters = ref([]);
const verses = ref([]);

const selectedVersion = ref(null);
const selectedBook = ref(null);
const selectedChapter = ref(null);
const currentVerseIndex = ref(-1);

const fetchVersions = () => {
    axios.get('/api/v1/bible/versions').then(res => {
        versions.value = res.data.data || [];
        if (versions.value.length) {
            selectedVersion.value = versions.value[0].id;
            fetchBooks();
        }
    });
};

const fetchBooks = () => {
    if (!selectedVersion.value) return;
    axios.get('/api/v1/bible/books', { params: { version_id: selectedVersion.value } }).then(res => {
        books.value = res.data.data || [];
        selectedBook.value = null;
        chapters.value = [];
        verses.value = [];
    });
};

const fetchChapters = () => {
    if (!selectedBook.value) return;
    axios.get('/api/v1/bible/chapters', { params: { book_id: selectedBook.value } }).then(res => {
        const list = res.data.data || [];
        chapters.value = list.map(c => ({ ...c, number: c.chapter_number }));
        selectedChapter.value = null;
        verses.value = [];
    });
};

const selectChapter = (chapter) => {
    selectedChapter.value = chapter;
    fetchVerses();
};

const fetchVerses = () => {
    if (!selectedBook.value || !selectedChapter.value) return;
    axios.get('/api/v1/bible/verses', { params: { chapter_id: selectedChapter.value.id } }).then(res => {
        const list = res.data.data || [];
        verses.value = list.map(v => ({ ...v, number: v.verse_number }));
    });
};

const formatVerseContent = (verse, versionAbbreviation) => {
    const book = books.value.find(b => b.id === selectedBook.value);
    const bookName = book ? book.name : '';
    const refText = `<span style="color: #fbbf24;">${bookName}</span> <span style="color: #fff;">${selectedChapter.value.number}:${verse.number}</span>`;
    const versionText = versionAbbreviation ? `<div class="bible-version-tag">${versionAbbreviation}</div>` : '';

    return `<div class="verse-slide">
        ${versionText}
        <div class="verse-text">${verse.text}</div>
        <div class="verse-ref">${refText}</div>
    </div>`;
};

const getVersionAbbreviation = () => {
    const version = versions.value.find(v => v.id === selectedVersion.value);
    return version ? version.abbreviation : '';
};

const previewVerse = (verse, index) => {
    if (index !== undefined) currentVerseIndex.value = index;
    emit('preview', {
        type: 'slide',
        content: formatVerseContent(verse, getVersionAbbreviation())
    });
};

const goLiveVerse = (verse, index) => {
    if (index !== undefined) currentVerseIndex.value = index;
    emit('go-live', {
        type: 'slide',
        content: formatVerseContent(verse, getVersionAbbreviation())
    });
};

const next = () => {
    if (verses.value.length === 0) return;
    const nextIndex = Math.min(currentVerseIndex.value + 1, verses.value.length - 1);
    if (nextIndex !== currentVerseIndex.value) goLiveVerse(verses.value[nextIndex], nextIndex);
};

const prev = () => {
    if (verses.value.length === 0) return;
    const prevIndex = Math.max(currentVerseIndex.value - 1, 0);
    if (prevIndex !== currentVerseIndex.value) goLiveVerse(verses.value[prevIndex], prevIndex);
};

defineExpose({ next, prev });

onMounted(() => {
    fetchVersions();
});
</script>
