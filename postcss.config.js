let plugins = [
  require('postcss-import'),
  require('tailwindcss/nesting'), // this
  // require('postcss-nested'), // or use this
  require('tailwindcss'),
  require('autoprefixer'),
]

if (process.env.NODE_ENV === 'production') {
  plugins.push(require('cssnano'))
}

module.exports = {
  plugins: plugins
}