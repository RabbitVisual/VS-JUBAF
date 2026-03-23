<template>
    <div class="flex h-screen bg-gray-900 text-white font-sans">
        <!-- Sidebar: Course Structure -->
        <div class="w-1/3 border-r border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-700 bg-gray-800">
                <h2 class="text-lg font-bold">Estrutura do Curso</h2>
                <div class="flex gap-2 mt-2">
                    <button @click="addModule" class="bg-blue-600 hover:bg-blue-500 text-xs px-3 py-1 rounded">
                        + Módulo
                    </button>
                    <button @click="saveStructure" :disabled="saving" class="bg-green-600 hover:bg-green-500 text-xs px-3 py-1 rounded ml-auto">
                        {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4" ref="modulesList">
                <div v-for="(module, mIndex) in course.modules" :key="module.tempId || module.id" class="mb-4 bg-gray-800 rounded border border-gray-700 module-item" :data-id="module.id">
                    <!-- Module Header -->
                    <div class="p-2 bg-gray-700 flex items-center justify-between cursor-move handle-module">
                        <input v-model="module.title" class="bg-transparent text-sm font-bold w-full outline-none" placeholder="Título do Módulo">
                        <button @click="deleteModule(mIndex)" class="text-red-400 hover:text-red-300 ml-2">
                            <i class="fa-duotone fa-trash"></i>
                        </button>
                    </div>

                    <!-- Lessons List -->
                    <div class="p-2 space-y-2 min-h-[50px] lessons-list" :data-module-index="mIndex">
                        <div v-for="(lesson, lIndex) in module.lessons" :key="lesson.tempId || lesson.id"
                             @click="selectLesson(lesson)"
                             :class="['p-2 rounded cursor-pointer border lesson-item flex items-center gap-2 group',
                                      selectedLesson === lesson ? 'bg-indigo-900 border-indigo-500' : 'bg-gray-700 border-gray-600 hover:bg-gray-600']">
                            <i class="fa-duotone fa-grip-lines text-gray-500 cursor-move handle-lesson"></i>
                            <i :class="getIconForType(lesson.type)" class="text-gray-400"></i>
                            <span class="text-xs truncate flex-1">{{ lesson.title || 'Nova Aula' }}</span>
                            <button @click.stop="deleteLesson(mIndex, lIndex)" class="text-gray-600 group-hover:text-red-400 hover:text-red-300 transition-colors">
                                <i class="fa-duotone fa-trash text-[10px]"></i>
                            </button>
                        </div>

                        <button @click="addLesson(mIndex)" class="w-full py-1 text-center text-xs text-gray-400 border border-dashed border-gray-600 rounded hover:bg-gray-700">
                            + Adicionar Aula
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area: Editor -->
        <div class="flex-1 flex flex-col bg-gray-900">
            <div v-if="selectedLesson" class="h-full">
                <LessonEditor :modelValue="selectedLesson" @update:modelValue="onLessonUpdate" />
            </div>
            <div v-else class="flex-1 flex items-center justify-center text-gray-600">
                <div class="text-center">
                    <i class="fa-duotone fa-person-chalkboard text-6xl mb-4"></i>
                    <p>Selecione uma aula para editar</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Sortable from 'sortablejs';
import LessonEditor from './LessonEditor.vue';

const props = defineProps({
    courseId: String // Passed from blade via attribute usually, or we parse URL. For now assume URL parsing or data attribute.
});

const course = ref({ modules: [] });
const selectedLesson = ref(null);
const saving = ref(false);
const modulesList = ref(null);

// Get ID from URL if prop missing (common in hybrid apps)
const getCourseId = () => {
    if (props.courseId) return props.courseId;
    const parts = window.location.pathname.split('/');
    const appEl = document.getElementById('worship-academy-admin');
    return appEl ? appEl.dataset.courseId : null;
};

const cId = ref(getCourseId());

const fetchCourse = async () => {
    if (!cId.value) return;
    const res = await axios.get(`/api/v1/worship/academy/courses/${cId.value}/structure`);
    course.value = res.data.data;

    // Ensure modules have lessons array
    course.value.modules.forEach(m => {
        if (!m.lessons) m.lessons = [];
    });

    nextTick(() => {
        initSortable();
    });
};

const initSortable = () => {
    // Sort Modules
    if (modulesList.value) {
        Sortable.create(modulesList.value, {
            handle: '.handle-module',
            animation: 150,
            onEnd: (evt) => {
                const item = course.value.modules.splice(evt.oldIndex, 1)[0];
                course.value.modules.splice(evt.newIndex, 0, item);
            }
        });

        // Sort Lessons (Nested)
        document.querySelectorAll('.lessons-list').forEach(el => {
            Sortable.create(el, {
                group: 'lessons',
                handle: '.handle-lesson',
                animation: 150,
                onEnd: (evt) => {
                    const fromModuleIndex = parseInt(evt.from.dataset.moduleIndex);
                    const toModuleIndex = parseInt(evt.to.dataset.moduleIndex);

                    const item = course.value.modules[fromModuleIndex].lessons.splice(evt.oldIndex, 1)[0];
                    course.value.modules[toModuleIndex].lessons.splice(evt.newIndex, 0, item);
                }
            });
        });
    }
};

const addModule = () => {
    course.value.modules.push({
        tempId: Date.now(),
        title: 'Novo Módulo',
        lessons: []
    });
    nextTick(() => initSortable());
};

const deleteModule = (index) => {
    if (confirm('Excluir módulo e todas as suas aulas?')) {
        course.value.modules.splice(index, 1);
    }
};

const deleteLesson = (moduleIndex, lessonIndex) => {
    if (confirm('Excluir aula?')) {
        const lesson = course.value.modules[moduleIndex].lessons[lessonIndex];
        course.value.modules[moduleIndex].lessons.splice(lessonIndex, 1);
        if (selectedLesson.value === lesson) {
            selectedLesson.value = null;
        }
    }
};

const addLesson = (moduleIndex) => {
    const newLesson = {
        tempId: Date.now(),
        title: 'Nova Aula',
        type: 'video',
        content: '',
        video_url: '',
        teacher_tips: '',
        multicam_video_url: '',
        pdf_path: '',
        sheet_music_pdf: '',
        bible_reference: '',
        duration_minutes: null,
    };
    course.value.modules[moduleIndex].lessons.push(newLesson);
    selectLesson(newLesson);
    nextTick(() => initSortable());
};

const selectLesson = (lesson) => {
    selectedLesson.value = lesson;
};

/** Sync editor changes into the lesson object inside course.modules so POST sends correct data (e.g. video_url). */
const onLessonUpdate = (updated) => {
    const current = selectedLesson.value;
    if (!current || !course.value.modules) return;
    for (const mod of course.value.modules) {
        const lesson = mod.lessons.find(l => l === current || (l.id && l.id === current.id) || (l.tempId && l.tempId === current.tempId));
        if (lesson) {
            Object.assign(lesson, updated);
            break;
        }
    }
};

const saveStructure = async () => {
    saving.value = true;
    try {
        const res = await axios.post(`/api/v1/worship/academy/courses/${cId.value}/structure`, {
            modules: course.value.modules
        });
        if (res.data && res.data.course) {
            course.value = res.data.course;
            course.value.modules.forEach(m => {
                if (!m.lessons) m.lessons = [];
            });
            if (selectedLesson.value) {
                const flat = course.value.modules.flatMap(m => m.lessons);
                const found = flat.find(l => l.id === selectedLesson.value.id);
                if (found) selectedLesson.value = found;
            }
        }
        alert('Salvo com sucesso!');
    } catch (e) {
        alert('Erro ao salvar estrutura');
        console.error(e);
    } finally {
        saving.value = false;
    }
};

const getIconForType = (type) => {
    switch(type) {
        case 'video': return 'fa-duotone fa-video';
        case 'chordpro': return 'fa-duotone fa-music';
        case 'material': return 'fa-duotone fa-file-pdf';
        default: return 'fa-duotone fa-file';
    }
};

onMounted(() => {
    fetchCourse();
});
</script>

<style scoped>
/* Scoped styles if needed */
</style>
