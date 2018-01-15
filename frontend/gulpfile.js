const gulp = require('gulp'),
    pkg = require('./package.json'),
    notify = require("gulp-notify"),
    sass = require('gulp-sass'),
    connect = require('gulp-connect'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    header = require('gulp-header'),
    minifyCSS = require('gulp-clean-css'),
    jsValidate = require('gulp-jsvalidate');

var config = {
    sassPath: './app/sass',
    htmlPath: './app/html',
    jsPath: './app/js',
    destPath: {
        html: './public/',
        css: './public/css',
        js: './public/js',
        font: './public/fonts',
    }
};

gulp.task('scripts', function() {
    gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        config.jsPath + '/**/*.js'
    ])
        .pipe(concat('app.js'))
        // .pipe(uglify())
        .pipe(jsValidate())
        .on("error", notify.onError(function (error) {
            return "Error: " + error.message;
        }))
        .pipe(header('/*! <%= pkg.name %> <%= pkg.version %> */\n', {pkg: pkg} ))
        .pipe(gulp.dest(config.destPath.js))
        .pipe(connect.reload())
});

gulp.task('connect', function() {
    connect.server({
        root: 'public',
        livereload: true,
        port: 8000
    })
});

gulp.task('css', function() {
    gulp
        .src([
            'node_modules/open-iconic/font/fonts/open-iconic.eot',
            'node_modules/open-iconic/font/fonts/open-iconic.otf',
            'node_modules/open-iconic/font/fonts/open-iconic.svg',
            'node_modules/open-iconic/font/fonts/open-iconic.ttf',
            'node_modules/open-iconic/font/fonts/open-iconic.woff',
        ])
        .pipe(gulp.dest(config.destPath.font));
    gulp
        .src([
            config.sassPath + '/**/*.scss',
            'node_modules/open-iconic/font/css/open-iconic-bootstrap.scss'
        ])
        .pipe(sass({
            outputStyle: 'compressed',
            loadPath: config.sassPath
        })
        .on("error", notify.onError(function (error) {
            return "Error: " + error.message;
        })))
        .pipe(concat('app.css'))
        .pipe(gulp.dest(config.destPath.css))
        .pipe(connect.reload())
});

gulp.task('html', function(){
    gulp.src(config.htmlPath + '/**/*.html')
    .pipe(gulp.dest(config.destPath.html))
    .pipe(connect.reload())
});

gulp.task('watch', function () {
    gulp.watch(config.sassPath + '/**/*.scss', ['css']);
    gulp.watch(config.htmlPath + '/**/*.html', ['html']);
    gulp.watch(config.jsPath + '/**/*.js', ['scripts']);
});

gulp.task('default', ['connect', 'watch']);