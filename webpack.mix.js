let mix = require('laravel-mix')

mix
  .setPublicPath('public')
  .js('resources/js/reviz.js', 'public')
  .sass('resources/sass/reviz.scss', 'public')
  .options({
    postCss: [
      require('postcss-import'),
      require('tailwindcss/nesting'),
      require('tailwindcss'),
      require('autoprefixer')
    ]
  })
  // .version()
  .copy('public', '../lara7/public/vendor/reviz')
