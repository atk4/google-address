/*global __dirname, require, module*/

const webpack = require('webpack');
const UglifyJsPlugin = webpack.optimize.UglifyJsPlugin;
const path = require('path');
const env  = require('yargs').argv.env; // use --env with webpack 2

let libraryName = 'atk';

let plugins = [
  new webpack.DefinePlugin({
    _ATKVERSION_ : JSON.stringify(require("./package.json").version)
  })
], outputFile;

if (env === 'build') {
  plugins.push(new UglifyJsPlugin({ minimize: true }));
  outputFile = libraryName + '-google-address.min.js';
} else {
  outputFile = libraryName + '-google-address.js';
}

const config = {
  entry: __dirname + '/src/atk-google-address.js',
  devtool: 'source-map',
  output: {
    path: __dirname + '/../public',
    filename: outputFile,
    library: libraryName,
    libraryTarget: 'umd',
    umdNamedDefine: true,
  },
  module: {
    rules: [
      {
        test: /(\.jsx|\.js)$/,
        loader: 'babel-loader',
        exclude: /(node_modules|bower_components)/
      }//,
      // {
      //   test: /(\.jsx|\.js)$/,
      //   loader: "eslint-loader",
      //   exclude: /node_modules/
      // }
    ]
  },
  externals: {jquery: 'jQuery'},
  resolve: {
    modules: [path.resolve('./src'), path.join(__dirname, 'node_modules')],
    extensions: ['.json', '.js'],
  },
  plugins: plugins
};

module.exports = config;
