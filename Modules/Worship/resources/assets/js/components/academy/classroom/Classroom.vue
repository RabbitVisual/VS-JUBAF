<template>
    <div class="flex h-screen bg-gray-900 text-white font-sans overflow-hidden">
        <!-- Sidebar: Lesson Navigation -->
        <div :class="['fixed inset-y-0 left-0 z-30 w-80 bg-gray-800 border-r border-gray-700 transform transition-transform duration-300 ease-in-out flex flex-col', sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']">
            <!-- Course Header -->
            <div class="p-6 border-b border-gray-700 bg-gray-800 relative">
                 <button @click="sidebarOpen = false" class="absolute top-4 right-4 md:hidden text-gray-400">
                    <i class="fa-duotone fa-times"></i>
                </button>
                <div class="flex items-center gap-4 mb-4">
                    <img v-if="course.cover_image" :src="course.cover_image" class="w-12 h-12 rounded-lg object-cover shadow-lg">
                    <div v-else class="w-12 h-12 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fa-duotone fa-music text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold leading-tight">{{ course.title }}</h2>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">{{ enrollment.progress_percent }}% CONCLUÍDO</p>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-gray-700 rounded-full h-1.5 overflow-hidden mb-4">
                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" :style="{ width: enrollment.progress_percent + '%' }"></div>
                </div>

                <a href="/painel/louvor/academy" class="flex items-center gap-2 text-[10px] font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">
                    <i class="fa-duotone fa-arrow-left"></i> Voltar ao Painel
                </a>
            </div>

            <!-- Modules & Lessons -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div v-for="module in course.modules" :key="module.id" class="border-b border-gray-700/50">
                    <div class="px-6 py-3 bg-gray-800/50 text-xs font-bold text-gray-400 uppercase tracking-widest sticky top-0 backdrop-blur-sm z-10">
                        {{ module.title }}
                    </div>
                    <div>
                        <div v-for="lesson in module.lessons" :key="lesson.id"
                             @click="canAccess(lesson) ? selectLesson(lesson) : null"
                             :class="['px-6 py-3 flex items-center gap-3 transition-colors border-l-2',
                                      currentLesson?.id === lesson.id ? 'bg-gray-700 border-indigo-500' : 'border-transparent',
                                      isCompleted(lesson.id) ? 'opacity-50' : '',
                                      canAccess(lesson) ? 'cursor-pointer hover:bg-gray-700' : 'cursor-not-allowed opacity-30 grayscale']">

                            <!-- Icon/Status -->
                            <div class="w-6 flex justify-center">
                                <i v-if="!canAccess(lesson)" class="fa-duotone fa-lock text-gray-600 text-[10px]"></i>
                                <i v-else-if="currentLesson?.id === lesson.id" class="fa-duotone fa-play text-indigo-400 text-xs"></i>
                                <i v-else-if="isCompleted(lesson.id)" class="fa-duotone fa-check-circle text-green-500 text-xs"></i>
                                <i v-else :class="getIconForType(lesson.type)" class="text-gray-500 text-xs"></i>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-200">{{ lesson.title }}</p>
                                <p class="text-[10px] text-gray-500">{{ lesson.duration_minutes }} min</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 md:ml-80 h-full flex flex-col relative">
            <!-- Mobile Header Toggle -->
             <div class="md:hidden p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center z-20">
                <span class="font-bold text-sm truncate pr-4">{{ currentLesson?.title || 'Carregando...' }}</span>
                <button @click="sidebarOpen = true" class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center text-white shrink-0">
                    <i class="fa-duotone fa-bars"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto bg-black custom-scrollbar">
                <div v-if="currentLesson" class="max-w-5xl mx-auto w-full">
                    <!-- Video Player Area -->
                    <div v-if="currentLesson.type === 'video'" class="w-full aspect-video bg-black shadow-2xl relative group">
                        <VideoPlayer :url="currentLesson.video_url" />
                    </div>

                    <!-- Content Area -->
                    <div class="p-8 pb-24">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h1 class="text-3xl font-bold mb-2">{{ currentLesson.title }}</h1>
                                <p class="text-gray-400 text-sm">{{ currentLesson.module?.title }}</p>
                            </div>

                            <button @click="markComplete"
                                    :disabled="markingComplete || isCompleted(currentLesson.id)"
                                    :class="['px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all transform active:scale-95 shadow-lg flex items-center gap-2',
                                             isCompleted(currentLesson.id) ? 'bg-green-900/20 text-green-500 cursor-default border border-green-500/30' : 'bg-indigo-600 hover:bg-indigo-500 text-white']">
                                <i :class="isCompleted(currentLesson.id) ? 'fa-duotone fa-check' : 'fa-duotone fa-circle-check'"></i>
                                {{ isCompleted(currentLesson.id) ? 'Concluída' : 'Concluir Aula' }}
                            </button>
                        </div>

                        <!-- Dynamic Content Viewer -->
                        <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700/50 min-h-[200px]">

                            <ChordProViewer v-if="currentLesson.type === 'chordpro'" :content="currentLesson.content" />

                            <DevotionalViewer v-if="currentLesson.type === 'devotional'"
                                            :bibleReference="currentLesson.bible_reference"
                                            :content="currentLesson.content" />

                            <div v-else-if="currentLesson.type === 'material'" class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-duotone fa-file-arrow-down text-2xl text-indigo-400"></i>
                                </div>
                                <h3 class="text-lg font-bold mb-2">Material de Apoio</h3>
                                <p class="text-gray-400 text-sm max-w-md mb-6">Baixe os recursos anexados a esta aula.</p>
                                <a v-if="currentLesson.pdf_path" :href="currentLesson.pdf_path" target="_blank" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-xs font-bold uppercase tracking-widest transition-colors">
                                    Baixar Recurso
                                </a>
                            </div>

                            <div v-else class="text-gray-400 text-sm leading-relaxed">
                                <p>Assista ao vídeo acima para concluir esta lição.</p>
                            </div>
                        </div>

                        <!-- Navigation Footer (Next/Prev) -->
                        <div class="mt-8 mb-12 flex justify-between">
                            <button @click="prevLesson" :disabled="!hasPrev" class="text-gray-500 hover:text-white disabled:opacity-30 disabled:hover:text-gray-500 transition-colors flex items-center gap-2 text-sm font-bold uppercase">
                                <i class="fa-duotone fa-arrow-left"></i> Anterior
                            </button>
                            <button @click="nextLesson" :disabled="!hasNext" class="text-indigo-400 hover:text-indigo-300 disabled:opacity-30 disabled:text-gray-500 transition-colors flex items-center gap-2 text-sm font-bold uppercase">
                                Próxima <i class="fa-duotone fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="flex h-full items-center justify-center">
                    <i class="fa-duotone fa-spinner-third fa-spin text-4xl text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import VideoPlayer from './VideoPlayer.vue';
import ChordProViewer from './ChordProViewer.vue';
import DevotionalViewer from './DevotionalViewer.vue';

const course = ref({ modules: [] });
const enrollment = ref({ progress_percent: 0 });
const completedLessons = ref([]);
const currentLesson = ref(null);
const sidebarOpen = ref(false);
const markingComplete = ref(false);

const getCourseId = () => {
    // Assuming URL pattern /worship/academy/course/{id}/classroom
    // Or data attribute
    const appEl = document.getElementById('worship-academy-classroom');
    return appEl ? appEl.dataset.courseId : window.location.pathname.split('/')[4];
};

const cId = getCourseId();

// Flattened list for navigation
const allLessons = computed(() => {
    let lessons = [];
    if(course.value && course.value.modules) {
        course.value.modules.forEach(m => {
            if(m.lessons) lessons = lessons.concat(m.lessons);
        });
    }
    return lessons;
});

const currentIndex = computed(() => {
    if(!currentLesson.value) return -1;
    return allLessons.value.findIndex(l => l.id === currentLesson.value.id);
});

// Locking Logic
const canAccess = (lesson) => {
    if (!lesson) return false;
    const index = allLessons.value.findIndex(l => l.id === lesson.id);
    if (index === 0) return true; // First lesson always accessible
    if (isCompleted(lesson.id)) return true; // Already completed

    // Accessible if previous lesson is completed
    const prev = allLessons.value[index - 1];
    return isCompleted(prev.id);
};

const hasNext = computed(() => {
    if (!currentLesson.value) return false;
    if (!isCompleted(currentLesson.value.id)) return false; // Must complete current first
    return currentIndex.value < allLessons.value.length - 1;
});

const hasPrev = computed(() => currentIndex.value > 0);

const fetchCourse = async () => {
    try {
        const res = await axios.get(`/api/v1/worship/academy/courses/${cId}`);
        const payload = res.data.data;
        course.value = payload.course;
        enrollment.value = payload.enrollment;
        completedLessons.value = payload.completed_lessons;

        // Auto select first lesson or first uncompleted
        if (allLessons.value.length > 0) {
            // Find first uncompleted
            const firstUncompleted = allLessons.value.find(l => !completedLessons.value.includes(l.id));
            currentLesson.value = firstUncompleted || allLessons.value[0];
        }
    } catch (e) {
        console.error("Failed to load course", e);
    }
};

const selectLesson = (lesson) => {
    currentLesson.value = lesson;
    sidebarOpen.value = false; // Close sidebar on mobile
};

const isCompleted = (id) => completedLessons.value.includes(id);

const getIconForType = (type) => {
    switch(type) {
        case 'video': return 'fa-duotone fa-play-circle';
        case 'chordpro': return 'fa-duotone fa-music';
        case 'devotional': return 'fa-duotone fa-book-heart';
        case 'material': return 'fa-duotone fa-file-lines';
        default: return 'fa-duotone fa-circle';
    }
};

const markComplete = async () => {
    if (!currentLesson.value || isCompleted(currentLesson.value.id)) return;

    markingComplete.value = true;
    try {
        const res = await axios.post(`/api/v1/worship/academy/lessons/${currentLesson.value.id}/complete`);
        completedLessons.value.push(currentLesson.value.id);
        enrollment.value.progress_percent = res.data.data.progress;

        // Auto advance after 1.5s
        if (hasNext.value) {
            setTimeout(() => {
                nextLesson();
            }, 1500);
        }
    } catch (e) {
        console.error(e);
        alert('Erro ao salvar progresso. Tente novamente.');
    } finally {
        markingComplete.value = false;
    }
};

const nextLesson = () => {
    if (hasNext.value) {
        currentLesson.value = allLessons.value[currentIndex.value + 1];
    }
};

const prevLesson = () => {
    if (hasPrev.value) {
        currentLesson.value = allLessons.value[currentIndex.value - 1];
    }
};

onMounted(() => {
    fetchCourse();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #1f2937;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>
