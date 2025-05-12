module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig"
    ],
    theme: {
        extend: {
            screens: {
                'print': { 'raw': 'print' }
            }
        }
    },
    plugins: []
};
