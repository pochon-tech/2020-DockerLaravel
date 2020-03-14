const mix = require('laravel-mix');

// mix.js('resources/js/app.js', 'public/js').sass('resources/sass/app.scss', 'public/css');
mix.browserSync({
    proxy: '0.0.0.0:80',
    open: false
  })
  .js('resources/js/app.js', 'public/js')
  .version()