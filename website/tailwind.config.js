/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')

module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        plugin(function ({addBase, theme}) {
            addBase({
                'h1': {fontSize: theme('fontSize.2xl')},
                'h2': {fontSize: theme('fontSize.xl')},
                'h3': {fontSize: theme('fontSize.lg')},
            })
        })
    ],

}
