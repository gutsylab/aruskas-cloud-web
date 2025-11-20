import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS entry points
                'resources/css/app.css',      // Public/landing page styles
                'resources/css/admin.css',    // Admin panel styles
                
                // JavaScript entry points
                'resources/js/app.js',        // Original app entry (if still needed)
                'resources/js/admin.js',      // Admin panel scripts
                'resources/js/public.js',     // Public/landing page scripts
            ],
            refresh: true,
        }),
    ],
});
