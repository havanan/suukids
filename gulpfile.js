const elixir = require("laravel-elixir");

elixir(mix => {
    // mix script
    // mix.scripts([
    //        './resources/js/example.js'
    // ], './public/assets/js/scripts.js');

    // mix sass
    // mix.sass([
    //     './resources/css/example.css',
    // ], './public/assets/css/styles.css');

    mix.sass(
        [
            "./public/theme/admin-lte/plugins/fontawesome-free/css/all.min.css",
            "./public/theme/admin-lte/dist/css/ionicons.min.css",
            "./public/theme/admin-lte/plugins/jquery-ui/jquery-ui.min.css",
            "./public/theme/admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css",
            "./public/theme/admin-lte/plugins/select2/css/select2.min.css",
            "./public/theme/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css",
            "./public/theme/admin-lte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css",
            "./public/theme/admin-lte/dist/css/adminlte.min.css",
            "./public/theme/admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css",
            "./public/theme/admin-lte/plugins/fontawesome-free/css/all.min.css",
            "./public/theme/admin-lte/plugins/color-picker/spectrum.min.css",
            "./public/theme/admin-lte/plugins/toastr/toastr.min.css",
            "./public/css/common.css",
            "./resources/sass/custom.scss"
        ],
        "./public/assets/release/css/layout.css"
    );

    mix.sass([
        './resources/sass/order.scss',
    ], './public/assets/release/css/order.css');

    mix.scripts(
        [
            "./public/theme/admin-lte/plugins/jquery/jquery.min.js",
            "./public/theme/admin-lte/plugins/jquery-ui/jquery-ui.min.js",
            "./public/theme/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js",
            "./public/theme/admin-lte/plugins/moment/moment.min.js",
            "./public/theme/admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js",
            "./public/theme/admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js",
            "./public/theme/admin-lte/plugins/sweetalert2/sweetalert2.all.js",
            "./public/theme/admin-lte/dist/js/adminlte.js",
            "./public/theme/admin-lte/plugins/color-picker/spectrum.min.js",
            // "./public/js/common.js",
            "./public/theme/admin-lte/plugins/toastr/toastr.min.js",
            "./public/theme/admin-lte/plugins/select2/js/select2.full.min.js",
            "./public/theme/admin-lte/plugins/toastr/toastr.min.js",
            "./public/js/bootstrap-notify.min.js",
        ],
        "./public/assets/release/js/layout.js"
    );
});

// Watch
gulp.task("watch", function() {
    gulp.watch("./resources/sass/*.scss", gulp.series('clear','build'));
    gulp.watch("./resources/js/**", gulp.series('clear','build'));
});
// gulp.task('default', gulp.series('server', 'watch'));

