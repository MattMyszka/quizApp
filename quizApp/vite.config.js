import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        https: false, 
        hmr: {
            host: 'riven.tail282969.ts.net',
            protocol: 'wss', 
            port: 5173 // Kluczowe: łączymy się bezpośrednio z portem 5173
        },
        allowedHosts: ['riven.tail282969.ts.net'],
    },
});