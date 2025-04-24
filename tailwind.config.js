// tailwind.config.js
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        // Add these if using PHP files:
        "./src/Controller/**/*.php",
        "./src/Form/**/*.php"
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}