const babel = require('@rollup/plugin-babel').babel;
const resolve = require('@rollup/plugin-node-resolve').default;
const commonjs = require('@rollup/plugin-commonjs');
const { terser } = require('rollup-plugin-terser');

module.exports = {
  input: './JavaScript/Form.js',
  plugins: [
    resolve({
      browser: true
    }),
    commonjs({
      sourceMap: false
    }),
    babel({
      exclude: './node_modules/**',
      babelHelpers: 'bundled'
    }),
    terser()
  ],
  output: {
    file: '../../Public/JavaScript/Powermail/Form.min.js',
    format: 'iife'
  },
};
