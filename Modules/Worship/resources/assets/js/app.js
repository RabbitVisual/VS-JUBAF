import './bootstrap';
import StageViewer from './components/StageViewer';
import MetronomeWidget from './components/MetronomeWidget';
import ChordEditor from './components/ChordEditor';
import RehearsalRoom from './components/RehearsalRoom';

import { createApp } from 'vue';
import Sortable from 'sortablejs'; // Import globally if needed or just inside components

// Admin Components
import CourseBuilder from './components/academy/admin/CourseBuilder.vue';
import LessonEditor from './components/academy/admin/LessonEditor.vue';
import CourseCreator from './components/academy/admin/CourseCreator.vue';

// --- EXISTING ALPINE LOGIC ---

// Define Alpine components
// Helper to register components safely
const registerComponents = () => {
    // Component for Song Editor
    Alpine.data('worshipSongEditor', ChordEditor);

    // Component for Setlist Management (Reordering and details)
    Alpine.data('worshipSetlistManager', (setlistId) => ({
        // ... (methods kept for axios logic)
        async updateItemKey(itemId, newKey) {
            try {
                await axios.post(`/admin/worship/setlist-items/${itemId}/update`, {
                    override_key: newKey
                });
            } catch (error) {
                console.error('Failed to update key', error);
            }
        },
        async updateItemNote(itemId, note) {
            try {
                await axios.post(`/admin/worship/setlist-items/${itemId}/update`, {
                    arrangement_note: note
                });
            } catch (error) {
                console.error('Failed to update note', error);
            }
        },
        async updateOrder(newOrder) {
            try {
                await axios.post(`/admin/worship/setlists/${setlistId}/reorder`, {
                    items: newOrder
                });
            } catch (error) {
                console.error('Failed to update order', error);
            }
        }
    }));

    // Component for Stage Mode
    Alpine.data('worshipStageViewer', StageViewer);

    // Component for Metronome
    Alpine.data('worshipMetronome', MetronomeWidget);

    // Component for Rehearsal Room
    Alpine.data('worshipRehearsal', RehearsalRoom);
};

// Register immediately if Alpine is already loaded, otherwise wait for init
// Register immediately if Alpine is already loaded, otherwise wait for init
console.log('Worship Module JS loaded. Checking Alpine...');
if (window.Alpine) {
    console.log('Alpine found on window. Registering components immediately.');
    registerComponents();
} else {
    console.log('Alpine NOT found on window. Waiting for alpine:init.');
    document.addEventListener('alpine:init', () => {
        console.log('alpine:init fired. Registering components.');
        registerComponents();
    });
}

// --- NEW VUE LOGIC ---

// Generic Mounter
const mountVueComponent = (elementId) => {
    const el = document.getElementById(elementId);
    if (!el) return;

    const componentName = el.dataset.component;
    const courseId = el.dataset.courseId;

    console.log(`Mounting Component: ${componentName} on #${elementId}`, { courseId });

    if (componentName === 'CourseCreator') {
        createApp(CourseCreator).mount(`#${elementId}`);
    } else if (componentName === 'CourseBuilder' || (elementId === 'worship-academy-admin' && courseId)) {
        createApp(CourseBuilder, { courseId }).mount(`#${elementId}`);
    }
};

mountVueComponent('worship-academy-admin');
