import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/getInitials.js',
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/dashboard.css',
                'resources/css/default.css',
                'resources/css/header.css',
                'resources/css/profile.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
