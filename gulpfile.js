// Load Gulp...of course
var gulp         = require( 'gulp' );

// CSS related plugins
var sass         = require( 'gulp-sass' );
var autoprefixer = require( 'gulp-autoprefixer' );
var minifycss    = require( 'gulp-uglifycss' );

// JS related plugins
var concat       = require( 'gulp-concat' );
var uglify       = require( 'gulp-uglify' );
var babelify     = require( 'babelify' );
var browserify   = require( 'browserify' );
var source       = require( 'vinyl-source-stream' );
var buffer       = require( 'vinyl-buffer' );
var stripDebug   = require( 'gulp-strip-debug' );

//Image related Plugins
var imagemin = require('gulp-imagemin');

// Utility plugins
var rename       = require( 'gulp-rename' );
var sourcemaps   = require( 'gulp-sourcemaps' );
var notify       = require( 'gulp-notify' );
var plumber      = require( 'gulp-plumber' );
var options      = require( 'gulp-options' );
var gulpif       = require( 'gulp-if' );

// Browers related plugins
var browserSync  = require( 'browser-sync' ).create();
var reload       = browserSync.reload;

// Project related variables
var projectURL   = 'http://opa.hm';

var styleSRC     = './src/scss/opa-style.scss';
var styleURL     = './assets/';
var mapURL       = './';

var styleAdminSRC     = './src/scss/opa-admin_style.scss';
var styleAdminURL     = './assets/';

var jsSRC        = './src/js/opa-admin_script.js';
var jsURL        = './assets/';

var styleWatch   = './src/scss/**/*.scss';
var jsWatch      = './src/js/**/*.js';
var phpWatch     = './**/*.php';
var imageWatch     = './src/images/*';

var imagesSRC	= './src/images/*';
var imagesURL	= 'assets/images/';

// Tasks

// optimize images
gulp.task('image', () =>
	gulp.src(imagesSRC)
	.pipe(imagemin())
	.pipe(gulp.dest(imagesURL))
);

gulp.task( 'browser-sync', function() {
	browserSync.init({
		proxy: projectURL,
		// Add SSl key and certificate for SSL enabled site (Https)
		// https: {
		// 	key: '/Users/opa/.valet/Certificates/test.dev.key',
		// 	cert: '/Users/opa/.valet/Certificates/test.dev.crt'
		// },
		injectChanges: true,
		open: false
	});
});

gulp.task( 'styles', function() {
	gulp.src( styleSRC )
		.pipe( sourcemaps.init() )
		.pipe( sass({
			errLogToConsole: true,
			outputStyle: 'compressed'
		}) )
		.on( 'error', console.error.bind( console ) )
		.pipe( autoprefixer({ browsers: [ 'last 2 versions', '> 5%', 'Firefox ESR' ] }) )
		.pipe( sourcemaps.write( mapURL ) )
		.pipe( gulp.dest( styleURL ) )
		.pipe( browserSync.stream() );

	//admin style
	gulp.src( styleAdminSRC )
	.pipe( sourcemaps.init() )
	.pipe( sass({
		errLogToConsole: true,
		outputStyle: 'compressed'
	}) )
	.on( 'error', console.error.bind( console ) )
	.pipe( autoprefixer({ browsers: [ 'last 2 versions', '> 5%', 'Firefox ESR' ] }) )
	.pipe( sourcemaps.write( mapURL ) )
	.pipe( gulp.dest( styleAdminURL ) )
	.pipe( browserSync.stream() );
});



gulp.task( 'js', function() {
	return browserify({
		entries: [jsSRC]
	})
	.transform( babelify, { presets: [ 'env' ] } )
	.bundle()
	.pipe( source( 'opa-admin_script.js' ) )
	.pipe( buffer() )
	.pipe( gulpif( options.has( 'production' ), stripDebug() ) )
	.pipe( sourcemaps.init({ loadMaps: true }) )
	.pipe( uglify() )
	.pipe( sourcemaps.write( '.' ) )
	.pipe( gulp.dest( jsURL ) )
	.pipe( browserSync.stream() );
 });


 gulp.task( 'default', ['styles', 'js', 'image'], function() {
	gulp.src( jsURL + 'opa-admin_script.min.js' )
		.pipe( notify({ message: 'Assets Compiled!' }) );
 });

 
 gulp.task( 'watch', ['default', 'browser-sync'], function() {
	gulp.watch( phpWatch, reload );
	gulp.watch( imageWatch, ['image',reload] );
	gulp.watch( styleWatch, [ 'styles', reload  ] );
	gulp.watch( jsWatch, [ 'js', reload ] );
	gulp.src( jsURL + 'opa-admin_script.min.js' )
		.pipe( notify({ message: 'Gulp is Watching, Happy Coding!' }) );
 });
