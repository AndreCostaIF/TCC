const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.styles(['resources/css/config.css','resources/css/header.css','resources/css/app.css'], 'public/css/style.css');
mix.scripts(['node_modules/jquery/dist/jquery.js'],
 'public/jquery.js')
 .scripts('node_modules/bootstrap/dist/js/bootstrap.bundle.js',
            'public/bootstrap/bootstrap.js')
.scripts('resources/js/form.js', 'public/form.js')
.scripts('resources/js/modal.js', 'public/modal.js')

mix.sass('node_modules/bootstrap/scss/bootstrap.scss',
'public/bootstrap/bootstrap.css');
