/**
 * Register custom post type for Hildon website: Frequently asked questions (for the general website) [faqs]
 *
 * This post type holds the FAQs for general website use, they appear on the help page.
 *
 * @category 	Compilation
 * @package  	hildon
 * @author  	Andi North <andi@mangopear.co.uk>
 * @copyright  	2015 Mangopear creative
 * @license   	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @version  	1.10.0
 * @since   	1.0.0
 */


/**
 * Contents
 *
 * [1]	Load plugins
 * [2]	Styles
 * [3]	Plugins
 * [4]	Scripts
 * [5]	Images
 * [6]	Watch
 * [7]	Task
 */


	/**
 	 * [1]	Load plugins
 	 */
 	
 	const sass = require('gulp-ruby-sass');
	 
	var gulp = require('gulp'),
	    notify = require("gulp-notify"),
	 	plugins = require('gulp-load-plugins')({ camelize: true }),
		lr = require('tiny-lr'),
		server = lr();


	/**
	 * [2]	Styles
	 */
	
	gulp.task('styles', function() {
		return sass('resources/css/sass/*.scss', {
			style: 'compressed'
		})
			.on('error', sass.logError)
			.pipe(plugins.autoprefixer('last 2 versions', 'ie 10', 'ios 6', 'android 4'))
			.pipe(gulp.dest('resources/css/compiled'))
			.pipe(plugins.notify({ title: 'MangUI', message: 'Your SASS and CSS has been processed.' }));
	});


	/**
	 * [3]	Plugins
	 */
	
	gulp.task('plugins', function() {
		return gulp.src(['resources/js/source/plugins.js', 'resources/js/vendor/*.js'])
			.pipe(plugins.concat('plugins.js'))
			.pipe(gulp.dest('resources/js/compiled'))
			.pipe(plugins.rename({ suffix: '.min' }))
			.pipe(plugins.uglify())
			.pipe(gulp.dest('resources/js/compiled'))
			.pipe(plugins.notify({ title: 'MangUI', message: 'Your JavaScript plugins have been processed.' }));
	});


	/**
	 * [4]	Scripts
	 */
	
	gulp.task('scripts', function() {
		return gulp.src(['resources/js/source/*.js', '!resources/js/source/plugins.js'])
			.pipe(plugins.jshint('.jshintrc'))
			.pipe(plugins.jshint.reporter('default'))
			.pipe(plugins.concat('global.js'))
			.pipe(gulp.dest('resources/js/compiled'))
			.pipe(plugins.rename({ suffix: '.min' }))
			.pipe(plugins.uglify())
			.pipe(gulp.dest('resources/js/compiled'))
			.pipe(plugins.notify({ title: 'MangUI', message: 'Your JavaScript scripts have been processed.' }));
		});


	/**
	 * [5]	Images
	 */
	
	gulp.task('images', function() {
		return gulp.src('resources/images/**/*', '!resources/images/**/*.psd')
			.pipe(plugins.cache(plugins.imagemin({ optimizationLevel: 7, progressive: true, interlaced: true })))
			.pipe(gulp.dest('resources/images'))
			.pipe(plugins.notify({ title: 'MangUI', message: 'Your images have been processed.' }));
	});


	/**
	 * [6]	Watch
	 *
	 * 		[a]	Watch on port 35729
	 * 		[b]	Watch .scss files
	 * 		[c]	Watch .js files
	 * 		[d]	Watch image files
	 */
	
	gulp.task('watch', function() {
		gulp.watch('resources/css/sass/**/*.scss', ['styles']); // [b]
		gulp.watch('resources/js/vendor/*.js', ['plugins']); // [c]
		gulp.watch('resources/js/source/*.js', ['scripts']); // [c]
		gulp.watch('resources/images/**/*', ['images']); // [d]
	});


	/**
	 * [7]	Task
	 */
	
	gulp.task('default', ['styles', 'plugins', 'scripts', 'watch']);