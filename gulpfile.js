// ***
// Gulp Styles Tasks
// ***
const glob = require('glob');
const gulp = require('gulp');
const gulpSass = require('gulp-sass')(require('sass'));
const wait = require('gulp-wait');
const sourcemaps = require('gulp-sourcemaps');
const dotenv = require('dotenv').config()

const requireNoCache = module => {
	delete require.cache[require.resolve(module)];
	return require(module);
};

function sassFunc(src) {
	return gulp.src(...src)
	.pipe(sourcemaps.init())
	.pipe(
		gulpSass({
			outputStyle: 'compressed',
			includePaths: glob.sync(`./wp-content/themes/**/css/`),
		}).on('error', gulpSass.logError)
	)
	.pipe(sourcemaps.write())
	.pipe(gulp.dest('./'));
}

gulp.task(`sass:all`, () => {
	const argsArr = [[`./wp-content/themes/**/*.{sass,scss}`], {base: './'}];
	return sassFunc(argsArr);
});

gulp.task('sass:all:watch', () => {
	gulp.watch([`./wp-content/themes/**/*.{sass,scss}`], gulp.task('sass:all'));
});

gulp.task('sass:prod', () => {
  return gulp.src(['./wp-content/themes/**/*.{sass,scss}'], {base: './'})
	.pipe(wait(100))
	.pipe(gulpSass({
	  outputStyle: 'compressed',
	  includePaths: glob.sync('./wp-content/themes/**/css/'),
	}).on('error', gulpSass.logError))
	.pipe(gulp.dest('./'));
});


// ***
// Gulp & Webpack JS Tasks
// ***
const webpack = require('webpack');
const path = require('path');
const through = require('through2');
const fs = require('fs');

gulp.task('clean-bundles', () => {
	return gulp.src(`./wp-content/themes/**/bundle*.js`).pipe(
		through.obj((chunk, enc, cb) => {
			fs.unlinkSync(chunk.path);
			cb(null, chunk);
		})
	);
});

const compileWebpack = (configName, name, cb) => {
	if (!fs.existsSync(`${configName}.js`)) {
		cb();
		return;
	}
	const config = requireNoCache(configName);
	if (!config) {
		cb();
		return;
	}
	config.mode = 'production';
	console.info(`\n[webpack][${name}]\n`);
	webpack(config, (err, stats) => {
		console.info(
			stats.toString({
				chunks: false,
				colors: true,
			})
		);
		cb();
	});
};

gulp.task('scripts:default', cb => {
	compileWebpack('./webpack.config', 'default', cb);
});

gulp.task('scripts:gutenberg', cb => {
	compileWebpack('./webpack.config.gutenberg', 'gutenberg', cb);
});

gulp.task('scripts', gulp.series('scripts:default', 'scripts:gutenberg'));

const compileOnWatch = compiler => {
	const watchIsCompiling = cb => {
		compiler.run((_error, stats) => {
			console.info(
				stats.toString({
					colors: true,
				})
			);
			console.info();
			cb();
		});
	};

	return watchIsCompiling;
};

// ***
// Gulp Watch & BrowserSync Tasks
// ***
const browserSync = require('browser-sync');

gulp.task('scripts:watch:browsers', () => {
	const config = requireNoCache('./webpack.config.js');
	const compiler = webpack(config);
	return gulp.watch([`./wp-content/themes/**/*.js`, `!./wp-content/themes/**/*.min.js`], compileOnWatch(compiler));
});

gulp.task('scripts:watch:gutenberg', () => {
	if (!fs.existsSync('webpack.config.gutenberg.js')) {
		return;
	}
	const config = requireNoCache('./webpack.config.gutenberg.js');
	if (!config) {
		return;
	}
	const compiler = webpack(config);
	return gulp.watch([`./wp-content/themes/**/*.js`, `!./wp-content/themes/**/*.min.js`], compileOnWatch(compiler));
});

gulp.task(
	'scripts:watch',
	gulp.parallel('scripts:watch:browsers', 'scripts:watch:gutenberg')
);

gulp.task('browsersync', () => {
	return browserSync({
		files: [
			{
				match: `wp-content/themes/**/*.*`,
			},
		],
		ignore: [`../wp-content/uploads/*`],
		watchEvents: ['change', 'add'],
		codeSync: true,
		proxy: 'http://weadapt-env/',
		snippetOptions: {
			ignorePaths: ['*/wp-admin/**'],
		},
	});
});

gulp.task(
	'watch',
	gulp.parallel('sass:all:watch',  'scripts:watch', 'browsersync')
);

gulp.task('buildassets', gulp.series('sass:all', 'clean-bundles', 'scripts'));
gulp.task('build', gulp.series('sass:prod', 'clean-bundles', 'scripts'));
gulp.task('default', gulp.series('buildassets', 'watch'));
