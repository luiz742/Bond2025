import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true, // hot reload para Laravel
        }),
        vue({
            template: {
                // Configurações do template do Vue, sem mexer em componentes individuais
                compilerOptions: {
                    // Ignora tags customizadas que não devem ser interpretadas como componentes
                    isCustomElement: (tag) => tag.startsWith('ion-'),
                },
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js', // Facilita imports
        },
    },
});
