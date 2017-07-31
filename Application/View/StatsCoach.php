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

    <?php include CONTENT_ROOT . 'img/icons/icons.php'; ?>
    
    <!-- PJAX Content Control -->
    <meta http-equiv="x-pjax-version" content="<?= $_SESSION['X_PJAX_Version'] ?>">
    

    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= $this->versionControl( "bower_components/bootstrap/dist/css/bootstrap.min.css" ) ?>">
    <!-- Ajax Data Togles -->
    <link rel="stylesheet" href="<?= $this->versionControl( "Public/Bootstrap-Toggle/bootstrap-toggle.min.css" ) ?>">
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

        /*! loadJS: load a JS file asynchronously. [c]2014 @scottjehl, Filament Group, Inc. (Based on http://goo.gl/REQGQ by Paul Irish). Licensed MIT */
        (function (w) {
            var loadJS = function (src, cb) {
                "use strict";
                var ref = w.document.getElementsByTagName("script")[0];
                var script = w.document.createElement("script");
                script.src = src;
                script.async = true;
                ref.parentNode.insertBefore(script, ref);
                if (cb && typeof(cb) === "function") {
                    script.onload = cb;
                }
                return script;
            };
            // commonjs
            if (typeof module !== "undefined") {
                module.exports = loadJS;
            }
            else {
                w.loadJS = loadJS;
            }
        }(typeof global !== "undefined" ? global : this));

    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


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
            <a href="<?= SITE ?>Privacy/">Privacy Policy</a> <b>Version</b> <?= SITE_VERSION ?>
        </div>
        <strong>Copyright &copy; 2014-2017 <a href="http://lilRichard.com">Stats Coach</a>.</strong>
    </div>
    <!-- /.container -->
</footer>
</div>
<?php $wrapper_footer = ob_get_clean(); ?>


<?php

# dump($GLOBALS);


if (!empty($_SESSION['id']) && is_object( $this->user[$_SESSION['id']] )) {
    if ($this->user[$_SESSION['id']]->user_type == 'Coach') {
        echo '<body class="skin-green fixed sidebar-mini sidebar-collapse"><div class="wrapper">';
        require_once CONTENT_ROOT . 'CoachLayout.php';
        echo $wrapper_footer;
    } elseif ($this->user[$_SESSION['id']]->user_type == 'Athlete') {
        echo '<body class="hold-transition skin-green layout-top-nav"><div class="wrapper">';
        require_once CONTENT_ROOT . 'AthleteLayout.php';
        echo $wrapper_footer;
    }
} elseif (array_key_exists( 'id', $_SESSION ) && !$_SESSION['id']) {
    echo '<body class="stats-wrap"><div class="container" id="ajax-content" style=""></div>';
} else {
    session_destroy();
    session_regenerate_id( TRUE );
    echo '<script type="text/javascript"> window.location = "' . SITE . '" </script>';
    // TODO - how often does this happen
} ?>

<!-- ./wrapper -->
<script>
    // JQuery
    loadJS("<?= $this->versionControl( 'components/jquery/jquery.min.js' ) ?>", function () {
        loadJS("<?= $this->versionControl( 'Public/jquery-backstretch/jquery.backstretch.min.js' ) ?>");
        //-- Bootstrap -->
        loadJS("<?= $this->versionControl( 'bower_components/bootstrap/dist/js/bootstrap.min.js' ) ?>", function () {
            <!-- Slim Scroll -->
            loadJS("<?= $this->versionControl( 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js' ) ?>");
            <!-- Fastclick -->
            loadJS("<?= $this->versionControl( 'bower_components/fastclick/lib/fastclick.js' ) ?>");
            <!-- AJAX Pace -->
            loadJS("<?= $this->versionControl( 'bower_components/PACE/pace.js' ) ?>");
            <!-- Admin LTE -->
            loadJS("<?= $this->versionControl( 'dist/js/adminlte.min.js' ) ?>", function () {
                $("[data-widget='collapse']").click(function() {
                    //Find the box parent
                    var box = $(this).parents(".box").first();
                    //Find the body and the footer
                    var bf = box.find(".box-body, .box-footer");
                    if (!box.hasClass("collapsed-box")) {
                        box.addClass("collapsed-box");
                        bf.slideUp();
                    } else {
                        box.removeClass("collapsed-box");
                        bf.slideDown();
                    }
                });
            });
        });
        <!-- PJAX-->
        loadJS("<?= $this->versionControl( 'Public/Jquery-Pjax/jquery.pjax.js' ) ?>", function () {

            jQuery.fn.exists = function () {
                return this.length > 0;
            };

            $(document).on('pjax:end pjax:start', function () {
                <?=$this->AJAXJavaScript()?>
            });

            $(document).on('pjax:start', function () {
                console.log("PJAX loaded!");
            });

            // All links will be sent with ajax
            $(document).pjax('a', '#ajax-content');

            // Set a data mask to force https request
            $(document).on("click", "a.no-pjax", false);

            $(document).on('pjax:click', function () {
                var content = $('#ajax-content').addClass('overlay').innerHTML = "<i class='fa fa-refresh fa-spin'></i>";
                Pace.restart();
            });

            $(document).on('pjax:success', function (event) {
                var url = (typeof event.data !== 'undefined') ? event.data.url : '';
                console.log("Successfully loaded " + url);
            });

            $(document).on('pjax:error', function (event) {
                var url = (typeof event.data !== 'undefined') ? event.data.url : '';
                console.log("Could not load " + url);
            });

            $(document).on('pjax:complete', function () {
                $('#ajax-content').fadeIn('fast').removeClass('overlay');
            });

            // On initial html page request, get already loaded inner content from server
            $.pjax.reload('#ajax-content');

        });
    });

</script>


</body>
</html>
