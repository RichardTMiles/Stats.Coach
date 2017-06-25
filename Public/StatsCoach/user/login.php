<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stats | Coach</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?= SITE_PATH ?>Public/favicon.png" type="image/x-icon"/>

    <!-- Bootstrap 3.3.5 -->
    <link rel="preload" href="<?= $this->versionControl( 'bootstrap/css/bootstrap.min.css' ); ?>" as="style" onload="this.rel='stylesheet'">
    <!-- Font Awesome -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" as="style" onload="this.rel='stylesheet'">

    <!-- Ionicons -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" as="style" onload="this.rel='stylesheet'">

    <!-- Theme style -->
    <link rel="preload" href="<?= $this->versionControl( 'dist/css/AdminLTE.min.css' ); ?>" as="style" onload="this.rel='stylesheet'">
    <!-- iCheck -->
    <link rel="preload" href="<?= $this->versionControl( 'plugins/iCheck/square/blue.css' ); ?>" as="style" onload="this.rel='stylesheet'">

    <!-- preload : AdminLTE -->
    <link rel="preload" href="<?= $this->versionControl( "dist/css/AdminLTE.min.css" ) ?>" as="style" onload="this.rel='stylesheet'">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

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

</head>
<style>
    html {
        /* This image will be displayed fullscreen */
        background: url('/Public/StatsCoach/img/Hole12.jpg') no-repeat center fixed;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;

        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: transparent;
    }
</style>
<body class="body hold-transition" id="body">
<div class="login-box">
    <div class="login-logo">
        <a href="<?= SITE_PATH ?>" style="color: #ffffff; font-size: 150%"><b>Stats</b>.Coach</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body" style="background-color: #ECF0F1; color: #0c0c0c; border: medium">
        <p class="login-box-msg">Sign in to start your session</p>

        <div id="alert"></div>
        
        <?php if ($this->UserName == false): ?>
            <!-- form action="index.php?controller=user&action=verify_signin&id=run" method="post" -->
            <form action="<?= SITE_PATH ?>login/" method="post">
                <div class="form-group has-feedback">

                    <input type="text" class="form-control" name="username"
                           placeholder="Username" value="<?= (isset($_POST['username']) ? htmlentities( $_POST['username'] ) : null); ?>">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">

                    <input type="password" name="password" class="form-control" placeholder="Password">

                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="RememberMe" value="1"> Remember Me
                            </label>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div><!-- /.col -->
                </div>
            </form>


            <div class="social-auth-links text-center">
                <p>- OR -</p><a href="<?= $this->faceBookLoginUrl() ?>" class="btn btn-block btn-social btn-facebook btn-flat">
                    <i class="fa fa-facebook"></i> Sign in using Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat">
                    <i class="fa fa-google-plus"></i> Sign in using Google+</a>
            </div><!-- /.social-auth-links -->

            <br/>
            <div class="categories-bottom">
                <a href="<?= SITE_PATH . 'recover/'; ?>">Forgot password<br></a>
                <a href="<?= SITE_PATH . 'register/'; ?>" class="text-center">Register a new membership</a>
            </div>

        <?php else: ?>

            <!-- Automatic element centering -->
            <div class="lockscreen-wrapper">
                <!-- User name -->
                <div class="lockscreen-name" style="text-align: center; font-size: 200%"><b><?= $this->FullName ?></b></div>

                <!-- START LOCK SCREEN ITEM -->
                <div class="lockscreen-item">
                    <!-- lockscreen image -->
                    <div class="lockscreen-image">
                        <img src="<?= $this->UserImage ?>" alt="User Image">
                    </div>
                    <!-- /.lockscreen-image -->

                    <!-- lockscreen credentials (contains the form) -->
                    <form class="lockscreen-credentials" action="<?= SITE_PATH ?>login/" method="post">
                        <div class="input-group">
                            <input style="display: none" type="text" value="1" name="RememberMe">
                            <input style="display: none" type="text" class="form-control" name="username" placeholder="Username" value="<?= $this->UserName ?>">
                            <input type="password" name="password" class="form-control" placeholder="Password">

                            <div class="input-group-btn">
                                <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                            </div>
                        </div>
                    </form>
                    <!-- /.lockscreen credentials -->

                </div>
                <!-- /.lockscreen-item -->
                <div class="help-block text-center">
                    Enter your password to retrieve your session
                </div>
                <div class="text-center">
                    <a href="<?= SITE_PATH . 'login/clear/' ?>">Or sign in as a different user</a>
                </div>
                <div class="lockscreen-footer text-center">
                    Copyright &copy; 2014-2017 <b><a href="http://lilRichard.com" class="text-black">Richard Miles</a></b><br>
                    All rights reserved
                </div>
            </div>
            <!-- /.center -->
        <?php endif; ?>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->


<!-- jQuery 2.1.4 -->
<script src="<?= TEMPLATE_PATH ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- BackStretch -->
<script>
    /*! Backstretch - v2.0.4 - 2013-06-19
     * http://srobbin.com/jquery-plugins/backstretch/
     * Copyright (c) 2013 Scott Robbin; Licensed MIT */
    (function (a, d, p) {
        a.fn.backstretch = function (c, b) {
            (c === p || 0 === c.length) && a.error("No images were supplied for Backstretch");
            0 === a(d).scrollTop() && d.scrollTo(0, 0);
            return this.each(function () {
                var d = a(this), g = d.data("backstretch");
                if (g) {
                    if ("string" == typeof c && "function" == typeof g[c]) {
                        g[c](b);
                        return
                    }
                    b = a.extend(g.options, b);
                    g.destroy(!0)
                }
                g = new q(this, c, b);
                d.data("backstretch", g)
            })
        };
        a.backstretch = function (c, b) {
            return a("body").backstretch(c, b).data("backstretch")
        };
        a.expr[":"].backstretch = function (c) {
            return a(c).data("backstretch") !== p
        };
        a.fn.backstretch.defaults = {centeredX: !0, centeredY: !0, duration: 5E3, fade: 0};
        var r = {left: 0, top: 0, overflow: "hidden", margin: 0, padding: 0, height: "100%", width: "100%", zIndex: -999999}, s = {
            position: "absolute",
            display: "none",
            margin: 0,
            padding: 0,
            border: "none",
            width: "auto",
            height: "auto",
            maxHeight: "none",
            maxWidth: "none",
            zIndex: -999999
        }, q = function (c, b, e) {
            this.options = a.extend({}, a.fn.backstretch.defaults, e || {});
            this.images = a.isArray(b) ? b : [b];
            a.each(this.images, function () {
                a("<img />")[0].src = this
            });
            this.isBody = c === document.body;
            this.$container = a(c);
            this.$root = this.isBody ? l ? a(d) : a(document) : this.$container;
            c = this.$container.children(".backstretch").first();
            this.$wrap = c.length ? c : a('<div class="backstretch"></div>').css(r).appendTo(this.$container);
            this.isBody || (c = this.$container.css("position"), b = this.$container.css("zIndex"), this.$container.css({
                position: "static" === c ? "relative" : c,
                zIndex: "auto" === b ? 0 : b,
                background: "none"
            }), this.$wrap.css({zIndex: -999998}));
            this.$wrap.css({position: this.isBody && l ? "fixed" : "absolute"});
            this.index = 0;
            this.show(this.index);
            a(d).on("resize.backstretch", a.proxy(this.resize, this)).on("orientationchange.backstretch", a.proxy(function () {
                this.isBody && 0 === d.pageYOffset && (d.scrollTo(0, 1), this.resize())
            }, this))
        };
        q.prototype = {
            resize: function () {
                try {
                    var a = {
                        left: 0,
                        top: 0
                    }, b = this.isBody ? this.$root.width() : this.$root.innerWidth(), e = b, g = this.isBody ? d.innerHeight ? d.innerHeight : this.$root.height() : this.$root.innerHeight(), j = e / this.$img.data("ratio"), f;
                    j >= g ? (f = (j - g) / 2, this.options.centeredY && (a.top = "-" + f + "px")) : (j = g, e = j * this.$img.data("ratio"), f = (e - b) / 2, this.options.centeredX && (a.left = "-" + f + "px"));
                    this.$wrap.css({width: b, height: g}).find("img:not(.deleteable)").css({width: e, height: j}).css(a)
                } catch (h) {
                }
                return this
            }, show: function (c) {
                if (!(Math.abs(c) > this.images.length - 1)) {
                    var b = this, e = b.$wrap.find("img").addClass("deleteable"), d = {relatedTarget: b.$container[0]};
                    b.$container.trigger(a.Event("backstretch.before", d), [b, c]);
                    this.index = c;
                    clearInterval(b.interval);
                    b.$img = a("<img />").css(s).bind("load", function (f) {
                        var h = this.width || a(f.target).width();
                        f = this.height || a(f.target).height();
                        a(this).data("ratio", h / f);
                        a(this).fadeIn(b.options.speed || b.options.fade, function () {
                            e.remove();
                            b.paused || b.cycle();
                            a(["after", "show"]).each(function () {
                                b.$container.trigger(a.Event("backstretch." + this, d), [b, c])
                            })
                        });
                        b.resize()
                    }).appendTo(b.$wrap);
                    b.$img.attr("src", b.images[c]);
                    return b
                }
            }, next: function () {
                return this.show(this.index < this.images.length - 1 ? this.index + 1 : 0)
            }, prev: function () {
                return this.show(0 === this.index ? this.images.length - 1 : this.index - 1)
            }, pause: function () {
                this.paused = !0;
                return this
            }, resume: function () {
                this.paused = !1;
                this.next();
                return this
            }, cycle: function () {
                1 < this.images.length && (clearInterval(this.interval), this.interval = setInterval(a.proxy(function () {
                    this.paused || this.next()
                }, this), this.options.duration));
                return this
            }, destroy: function (c) {
                a(d).off("resize.backstretch orientationchange.backstretch");
                clearInterval(this.interval);
                c || this.$wrap.remove();
                this.$container.removeData("backstretch")
            }
        };
        var l, f = navigator.userAgent, m = navigator.platform, e = f.match(/AppleWebKit\/([0-9]+)/), e = !!e && e[1], h = f.match(/Fennec\/([0-9]+)/), h = !!h && h[1], n = f.match(/Opera Mobi\/([0-9]+)/), t = !!n && n[1], k = f.match(/MSIE ([0-9]+)/), k = !!k && k[1];
        l = !((-1 < m.indexOf("iPhone") || -1 < m.indexOf("iPad") || -1 < m.indexOf("iPod")) && e && 534 > e || d.operamini && "[object OperaMini]" === {}.toString.call(d.operamini) || n && 7458 > t || -1 < f.indexOf("Android") && e && 533 > e || h && 6 > h || "palmGetResource" in d && e && 534 > e || -1 < f.indexOf("MeeGo") && -1 < f.indexOf("NokiaBrowser/8.5.0") || k && 6 >= k)
    })(jQuery, window);
</script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= TEMPLATE_PATH ?>bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?= TEMPLATE_PATH ?>plugins/iCheck/icheck.min.js"></script>
<!-- Bootstrapped alerts -->
<script src="<?= CONTENT_PATH ?>alert/alerts.js"></script>

<script>

    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
