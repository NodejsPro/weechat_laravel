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
    'bootstrap_select' :  'vendor/bower_dl/bootstrap-select',
    'bootstrap_datetimepicker' :  'vendor/bower_dl/eonasdan-bootstrap-datetimepicker/build',
    'moment' :  'vendor/bower_dl/moment/',
    'moment_timezone' :  'vendor/bower_dl/moment-timezone',
    'fusioncharts' :  'vendor/bower_dl/fusioncharts/',
    'bootstrap_fileinput' :  'vendor/bower_dl/bootstrap-fileinput',
    'fileUpload' : 'public/js/bucketadmin/fileUpload'
};


mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');
mix.copy(paths.bootstrap + '/fonts/', 'public/build/fonts');
mix.copy(paths.bootstrap + '/dist/css/bootstrap.min.css.map', 'public/build/css/bootstrap.min.css.map');
mix.copy(paths.bootstrap_select + '/dist/js/bootstrap-select.js.map', 'public/build/js/bootstrap-select.js.map');
mix.copy(paths.fontawesome + '/fonts/**', 'public/build/fonts');
mix.copy('public/images', 'public/build/images');
mix.copy('public/images/fancybox', 'public/build/css');
mix.copy('public/img', 'public/build/img');
mix.copy(paths.jquery + '/jquery.min.js', 'public/js/');
mix.styles([
    paths.bootstrap + '/dist/css/bootstrap.min.css',
    paths.fontawesome + '/css/font-awesome.min.css',
    'public/css/select2/select2.min.css',
    'public/css/bucketadmin/jquery-ui.css',
    'public/css/jquery.tagsinput.css',
    // 'public/css/bucketadmin/bootstrap-datepicke/datepicker.css',
    'public/css/bucketadmin/bootstrap-reset.css',
    'public/css/bucketadmin/style.css',
    'public/css/bucketadmin/style-responsive.css',
    'public/css/bucketadmin/bootstrap-switch/bootstrap-switch.css',
    'resources/assets/build/app.css',
    'public/css/jquery.dataTables.min.css',
    paths.bootstrap_select + '/dist/css/bootstrap-select.css',
    'public/css/html5tooltips/html5tooltips.css',
    'public/css/html5tooltips/html5tooltips.animation.css',
], 'public/css/app.css','./');
mix.styles([
    'public/css/bucketadmin/iCheck/skins/minimal/blue.css',
    'public/css/bucketadmin/iCheck/skins/minimal/minimal.css',
], 'public/css/iCheck.css','./');
mix.styles([
    paths.bootstrap_datetimepicker + '/css/bootstrap-datetimepicker.min.css'
], 'public/css/datetimepicker.css','./');


mix.scripts([
    paths.jquery + '/jquery.min.js',
    paths.bootstrap + '/dist/js/bootstrap.min.js',
    paths.bootstrap_select + '/dist/js/bootstrap-select.js',
    'public/js/bucketadmin/jquery-ui.js',
    'public/js/bucketadmin/jquery.dcjqaccordion.js',
    'public/js/bucketadmin/jquery.scrollTo.min.js',
    'public/js/bucketadmin/bootstrap-switch/bootstrap-switch.js',
    'public/js/bucketadmin/jquery.slimscroll.min.js',
    'public/js/bucketadmin/jquery.nicescroll.js',
    // paths.moment + '/moment.min.js',
    'public/js/bucketadmin/scripts.js',
    'public/js/select2/select2.full.min.js',
    'public/js/jquery.dataTables.min.js',
    'public/js/dataTables.bootstrap.min.js',
    'public/js/popover.min.js',
    'public/js/html5tooltips/html5tooltips.js'
], 'public/js/app.js','./');
mix.scripts([
    paths.moment + '/moment.js',
    paths.bootstrap_datetimepicker + '/js/bootstrap-datetimepicker.min.js',
], 'public/js/datetimepicker.js','./');
mix.scripts([
    paths.bootstrap_datetimepicker + '/js/bootstrap-datetimepicker.min.js',
], 'public/js/bootstrap.datetimepicker.js','./');
mix.scripts([
    paths.moment + '/moment.js'
], 'public/js/moment.js','./');
mix.scripts([
    paths.moment + '/moment.js',
    paths.moment_timezone + '/moment-timezone.js',
    paths.moment_timezone + '/moment-timezone-utils.js',
    'public/js/moment-timezone-with-data-2012-2022.min.js',
], 'public/js/timezone.js','./');

mix.scripts([
    'public/js/bucketadmin/iCheck/jquery.icheck.js',
    'public/js/bucketadmin/icheck-init.js'
], 'public/js/iCheck.js','./');
mix.scripts([
    'public/js/jquery.dataTables.min.js',
    'public/js/dataTables.bootstrap.min.js'
], 'public/js/jquery.dataTables.js','./');
mix.setPublicPath('public');
mix.version([
    'css/app.css',
    'css/iCheck.css',
    'css/datetimepicker.css',
    'js/app.js',
    'js/datetimepicker.js',
    'js/bootstrap.datetimepicker.js',
    'js/iCheck.js',
]);
