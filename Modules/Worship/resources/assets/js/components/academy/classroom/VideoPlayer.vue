<template>
    <div class="w-full h-full">
        <iframe
            v-if="embedUrl"
            class="w-full h-full"
            :src="embedUrl"
            title="Lesson Video"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
        </iframe>
        <div v-else class="w-full h-full flex items-center justify-center bg-gray-900 text-gray-500">
            <p>Nenhum vídeo disponível para esta aula.</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    url: String
});

const embedUrl = computed(() => {
    if (!props.url) return null;
    let url = props.url;

    // Convert YouTube
    if (url.includes('youtube.com/watch?v=') || url.includes('youtu.be/')) {
        const videoId = url.split('v=')[1]?.split('&')[0] || url.split('/').pop();
        return `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1`;
    }

    // Vimeo
    if (url.includes('vimeo.com')) {
         const videoId = url.split('/').pop();
         return `https://player.vimeo.com/video/${videoId}`;
    }

    return url;
});
</script>
