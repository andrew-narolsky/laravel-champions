import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/vendors/mdi/css/materialdesignicons.min.css',
                'resources/assets/js/main.js',
                'resources/assets/css/custom.css',
                'resources/assets/css/styles.css',
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/assets/images',
                    dest: '',
                },
            ]
        }),
    ],
});
