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
var browserSync = require('browser-sync').create();

// 压缩JavaScript文件
gulp.task('minify', function () {
    return gulp.src([
        './assets/Config/HyperMD.js',
        './assets/Config/Patch.js'])
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(uglify().on('error', function(e){
            console.log(e);
        })) //压缩
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(rename({suffix: '.min'})) //重命名
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/Config/'));
});

// 复制资源源码文件
gulp.task('copy-assets', function () {
    // 复制 require 文件
    gulp.src('./node_modules/requirejs/require.js').pipe(gulp.dest('./assets/Requirejs/'));

    // 复制 hypermd 文件夹
    gulp.src('./node_modules/hypermd/**/*').pipe(gulp.dest('./assets/Hypermd/'));

    // 复制 codemirror 文件夹
    gulp.src('./node_modules/codemirror/**/*').pipe(gulp.dest('./assets/CodeMirror/'));

    // 复制 mathjax 文件夹
    gulp.src('./node_modules/mathjax/**/*').pipe(gulp.dest('./assets/Mathjax/'));

    // 复制 katex 文件夹
    gulp.src('./node_modules/katex/**/*').pipe(gulp.dest('./assets/KaTeX/'));

    // 复制 marked 文件夹
    gulp.src('./node_modules/marked/**/*').pipe(gulp.dest('./assets/Marked/'));

    // 复制 turndown 文件夹
    gulp.src('./node_modules/turndown/**/*').pipe(gulp.dest('./assets/Turndown/'));

    // 复制 emojione 文件夹
    gulp.src('./node_modules/emojione/**/*').pipe(gulp.dest('./assets/EmojiOne/'));

    // 复制 twemoji 文件夹
    gulp.src('./node_modules/twemoji/**/*').pipe(gulp.dest('./assets/Twemoji/'));

    // 复制 mermaid 文件夹
    gulp.src('./node_modules/mermaid/**/*').pipe(gulp.dest('./assets/Mermaid/'));

    // 复制 turndown-plugin-gfm 文件夹
    gulp.src('./node_modules/turndown-plugin-gfm/**/*').pipe(gulp.dest('./assets/Turndown-Plugin-GFM/'));
});

// 删除发布资源文件
gulp.task('clean-assets', function () {
    return del([
        './assets/Requirejs/',
        './assets/HyperMD/',
        './assets/CodeMirror/',
        './assets/KaTeX/',
        './assets/Marked/',
        './assets/MathJax/',
        './assets/Mermaid/',
        './assets/EmojiOne/',
        './assets/Twemoji/',
        './assets/Turndown/',
        './assets/Turndown-Plugin-GFM/'
    ]);
});

gulp.task('browser-sync', function () {
    browserSync.init([
        "./assets/Config/*.css",
        "./assets/Config/*.js",
        "./src/**/*.php"
    ], {
        "proxy": "http://wordpress.test/",
        "notify": false
    });
});

gulp.task('watch', ['browser-sync'], function () {
    gulp.watch(['assets/Config/HyperMD.js','assets/Config/Patch.js'], ['minify']);
});

// 默认任务
gulp.task('default', ['watch']);