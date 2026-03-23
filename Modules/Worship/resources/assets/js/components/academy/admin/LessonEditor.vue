<template>
    <div class="h-full flex flex-col bg-gray-800 border-l border-gray-700">
        <!-- Header -->
        <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-900">
            <h3 class="font-bold text-white">Editando Aula</h3>
            <span class="text-xs text-gray-500 uppercase tracking-widest">{{ modelValue.id ? 'ID: ' + modelValue.id : 'Não Salvo' }}</span>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Título</label>
                    <input v-model="localLesson.title" @input="update" type="text" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Tipo</label>
                        <select v-model="localLesson.type" @change="update" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                            <option value="video">Vídeo Aula</option>
                            <option value="chordpro">ChordPro / Cifra</option>
                            <option value="devotional">Devocional / Bíblia</option>
                            <option value="material">Material para Download</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Duração (min)</label>
                        <input v-model="localLesson.duration_minutes" @input="update" type="number" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                    </div>
                </div>
            </div>

            <hr class="border-gray-700">

            <!-- Type Specific Editors -->

            <!-- VIDEO EDITOR -->
            <div v-if="localLesson.type === 'video'" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">URL do Vídeo (YouTube/Vimeo)</label>
                    <input v-model="localLesson.video_url" @input="update" type="text" placeholder="https://youtube.com/..." class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">URL Vídeo Multicâmera</label>
                    <input v-model="localLesson.multicam_video_url" @input="update" type="text" placeholder="Opcional" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                </div>
                <div v-if="localLesson.video_url" class="aspect-video bg-black rounded overflow-hidden">
                    <iframe class="w-full h-full" :src="getEmbedUrl(localLesson.video_url)" frameborder="0" allowfullscreen></iframe>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Descrição da aula</label>
                    <textarea v-model="localLesson.content" @input="update" rows="4" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none resize-y" placeholder="Exibida na aba Descrição na sala de aula."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Guia de estudo (caminho/URL PDF)</label>
                    <input v-model="localLesson.pdf_path" @input="update" type="text" placeholder="Ex: storage/materials/guia.pdf" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Partitura (PDF)</label>
                    <div class="flex gap-2">
                        <input v-model="localLesson.sheet_music_pdf" @input="update" type="text" placeholder="Caminho ou URL" class="flex-1 bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                        <label class="shrink-0 px-3 py-2 bg-gray-600 hover:bg-gray-500 rounded text-xs font-bold text-white cursor-pointer">
                            <i class="fa-duotone fa-cloud-arrow-up mr-1"></i> Enviar
                            <input type="file" accept=".pdf" @change="(e) => uploadFileForField('sheet_music_pdf', e)" class="hidden">
                        </label>
                    </div>
                </div>
            </div>

            <!-- CHORDPRO EDITOR -->
            <div v-if="localLesson.type === 'chordpro'" class="h-[500px] flex gap-4">
                <div class="w-1/2 flex flex-col">
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Sintaxe ChordPro / Cifra</label>
                    <textarea v-model="localLesson.content" @input="update" class="flex-1 w-full bg-gray-900 text-green-400 font-mono text-sm p-4 rounded border border-gray-600 focus:border-indigo-500 outline-none resize-none" placeholder="[G] Amazing [C] Grace..."></textarea>
                    <p class="text-[10px] text-gray-500 mt-1">Use [Nota] para acordes. {c: Comentário} para anotações.</p>
                </div>
                <div class="w-1/2 flex flex-col">
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Prévia</label>
                    <div class="flex-1 bg-white text-black p-4 rounded overflow-y-auto font-sans shadow-lg">
                        <div v-html="renderedChordPro"></div>
                    </div>
                </div>
            </div>

            <!-- DEVOTIONAL EDITOR -->
            <div v-if="localLesson.type === 'devotional'" class="space-y-6">
                <div class="bg-indigo-900/20 p-6 rounded-2xl border border-indigo-500/30">
                    <label class="block text-xs font-bold text-indigo-400 mb-2 uppercase tracking-widest">Referência Bíblica</label>
                    <div class="flex gap-2">
                        <input v-model="bibleSearch"
                               @keyup.enter="searchBible"
                               type="text"
                               placeholder="Ex: Salmos 23:1 ou João 3:16"
                               class="flex-1 bg-gray-900 text-white rounded-xl p-3 border border-gray-600 focus:border-indigo-500 outline-none transition-all">
                        <button @click="searchBible"
                                :disabled="searching"
                                class="bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white px-6 rounded-xl font-bold transition-all flex items-center gap-2">
                            <i v-if="searching" class="fa-duotone fa-spinner fa-spin"></i>
                            <i v-else class="fa-duotone fa-magnifying-glass"></i>
                            Buscar
                        </button>
                    </div>

                    <!-- Selected Reference Preview -->
                    <div v-if="localLesson.bible_reference" class="mt-4 p-4 bg-gray-900/50 rounded-xl border border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-indigo-400 font-bold text-sm">{{ localLesson.bible_reference }}</span>
                            <button @click="localLesson.bible_reference = null; update()" class="text-red-400 hover:text-red-300">
                                <i class="fa-duotone fa-trash-can"></i>
                            </button>
                        </div>
                        <div v-if="scripturePreview" class="text-gray-300 text-xs italic line-clamp-2">
                             {{ scripturePreview }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase tracking-widest">Reflexão Devocional</label>
                    <textarea v-model="localLesson.content"
                            @input="update"
                            class="w-full bg-gray-700 text-white rounded-xl p-4 border border-gray-600 focus:border-indigo-500 outline-none h-64 resize-none"
                            placeholder="Escreva o conteúdo da devocional ou reflexão aqui..."></textarea>
                </div>
            </div>

            <!-- MATERIAL EDITOR -->
            <div v-if="localLesson.type === 'material'" class="space-y-4">
                <div class="bg-gray-700/50 p-6 rounded-xl border-2 border-dashed border-gray-600 flex flex-col items-center justify-center text-gray-400 relative">
                    <input type="file" @change="uploadFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i v-if="uploading" class="fa-duotone fa-spinner fa-spin text-4xl mb-2 text-indigo-500"></i>
                    <i v-else class="fa-duotone fa-cloud-arrow-up text-4xl mb-2"></i>
                    <p class="text-sm">{{ uploading ? 'Enviando...' : 'Clique ou Arraste para enviar o Material' }}</p>
                    <p class="text-xs">(Máx 50MB)</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Caminho / URL do Material</label>
                    <input v-model="localLesson.pdf_path" @input="update" type="text" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none">
                </div>
            </div>

            <hr class="border-gray-700">

            <!-- Dicas do Professor (todos os tipos) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Dicas do Professor</label>
                <textarea v-model="localLesson.teacher_tips" @input="update" rows="4" class="w-full bg-gray-700 text-white rounded p-2 border border-gray-600 focus:border-indigo-500 outline-none resize-y" placeholder="Dicas exibidas na aba Dicas do Professor na sala de aula."></textarea>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: Object
});

const emit = defineEmits(['update:modelValue']);

const localLesson = ref({ ...props.modelValue });
const uploading = ref(false);
const searching = ref(false);
const bibleSearch = ref('');
const scripturePreview = ref('');

watch(() => props.modelValue, (newVal) => {
    localLesson.value = { ...newVal };
});

const renderedChordPro = computed(() => renderChordPro(localLesson.value.content));

const searchBible = async () => {
    if (!bibleSearch.value) return;

    searching.value = true;
    try {
        const res = await axios.get('/api/v1/bible/find', {
            params: { ref: bibleSearch.value }
        });
        const data = res.data.data;
        if (data && data.reference) {
            localLesson.value.bible_reference = data.reference;
            scripturePreview.value = (data.verses || []).map(v => v.text).join(' ');
            update();
        }
    } catch (e) {
        console.error(e);
        alert('Referência não encontrada. Verifique o formato, ex: "João 3:16"');
    } finally {
        searching.value = false;
    }
};

const update = () => {
    emit('update:modelValue', localLesson.value);
};

const uploadFile = async (event) => {
    await doUpload(event, 'pdf_path');
};

const uploadFileForField = async (field, event) => {
    await doUpload(event, field);
};

const doUpload = async (event, field) => {
    const file = event.target.files?.[0];
    if (!file) return;

    uploading.value = true;
    const formData = new FormData();
    formData.append('file', file);

    try {
        const res = await axios.post('/admin/worship/api/assets', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        const path = res.data.path || res.data.file_path || res.data.url;
        if (field === 'pdf_path') {
            localLesson.value.pdf_path = path;
        } else if (field === 'sheet_music_pdf') {
            localLesson.value.sheet_music_pdf = path;
        }
        update();
    } catch (e) {
        console.error(e);
        alert('Falha no upload');
    } finally {
        uploading.value = false;
    }
    event.target.value = '';
};

const getEmbedUrl = (url) => {
    if (!url) return '';
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        const videoId = url.split('v=')[1]?.split('&')[0] || url.split('/').pop();
        return `https://www.youtube.com/embed/${videoId}`;
    }
    return url;
};

// Simple ChordPro Parser for Preview
const renderChordPro = (text) => {
    if (!text) return '<div class="text-gray-400 italic">Escreva sua cifra para ver a prévia...</div>';

    const lines = text.split('\n');
    let html = '';

    lines.forEach(line => {
        const trimmed = line.trim();

        // Directives like {c: comment} or {title: heading}
        if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
             const directive = trimmed.substring(1, trimmed.length - 1);
             const [key, ...valParts] = directive.split(':');
             const value = valParts.join(':').trim();

             if (key.toLowerCase() === 'c' || key.toLowerCase() === 'comment') {
                 html += `<div class="text-gray-400 italic text-sm mb-2 border-l-2 border-gray-200 pl-2 ml-1">${value}</div>`;
             } else {
                 html += `<div class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest mb-2 opacity-50">${key}: ${value}</div>`;
             }
             return;
        }

        // Comments with #
        if (trimmed.startsWith('#')) {
            html += `<div class="text-gray-400 italic text-sm mb-1 opacity-50">${trimmed.substring(1)}</div>`;
            return;
        }

        // Empty lines
        if (trimmed === '') {
            html += '<div class="h-4"></div>';
            return;
        }

        // Replace [Chord] with styled spans
        let processedLine = line.replace(/\[(.*?)\]/g, (match, chord) => {
            return `<span class="inline-block bg-indigo-50 text-indigo-700 font-bold text-[11px] px-1.5 py-0.5 rounded shadow-sm mx-0.5 transform -translate-y-1">${chord}</span>`;
        });

        html += `<div class="mb-2 leading-relaxed text-gray-800 font-medium">${processedLine}</div>`;
    });

    return html;
};
</script>
