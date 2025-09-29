import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    build: {
        chunkSizeWarningLimit: 4000,
        outDir: 'public/build',
        manifest: true,
        parallel: true,
        sourcemap: false,
        rollupOptions: {
        output: {
            manualChunks(id) {
                if (id.includes('node_modules')) {
                    return 'vendor';
                }
            },
        },
    },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    ],
    base: '/build/',
    resolve: {
        alias: {
            '@assets': '/resources/', // Update this with the correct path to your images
            '@favicon': '/resources/images/', // Update this with the correct path to your images
        },
    },


});

