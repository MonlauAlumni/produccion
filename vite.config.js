import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

// Agregar el alias para que @ apunte a la carpeta correcta
export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            '@': '/resources/js', // Alias para @ apuntando a resources/js
        },
    },

    server: {
        cors: {
            origin: '*',
        },
    },
});
