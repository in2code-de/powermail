/* jshint node: true */
'use strict';

const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
const rollup = require('rollup').rollup;
const rollupConfig = require('./rollup.config');
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const rename = require('gulp-rename');

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

function jsForm(done) {
  rollup(rollupConfig).then(bundle => {
    rollupConfig.output.plugins = rollupConfig
    bundle.write(rollupConfig.output).then(() => done());
  });
};

function jsMarketing() {
  return src([__dirname + '/JavaScript/Marketing.js'])
    .pipe(plumber())
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(dest(project.js));
};

function jsBackend() {
  return src([__dirname + '/JavaScript/Backend.js'])
    .pipe(plumber())
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(dest(project.js));
};

// "npm run build"
const build = series(jsForm, jsMarketing, jsBackend, css);

// "npm run watch"
const def = parallel(
  function watchSCSS() { return watch(__dirname + '/../Sass/**/*.scss', series(css)) },
  function watchJS() { return watch(__dirname + '/JavaScript/*.js', series(jsForm, jsMarketing)) }
);

module.exports = {
  default: def,
  build,
  css,
  jsForm,
  jsMarketing,
  jsBackend,
};
