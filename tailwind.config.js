// tailwind.config.js
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./templates/**/*.html.twig",
        "./assets/**/*.js"
    ],
    theme: {
        extend: {
            screens: {
                'print': {'raw': 'print'}
            }
        },
    },
    plugins: [],
    variants: {
        extend: {
            backgroundColor: ['disabled', 'enabled'],
            opacity: ['disabled'],
            cursor: ['disabled'],
        },
    },
}