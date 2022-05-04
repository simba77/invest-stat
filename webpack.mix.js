const mix = require('laravel-mix');
require('laravel-mix-eslint');

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
  .vue({version: 3})
  .eslint()
  .sass('resources/sass/app.scss', 'public/assets').version();

