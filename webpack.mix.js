const mix = require('laravel-mix')
const path = require('path')

const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const CompressionPlugin = require('compression-webpack-plugin')

/**
 * Disable OS Notifications
 */

mix.disableNotifications()

/**
 * Configuration regarding modules
 */

mix.webpackConfig({
  output: {
    filename: '[name].js',
    chunkFilename: 'js/chunks/[chunkhash].js'
  },
  module: {
    rules: [
      {
        test: /\.pug$/,
        oneOf: [
          {
            resourceQuery: /^\?vue/,
            use: ['pug-plain-loader']
          },
          {
            use: ['raw-loader', 'pug-plain-loader']
          }
        ]
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        'mix-manifest.json',
        'css/*',
        'fonts/*',
        'js/*'
      ]
    }),
    new CompressionPlugin({
      exclude: mix.inProduction() ? '' : /\.(js|css|html)$/,
      filename: '[path].gz[query]',
      algorithm: 'gzip',
      test: /\.(js|css|html)$/
    }),
    new CompressionPlugin({
      exclude: mix.inProduction() ? '' : /\.(js|css|html|svg)$/,
      filename: '[path].br[query]',
      algorithm: 'brotliCompress',
      test: /\.(js|css|html|svg)$/,
      compressionOptions: { level: 11 }
    })
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js/'),
      '#': path.resolve(__dirname, 'resources/sass/')
    }
  }
})

/**
 * Mix Options
 */

mix.options({
  terser: {
    extractComments: false
  }
})

/**
 * Mix Asset Management
 */

mix
  .js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')
  .version()

if (!mix.inProduction()) {
  mix.sourceMaps()
}
