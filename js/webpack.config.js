const webpack = require('webpack');
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const packageVersion = require('./package.json').version;

module.exports = (env) => {
  // determine which mode
  const isProduction = env.production;
  const srcDir = path.resolve(__dirname, './src');
  const publicDir = path.resolve(__dirname, '../assets');
  const libraryName = 'atk.google.maps';
  const filename = 'atk-google-maps';

  const prodPerformance = {
    hints: false,
    maxEntrypointSize: 640000,
    maxAssetSize: 640000
  };

  return {
    entry: { [filename]: srcDir + '/atk-google-api.js' },
    mode: isProduction ? 'production' : 'development',
    devtool: isProduction ? false : 'source-map',
    performance: isProduction ? prodPerformance : {},
    output: {
      path: publicDir,
      filename: isProduction ? '[name].min.js' : '[name].js',
      library: libraryName,
      libraryTarget: 'umd',
      libraryExport: 'default',
      umdNamedDefine: true
    },
    optimization: {
      splitChunks: {
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors'
          }
        }
      },
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            output: {
              comments: false
            }
          },
          extractComments: false
        })
      ]
    },
    module: {
      rules: [
        {
          test: /(\.jsx|\.js)$/,
          loader: 'babel-loader',
          exclude: /(node_modules|bower_components)/
        },
        // this will apply to both plain `.css` files
        // AND `<style>` blocks in `.vue` files
        {
          test: /\.css$/,
          use: [
            'style-loader',
            'css-loader'
          ]
        }
      ]
    },
    externals: { atk: 'atk', jquery: 'jQuery' },
    resolve: {
      modules: [
        path.resolve(__dirname, 'src/'),
        'node_modules'
      ],
      extensions: [
        '.json',
        '.js'
      ]
    },
    plugins: [
      new webpack.DefinePlugin({
        _ATK_GOOGLE_VERSION_: JSON.stringify(packageVersion)
      })
    ]
  };
};
