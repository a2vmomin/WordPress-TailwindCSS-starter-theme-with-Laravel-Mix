const mix = require("laravel-mix");
const isProd = mix.inProduction();
module.exports = {
    purge: {
        enabled: isProd,
        content: [
            "./**/*.php",
            "./js/**/*.js",
        ]
    },
    theme: {
        extend: {},
    },
    variants: {},
    plugins: [],
}