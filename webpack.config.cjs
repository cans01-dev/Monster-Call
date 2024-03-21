const path = require('path'); 

module.exports = {
  mode: 'development',
  entry: './resources/js/app.js',
  output: {
    path: path.resolve(__dirname, 'public/js/'), // 出力されるディレクトリの指定
    filename: 'index.js' // 出力されるファイル名の指定
  }
}