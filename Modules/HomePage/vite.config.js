import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { dirname } from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    build: {
        outDir: '../../public/build-homepage',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-homepage',
            input: [
                __dirname + '/resources/assets/sass/app.scss',
                __dirname + '/resources/assets/js/app.js'
            ],
            refresh: true,
        }),
    ],
});

// Entry points used by @vite() in views (root build collects these via vite-module-loader).
export const paths = [
    'Modules/HomePage/resources/assets/sass/app.scss',
    'Modules/HomePage/resources/assets/js/app.js',
];
