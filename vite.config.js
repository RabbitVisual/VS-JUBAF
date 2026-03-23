import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import collectModuleAssetsPaths from './vite-module-loader.js';

async function getPaths() {
    const paths = [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/tours.js',
    ];

    return await collectModuleAssetsPaths(paths, 'Modules');
}

export default defineConfig(async () => {
    const paths = await getPaths();
    const inputPaths = Object.values(paths);

    return {
        plugins: [
            laravel({
                input: inputPaths,
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            tailwindcss(),
        ],
        build: {
            chunkSizeWarningLimit: 1500,
            // Evita avisos "preloaded but not used": não injeta <link rel="modulepreload"> para
            // chunks JS (múltiplos entry points fazem o browser preloadar recursos não usados no first paint).
            // Uso recomendado: build.modulePreload com resolveDependencies vazio (Vite continua a carregar os chunks normalmente).
            modulePreload: {
                resolveDependencies: () => [],
            },
            rollupOptions: {
                output: {
                    manualChunks(id) {
                        if (id.includes('node_modules')) {
                            if (id.includes('apexcharts')) {
                                return 'vendor-charts-apex';
                            }
                            if (id.includes('chart.js')) {
                                return 'vendor-charts-js';
                            }
                            if (id.includes('popperjs') || id.includes('flowbite')) {
                                return 'vendor-ui';
                            }
                            if (id.includes('aos')) {
                                return 'vendor-animations';
                            }
                            if (id.includes('alpine')) {
                                return 'vendor-alpine';
                            }
                            return 'vendor';
                        }
                    },
                },
            },
        },
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
