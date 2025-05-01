module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig"
    ],
    theme: {
        extend: {},
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