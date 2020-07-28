const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");

if (!mix.inProduction()) {
    mix.sass("./resources/sass/app.scss", "./css/").options({
        processCssUrls: false,
        postCss: [require("autoprefixer")(), tailwindcss("./tailwind.config.js")]
    });
    mix.js("./resources/js/app.js", "./js/app.js");
} else {
    mix.sass("./resources/sass/app.scss", "./dist/css/").options({
        processCssUrls: false,
        postCss: [require("autoprefixer")(), tailwindcss("./tailwind.config.js")]
    });
    mix.js("./resources/js/app.js", "./dist/js/app.js");
}