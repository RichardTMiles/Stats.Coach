<?php

/*
 * This file selects the content wrappers for our different types of users
 * Currently this equates to:
 *  Athlete
 *  Coach
 */

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= SITE_TITLE ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?= SITE ?>Public/favicon.png" type="image/x-icon"/>

    <!-- PJAX Content Control -->
    <meta http-equiv="x-pjax-version" content="<?=$_SESSION['X_PJAX_Version']?>">

    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= $this->versionControl( "bower_components/bootstrap/dist/css/bootstrap.min.css" ) ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $this->versionControl( "dist/css/AdminLTE.min.css" ) ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="preload" href="<?= $this->versionControl( "dist/css/skins/_all-skins.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- DataTables.Bootstrap -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" ) ?>" as="style"
          onload="this.rel='stylesheet'">
    <!-- iCheck -->
    <link rel="preload" href="<?= $this->versionControl( 'plugins/iCheck/square/blue.css' ); ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Ionicons -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/Ionicons/css/ionicons.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Back color -->
    <link rel="preload" href="<?= $this->versionControl( "dist/css/skins/skin-green.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Multiple input dynamic form -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/select2/dist/css/select2.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Check Ratio Box -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/iCheck/flat/blue.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- I dont know but keep it -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/morris.js/morris.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- fun ajax refresh -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/pace/pace.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Jquery -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/jvectormap/jquery-jvectormap.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- datepicker -->
    <link rel="preload" href="<?= $this->versionControl( "bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" ) ?>" as="style"
          onload="this.rel='stylesheet'">

    <link rel="preload" href="<?= $this->versionControl( "bower_components/bootstrap-daterangepicker/daterangepicker.css" ) ?>" as="style"
          onload="this.rel='stylesheet'">
    <link rel="preload" href="<?= $this->versionControl( "plugins/timepicker/bootstrap-timepicker.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Wysihtml -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" ) ?>" as="style"
          onload="this.rel='stylesheet'">
    <!-- Font Awesome -->
    <link rel="preload" href="<?= $this->versionControl( "components/font-awesome/css/font-awesome.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">

    <script>
        /*! loadCSS. [c]2017 Filament Group, Inc. MIT License */
        !function (a) {
            "use strict";
            var b = function (b, c, d) {
                function e(a) {
                    return h.body ? a() : void setTimeout(function () {
                        e(a)
                    })
                }

                function f() {
                    i.addEventListener && i.removeEventListener("load", f), i.media = d || "all"
                }

                var g, h = a.document, i = h.createElement("link");
                if (c)g = c; else {
                    var j = (h.body || h.getElementsByTagName("head")[0]).childNodes;
                    g = j[j.length - 1]
                }
                var k = h.styleSheets;
                i.rel = "stylesheet", i.href = b, i.media = "only x", e(function () {
                    g.parentNode.insertBefore(i, c ? g : g.nextSibling)
                });
                var l = function (a) {
                    for (var b = i.href, c = k.length; c--;)if (k[c].href === b)return a();
                    setTimeout(function () {
                        l(a)
                    })
                };
                return i.addEventListener && i.addEventListener("load", f), i.onloadcssdefined = l, l(f), i
            };
            "undefined" != typeof exports ? exports.loadCSS = b : a.loadCSS = b
        }("undefined" != typeof global ? global : this);
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        !function (a) {
            if (a.loadCSS) {
                var b = loadCSS.relpreload = {};
                if (b.support = function () {
                        try {
                            return a.document.createElement("link").relList.supports("preload")
                        } catch (b) {
                            return !1
                        }
                    }, b.poly = function () {
                        for (var b = a.document.getElementsByTagName("link"), c = 0; c < b.length; c++) {
                            var d = b[c];
                            "preload" === d.rel && "style" === d.getAttribute("as") && (a.loadCSS(d.href, d, d.getAttribute("media")), d.rel = null)
                        }
                    }, !b.support()) {
                    b.poly();
                    var c = a.setInterval(b.poly, 300);
                    a.addEventListener && a.addEventListener("load", function () {
                        b.poly(), a.clearInterval(c)
                    }), a.attachEvent && a.attachEvent("onload", function () {
                        a.clearInterval(c)
                    })
                }
            }
        }(this);
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <!-- Debugbar TODO remove -->
    <!--?php echo $GLOBALS['debugbarRenderer']->renderHead() ?-->
</head>
<style>
    body {
        background-color: black;
    }
    .content-wrapper, .stats-wrap {
        /* This image will be displayed fullscreen
        /Public/StatsCoach/img/augusta-master.jpg
        http://site.rockbottomgolf.com/blog_images/Hole%2012%20-%20Imgur.jpg
        */
        opacity: .7;

        background: url('https://c1.staticflickr.com/9/8394/8637537151_227a0b7baf_b.jpg') no-repeat center fixed;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;
        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: black;
    }
</style>
<?php ob_start(); ?>
<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container" id="ajax-content" style=""></div>
    <!-- /.container -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer" style="">
    <div class="container">
        <div class="pull-right hidden-xs">
            <a href="<?= SITE ?>Privacy/">Privacy Policy</a> <b>Version</b> 0.4.0
        </div>
        <strong>Copyright &copy; 2014-2017 <a href="http://lilRichard.com">Stats Coach</a>.</strong>
    </div>
    <!-- /.container -->
</footer>
</div>
<?php $wrapper_footer = ob_get_clean(); ?>


<?php if ($this->user->user_type == 'Coach') { ?>

<body class="skin-green fixed sidebar-mini sidebar-collapse">
<div class="wrapper">
    <?php require_once CONTENT_ROOT . 'CoachLayout.php';
    echo $wrapper_footer;
    } elseif ($this->user->user_type == 'Athlete') { ?>
    <body class="hold-transition skin-green layout-top-nav">
    <div class="wrapper">
        <?php require_once CONTENT_ROOT . 'AthleteLayout.php';
        echo $wrapper_footer;
        } elseif (!$this->user->user_id) { ?>
        <body class="stats-wrap"><div class="container" id="ajax-content" style=""></div>
        <?php } else { ?>
        <body><div class="alert"></div>
        <script>bootstrapAlert('A critical error has occured', 'danger')</script>
        <?php } ?>

        <!-- ./wrapper -->
        <!-- JQuery -->
        <script src="<?= $this->versionControl( 'components/jquery/jquery.min.js' ) ?>"></script>
        <!-- Background -->
        <script src="<?= $this->versionControl( 'Public/jquery-backstretch/jquery.backstretch.min.js' ) ?>"></script>
        <!-- Select 2 -->
        <script src="<?= $this->versionControl( 'bower_components/select2/dist/js/select2.full.min.js' ) ?>"></script>
        <!-- Bootstrap -->
        <script src="<?= $this->versionControl( 'bower_components/bootstrap/dist/js/bootstrap.min.js' ) ?>"></script>
        <!-- Data tables -->
        <!--script src="<?= $this->versionControl( 'bower_components/datatables.net-bs/js/dataTables.bootstrap.js' ) ?>"></script>
        <!-- Input Mask -->
        <script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.js' ) ?>"></script>
        <script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.date.extensions.js' ) ?>"></script>
        <script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.extensions.js' ) ?>"></script>
        <!-- Slim Scroll -->
        <script src="<?= $this->versionControl( 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js' ) ?>"></script>
        <!-- Fastclick -->
        <script src="<?= $this->versionControl( 'bower_components/fastclick/lib/fastclick.js' ) ?>"></script>
        <!-- AJAX Pace -->
        <script src="<?= $this->versionControl( 'bower_components/PACE/pace.js' ) ?>"></script>
        <!-- Admin LTE -->
        <script src="<?= $this->versionControl( 'dist/js/adminlte.min.js' ) ?>"></script>
        <!-- Stats Coach Bootstrap Alert -->
        <script src="<?= $this->versionControl( 'alert/alerts.js' ) ?>"></script>
        <!-- iCheck -->
        <script src="<?= $this->versionControl( 'plugins/iCheck/icheck.min.js' ) ?>"></script>
        <!-- bootstrap datepicker -->
        <script src="<?= $this->versionControl( 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js' ) ?>"></script>
        <!-- bootstrap color picker -->
        <script src="<?= $this->versionControl( 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js' ) ?>"></script>
        <!-- bootstrap time picker -->
        <script src="<?= $this->versionControl( 'plugins/timepicker/bootstrap-timepicker.min.js' ) ?>"></script>
        <!-- Google -->
        <script src="<?= $this->versionControl( 'Public/Analytics/google.analytics.js' ) ?>"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="<?= $this->versionControl( 'dist/js/demo.js' ) ?>"></script>
        <!-- jQuery Knob -->
        <script src="<?= $this->versionControl( 'bower_components/jquery-knob/js/jquery.knob.js' ) ?>"></script>
        <!-- PJAX-->
        <script src="<?= $this->versionControl( 'Public/Jquery-Pjax/jquery.pjax.js' ) ?>"></script>
        <!-- Better PJAX - https://github.com/defunkt/jquery-pjax/issues/469  -->

        <script>
            jQuery.fn.exists = function () {
                return this.length > 0;
            };

            $(document).pjax('a', '#ajax-content');
            $.pjax.reload('#ajax-content');

            $.fn.on('pjax:send', function () {
                $('#ajax-content').addClass('overlay').innerHTML = "<i class='fa fa-refresh fa-spin'></i>";
                Pace.restart();
            });

            $.fn.on('pjax:complete', function () {
                $('#ajax-content').removeClass('overlay'); });

            $(document).on("click", "a.no-pjax", false);


            $(function () {
                var closure = function() {$(document).on("pjax:complete", function (event) {
                    $(event.target).find("script[data-exec-on-popstate]").each(function () {
                        $.globalEval(this.text || this.textContent || this.innerHTML || '');
                    })
                })};
                $(document).on("pjax:popstate", function () {
                    closure();
                });  //.on("pjax:pushstate")

            });

        </script>


        </body>
</html>

