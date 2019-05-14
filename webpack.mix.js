const mix = require('laravel-mix');

mix.setResourceRoot(path.normalize('resources'));
mix.setPublicPath(path.normalize('public'));

mix.extract([
    'vue',
    'lodash',
    'axios',
    'jquery',
    'bootstrap',
]);

mix.js('resources/js/app.js', 'public/dist').version().sourceMaps();

mix.sass('resources/sass/app.scss', 'public/dist').version().sourceMaps();
