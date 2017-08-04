// PJAX Forum Request
$(document).on('submit', 'form[data-pjax]', function (event) {
    $('#ajax-content').hide();
    $.pjax.submit(event, '#ajax-content')
});

// Set up Box Annotations
$(".box").boxWidget({
    animationSpeed: 500,
    collapseTrigger: '[data-widget="collapse"]',
    removeTrigger: '[data-widget="remove"]',
    collapseIcon: 'fa-minus',
    expandIcon: 'fa-plus',
    removeIcon: 'fa-times'
});


//-- iCheck -->
$('input').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' // optional
});


$('#my-box-widget').boxRefresh('load');

// Select 2 -->
$(".select2").select2();

// Data tables loadJS("<?= $this->versionControl( 'bower_components/datatables.net-bs/js/dataTables.bootstrap.js' ) ?>");-->

// Input Mask -->
$("[data-mask]").inputmask();  //Money Euro

// Bootstrap Datepicker -->
$('#datepicker').datepicker({autoclose: true});

//-- Bootstrap Time Picker -->
$('.timepicker').timepicker({showInputs: false});

//<!-- AdminLTE for demo purposes loadJS("<?= $this->versionControl( 'dist/js/demo.js' ) ?>");

//-- jQuery Knob -->
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


