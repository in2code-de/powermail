/* jshint node: true */
'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var debug = getArg('--debug');
var rename = require('gulp-rename');

// SCSS zu css
gulp.task('css', function() {
	var config = {};
	if (debug) {
		config.sourceMap = 'inline';
		config.sourceMapEmbed = true;
	} else {
		config.outputStyle = 'compressed';
	}
	return gulp.src('Sass/*.scss')
			.pipe(plumber())
			.pipe(sass(config))
			.pipe(gulp.dest('../Public/Css'));
});

gulp.task('js', function() {
	gulp.src('JavaScripts/**/*.js')
			.pipe(plumber())
			.pipe(uglify())
			.pipe(rename({
				suffix: '.min'
			}))
			.pipe(gulp.dest('../Public/JavaScripts'));
});

/*********************************
 *
 *         Watch Tasks
 *
 *********************************/

gulp.task('watch', function() {
	gulp.watch('Sass/**/*.scss', ['css']);
	gulp.watch('JavaScripts/**/*.js', ['js']);
});

gulp.task('default', ['css', 'js', 'watch']);

/**
 * Get arguments from commandline
 */
function getArg(key) {
	var index = process.argv.indexOf(key);
	var next = process.argv[index + 1];
	return (0 > index) ? null : (!next || '-' === next[0]) ? true : next;
}
