//-- Bootstrap -->
loadJS("<?= $this->versionControl( 'bower_components/bootstrap/dist/js/bootstrap.min.js' ) ?>", function () {
    //<!-- Slim Scroll -->
    loadJS("<?= $this->versionControl( 'bower_components/jquery-slimscroll/jquery.slimscroll.min.js' ) ?>");
    //<!-- Fastclick -->
    loadJS("<?= $this->versionControl( 'bower_components/fastclick/lib/fastclick.js' ) ?>");
    //<!-- AJAX Pace -->
    loadJS("<?= $this->versionControl( 'bower_components/PACE/pace.js' ) ?>");
    //<!-- Admin LTE -->
    loadJS("<?= $this->versionControl( 'dist/js/adminlte.min.js' ) ?>");
    //<!-- iCheck -->
    loadJS("<?= $this->versionControl('plugins/iCheck/icheck.min.js')?>", function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

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


//<!-- Stats Coach Bootstrap Alert -->
loadJS("<?= $this->versionControl( 'alert/alerts.js' ) ?>");
$(document).on('submit', 'form[data-pjax]', function (event) {
    $(event.target).closest('box').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.pjax.submit(event, '#ajax-content')
});

//<!-- Select 2 -->
loadJS("<?= $this->versionControl( 'bower_components/select2/dist/js/select2.full.min.js' ) ?>", function () {
    $(".select2").select2();
});
// Data tables loadJS("<?= $this->versionControl( 'bower_components/datatables.net-bs/js/dataTables.bootstrap.js' ) ?>");-->
// <!-- Input Mask -->
loadJS("<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.js' ) ?>", function () {
    loadJS("<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.date.extensions.js' ) ?>");
    loadJS("<?= $this->versionControl( 'plugins/input-mask/jquery.inputmask.extensions.js' ) ?>");
    $("[data-mask]").inputmask();  //Money Euro

});


//<!-- iCheck -->
loadJS("<?= $this->versionControl( 'plugins/iCheck/icheck.min.js' ) ?>");
<!-- bootstrap datepicker -->
loadJS("<?= $this->versionControl( 'bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js' ) ?>", function () {
    $('#datepicker').datepicker({autoclose: true});
});
//<!-- bootstrap color picker -->
loadJS("<?= $this->versionControl( 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js' ) ?>");
//<!-- bootstrap time picker -->
loadJS("<?= $this->versionControl( 'plugins/timepicker/bootstrap-timepicker.min.js' ) ?>", function () {
    $('.timepicker').timepicker({showInputs: false});
});
//<!-- Google -->
loadJS("<?= $this->versionControl( 'Public/Analytics/google.analytics.js' ) ?>");
//<!-- AdminLTE for demo purposes loadJS("<?= $this->versionControl( 'dist/js/demo.js' ) ?>");
//<!-- jQuery Knob -->
loadJS("<?= $this->versionControl( 'bower_components/jquery-knob/js/jquery.knob.js' ) ?>", function () {


    $(".knob").knob({
        /*change : function (value) {
         //console.log("change : " + value);
         },
         release : function (value) {
         console.log("release : " + value);
         },
         cancel : function () {
         console.log("cancel : " + this.value);
         },*/
        draw: function () {

            // "tron" case
            if (this.$.data('skin') == 'tron') {

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
    /* END JQUERY KNOB */
});

