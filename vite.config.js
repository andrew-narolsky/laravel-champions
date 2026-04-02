import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/vendors/mdi/css/materialdesignicons.min.css',
                'resources/assets/css/custom.css',
                'resources/assets/css/styles.css',
                'resources/assets/js/app.js',
                'resources/assets/js/utils/text-editor.js',
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/assets/images',
                    dest: '',
                },
                {
                    src: 'resources/assets/fonts',
                    dest: '',
                }
            ]
        }),
    ],
});
