/* jshint node: true */
'use strict';

const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
const rollup = require('rollup').rollup;
const rollupConfig = require('./rollup.config');
const plumber = require('gulp-plumber');

const project = {
	base: __dirname + '/../../Public',
	css: __dirname + '/../../Public/Css',
	js: __dirname + '/../../Public/JavaScript/Powermail',
	images: __dirname + '/../../Public/Images'
};

// SCSS zu css
function css() {
	const config = {};
	config.outputStyle = 'compressed';

	return src(__dirname + '/../Sass/*.scss')
		.pipe(plumber())
		.pipe(sass(config))
		.pipe(dest(project.css));
};

function js(done) {
  rollup(rollupConfig).then(bundle => {
    rollupConfig.output.plugins = rollupConfig
    bundle.write(rollupConfig.output).then(() => done());
  });
};

// "npm run build"
const build = series(js, css);

// "npm run watch"
const def = parallel(
  function watchSCSS() { return watch(__dirname + '/../Sass/**/*.scss', series(css)) },
  function watchJS() { return watch(__dirname + '/JavaScript/**/*.js', series(js)) }
);

module.exports = {
  default: def,
  build,
  css,
  js
};
