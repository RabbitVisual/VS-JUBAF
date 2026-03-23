<template>
    <div class="chordpro-viewer text-gray-300 space-y-5 max-w-4xl">
        <template v-if="blocks.length > 0">
            <template v-for="(block, i) in blocks" :key="i">
                <!-- Section header: [Intro], [Tab - Intro], [Refrão], etc. -->
                <div v-if="block.type === 'section'" class="text-indigo-400 font-bold text-sm sm:text-base uppercase tracking-wider mt-6 first:mt-0">
                    {{ block.title }}
                </div>
                <!-- Tablature block (Cifra Club style) -->
                <pre v-else-if="block.type === 'tab'" class="tab-block overflow-x-auto text-xs sm:text-sm font-mono text-gray-300 bg-gray-800/50 rounded-lg p-4 border border-white/5 whitespace-pre">{{ block.lines }}</pre>
                <!-- Chord-only line (e.g. Dm7  Bb9(11+) above tab) -->
                <div v-else-if="block.type === 'chordOnly'" class="font-mono text-indigo-400 font-bold text-sm sm:text-base whitespace-pre-wrap">{{ block.text }}</div>
                <!-- Chord + lyric line -->
                <div v-else-if="block.type === 'chordLyric'" class="mb-4">
                    <div class="flex flex-wrap items-end leading-relaxed">
                        <div v-for="(chunk, k) in block.chunks" :key="k" class="flex flex-col mr-1 mb-1">
                            <span v-if="chunk.chord" class="text-indigo-400 font-bold text-xs mb-0.5 min-h-[1rem]">{{ chunk.chord }}</span>
                            <span v-else class="min-h-[1rem] block"></span>
                            <span class="text-gray-200 whitespace-pre">{{ chunk.lyric }}</span>
                        </div>
                    </div>
                </div>
                <!-- Plain text (label like "Parte 1 de 4" or lyrics only) -->
                <div v-else-if="block.type === 'plain'" class="text-gray-400 text-sm font-medium whitespace-pre-wrap">{{ block.text }}</div>
            </template>
        </template>
        <div v-else class="text-center text-gray-500 italic py-8">
            Nenhum conteúdo disponível.
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    content: String
});

const TAB_LINE_REGEX = /^[EADGB]\|.+/;

function parseChordLyricLine(line) {
    const chunks = [];
    const parts = line.split(/(\[.*?\])/);
    let currentChord = null;
    for (let i = 0; i < parts.length; i++) {
        const part = parts[i];
        if (part.startsWith('[') && part.endsWith(']')) {
            currentChord = part.slice(1, -1);
            if (i + 1 < parts.length && !parts[i + 1].startsWith('[')) {
                chunks.push({ chord: currentChord, lyric: parts[i + 1] });
                i++;
                currentChord = null;
            } else {
                chunks.push({ chord: currentChord, lyric: '' });
                currentChord = null;
            }
        } else {
            if (part.length > 0) chunks.push({ chord: null, lyric: part });
        }
    }
    return chunks;
}

function isChordOnlyLine(line) {
    const t = line.trim();
    if (!t.length) return false;
    // Line with only [chord] tokens and spaces (no other text)
    const withoutChords = t.replace(/\[[^\]]+\]/g, '').trim();
    return withoutChords.length === 0 && /\[[^\]]+\]/.test(t);
}

const blocks = computed(() => {
    if (!props.content || !props.content.trim()) return [];

    const lines = props.content.split('\n');
    const result = [];
    let i = 0;

    while (i < lines.length) {
        const line = lines[i];
        const trimmed = line.trim();

        if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
            i++;
            continue;
        }
        if (trimmed.startsWith('#')) {
            i++;
            continue;
        }

        // Section header: [Intro], [Refrão], etc.
        if (/^\[([^\]]+)\]$/.test(trimmed)) {
            const title = trimmed.slice(1, -1);
            result.push({ type: 'section', title });
            i++;
            continue;
        }

        // Tablature block: consecutive E|----, B|---- lines
        if (TAB_LINE_REGEX.test(trimmed)) {
            const tabLines = [];
            while (i < lines.length && TAB_LINE_REGEX.test(lines[i].trim())) {
                tabLines.push(lines[i]);
                i++;
            }
            result.push({ type: 'tab', lines: tabLines.join('\n') });
            continue;
        }

        // Chord-only line: [Dm7]  [Bb9(11+)] or **Dm7**  **Bb9(11+)** (Cifra Club bold)
        const normalized = line.replace(/\*\*([^*]+)\*\*/g, '$1');
        if (isChordOnlyLine(normalized)) {
            result.push({ type: 'chordOnly', text: line.replace(/\*\*([^*]+)\*\*/g, '$1') });
            i++;
            continue;
        }

        // Chord + lyric line ([Chord]lyric)
        if (trimmed.includes('[') && trimmed.includes(']')) {
            const chunks = parseChordLyricLine(line);
            if (chunks.length > 0) {
                result.push({ type: 'chordLyric', chunks });
            }
            i++;
            continue;
        }

        // Empty line → skip (or add spacing)
        if (trimmed.length === 0) {
            i++;
            continue;
        }

        // Plain text
        result.push({ type: 'plain', text: line });
        i++;
    }

    return result;
});
</script>

<style scoped>
.tab-block {
    line-height: 1.5;
    letter-spacing: 0.02em;
}
</style>
