let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

var paths = {
    'jquery': 'vendor/bower_dl/jquery/dist',
    'bootstrap': 'vendor/bower_dl/bootstrap',
    'fontawesome': 'vendor/bower_dl/font-awesome',
    'moment' :  'vendor/bower_dl/moment/',
    'moment_timezone' :  'vendor/bower_dl/moment-timezone',
    'fusioncharts' :  'vendor/bower_dl/fusioncharts/',
    'fileUpload' : 'public/js/bucketadmin/fileUpload'
};

mix.copy(paths.bootstrap + '/fonts/', 'public/build/fonts');
mix.copy(paths.fontawesome + '/fonts/**', 'public/build/fonts');
mix.copy('public/images', 'public/build/images');
mix.copy('public/img', 'public/build/img');
mix.copy(paths.jquery + '/jquery.min.js', 'public/build/js/');
mix.sass('resources/assets/sass/app.scss', 'public/build/css/app.css');
mix.styles([
    paths.bootstrap + '/dist/css/bootstrap.min.css',
    paths.fontawesome + '/css/font-awesome.min.css',
    'public/css/select2/select2.min.css',
    'public/css/bucketadmin/jquery-ui.css',
    'public/css/bucketadmin/bootstrap-reset.css',
    'public/css/bucketadmin/style.css',
    'public/css/bucketadmin/style-responsive.css',
    'public/build/css/app.css',
    'public/css/jquery.dataTables.min.css',
], 'public/build/css/app.css','./');
mix.styles([
    'public/css/bucketadmin/iCheck/skins/minimal/blue.css',
    'public/css/bucketadmin/iCheck/skins/minimal/minimal.css',
], 'public/build/css/iCheck.css','./');
mix.scripts([
    paths.jquery + '/jquery.min.js',
    paths.bootstrap + '/dist/js/bootstrap.min.js',
    'public/js/bucketadmin/jquery-ui.js',
    'public/js/bucketadmin/jquery.dcjqaccordion.js',
    'public/js/bucketadmin/jquery.scrollTo.min.js',
    'public/js/bucketadmin/jquery.slimscroll.min.js',
    //'public/js/bucketadmin/jquery.nicescroll.js',
    // paths.moment + '/moment.min.js',
    'public/js/bucketadmin/scripts.js',
    'public/js/select2/select2.full.min.js',
    'public/js/jquery.tagsinput.js',
    'public/js/jquery.dataTables.min.js',
    'public/js/dataTables.bootstrap.min.js',
], 'public/build/js/app.js','./');
mix.scripts([
    paths.moment + '/moment.js'
], 'public/build/js/moment.js','./');
mix.scripts([
    paths.moment + '/moment.js',
    paths.moment_timezone + '/moment-timezone.js',
    paths.moment_timezone + '/moment-timezone-utils.js',
    'public/js/moment-timezone-with-data-2012-2022.min.js',
], 'public/build/js/timezone.js','./');
mix.scripts([
    'public/js/bucketadmin/iCheck/jquery.icheck.js',
    'public/js/bucketadmin/icheck-init.js'
], 'public/build/js/iCheck.js','./');
mix.scripts([
    'public/js/jquery.dataTables.min.js',
    'public/js/dataTables.bootstrap.min.js'
], 'public/build/js/jquery.dataTables.js','./');
mix.version([
    'public/build/css/app.css',
    'public/build/css/iCheck.css',
    'public/build/js/app.js',
    'public/build/js/iCheck.js',
    'public/build/js/moment.js',
]);
