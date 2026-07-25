import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/css/sidebar.css',
                'resources/js/app.js',
                'resources/js/login.js',
                'resources/js/dashboard.js',
                'resources/js/allstocks.js',
                'resources/js/monitoring.js',
                'resources/js/pos_terminal.js',
                'resources/js/product-categorization.js',
                'resources/js/replacing-items.js',
                'resources/js/reverse-logistics.js',
                'resources/js/user_management.js',
                'resources/js/warehouse_management.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: '192.168.100.35',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
