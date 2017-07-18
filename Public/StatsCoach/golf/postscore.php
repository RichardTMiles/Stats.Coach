<?php

##########  Step 1.5, return list of courses from given state
if (!empty($this->courses)) {
    if (!$this->ajax) throw new \Exception();

    echo "<option value='' selected disabled>Course Select</option>";
    echo "<option value='Add'>Add Course</option>";

    if (is_array( $this->courses ) && $this->courses !== true) {
        foreach ($this->courses as $key) {
            echo "<option value='" . $key['course_id'] . "'>{$key['course_name']}</option>";
        }
    }
    exit (1);  // This will stop the run and just return the list, note if you used return. the values would be caught by the output buffer.
}
#### STEP 1
if (!$this->state) { ?>
    <script>

        var state = null;

        function course_given_states(select) {
            state = select.value;
            var courses = $("#course"); // container to be placed in

            courses.removeAttr("disabled", "disabled");     // To ensure they at least search for it

            $.ajax({  // Get a reduced list of all courses within a state
                url: (document.location + state + "/"), success: function (result) {
                    courses.html(result);
                }
            });
        }

        function box_colors_given_id(select) {
            var courseId = select.value;

            // Jump to a new page using Pjax
            if (courseId === "Add") {
                return $.pjax({
                    url: ('https://' + window.location.hostname + '/AddCourse/' + state + '/'),         // Redirect
                    container: '#ajax-content'
                });
            }

            $.pjax({
                url: ('https://' + window.location.hostname + '/PostScore/' + state + '/' + courseId + '/'),         // Redirect
                container: '#ajax-content'
            });

        }
    </script>


    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="color:#fff;">
            Post Score
            <small style="color: ghostwhite;">Course Select</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" style="color: ghostwhite; "><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active" style="color: ghostwhite;">Post Score</li>
        </ol>
    </section>

    <!-- Alerts -->
    <div id="alert"></div>

    <!-- Main content -->
    <section class="content">
        <!-- SELECT COURSE -->
        <div class="box box-custom" style="background-color: #2c3b41; border-top-color: #2c3b41; color: ghostwhite !important;">

            <div class="box-header">
                <h3 class="box-title" style="color: ghostwhite;">Where Was Your Round?</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>State</label>
                            <select class="form-control select2" style="width: 100%;" onchange="course_given_states(this)" required>
                                <option selected="selected" disabled value="">State</option>
                                <?php foreach ($this->states as $state) echo "<option value='$state'>$state</option>"; ?>
                            </select>
                        </div><!-- /.form-group -->

                        <div data-pjax class="form-group">
                            <label>Course</label>
                            <select id="course" class="form-control select2" <?= (!isset($this->courses) ? 'disabled="disabled"' : null); ?>
                                    onchange="box_colors_given_id(this)" style="width: 100%;">
                            </select>
                        </div><!-- /.form-group -->
                    </div><!-- /.col -->
                </div>
            </div>

        </div>

    </section><!-- /.content -->

    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
    </script>

    <?php return 1;
} elseif ($this->course_colors) {
#######################  STEP 2 , Course Tee Box #####################################?>
    <script>
        function startScoreCard(boxColor) {
            $.pjax({
                url: (window.location + boxColor + '/'),         // Redirect
                container: '#ajax-content'
            });
        }
    </script>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="color: #fff">
            <?= $this->golf->course->course_name ?>
            <small style="color: ghostwhite;">What tee box did you play from?</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" style="color: ghostwhite; "><i class="fa fa-dashboard"></i> Post Score</a></li>
            <li><a href="#" style="color: ghostwhite;"><?= $this->golf->course->state ?></a></li>
            <li class="active" style="color: ghostwhite;">Box Color</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php foreach ($this->course_colors as $value) {
                if (empty($value)) break; ?>
                <div class="col-lg-12 col-xs-12" onclick="startScoreCard('<?= $value ?>')">
                    <!-- small box -->
                    <div class="small-box bg-<?= (($color = strtolower($value)) == 'gold' ? 'yellow' : $color ) ?>">
                        <div class="inner">
                            <h3><?= $value ?><sup style="font-size: 12px">Tee Box</sup></h3>

                        </div>
                        <div class="icon">
                            <i class="fa fa-flag-o"></i>
                        </div>
                        <a class="small-box-footer">
                            Enter Score <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <?php return 1;
} elseif (is_object( $this->golf->course ) && is_object( $this->golf->distance )) {


#######################  STEP 3 , input  #####################################{ ?>

    <script>
        $(document).on('submit', 'form[data-pjax]',
            function (event) {
                event.preventDefault();
                document.getElementById('input-score-hole-18').style.display = "none";
                $.pjax.submit(event, '#ajax-content')
            });

        function next_score_input(current) {
            document.getElementById("input-score-hole-" + current++).style.display = "none";
            document.getElementById("input-score-hole-" + current).style.display = "block";
        }

        function last_score_input(current) {
            document.getElementById("input-score-hole-" + current--).style.display = "none";
            document.getElementById("input-score-hole-" + current).style.display = "block";
        }
    </script>

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="color: #fff">
            <?= $this->golf->course->course_name ?>
            <small style="color: ghostwhite;">Yo, what'd you shoot?</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" style="color: ghostwhite; "><i class="fa fa-dashboard"></i> Post Score</a></li>
            <li><a href="#" style="color: ghostwhite;"><?= $this->golf->course->state ?></a></li>
            <li style="color: ghostwhite;"><?= $this->golf->course->course_name ?></li>
            <li style="color: ghostwhite;"><?= $this->golf->distance->distance_color ?></li>
            <li style="color: ghostwhite;"> Score Input</li>
        </ol>
    </section>

    <section class="content">
        <form data-pjax class="form-horizontal" method="post"
              action="<?= SITE . 'PostScore/' . $this->golf->course->state . '/' . $this->golf->course->course_id . '/' . $this->golf->distance->distance_color . '/' ?>"
              name="addCourse">
            <div class="row" id="dateTime">
                <div class="col-xs-12">
                    <div class="box box-solid">
                        <div class="box-header">
                            <i class="fa fa-clock-o"></i>
                            <h3 class="box-title">Tee off time?</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date:</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input name="datepicker" type="text" class="form-control pull-right" id="datepicker" value="<?= date( 'm/d/Y', time() ) ?>">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- time Picker -->
                                        <div class="bootstrap-timepicker" style="color: #000">
                                            <div class="form-group">
                                                <label style="color: #fff">Time:</label>
                                                <div class="input-group">
                                                    <input name="timepicker" type="text" class="form-control timepicker"/>
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div><!-- /.input group -->
                                            </div><!-- /.form group -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php for ($i = 1; $i <= 18; $i++) { ?>
                <!-- row -->
                <div class="row" id="input-score-hole-<?= $i ?>" style="display: <?= ($i == 1 ? "block" : "none") ?>">
                    <div class="col-xs-12">
                        <!-- jQuery Knob -->
                        <div class="box box-solid">
                            <div class="box-header">
                                <i class="fa fa-bar-chart-o"></i>
                                <h3 class="box-title">Hole <?= $i ?> Stats</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 text-center">
                                        <div class="col-xs-12 col-sm-6 col-md-6 text-center">

                                            <div style="display:inline;width:200px;height:200px;">
                                                <input type="text" class="knob" value="<?= $this->golf->distance->distance[$i - 1] ?>" data-min="1"
                                                       data-max="<?= (ceil( $this->golf->distance->distance_tot / 18 ) + 400) ?>"
                                                       data-thickness="0.25" data-height="180" data-width="180"
                                                       data-fgcolor="#3c8dbc" data-readonly="true" readonly="readonly"
                                                       style="width: 100%; height: 100%; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(60, 141, 188); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;">
                                            </div>
                                            <div class="knob-label">Distance</div>

                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-6 text-center">

                                            <div style="display:inline;width:200px;height:200px;">
                                                <input type="text" class="knob" value="<?= $this->golf->course->course_par[$i - 1] ?>" data-min="1" data-max="9"
                                                       data-fgcolor="#f56954" data-readonly="true" readonly="readonly" data-height="180" data-width="180"
                                                       style="idth: 100%; height: 100%; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(245, 105, 84); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;">
                                            </div>
                                            <div class="knob-label">Par</div>
                                        </div>

                                    </div>
                                    <!-- ./col -->
                                    <div class="col-xs-12 col-sm-12 col-md-6 text-center">

                                        <div class="col-xs-12 col-sm-6 col-md-6  text-center">
                                            <div style="display:inline;width:200px;height:200px;">
                                                <input name="hole-<?= $i ?>" type="text" class="knob" value="1" data-min="1" data-max="9"
                                                       data-fgcolor="#00a65a" data-height="180" data-width="180"
                                                       style="width: 100%; height: 100%; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial; background-repeat: initial;">
                                            </div>
                                            <div class="knob-label">Strokes</div>


                                        </div>

                                        <div class="col-xs-12 col-sm-6 col-md-6 text-center">

                                            <div style="display:inline;width:200px;height:200px;">
                                                <input name="putts-<?= $i ?>" type="text" class="knob" value="1" data-min="1" data-max="8"
                                                       data-fgcolor="#00c0ef" data-height="180" data-width="180"
                                                       data-angleArc="250" data-angleoffset="-125"
                                                       style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;">
                                            </div>
                                            <div class="knob-label">Putts</div>

                                        </div>


                                    </div>
                                    <!-- ./col -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <br>
                                        <div class="col-xs-6 col-sm-6 col-md-6 text-center">
                                            <div class="checkbox">
                                                <label>
                                                    <input name="ffs-<?= $i ?>" type="checkbox" value="1"> Fairway on first shot
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6 text-center">
                                            <div class="checkbox">
                                                <label>
                                                    <input name="gnr-<?= $i ?>" type="checkbox" value="1"> Green in regulation
                                                </label>
                                            </div>
                                        </div>
                                        <br><br>
                                    </div>
                                    <!-- ./col -->
                                </div>
                                <!-- /.row -->

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <?php if ($i != 1) { ?>
                                    <button type="button" class="btn btn-default" onclick="last_score_input('<?= $i ?>')"><< Back</button>
                                <?php } ?>
                                <!-- button -->
                                <button class="btn btn-info pull-right" type="<?= ($i != 18 ? "button" : "submit") ?>"
                                        onclick="<?= ($i != 18 ? "next_score_input('$i')" : null) ?>">
                                    Next &gt;&gt;</button>
                            </div>
                            <!-- /.box-footer -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            <?php } ?>
        </form>
    </section>

    <script>
        $(function () {
            //Date picker
            $('#datepicker').datepicker({autoclose: true});
            //Timepicker
            $('.timepicker').timepicker({showInputs: false});

        });
    </script>

    <script>
        $(function () {
            /* jQueryKnob */

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
    </script>

    <?php return 1;
}
