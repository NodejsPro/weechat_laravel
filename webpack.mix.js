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
    'jquery_validation': 'vendor/bower_dl/jquery-validation/dist',
    'bootstrap': 'vendor/bower_dl/bootstrap',
    'fontawesome': 'vendor/bower_dl/font-awesome',
    'moment' :  'vendor/bower_dl/moment/',
    'moment_timezone' :  'vendor/bower_dl/moment-timezone',
    'fusioncharts' :  'vendor/bower_dl/fusioncharts/',
    'bootstrap_fileinput' :  'vendor/bower_dl/bootstrap-fileinput',
    'fileUpload' : 'public/js/bucketadmin/fileUpload',
    'sass' : 'resources/assets/sass',
};

// mix.copy(paths.bootstrap + '/fonts/', 'public/build/fonts');
// mix.copy(paths.fontawesome + '/fonts/**', 'public/build/fonts');
mix.copy('public/images', 'public/build/images');
mix.copy('public/img', 'public/build/img');
mix.copy(paths.jquery + '/jquery.min.js', 'public/build/js/');
mix.sass(paths.sass + '/app.scss', 'public/build/css/app.css');

mix.sass(paths.sass + '/conversation.scss', 'public/build/css/conversation.css');
mix.sass(paths.sass + '/conversation-efo.scss', 'public/build/css/conversation-efo.css');
mix.sass(paths.sass + '/conversation-content-left.scss', 'public/build/css/conversation-content-left.css');

mix.styles([
    paths.bootstrap + '/dist/css/bootstrap-grid.css',
    paths.bootstrap + '/dist/css/bootstrap-reboot.css',
    paths.bootstrap + '/dist/css/bootstrap.css',
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
mix.combine([
    paths.jquery + '/jquery.min.js',
    paths.bootstrap + '/dist/js/bootstrap.bundle.js',
    paths.bootstrap + '/dist/js/bootstrap.js',
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
], 'public/build/js/app.js');
mix.combine([
    paths.moment + '/moment.js'
], 'public/build/js/moment.js');
mix.combine([
    paths.moment + '/moment.js',
    paths.moment_timezone + '/moment-timezone.js',
    paths.moment_timezone + '/moment-timezone-utils.js',
    'public/js/moment-timezone-with-data-2012-2022.min.js',
], 'public/build/js/timezone.js');
mix.styles([
    paths.bootstrap_fileinput + '/css/fileinput.min.css',
    paths.bootstrap_fileinput + '/css/fileinput-rtl.min.css',
], 'public/build/css/template_upload.css','./');
mix.combine([
    paths.bootstrap_fileinput + '/js/fileinput.min.js',
], 'public/build/js/template_upload.js');
mix.combine([
    paths.bootstrap_fileinput + '/js/locales/ja.js',
], 'public/build/js/template_upload_language_ja.js');
mix.combine([
    paths.bootstrap_fileinput + '/js/locales/vi.js',
], 'public/build/js/template_upload_language_vn.js');
mix.combine([
    'public/js/bucketadmin/icheck-init.js',
    'public/js/bucketadmin/iCheck/jquery.icheck.js'
], 'public/build/js/iCheck.js');
mix.combine([
    'public/js/jquery.dataTables.min.js',
    'public/js/dataTables.bootstrap.min.js'
], 'public/build/js/jquery.dataTables.js');

mix.combine([
    'public/js/socket_io/socket.io.js',
], 'public/build/js/socket.io.js');

mix.styles([
    // 'public/css/slick.css',
    // 'public/css/slick-theme.css',
    'public/build/css/conversation.css',
    'public/build/css/conversation-content-left.css',
], 'public/build/css/conversation.css','./');

/**
 * jquery step
 * */
mix.styles([
    'public/css/jquery.steps.css',
], 'public/build/css/jquery.steps.css','./');

/**
 * jquery validation
 * */
mix.styles([
    paths.jquery_validation + '/jquery.validate.js',
    paths.jquery_validation + '/additional-methods.js',
    'public/customize.js',
], 'public/build/js/jquery.validation.js');

mix.combine([
    'public/js/jquery.steps.js',
], 'public/build/js/jquery.steps.js');

mix.version([
    'public/build/css/app.css',
    'public/build/css/iCheck.css',
    'public/build/css/template_upload.css',
    'public/build/js/app.js',
    'public/build/js/iCheck.js',
    'public/build/js/moment.js',
    'public/build/js/template_upload.js',
    'public/build/css/jquery.steps.css',
    'public/build/js/jquery.steps.js',
    'public/build/js/jquery.validation.js',
    'public/build/js/socket.io.js',
    'public/build/css/conversation.css',
]);
