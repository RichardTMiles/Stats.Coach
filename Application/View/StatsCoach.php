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
    <link rel="shortcut icon" href="<?= SITE_PATH ?>Public/favicon.png" type="image/x-icon"/>
    
    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= $this->versionControl( "bootstrap/css/bootstrap.css" ) ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $this->versionControl( "dist/css/AdminLTE.min.css" ) ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="preload" href="<?= $this->versionControl( "dist/css/skins/_all-skins.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- DataTables.Bootstrap -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/datatables/dataTables.bootstrap.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Font Awesome -->
    <link rel="preload" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" as="style" onload="this.rel='stylesheet'">
    <!-- Ionicons -->
    <!--link rel="preload" href="<?= SITE_PATH ?>Application/Services/vendor/ionicons/ionicons.min.css" as="style" onload="this.rel='stylesheet'"-->
    <!-- Back color -->
    <link rel="preload" href="<?= $this->versionControl( "dist/css/skins/skin-green.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Multiple input dynamic form -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/select2/select2.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Check Ratio Box -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/iCheck/flat/blue.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- I dont know but keep it -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/morris/morris.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- fun ajax refresh -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/pace/pace.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Jquery -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/jvectormap/jquery-jvectormap-1.2.2.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    <!--
    <link rel="preload" href="<?= $this->versionControl( "plugins/datepicker/datepicker3.css" ) ?>" as="style" onload="this.rel='stylesheet'">>
    <link rel="preload" href="<?= $this->versionControl( "plugins/daterangepicker/daterangepicker.css" ) ?>" as="style" onload="this.rel='stylesheet'">
    -->
    <link rel="preload" href="<?= $this->versionControl( "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" ) ?>" as="style"
          onload="this.rel='stylesheet'">

    <!-- Font Awesome -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" as="style" onload="this.rel='stylesheet'">
    <!-- Ionicons -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" as="style" onload="this.rel='stylesheet'">

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


</head>
<style>
    .content-wrapper {
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

    .menu {
        height: 100px;
    }

</style>
<div class="wrapper">
<?php

// This is the body attributes for each user

if ($this->user->user_type == 'Coach') { ?>
    <body class="skin-green fixed sidebar-mini sidebar-collapse">
    <?php require_once CONTENT_ROOT . 'CoachLayout.php';

} elseif ($this->user->user_type == 'Athlete') { ?>
    <body class="hold-transition skin-green layout-top-nav">
    <?php require_once CONTENT_ROOT . 'AthleteLayout.php';

} else sortDump();

?>

<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container" id="ajax-content" style=""></div>
    <!-- /.container -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer" style="">
    <div class="container">
        <div class="pull-right hidden-xs">
            <a href="<?= SITE_PATH ?>Privacy/">Privacy Policy</a> <b>Version</b> 0.4.0
        </div>
        <strong>Copyright &copy; 2014-2017 <a href="http://lilRichard.com">Stats Coach</a>.</strong>
    </div>
    <!-- /.container -->
</footer>
</div>
<!-- ./wrapper -->
<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
<!-- Background -->
<script src="<?= SITE_PATH ?>Public/Backstretch/jquery.backstretch.min.js"></script>
<!-- Menu Options -->
<script src="<?= $this->versionControl( 'plugins/select2/select2.full.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'bootstrap/js/bootstrap.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/datatables/jquery.dataTables.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/datatables/dataTables.bootstrap.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.date.extensions.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.extensions.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/slimScroll/jquery.slimscroll.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/fastclick/fastclick.min.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'plugins/pace/pace.js' ); ?>"></script>
<script src="<?= $this->versionControl( 'dist/js/app.min.js' ); ?>"></script>
<script src="<?= CONTENT_PATH . "alert/alerts.js" ?>"></script>
<script src="<?= SITE_PATH ?>Public/Analytics/google.analytics.js"></script>
<!-- AdminLTE for demo purposes -->
<!--script src="<?= TEMPLATE_PATH ?>dist/js/demo.js"></script-->
<!-- jQuery Knob -->
<script src="<?= TEMPLATE_PATH ?>plugins/knob/jquery.knob.js"></script>
<!-- Sparkline -->
<script src="<?= TEMPLATE_PATH ?>plugins/sparkline/jquery.sparkline.min.js"></script>

<script src="<?= SITE_PATH ?>Public/Jquery-Pjax/jquery.pjax.js"></script>
<script>
    $(function () {
        // initial content
        $.pjax.reload('#ajax-content');
        // Every href on 'a' element
        // when on document load add event to every a tag, when event fired trigger smart refresh
        $.when($(document).pjax('a', '#ajax-content')).then(function () {
            $('#ajax-content').addClass("overlay").innerHTML = "<i class='fa fa-refresh fa-spin'></i>";
            Pace.restart();
        }).done(function () { $('#ajax-content').removeClass('overlay');});
    });
</script>

</body>
</html>

