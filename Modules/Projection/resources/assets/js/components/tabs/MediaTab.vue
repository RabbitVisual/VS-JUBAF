<template>
    <div class="flex h-full">
        <!-- Upload / List -->
        <div class="w-1/3 border-r border-slate-800 p-4 flex flex-col">
            <h3 class="text-sm font-bold text-slate-400 mb-4 uppercase">Biblioteca de Mídia</h3>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-300" for="file_input">Upload</label>
                <div class="flex gap-2">
                    <input class="block w-full text-sm text-slate-300 border border-slate-600 rounded-lg cursor-pointer bg-slate-800 focus:outline-none"
                           id="file_input" type="file" ref="fileInput" @change="handleFileChange">
                    <button @click="uploadFile" :disabled="!selectedFile || uploading" class="bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white px-4 rounded transition">
                        <i v-if="uploading" class="fa-duotone fa-spinner fa-spin"></i>
                        <i v-else class="fa-duotone fa-upload"></i>
                    </button>
                </div>
                <p v-if="uploadError" class="mt-1 text-xs text-red-500">{{ uploadError }}</p>
            </div>

            <div class="flex-1 overflow-y-auto pr-2 space-y-2">
                <div v-for="asset in assets" :key="asset.id"
                     @click="selectAsset(asset)"
                     class="flex items-center gap-3 p-2 rounded cursor-pointer hover:bg-slate-800 transition border border-white/5 bg-slate-800/20 group/item">
                    <div class="w-16 h-10 bg-slate-900 rounded overflow-hidden flex items-center justify-center shrink-0">
                        <img v-if="asset.type === 'image'" :src="asset.thumbnail_path" class="w-full h-full object-cover">
                        <i v-else class="fa-duotone fa-video text-slate-500"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-200 truncate">{{ asset.title }}</p>
                        <p class="text-[8px] text-slate-500 uppercase">{{ asset.type }}</p>
                    </div>
                    <button @click.stop="deleteAsset(asset)" class="p-2 text-slate-600 hover:text-red-500 transition-all" title="Excluir Permanentemente">
                        <i class="fa-duotone fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Selection -->
        <div class="flex-1 p-4 bg-slate-900/50 flex flex-col items-center justify-center text-slate-500 relative">
            <div v-if="selectedAsset" class="text-center w-full max-w-2xl">
                 <div class="bg-black rounded border border-slate-700 overflow-hidden mb-4 relative aspect-video flex items-center justify-center shadow-2xl">
                      <img v-if="selectedAsset.type === 'image'" :src="selectedAsset.url || selectedAsset.file_path" class="max-w-full max-h-full">
                      <video v-else controls :src="selectedAsset.url || selectedAsset.file_path" class="max-w-full max-h-full"></video>
                 </div>
                  <h4 class="text-white text-sm font-bold mb-4 uppercase tracking-widest">{{ selectedAsset.title }}</h4>
                  <div class="flex flex-wrap justify-center gap-2">
                      <button @click="previewAsset" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg">PREVIEW</button>
                      <button @click="goLiveAsset" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg">ENVIAR P/ TELA</button>
                      <button @click="setAsBackground" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg">DEFINIR COMO FUNDO</button>
                  </div>
            </div>
            <div v-else class="text-center">
                <i class="fa-duotone fa-images text-6xl mb-4 opacity-20"></i>
                <p class="text-[10px] font-black uppercase tracking-widest">Selecione uma mídia para gerenciar</p>
            </div>

            <!-- Global Media Actions & Logo Customization -->
            <div class="absolute bottom-4 left-4 right-4 flex flex-col gap-4">
                <!-- Advanced Logo Settings -->
                <div class="bg-slate-950/80 border border-indigo-500/30 p-4 rounded-2xl backdrop-blur-xl shadow-2xl">
                    <h5 class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                        <i class="fa-duotone fa-sliders"></i> Personalização do Logo
                    </h5>

                    <div class="flex flex-col gap-3">
                         <div class="relative">
                            <input v-model="localLogoMessage" type="text"
                                   class="w-full bg-black/50 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:border-indigo-500 outline-none transition-all placeholder-slate-700"
                                   placeholder="Mensagem Personalizada...">
                         </div>

                         <div class="grid grid-cols-3 gap-2">
                            <button @click="triggerLogoWith('verse')"
                                    :class="['px-2 py-2 rounded-lg font-black text-[9px] uppercase transition-all', (props.state.type === 'logo' && !props.state.isClear && props.state.logoOption === 'verse') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-500 hover:text-white']">
                                <i class="fa-duotone fa-book-bible mr-1"></i> + Versículo
                            </button>
                            <button @click="triggerLogoWith('message')"
                                    :class="['px-2 py-2 rounded-lg font-black text-[9px] uppercase transition-all', (props.state.type === 'logo' && !props.state.isClear && props.state.logoOption === 'message') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-500 hover:text-white']">
                                <i class="fa-duotone fa-message mr-1"></i> + Mensagem
                            </button>
                            <button @click="triggerLogoWith('none')"
                                    :class="['px-2 py-2 rounded-lg font-black text-[9px] uppercase transition-all', (props.state.type === 'logo' && !props.state.isClear && props.state.logoOption === 'none') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-500 hover:text-white']">
                                <i class="fa-duotone fa-church mr-1"></i> Só Logo
                            </button>
                         </div>
                    </div>
                </div>

                 <button @click="clearBackground" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-950/40 border border-red-500/30 text-red-500 hover:bg-red-600 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 shadow-lg backdrop-blur-md">
                    <i class="fa-duotone fa-image-slash"></i> Remover Fundo Atual
                 </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    state: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['preview', 'go-live']);

const assets = ref([]);
const selectedAsset = ref(null);
const fileInput = ref(null);
const selectedFile = ref(null);
const uploading = ref(false);
const uploadError = ref('');

const localLogoMessage = ref(props.state.logoMessage || 'Sejam Bem Vindos!');
const logoOption = ref(props.state.logoOption || 'verse');

const fetchAssets = () => {
    axios.get('/api/v1/projection/assets').then(res => {
        assets.value = res.data?.data ?? res.data ?? [];
    });
};

const handleFileChange = (event) => {
    selectedFile.value = event.target.files[0];
    uploadError.value = '';
};

const uploadFile = () => {
    if (!selectedFile.value) return;

    uploading.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);

    axios.post('/api/v1/projection/assets', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(res => {
        const asset = res.data?.data ?? res.data;
        if (asset) assets.value.unshift(asset);
        selectedFile.value = null;
        if (fileInput.value) fileInput.value.value = '';
        uploading.value = false;
    })
    .catch(err => {
        console.error(err);
        uploadError.value = 'Falha no upload.';
        uploading.value = false;
    });
};

const selectAsset = (asset) => {
    selectedAsset.value = asset;
};

const deleteAsset = (asset) => {
    if (!confirm('Tem certeza que deseja excluir esta mídia permanentemente?')) return;

    axios.delete(`/api/v1/projection/assets/${asset.id}`)
        .then(() => {
            assets.value = assets.value.filter(a => a.id !== asset.id);
            if (selectedAsset.value?.id === asset.id) {
                selectedAsset.value = null;
            }
        })
        .catch(err => {
            console.error('Erro ao excluir mídia', err);
            alert('Falha ao excluir mídia.');
        });
};

const previewAsset = () => {
    if (!selectedAsset.value) return;
    const url = selectedAsset.value.url || selectedAsset.value.file_path;
    emit('preview', {
        type: selectedAsset.value.type,
        url,
        content: ''
    });
};

const goLiveAsset = () => {
    if (!selectedAsset.value) return;
    const url = selectedAsset.value.url || selectedAsset.value.file_path;
    emit('go-live', {
        type: selectedAsset.value.type,
        url,
        content: ''
    });
};

const setAsBackground = () => {
    if (!selectedAsset.value) return;
    const url = selectedAsset.value.url || selectedAsset.value.file_path;
    emit('go-live', {
        bgUrl: url,
        bgType: selectedAsset.value.type,
        logoOption: logoOption.value,
        logoMessage: localLogoMessage.value,
    });
};

const triggerLogoWith = (option) => {
    logoOption.value = option;
    emit('go-live', {
        type: 'logo',
        logoOption: logoOption.value,
        logoMessage: localLogoMessage.value,
        isClear: false,
        isBlackout: false,
        content: '',
        footer: ''
    });
};

const clearBackground = () => {
    emit('go-live', {
        bgUrl: '',
        bgType: 'image'
    });
};

onMounted(() => {
    fetchAssets();
});
</script>
