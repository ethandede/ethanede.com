const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const { pathToFileURL } = require('url');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const { exec } = require('child_process');
const { promisify } = require('util');
const execAsync = promisify(exec);

// Compile Sass and minify CSS
function styles() {
  return gulp.src('assets/scss/main.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      api: 'modern-compiler', // Use modern Sass API to avoid deprecation warnings
      silenceDeprecations: ['mixed-decls', 'legacy-js-api'], // Temporarily silence while we fix
      verbose: true
    }).on('error', sass.logError))
    .pipe(cleanCSS({
      sourceMap: true,
      compatibility: '*',
      level: {
        1: {
          removeEmpty: true,
          removeWhitespace: true,
          removeQuotes: true,
          removeUrlQuotes: true,
          normalizeUrls: true
        },
        2: {
          mergeMedia: true,
          removeDuplicateRules: true,
          removeUnusedAtRules: false,
          mergeNonAdjacentRules: true,
          reduceNonAdjacentRules: true,
          removeDuplicateFontRules: true,
          removeDuplicateMediaBlocks: true
        }
      }
    }))
    .pipe(sourcemaps.write('./', {
      includeContent: false,
      sourceRoot: '../scss',
      addComment: process.env.NODE_ENV !== 'production'
    }))
    .pipe(gulp.dest('assets/css'));
}

// Process Tailwind CSS
async function tailwind() {
  try {
    await execAsync('npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/tailwind-output.css --minify');
    console.log('Tailwind CSS processed successfully');
  } catch (error) {
    console.error('Error processing Tailwind CSS:', error);
  }
}

// Watch for changes in Sass files
function watchFiles() {
  gulp.watch('assets/scss/**/*.scss', styles);
  gulp.watch('assets/css/tailwind.css', tailwind);
}

exports.styles = styles;
exports.tailwind = tailwind;
exports.watch = watchFiles;
exports.default = gulp.series(styles, tailwind, watchFiles);
