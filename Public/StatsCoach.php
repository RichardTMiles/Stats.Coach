<?php

const COMPOSER = 'Data' . DS . 'vendor' . DS;
const TEMPLATE = COMPOSER . 'almasaeed2010' . DS . 'adminlte' . DS;


?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= SITE_TITLE ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- PJAX Content Control -->
    <meta http-equiv="x-pjax-version" content="<?= $_SESSION['X_PJAX_Version'] ?>">
    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap/dist/css/bootstrap.min.css") ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $this->versionControl(TEMPLATE . "dist/css/AdminLTE.min.css") ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "dist/css/skins/_all-skins.min.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- DataTables.Bootstrap -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- iCheck -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "plugins/iCheck/square/blue.css"); ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Ionicons -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/Ionicons/css/ionicons.min.css") ?>"
          as="style" onload="this.rel='stylesheet'">
    <!-- Back color -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "dist/css/skins/skin-green.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Multiple input dynamic form -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/select2/dist/css/select2.min.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Check Ratio Box -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "plugins/iCheck/flat/blue.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- I dont know but keep it -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/morris.js/morris.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- fun ajax refresh -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "plugins/pace/pace.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Jquery -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/jvectormap/jquery-jvectormap.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- datepicker -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css") ?>" as="style" onload="this.rel='stylesheet'">

    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap-daterangepicker/daterangepicker.css") ?>" as="style" onload="this.rel='stylesheet'">

    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "plugins/timepicker/bootstrap-timepicker.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Wysihtml -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css") ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Font Awesome -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/font-awesome/css/font-awesome.min.css") ?>" as="style" onload="this.rel='stylesheet'">

    <!-- Font Awesome -->
    <link rel="preload" href="<?= $this->versionControl(TEMPLATE . "bower_components/font-awesome/css/font-awesome.min.css") ?>" as="style" onload="this.rel='stylesheet'">

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
                if (c) g = c; else {
                    var j = (h.body || h.getElementsByTagName("head")[0]).childNodes;
                    g = j[j.length - 1]
                }
                var k = h.styleSheets;
                i.rel = "stylesheet", i.href = b, i.media = "only x", e(function () {
                    g.parentNode.insertBefore(i, c ? g : g.nextSibling)
                });
                var l = function (a) {
                    for (var b = i.href, c = k.length; c--;) if (k[c].href === b) return a();
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
        }(typeof global !== "undefined" ? global : this));// Hierarchical PJAX Request

    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- ./wrapper -->
</head>

<?php

$userType = ($this->user[$_SESSION['id']]['user_type'] ?? false);
$layout = ($userType == 'Athlete') ? 'hold-transition skin-green layout-top-nav' :
    (($userType == 'Coach') ? 'skin-green fixed sidebar-mini sidebar-collapse' :
        'stats-wrap');
?>

<!-- Full Width Column -->
<body class="<?= $layout ?>" style="background: transparent">
<div <?= (($userType == 'Athlete' || $userType == 'Coach') ? 'class="wrapper"' : 'class="container" id="pjax-content"') ?>
        style="background: transparent">

    <?php

    if (($_SESSION['id'] ?? false) && is_array($this->user[$_SESSION['id']])):
        $my = $this->user[$_SESSION['id']];

        if ($userType == 'Coach'):
            require_once SERVER_ROOT . 'Public/AdminLTE/CoachLayout.php';
        else:
            require_once SERVER_ROOT . 'Public/AdminLTE/AthleteLayout.php';
        endif;
        ?>

        <div class="content-wrapper" style="background: transparent">
            <div class="container">
                <div id="alert"></div>
                <!-- content -->
                <div id="pjax-content"></div>
                <!-- /.content -->
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <!-- /.container -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer" style="">
            <div class="container">
                <div class="pull-right hidden-xs">
                    <a href="<?= SITE ?>Privacy/">Privacy Policy</a> <b>Version</b> <?= SITE_VERSION ?>
                </div>
                <strong>Copyright &copy; 2014-2017 <a href="http://lilRichard.com">Stats Coach</a>.</strong>
                <!--script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script-->
            </div>
            <!-- /.container -->
        </footer>

    <?php else:
        include SERVER_ROOT . 'Public/User/login.php';
    endif; ?>
</div>
<script>
    //-- JQuery -->
    loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery/dist/jquery.min.js') ?>", function () {

        //-- Jquery Form -->
        loadJS('<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-form/src/jquery.form.js')?>');

        //-- Background Stretch -->
        loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-backstretch/jquery.backstretch.min.js') ?>", function () {
            $.backstretch('<?=SITE?>Public/img/final.jpg');
        });

        //-- Slim Scroll -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js') ?>");

        //-- Fastclick -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/fastclick/lib/fastclick.js') ?>", function () {
            //-- Admin LTE -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'dist/js/adminlte.min.js') ?>");
        });

        //-- Bootstrap -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap/dist/js/bootstrap.min.js') ?>", function () {

            //-- AJAX Pace -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/PACE/pace.js') ?>", function () {
                $(document).ajaxStart(function () {
                    Pace.restart();
                });
            });

            //-- Select 2 -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/select2/dist/js/select2.full.min.js') ?>");

            //-- iCheck -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/iCheck/icheck.min.js')?>");

            //-- Input Mask -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.js') ?>", function () {
                loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.date.extensions.js') ?>");
                loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.extensions.js') ?>");
            });

            //-- jQuery Knob -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery-knob/js/jquery.knob.js') ?>");

            //-- Bootstrap Time Picker -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/timepicker/bootstrap-timepicker.min.js') ?>");

            //--Bootstrap Datepicker -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js') ?>");

            //--Bootstrap Color Picker -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') ?>");

            //-- PJAX-->
            loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-pjax/jquery.pjax.js') ?>", function () {
                loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/mustache.js/mustache.js') ?>", function () {
                    loadJS("<?= $this->versionControl(COMPOSER . 'richardtmiles/carbonphp/Helpers/Carbon.js')?>", function () {
                        Carbon('#pjax-content', '');
                        $.fn.sendEvent('Messages/');
                        $.fn.sendEvent('Notifications/');
                        $.fn.sendEvent('tasks/');
                    });
                });
            });
        });
    });

</script>

</body>
</html>
