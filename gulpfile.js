// 声明变量
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var watch = require('gulp-watch');
var cssnano = require('gulp-cssnano');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var merge2 = require('merge2');
var imagemin = require('gulp-imagemin');
var ignore = require('gulp-ignore');
var rimraf = require('gulp-rimraf');
var clone = require('gulp-clone');
var merge = require('gulp-merge');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');
var cleanCSS = require('gulp-clean-css');
var gulpSequence = require('gulp-sequence');
var replace = require('gulp-replace');
var autoprefixer = require('gulp-autoprefixer');
var rev = require('gulp-rev');

// 引入配置文件
var cfg = require('./gulpconfig.json');
var paths = cfg.paths;

// 压缩JavaScript文件
gulp.task('minify', function () {
    gulp.src(paths.assets + './src/Config/**/*')
        .pipe(uglify())
        .pipe(gulp.dest(paths.assets + './Config/'))
});

// 复制资源源码文件
gulp.task('copy-assets', function () {
    // 复制 require 文件
    gulp.src(paths.node + './requirejs/require.js').pipe(gulp.dest(paths.assets + './Require/'));

    // 复制 hypermd 文件夹
    gulp.src(paths.node + './hypermd/**/*').pipe(gulp.dest(paths.assets + './HyperMD/'));

    // 复制 codemirror 文件夹
    gulp.src(paths.node + './codemirror/**/*').pipe(gulp.dest(paths.assets + './CodeMirror/'));

    // 复制 mathjax 文件夹
    gulp.src(paths.node + './mathjax/**/*').pipe(gulp.dest(paths.assets + './MathJax/'));

    // 复制 katex 文件夹
    gulp.src(paths.node + './katex/**/*').pipe(gulp.dest(paths.assets + './KaTeX/'));

    // 复制 marked 文件夹
    gulp.src(paths.node + './marked/**/*').pipe(gulp.dest(paths.assets + './Marked/'));

    // 复制 turndown 文件夹
    gulp.src(paths.node + './turndown/**/*').pipe(gulp.dest(paths.assets + './Turndown/'));

    // 复制 turndown-plugin-gfm 文件夹
    gulp.src(paths.node + './turndown-plugin-gfm/**/*').pipe(gulp.dest(paths.assets + './Turndown-Plugin-GFM/'));
});

// 删除发布资源文件
gulp.task('clean-assets', function () {
    return del([
        paths.assets + './CodeMirror/',
        paths.assets + './Config/',
        paths.assets + './HyperMD/',
        paths.assets + './KaTeX/',
        paths.assets + './Marked/',
        paths.assets + './MathJax/',
        paths.assets + './Require/',
        paths.assets + './Turndown/',
        paths.assets + './Turndown-Plugin-GFM/'
    ]);
});

// 监听文件
gulp.task('watch', function () {
    console.log("监听文件");
    gulp.watch([
        paths.assets + './src/Config/HyperMD.js',
        paths.assets + './src/Config/Patch.js'
    ], ['minify']);
});