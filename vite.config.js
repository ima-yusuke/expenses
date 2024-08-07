import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/side-menu.css',
                'resources/js/app.js',
                'resources/js/side-menu.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost'
        }
    },
    build: {
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                appStyles: 'resources/css/app.css',
                sideMenuStyles: 'resources/css/side-menu.css',
                app: 'resources/js/app.js',
                sideMenu: 'resources/js/side-menu.js',
            }
        }
    }
});
