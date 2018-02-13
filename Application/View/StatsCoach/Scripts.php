<?php

use Carbon\View;

?>

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
    loadJS("<?= View::versionControl(TEMPLATE . 'bower_components/jquery/dist/jquery.min.js') ?>", () => {

        //-- Jquery Form -->
        loadJS('<?= View::versionControl(COMPOSER . 'bower-asset/jquery-form/src/jquery.form.js')?>');

        //-- Background Stretch -->
        loadJS("<?= View::versionControl(COMPOSER . 'bower-asset/jquery-backstretch/jquery.backstretch.min.js') ?>", () => {
            $.backstretch('<?=SITE?>Application/View/img/final.jpg');
        });

        //-- Slim Scroll -->
        loadJS("<?= View::versionControl(TEMPLATE . 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js') ?>");

        //-- Fastclick -->
        loadJS("<?= View::versionControl(TEMPLATE . 'bower_components/fastclick/lib/fastclick.js') ?>", () => {
            //-- Admin LTE -->
            loadJS("<?= View::versionControl(TEMPLATE . 'dist/js/adminlte.min.js') ?>");
        });

        //-- Bootstrap -->
        loadJS("<?= View::versionControl(TEMPLATE . 'bower_components/bootstrap/dist/js/bootstrap.min.js') ?>", () => {

            //-- AJAX Pace -->
            loadJS("<?= View::versionControl(TEMPLATE . 'bower_components/PACE/pace.js') ?>", () => $(document).ajaxStart(() => Pace.restart()));

            $.fn.CarbonJS = (sc, cb) => (!JSLoaded.has(sc) ? loadJS(sc, cb) : cb());

            //-- Select 2 -->
            $.fn.load_select2 = (select2) =>
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'bower_components/select2/dist/js/select2.full.min.js') ?>", () =>
                    $(select2).select2());

            //-- Data tables -->
            $.fn.load_datatables = (table) =>
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE .'bower_components/datatables.net-bs/js/dataTables.bootstrap.js') ?>", () => {
                    try { return $(table).DataTable() } catch (err) { return false }});

            //-- iCheak -->
            $.fn.load_iCheck = (input) => {
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'plugins/iCheck/icheck.min.js')?>", () => {
                    $(input).iCheck({
                        checkboxClass: 'icheckbox_square-blue', radioClass: 'iradio_square-blue', increaseArea: '20%' // optional
                    });
                });
            };

            //-- Input Mask -->
            $.fn.load_inputmask = (mask) =>
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.js') ?>", () => {
                    loadJS("<?= View::versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.date.extensions.js') ?>",
                        () => $(mask).inputmask());
                    loadJS("<?= View::versionControl(TEMPLATE . 'plugins/input-mask/jquery.inputmask.extensions.js') ?>",
                        () => $(mask).inputmask());
                }, () => $(mask).inputmask());

            //-- jQuery Knob -->
            $.fn.load_knob = (knob) => {
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'bower_components/jquery-knob/js/jquery.knob.js') ?>", () => {
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
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'plugins/timepicker/bootstrap-timepicker.min.js') ?>", () => {
                    $(timepicker).timepicker({showInputs: false});
                });
            };

            //--Bootstrap Datepicker -->
            $.fn.load_datepicker = (datepicker) =>
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js') ?>", () =>
                    $(datepicker).datepicker({autoclose: true}));

            //--Bootstrap Color Picker -->
            $.fn.load_colorpicker = (colorpicker) =>
                $.fn.CarbonJS("<?= View::versionControl(TEMPLATE . 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') ?>", () =>
                    $(colorpicker).colorpicker());

            //-- PJAX-->
            loadJS("<?= View::versionControl(COMPOSER . 'bower-asset/jquery-pjax/jquery.pjax.js') ?>", () =>
                loadJS("<?= View::versionControl(COMPOSER . 'bower-asset/mustache.js/mustache.js') ?>", () =>
                    loadJS("<?= View::versionControl(COMPOSER . 'richardtmiles/carbonphp/Helpers/Carbon.js')?>", () =>
                        CarbonJS('#pjax-content', '<?=($_SESSION['id']??false)?'wss://stats.coach:8888/':null?>', false))));

            //<!-- AdminLTE for demo purposes loadJS("<?= View::versionControl('dist/js/demo.js') ?>//");

        });
    });

    <!-- Global site tag (gtag.js) - Google Analytics -->
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-100885582-1');
</script>