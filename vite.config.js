import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/eco.css',
                'resources/js/eco-scene.js',
                'resources/js/eco-student.js',
                'resources/js/game-runner.js',
                'resources/js/game-environment-3d.js',
                'resources/js/games/photosynthesis.js',
                'resources/js/games/seed-grow.js',
                'resources/js/games/vine-growth.js',
                'resources/js/games/star-patterns.js',
                'resources/js/games/rainbow.js',
                'resources/js/games/mosquito-lifecycle.js',
                'resources/js/games/water-cycle.js',
                'resources/js/games/day-night.js',
                'resources/js/games/solar-eclipse.js',
                'resources/js/games/lunar-eclipse.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
