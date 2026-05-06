import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/css/**/*.css',
    ],

    safelist: [
        'attractions-map',
        'attractions-map-popup',
        'attractions-map-popup__image',
        'attractions-map-popup__content',
        'attractions-map-popup__meta',
        'explore-map-section',
        'explore-map-shell',
        'explore-map-head',
        'explore-map-badge',
        'explore-map-empty',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
