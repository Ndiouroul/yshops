import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/accueil-style.css',
                'resources/css/inscription-style.css',
                'resources/css/connexion-style.css',
                'resources/css/dashboard-style.css',
                'resources/css/admin-style.css',
                'resources/js/inscription-script.js',
                'resources/js/connexion-script.js',
                'resources/js/admin-script.js',
                'resources/js/dashboard-script.js',
                'resources/js/accueil-script.js',
                'resources/js/shop-script.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
