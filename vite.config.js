import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/suppliers.js',
                'resources/js/layups.js',
                'resources/js/layers.js'
            ],
            refresh: true,
        }),
    ],
});
