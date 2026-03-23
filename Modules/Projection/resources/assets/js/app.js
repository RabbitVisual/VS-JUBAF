import { createApp } from 'vue';
import ProjectionConsole from './components/ProjectionConsole.vue';

// Setup FontAwesome if needed or rely on existing global CSS
// If relying on global CSS (Task 3 mentions vanilla JS screen, but console is Vue),
// we just mount the app.

const mountEl = document.getElementById('projection-app');

if (mountEl) {
    const app = createApp(ProjectionConsole);
    app.mount(mountEl);
}
