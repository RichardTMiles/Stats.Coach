<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= SITE_TITLE ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- PJAX Content Control -->
    <meta http-equiv="x-pjax-version" content="<?= $_SESSION['X_PJAX_Version'] ?>">
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-100885582-1"></script>

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
            var loadJS;
            loadJS = function (src, cb) {
                "use strict";
                var ref = w.document.getElementsByTagName("script")[0];
                var script = w.document.createElement("script");
                script.src = src;
                script.async = true;
                ref.parentNode.insertBefore(script, ref);
                if (cb && typeof(cb) === "function")
                    script.onload = cb;

                return script;
            }; // commonjs
            if (typeof module !== "undefined") module.exports = loadJS;
            else w.loadJS = loadJS;
        }(typeof global !== "undefined" ? global : this));// Hierarchical PJAX Request

        // Facebook Analytics
        window.fbAsyncInit = function () {
            FB.init({
                appId: <?=FACEBOOK_APP_ID?>,
                xfbml: true,
                version: 'v2.11'
            });
            FB.AppEvents.logPageView();
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Document ready => jQuery => PJAX => CarbonPHP = loaded
        function Carbon(cb) {
            document.addEventListener("Carbon", function fn(event) {
                document.removeEventListener("Carbon", fn);
                cb(event);
            });
        }
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

    if (($_SESSION['id'] ?? false) && is_array($this->user[$_SESSION['id']] ?? false)):
        $my = $this->user[$_SESSION['id']];
        ($userType == 'Coach' ?  require_once PUBLIC_FOLDER . 'AdminLTE/CoachLayout.php' :
            require_once PUBLIC_FOLDER . 'AdminLTE/AthleteLayout.php'); ?>

        <div class="content-wrapper" style="background: transparent">
            <div class="container">
                <div id="alert"></div>
                <!-- content -->
                <div id="pjax-content">
                    <?= $this->bufferedContent ?>
                </div>
                <!-- /.content -->
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
        print $this->bufferedContent;
    endif; ?>
</div>

<noscript id="deferred-styles">
    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap/dist/css/bootstrap.min.css") ?>">
    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "dist/css/AdminLTE.min.css") ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "dist/css/skins/_all-skins.min.css") ?>">
    <!-- DataTables.Bootstrap -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css") ?>">
    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "plugins/iCheck/all.css"); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/Ionicons/css/ionicons.min.css") ?>">
    <!-- Back color -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "dist/css/skins/skin-green.css") ?>">
    <!-- Multiple input dynamic form -->
    <link rel="stylesheet" type="text/css"
          href="<?= $this->versionControl(TEMPLATE . "bower_components/select2/dist/css/select2.min.css") ?>" as="style"
          onload="this.rel='stylesheet'">
    <!-- Check Ratio Box -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "plugins/iCheck/flat/blue.css") ?>">
    <!-- I dont know but keep it -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/morris.js/morris.css") ?>">
    <!-- fun ajax refresh -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "plugins/pace/pace.css") ?>">
    <!-- Jquery -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/jvectormap/jquery-jvectormap.css") ?>">
    <!-- datepicker -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css") ?>">
    <!-- date-range-picker -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/bootstrap-daterangepicker/daterangepicker.css") ?>">
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "plugins/timepicker/bootstrap-timepicker.css") ?>">
    <!-- Wysihtml -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css") ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/font-awesome/css/font-awesome.min.css") ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="<?= $this->versionControl(TEMPLATE . "bower_components/font-awesome/css/font-awesome.min.css") ?>">
</noscript>
<script>
    // Google
    let loadDeferredStyles = function() {
        let addStylesNode = document.getElementById("deferred-styles");
        let replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
    };
    let raf = requestAnimationFrame || mozRequestAnimationFrame ||
        webkitRequestAnimationFrame || msRequestAnimationFrame;
    if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
    else window.addEventListener('load', loadDeferredStyles);

    let JSLoaded = new Set();

    //-- JQuery -->
    loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery/dist/jquery.min.js') ?>", () => {

        //-- Jquery Form -->
        loadJS('<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-form/src/jquery.form.js')?>');

        //-- Background Stretch -->
        loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-backstretch/jquery.backstretch.min.js') ?>", () => {
            $.backstretch('<?=SITE?>Application/View/img/final.jpg');
        });

        //-- Slim Scroll -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js') ?>");

        //-- Fastclick -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/fastclick/lib/fastclick.js') ?>", () => {
            //-- Admin LTE -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'dist/js/adminlte.min.js') ?>");
        });

        //-- Bootstrap -->
        loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap/dist/js/bootstrap.min.js') ?>", () => {

            //-- AJAX Pace -->
            loadJS("<?= $this->versionControl(TEMPLATE . 'bower_components/PACE/pace.js') ?>", () => $(document).ajaxStart(() => Pace.restart()));

            $.fn.CarbonJS = (sc, cb) => (!JSLoaded.has(sc) ? loadJS(sc, cb) : cb());

            //-- Select 2 -->
            $.fn.load_select2 = (select2) =>
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'bower_components/select2/dist/js/select2.full.min.js') ?>", () =>
                    $(select2).select2());

            //-- Data tables -->
            $.fn.load_datatables = (table) =>
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE .'bower_components/datatables.net-bs/js/dataTables.bootstrap.js') ?>", () => {
                    try { return $(table).DataTable() } catch (err) { return false }});

            //-- iCheak -->
            $.fn.load_iCheck = (input) => {
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'plugins/iCheck/icheck.min.js')?>", () => {
                    $(input).iCheck({
                        checkboxClass: 'icheckbox_square-blue', radioClass: 'iradio_square-blue', increaseArea: '20%' // optional
                    });
                });
            };

            //-- Input Mask -->
            $.fn.load_inputmask = (mask) =>
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.js') ?>", () => {
                    loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.date.extensions.js') ?>",
                        () => $(mask).inputmask());
                    loadJS("<?= $this->versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.extensions.js') ?>",
                        () => $(mask).inputmask());
                }, () => $(mask).inputmask());

            //-- jQuery Knob -->
            $.fn.load_knob = (knob) => {
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'bower_components/jquery-knob/js/jquery.knob.js') ?>", () => {
                    $(knob).knob({
                        draw: function () {
                            // "tron" case
                            if (this.$.data('skin') === 'tron') {

                                var a = this.angle(this.cv)  // Angle
                                    , sa = this.startAngle          // Previous start angle
                                    , sat = this.startAngle         // Start angle
                                    , ea                            // Previous end angle
                                    , eat = sat + a                 // End angle
                                    , r = true;

                                this.g.lineWidth = this.lineWidth;

                                this.o.cursor
                                && (sat = eat - 0.3)
                                && (eat = eat + 0.3);

                                if (this.o.displayPrevious) {
                                    ea = this.startAngle + this.angle(this.value);
                                    this.o.cursor
                                    && (sa = ea - 0.3)
                                    && (ea = ea + 0.3);
                                    this.g.beginPath();
                                    this.g.strokeStyle = this.previousColor;
                                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
                                    this.g.stroke();
                                }

                                this.g.beginPath();
                                this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
                                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
                                this.g.stroke();

                                this.g.lineWidth = 2;
                                this.g.beginPath();
                                this.g.strokeStyle = this.o.fgColor;
                                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
                                this.g.stroke();

                                return false;
                            }
                        }
                    });
                });
            };

            //-- Bootstrap Time Picker -->
            $.fn.load_timepicker = (timepicker) => {
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'plugins/timepicker/bootstrap-timepicker.min.js') ?>", () => {
                    $(timepicker).timepicker({showInputs: false});
                });
            }

            //--Bootstrap Datepicker -->
            $.fn.load_datepicker = (datepicker) =>
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js') ?>", () =>
                    $(datepicker).datepicker({autoclose: true}));

            //--Bootstrap Color Picker -->
            $.fn.load_colorpicker = (colorpicker) =>
                $.fn.CarbonJS("<?= $this->versionControl(TEMPLATE . 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') ?>", () =>
                    $(colorpicker).colorpicker());

            //-- PJAX-->
            loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/jquery-pjax/jquery.pjax.js') ?>", () =>
                loadJS("<?= $this->versionControl(COMPOSER . 'bower-asset/mustache.js/mustache.js') ?>", () =>
                    loadJS("<?= $this->versionControl(COMPOSER . 'richardtmiles/carbonphp/Helpers/Carbon.js')?>", () =>
                        CarbonJS('#pjax-content', '<?=($_SESSION['id']??false)?'wss://stats.coach:8888/':null?>', true))));

            //<!-- AdminLTE for demo purposes loadJS("<?= $this->versionControl('dist/js/demo.js') ?>//");

        });
    });

    <!-- Global site tag (gtag.js) - Google Analytics -->
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-100885582-1');
</script>

</body>
</html>
