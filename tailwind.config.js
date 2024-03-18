import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        screens: {
            'sm': '500px',
            'md': [
              // Sidebar appears at 768px, so revert to `sm:` styles between 768px
              // and 868px, after which the main content area is wide enough again to
              // apply the `md:` styles.
              {'min': '668px', 'max': '767px'},
              {'min': '868px'}
            ],
            'lg': '1100px',
            'xl': '1400px',
          },
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
