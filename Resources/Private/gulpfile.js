/* jshint node: true */
'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');

var project = {
	base: __dirname + '/../Public',
	css: __dirname + '/../Public/Css',
	js: __dirname + '/../Public/JavaScripts'
};

gulp.task('css', function() {
	var config = {};
	config.outputStyle = 'compressed';

	return gulp.src(__dirname + '/Sass/*.scss')
		.pipe(plumber())
		.pipe(sass(config))
		.pipe(gulp.dest(project.css));
});

gulp.task('js', function() {
	return gulp.src([__dirname + '/JavaScripts/**/*.js'])
		.pipe(plumber())
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest(project.js));
});

/*********************************
 *         Watch Tasks
 *********************************/
gulp.task('default', function() {
	gulp.watch(__dirname + '/Sass/*.scss', gulp.series('css'));
	gulp.watch(__dirname + '/JavaScripts/**/*.js', gulp.series('js'));
});
