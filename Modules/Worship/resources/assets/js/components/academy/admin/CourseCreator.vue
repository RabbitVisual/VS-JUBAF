<template>
    <div class="min-h-screen bg-gray-900 text-white p-6 md:p-10 font-sans">
        <!-- Progress Bar -->
        <div class="max-w-4xl mx-auto mb-10">
            <div class="flex items-center justify-between relative">
                <div class="absolute inset-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-800 w-full z-0"></div>
                <div class="absolute inset-0 top-1/2 -translate-y-1/2 h-0.5 bg-indigo-600 transition-all duration-500 z-0" :style="{ width: step === 1 ? '0%' : '100%' }"></div>

                <div v-for="s in 2" :key="s" class="relative z-10 flex flex-col items-center">
                    <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 border-2',
                                 step >= s ? 'bg-indigo-600 border-indigo-600 text-white shadow-[0_0_15px_rgba(79,70,229,0.5)]' : 'bg-gray-900 border-gray-700 text-gray-500']">
                        <i v-if="step > s" class="fa-solid fa-check"></i>
                        <span v-else>{{ s }}</span>
                    </div>
                    <span :class="['text-xs mt-2 font-bold uppercase tracking-wider transition-colors duration-300', step >= s ? 'text-indigo-400' : 'text-gray-600']">
                        {{ s === 1 ? 'Informações Básicas' : 'Estrutura do Curso' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Step 1: Base Info -->
        <transition name="fade-slide" mode="out-in">
            <div v-if="step === 1" key="step1" class="max-w-4xl mx-auto">
                <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700 p-8 rounded-3xl shadow-2xl">
                    <h2 class="text-3xl font-extrabold mb-8 bg-clip-text text-transparent bg-linear-to-r from-white to-gray-500">
                        Criar Novo Curso
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                             <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Título do Curso</label>
                             <input v-model="form.title" type="text" placeholder="Ex: Guitarra Worship Masterclass"
                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-2xl px-6 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Instrumento / Área</label>
                            <select v-model="form.instrument_id"
                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-2xl px-6 py-4 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">Selecione...</option>
                                <option v-for="inst in instruments" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Nível</label>
                            <select v-model="form.level"
                                    class="w-full bg-gray-900/50 border border-gray-700 rounded-2xl px-6 py-4 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="beginner">Iniciante</option>
                                <option value="intermediate">Intermediário</option>
                                <option value="advanced">Avançado</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Descrição</label>
                            <textarea v-model="form.description" rows="4" placeholder="Descreva o que os alunos aprenderão..."
                                      class="w-full bg-gray-900/50 border border-gray-700 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="md:col-span-2 flex items-center gap-6 p-4 bg-gray-900/50 rounded-2xl border border-gray-700">
                            <div class="flex-1">
                                <h4 class="font-bold">Status Inicial</h4>
                                <p class="text-xs text-gray-500">O curso será visível para alunos?</p>
                            </div>
                            <div class="flex gap-2 p-1 bg-gray-800 rounded-xl">
                                <button @click="form.status = 'draft'" :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all', form.status === 'draft' ? 'bg-gray-700 text-white shadow-xl' : 'text-gray-500']">Rascunho</button>
                                <button @click="form.status = 'published'" :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all', form.status === 'published' ? 'bg-indigo-600 text-white shadow-xl' : 'text-gray-500']">Publicado</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-end">
                        <button @click="createBaseCourse" :disabled="loading"
                                class="flex items-center gap-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white px-10 py-4 rounded-2xl font-bold shadow-[0_10px_30px_rgba(79,70,229,0.3)] hover:scale-105 active:scale-95 transition-all">
                            <span>Próximo Passo: Estrutura</span>
                            <i v-if="loading" class="fa-solid fa-circle-notch fa-spin"></i>
                            <i v-else class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Structure (Transitioning to Builder) -->
            <div v-else key="step2" class="max-w-6xl mx-auto">
                <div class="bg-gray-800 border border-gray-700 rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[700px]">
                    <div class="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-800/80 backdrop-blur-md">
                        <div>
                            <h3 class="text-xl font-bold">Estrutura do Curso: {{ form.title }}</h3>
                            <p class="text-xs text-gray-400">Adicione módulos e lições ao seu curso.</p>
                        </div>
                        <div class="flex gap-3">
                             <button @click="saveFinal" :disabled="loading" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-xl font-bold transition-all">
                                Finalizar e Ver Curso
                             </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-hidden">
                         <CourseBuilder :courseId="courseId" />
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import CourseBuilder from './CourseBuilder.vue';

const step = ref(1);
const loading = ref(false);
const courseId = ref(null);
const instruments = ref([]);

const form = ref({
    title: '',
    instrument_id: '',
    level: 'beginner',
    description: '',
    status: 'draft'
});

const fetchInstruments = async () => {
    try {
        const res = await axios.get('/admin/worship/instruments'); // Should have an API or we pass via props
        // Actually instruments might be passed as props from blade
        if (window.worshipInstruments) {
            instruments.value = window.worshipInstruments;
        }
    } catch (e) { console.error(e); }
};

const createBaseCourse = async () => {
    if (!form.value.title || !form.value.instrument_id) {
        alert('Por favor, preencha o título e selecione um instrumento.');
        return;
    }

    loading.value = true;
    try {
        const res = await axios.post('/api/v1/worship/academy/courses', form.value);
        courseId.value = res.data.data.id;
        step.value = 2;
    } catch (e) {
        alert('Erro ao criar curso base. Verifique os dados.');
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const saveFinal = () => {
    window.location.href = `/admin/worship/academy/courses/${courseId.value}`;
};

onMounted(() => {
    if (window.worshipInstruments) {
        instruments.value = window.worshipInstruments;
    }
});
</script>

<style scoped>
.fade-slide-enter-active, .fade-slide-leave-active {
    transition: all 0.4s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>
