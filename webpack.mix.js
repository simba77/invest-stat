const mix = require('laravel-mix');
require('laravel-mix-eslint');
require('mix-tailwindcss');

mix.options({
  processCssUrls: false
});

mix.webpackConfig({
  resolve: {
    extensions: ['.js', '.vue', '.json', '.ts'],
    alias: {
      '@': __dirname + '/resources/js'
    },
  },
})

mix.ts('resources/js/app.ts', 'public/assets')
  .disableSuccessNotifications()
  .vue({version: 3})
  .eslint()
  .sass('resources/sass/app.scss', 'public/assets')
  .tailwind()
  .version();
