import * as del from 'del';
import * as gulp from 'gulp';
import * as loadPlugins from 'gulp-load-plugins';
import * as log from 'fancy-log';
import * as browserify from 'browserify';
import * as tsify from 'tsify';
import * as source from 'vinyl-source-stream';
import * as buffer from 'vinyl-buffer';

import {DIST_DIR, APP_DIR, TEST_DIST, OUTPUT_FILE, INPUT_FILE, SOURCE_DIR, APP_NAME} from './config';
import {join} from 'path';
import {PassThrough} from 'stream';

let plugins: any = loadPlugins();
let isRelease: boolean = process.argv.indexOf('--release') > -1;

function nope(): PassThrough {
  'use strict';
  return new PassThrough({ objectMode: true });
}

// create the d.ts bundle
function createSource(done: Function): void {
  let tsProject: any = plugins.typescript.createProject('tsconfig.json');
  gulp.src([join(APP_DIR, '**/*.ts'), '!' + join(APP_DIR, '**/*.spec.ts')])
    .pipe(tsProject())
    .pipe(plugins.replace('<<VERSION>>', require('./package.json').version))
    .pipe(gulp.dest(SOURCE_DIR))
    .on('end', done);
}

// create the bundle with dependencies
function createBundle(done: Function): void {
  browserify({
    basedir: '.',
    standalone: APP_NAME,
    debug: !isRelease,
    entries: [join(APP_DIR, INPUT_FILE)],
    cache: {},
    packageCache: {}
  }).plugin(tsify)
    .add('node_modules/promise-polyfill/dist/polyfill.js')
    .bundle()
    .pipe(source(OUTPUT_FILE))
    .pipe(buffer())
    .pipe(plugins.replace('<<VERSION>>', require('./package.json').version))
    .pipe(isRelease ? nope() : plugins.sourcemaps.init({loadMaps: true}))
    .pipe(plugins.sourcemaps.write())
    .pipe(gulp.dest(DIST_DIR))
    .pipe(isRelease ? plugins.uglify() : nope())
    .pipe(plugins.rename({ extname: '.min.js' }))
    .pipe(gulp.dest(DIST_DIR))
    .on('end', done);
}

// delete everything used in our test cycle here
function clean(): any {
  'use strict';

  return del([DIST_DIR + '**/*', SOURCE_DIR + '**/*', '!' + TEST_DIST]).then((paths: Array<any>) => {
    log('Deleted \x1b[33m', paths && paths.join(', ') || '-', '\x1b[0m');
  });
}

gulp.task('build.sdk', createBundle);
gulp.task('build.source', createSource);
gulp.task('clean', clean);

gulp.task('build',
  gulp.series('clean', gulp.parallel('build.sdk', 'build.source'), done => done()));
