<template>
    <div class="devotional-viewer space-y-8 animate-in">
        <!-- Scripture Section -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-12 text-gray-500 italic">
            <i class="fa-duotone fa-spinner-third fa-spin text-3xl mb-4 text-indigo-500"></i>
            <p>Buscando as Escrituras...</p>
        </div>

        <div v-else-if="bibleData" class="bible-card bg-white rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden group">
            <!-- Decorative Bible Icon -->
            <i class="fa-duotone fa-book-bible absolute -top-4 -right-4 text-9xl text-indigo-50 opacity-[0.03] rotate-12 pointer-events-none"></i>

            <div class="relative z-10 text-center space-y-6">
                <span class="inline-block px-4 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase tracking-[0.2em]">
                    Palavra de Deus
                </span>

                <h2 class="text-2xl md:text-3xl font-serif font-bold text-gray-900 leading-tight">
                    {{ bibleData.reference }}
                </h2>

                <div class="max-w-2xl mx-auto space-y-4">
                    <p v-for="verse in bibleData.verses" :key="verse.verse_number" class="text-gray-700 text-lg md:text-xl font-serif leading-relaxed text-left indent-8">
                        <sup class="text-indigo-400 font-bold mr-1">{{ verse.verse_number }}</sup>
                        {{ verse.text }}
                    </p>
                </div>

                <div class="pt-6 border-t border-indigo-50">
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">
                        {{ bibleData.bible_version || 'Almeida Revista e Corrigida' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Reflection Section -->
        <div v-if="content" class="reflection-section space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-[2px] bg-indigo-500/30"></div>
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-[0.2em]">Reflexão e Edificação</h3>
                <div class="flex-1 h-[px] bg-indigo-500/10"></div>
            </div>

            <div class="text-gray-200 text-lg leading-relaxed font-light whitespace-pre-wrap px-4">
                {{ content }}
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && !bibleData && !content" class="text-center py-20 text-gray-500">
            <i class="fa-duotone fa-book-open-reader text-5xl mb-4 opacity-20"></i>
            <p>Nenhuma reflexão disponível para esta aula.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    bibleReference: String,
    content: String
});

const bibleData = ref(null);
const loading = ref(false);

const fetchBible = async () => {
    if (!props.bibleReference) {
        bibleData.value = null;
        return;
    }

    loading.value = true;
    try {
        const res = await axios.get('/api/v1/bible/find', {
            params: { ref: props.bibleReference }
        });
        bibleData.value = res.data.data || null;
    } catch (e) {
        console.error("Failed to fetch bible reference", e);
        bibleData.value = null;
    } finally {
        loading.value = false;
    }
};

watch(() => props.bibleReference, () => {
    fetchBible();
});

onMounted(() => {
    fetchBible();
});
</script>

<style scoped>
.bible-card {
    background-image: radial-gradient(circle at 100% 100%, #eff6ff 0%, transparent 20%);
}
.font-serif {
    font-family: 'Crimson Pro', 'Georgia', serif;
}
</style>
