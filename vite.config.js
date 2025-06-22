import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/pages/assignment.css',
                'resources/js/assignment-modal.js',
                'resources/js/assignment-utils.js',
                'resources/js/admin-sidebar.js',
                'resources/js/pages/student-assignment.js',
                'resources/js/assignment-create.js',
                'resources/js/assignment-list.js',
                'resources/js/assignment-show.js',
                'resources/js/editor.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'tinymce',
                        '@fancyapps/ui'
                    ]
                }
            }
        },
        chunkSizeWarningLimit: 1000
    }
});
