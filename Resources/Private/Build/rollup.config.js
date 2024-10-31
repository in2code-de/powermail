const babel = require('@rollup/plugin-babel').babel;
const resolve = require('@rollup/plugin-node-resolve').default;
const commonjs = require('@rollup/plugin-commonjs');
const { terser } = require('rollup-plugin-terser');

module.exports = {
  form: {
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
  },
  backend: {
    external: [
        /^@typo3\//,
        'jquery'
    ],
    input: './JavaScript/Backend.js',
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
      })
    ],
    output: {
      file: '../../Public/JavaScript/Powermail/Backend.min.js',
      format: 'esm'
    },
  }
};
