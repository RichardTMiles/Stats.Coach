<script>
    // Google
    let loadDeferredStyles = function () {
        let addStylesNode = document.getElementById("deferred-styles");
        let replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
    };
    let raf = requestAnimationFrame || mozRequestAnimationFrame ||
        webkitRequestAnimationFrame || msRequestAnimationFrame;
    if (raf) raf(function () {
        window.setTimeout(loadDeferredStyles, 0);
    });
    else window.addEventListener('load', loadDeferredStyles);

    // C6
    let JSLoaded = new Set();

    //-- JQuery -->
    loadJS("<?= SITE . TEMPLATE . 'bower_components/jquery/dist/jquery.min.js' ?>", () => {

        $.fn.CarbonJS = (sc, cb) => (!JSLoaded.has(sc) ? loadJS(sc, cb) : cb());

        //-- Jquery Form -->
        $.fn.CarbonJS('<?=  SITE . APP_VIEW . 'bower-asset/jquery-form/src/jquery.form.js'?>');

        //-- Bootstrap -->
        $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/bootstrap/dist/js/bootstrap.min.js' ?>", () =>
            //-- Slim Scroll -->
            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js' ?>", () =>

                //-- Fastclick -->
                $.fn.CarbonJS("<?= SITE . TEMPLATE . 'bower_components/fastclick/lib/fastclick.js' ?>", () =>
                    //-- Admin LTE -->
                    $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'dist/js/adminlte.min.js' ?>", () => {

                        $.fn.load_backStreach = (img, selector) =>
                            $.fn.CarbonJS("<?=  SITE . APP_VIEW . 'Layout/jquery.backstretch.js' ?>", () =>
                                $(selector).length ? $(selector).backstretch(img) : $.backstretch(img));


                        $.fn.CarbonJS("<?=  SITE . APP_VIEW . 'bower-asset/jquery-backstretch/jquery.backstretch.min.js' ?>", () => {
                            $.backstretch('<?=SITE . APP_VIEW?>Img/final.jpg');
                        });


                        //-- Select 2 -->
                        $.fn.load_select2 = (select2) =>
                            $.fn.CarbonJS("<?= SITE . TEMPLATE . 'bower_components/select2/dist/js/select2.full.min.js' ?>", () =>
                                $(select2).select2());

                        //-- Data tables -->
                        $.fn.load_datatables = (table) =>
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/datatables.net-bs/js/dataTables.bootstrap.js' ?>", () => {
                                try {
                                    return $(table).DataTable()
                                } catch (err) {
                                    return false
                                }
                            });

                        //-- iCheak -->
                        $.fn.load_iCheck = (input) => {
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'plugins/iCheck/icheck.min.js'?>", () => {
                                $(input).iCheck({
                                    checkboxClass: 'icheckbox_square-blue',
                                    radioClass: 'iradio_square-blue',
                                    increaseArea: '20%' // optional
                                });
                            });
                        };

                        //-- WYSIHTML5 -->
                        $.fn.load_wysihtml5 = (input) => {
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'?>", () => {
                                $(input).wysihtml5();
                            });
                        };

                        //-- Input Mask -->
                        $.fn.load_inputmask = (mask) =>
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'plugins/input-mask/jquery.inputmask.js' ?>", () => {
                                loadJS("<?=  SITE . TEMPLATE . 'plugins/input-mask/jquery.inputmask.date.extensions.js' ?>",
                                    () => $(mask).inputmask());
                                loadJS("<?=  SITE . TEMPLATE . 'plugins/input-mask/jquery.inputmask.extensions.js' ?>",
                                    () => $(mask).inputmask());
                            }, () => $(mask).inputmask());

                        //-- jQuery Knob -->
                        $.fn.load_knob = (knob) => {
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/jquery-knob/js/jquery.knob.js' ?>", () => {
                                $(knob).knob({
                                    draw: function () {
                                        // "tron" case
                                        if (this.$.data('skin') === 'tron') {

                                            let a = this.angle(this.cv)  // Angle
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
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'plugins/timepicker/bootstrap-timepicker.min.js' ?>", () => {
                                $(timepicker).timepicker({showInputs: false});
                            });
                        };

                        //--Bootstrap Datepicker -->
                        $.fn.load_datepicker = (datepicker) =>
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js' ?>", () =>
                                $(datepicker).datepicker({autoclose: true}));

                        //--Bootstrap Color Picker -->
                        $.fn.load_colorpicker = (colorpicker) =>
                            $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js' ?>", () =>
                                $(colorpicker).colorpicker());

                        //-- PJAX-->
                        $.fn.CarbonJS("<?=SITE . APP_VIEW?>AdminLTE/Demo/demo.js", () =>
                            $.fn.CarbonJS("<?=  SITE . APP_VIEW . 'bower-asset/jquery-pjax/jquery.pjax.js' ?>", () =>
                                $.fn.CarbonJS("<?=  SITE . APP_VIEW . 'bower-asset/mustache.js/mustache.js' ?>", () =>
                                    $.fn.CarbonJS("<?=  SITE . COMPOSER . 'richardtmiles/carbonphp/Helpers/Carbon.js'?>", () => {
                                        CarbonJS('#pjax-content', '', false)
                                        //-- Activate Left Sidebar Tree Menu
                                        $('.sidebar-menu').tree()
                                    }))))
                        // <!--?=($_SESSION['id'] ?? false) ? 'wss://stats.coach:8888/' : null?-->

                        //-- AJAX Pace -->
                        $.fn.CarbonJS("<?=  SITE . TEMPLATE . 'bower_components/PACE/pace.js' ?>", () => $(document).ajaxStart(() => Pace.restart()));

                    }))));


    });

    <!-- Global site tag (gtag.js) - Google Analytics -->
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-100885582-1');
</script>