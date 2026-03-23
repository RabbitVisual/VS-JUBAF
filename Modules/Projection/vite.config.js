import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export const paths = [
    'Modules/Projection/resources/assets/js/app.js',
    'Modules/Projection/resources/assets/js/remote.js',
];

export default defineConfig({
    plugins: [
        laravel({
            input: paths,
            buildDirectory: 'build/projection',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
