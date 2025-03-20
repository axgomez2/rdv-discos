const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
            },
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            },
            transitionDuration: {
                '250': '250ms',
            },
            transitionTimingFunction: {
                'ease-in-custom': 'cubic-bezier(0.4, 0, 1, 1)',
                'ease-out-custom': 'cubic-bezier(0, 0, 0.2, 1)',
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
            opacity: {
                '15': '0.15',
                '35': '0.35',
                '65': '0.65',
                '85': '0.85',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
};
