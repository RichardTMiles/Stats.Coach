<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/10/17
 * Time: 1:53 AM
 */

global $access, $holes, $par, $tee_boxes, $teeBox, $handicap_number, $phone, $course_website, $pga_pro, $style, $name; ?>

<script>
    Carbon(() => {
        $.fn.load_datepicker('#datepicker');
        $.fn.load_knob('.knob');                // were pre loading
        $.fn.load_timepicker('.timepicker');
        $.fn.load_inputmask("[data-mask]");
        $.fn.load_select2(".select2");

        var Form = document.forms["addCourse"],
            fields = ["c_name", "c_access", "c_style", "c_street", "c_city", "c_state", 'tee_boxes', 'Handicap_number'],
            tee_boxes,
            Handicap_number,
            total_holes = 18,
            course_hc = [],
            tee_box_colors = [];


        // TODO - add event listeners to see if general form is altered

        $.fn.hasDuplicates = (array)=>{
            return (new Set(array)).size !== array.length;
        };

        $.fn.hideMeShow = (hide, show) =>{
            if ($.fn.isset(hide)) $('#' + hide).boxWidget('collapse');
            if ($.fn.isset(show)) document.getElementById(show).style.display = "block";
            if ($.fn.isset(show)) $('#' + show).boxWidget('expand');
        };

        /* validate course data <int> */

        $.fn.validateGeneral = ()=>{
            let Form = document.forms["addCourse"],
                fields = ["c_name", "c_access", "c_style", "c_street", "c_city", "c_state", 'tee_boxes', 'Handicap_number'],
                lengthF = fields.length, e = false;

            for (let i = 0; i < lengthF; i++)
                if (Form[fields[i]].value === null || Form[fields[i]].value === "") {
                    $("#" + fields[i]).removeClass("has-success").addClass("has-error");
                    e = true;
                } else $("#" + fields[i]).removeClass("has-error").addClass("has-success");

            if (e) return alert("Please Fill All Required Field");
            total_holes = Form[fields[2]].value;
            tee_boxes = Form[fields[6]].value;
            Handicap_number = Form[fields[7]].value;
            $.fn.new_tee_box_color_input();                                  // Generate new tee boxes
            return $.fn.hideMeShow('CourseInfo', 'Tee-Box-Color-Section');   // on success
        };

        $.fn.addColor = (number, color)=> {
            let input = 'tee_' + number + '_color'; // To ensure they at least search for it
            tee_box_colors[number - 1] = color;
            document.getElementById(input).disabled = false;
            document.getElementById(input).setAttribute('value', color);
            document.getElementById(input).setAttribute("style", "background-color: " + color + ";")
        };

        $.fn.new_tee_box_color_input = ()=>{
            let container = document.getElementById('teebox-color-selection'),
                node = document.createElement("DIV"), current_hole = 1;
            container.innerHTML = null;

            do {
                let html =
                    "<!-- Add Tee Box Selection --><div id='tee-box-color-" + current_hole + "' style='display:" + (current_hole === 1 ? 'block' : 'none') + ";'><div class='box box-success' id='Tee-Box-Color-Section' style='background-color: #2c3b41; color: ghostwhite !important;'><div class='box-header with-border' style='width: 100%; text-align: center'><h3 class='box-title' style='font-size: 200%; color: #ffffff;'>Tee Box Color Selection</h3> \<\
                            <div class=\"box-tools pull-right\"><button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-minus\"></i></button></div></div> \
                <div class='box-body' style='background-color: #2c3b41; color: ghostwhite !important;'> <h3>Tee Box " + current_hole + " Color</h3> \
                <div class='row col-lg-12 col-md-12'><div id='color-tee-box-selection' class='input-group input-group-lgsd'> \
                <div class='input-group-btn '><button type='button' class='btn dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>      Color Select    <span class='fa fa-caret-down'></span></button><ul class='dropdown-menu'> \
                <li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>Blue</a></li><li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>Black</a></li> \
                <li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>Green</a></li><li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>Gold</a></li> \
                <li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>Red</a></li><li><a onclick='$.fn.addColor(" + current_hole + ", this.innerHTML)'>White</a></li>";

                let h2 =
                    "</ul></div><input id='tee_" + current_hole + "_color' name='tee_" + current_hole + "_color' type='text' class='form-control' disabled></div></div><br><br><br><br><div class='row'><div class='col-md-6 col-sm-6'> \
                <div class='col-xs-6 col-md-6 text-center'><input name='general_difficulty_" + current_hole + "' type='text' class='knob' value='0' data-step='0.1' data-min='0' data-max='90' data-anglearc='250' data-angleoffset='145' data-width='90' data-height='90' data-fgcolor='#3c8dbc' style='width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(60, 141, 188); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;'></div> \
                <div class='col-xs-6 col-md-6 text-center'><input name='general_slope_" + current_hole + "' type='text' class='knob' value='0' data-min='0' data-max='200' data-anglearc='250' data-angleoffset='-35' data-width='90' data-height='90' data-fgcolor='#f56954' style='width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(245, 105, 84); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;'> \
                </div><div class='col-xs-12 col-md-12'><div class='knob-label' style='color: #fff;'>General<br>Difficulty / Slope</div></div></div> \
                <div class='col-md-6 col-sm-6'><div class='col-xs-6 col-md-6 text-center'><input name='women_difficulty_" + current_hole + "' type='text' class='knob' value='0' data-step='0.1' data-min='0' data-max='90' data-anglearc='250' data-angleoffset='145' data-width='90' data-height='90' data-fgcolor='#00a65a' style='width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;'></div> \
                <div class='col-xs-6 col-md-6 text-center'><input name='women_slope_" + current_hole + "'type='text' class='knob' value='0' data-min='0' data-max='200' data-anglearc='250' data-angleoffset='-35' data-width='90' data-height='90' data-fgcolor='#00c0ef' style='width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 192, 239); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;'> \
                </div><div class='col-xs-12 col-md-12'><div class='knob-label' style='color: #fff;;'>Women\'s<br>Difficulty / Slope</div></div></div></div><!-- ./col -->";

                let back = "<button type='button' class='btn btn-default' onclick=\"$.fn.hideMeShow('tee-box-color-" + current_hole + "','" + (current_hole == 1 ? 'CourseInfo' : ('tee-box-color-' + (current_hole - 1))) + "')\"><< Back</button>";
                let next = "<button type='button' class='btn btn-info pull-right' onclick='" + (current_hole !== 18 ? '$.fn.validate_tee_box_colors(\"' + current_hole + '\")' : null) + "'>Next >></button></div></div>";

                node.innerHTML = html + h2 + back + next;
                container.innerHTML += node.innerHTML;
            } while (current_hole++ < tee_boxes);

            $.fn.bootstrapAlert("Tee boxes difficulty and slope may be left at 0 if not set.", "info");

            $.fn.load_knob('.knob');

        };

        $.fn.validate_tee_box_colors = (current) => {
            if ($.fn.hasDuplicates(tee_box_colors) || tee_box_colors.length === 0) {
                return alert("Sorry, you must enter all colors with no duplicates.");
            }

            if (tee_box_colors.length !== tee_boxes && current !== tee_boxes) {
                return $.fn.HideMeShow("tee-box-color-" + current, "tee-box-color-" + (++current));
            }

            $.fn.distance_box_generate();
            $.fn.hideMeShow("tee-box-color-" + current, 'hole-1-distances');
        };

        $.fn.validateDistance = (current_hole)=>{
            let Form = document.forms["addCourse"], input;

            input = "par_" + current_hole;
            input = Form[input].value;
            if (input < 1 || input > 12)
                return alert("Sorry, your par should be between 1 and 12 inclusively.");

            for (let i = 0; i < tee_boxes; i++) {
                input = "tee_" + (i + 1) + "_" + current_hole;
                input = Form[input].value;
                if (input < 1 || input > 3000)
                    return alert("Sorry, your input for tee box " + (i + 1) + " should be between 1 and 3,000 inclusively.");
            }
            for (let i = 0; i < Handicap_number; i++) {
                input = "hc_" + (i + 1) + "_" + current_hole;
                course_hc[current_hole - 1] = input = Form[input].value;
                if (input < 1 || input > 18)
                    return alert("The handicap value(s) must be a valid integer between 1 and 18 inclusively.");
                if ($.fn.hasDuplicates(course_hc))
                    return alert("You have entered the same Handicap twice.")
            }
            $.fn.hideMeShow('hole-' + current_hole + '-distances', 'hole-' + (++current_hole) + '-distances');
        };

        $.fn.distance_box_generate =()=>{
            let container = document.getElementById('Tee_box_distances'),
                node = document.createElement("DIV"),
                current_hole = 1, box_color_input;
            container.innerHTML = null;
            do {
                box_color_input = document.createElement("DIV");

                // Tee box 1 , 2?
                for (let i = 0; i < tee_boxes; i++)
                    box_color_input.innerHTML +=
                        '<div class="col-xs-12 col-sm-6 col-md-3 text-center"><div style="display:inline;width:180px;height:180px;">' +
                        '<input name="tee_' + (i + 1) + '_' + current_hole + '" type="text" class="knob" value="1" data-min="1" data-max="1000" data-width="180" data-height="180" data-fgcolor="' + tee_box_colors[i] + '" ' +
                        'style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;"> ' +
                        '</div><div class="knob-label" style="color: white;">' + tee_box_colors[i] + '\'s Distance</div></div>';


                //'<input class="form-control input-lg" name="tee_' + (i + 1) + '_' + current_hole + '" min="1" max="3000" type="number" placeholder="' + tee_box_colors[i] + '\'s Hole #' + current_hole + ' Distance" style="text-align: center; border-color: ' + tee_box_colors[i] + '; display: inherit"><br>';

                // PAR
                box_color_input.innerHTML +=
                    '<div class="col-xs-12 col-sm-6 col-md-3 text-center centered"><div style="display:inline;width:180px;height:180px;">' +
                    '<input name="par_' + current_hole + '" type="text" class="knob" value="1" data-min="1" data-max="9" data-width="180" data-height="180" data-fgcolor="yellow" ' +
                    'style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;"> ' +
                    '</div><div class="knob-label" style="color: white;">Par ' + current_hole + '</div></div>';


                // handicap 1 ?
                for (let i = 0; i < Handicap_number; i++)
                    box_color_input.innerHTML +=
                        '<div class="col-xs-12 col-sm-6 col-md-3 text-center centered"><div style="display:inline;width:180px;height:180px;">' +
                        '<input name="hc_' + (i + 1) + '_' + current_hole + '" type="text" class="knob" value="1" data-min="1" data-max="18" data-width="180" data-height="180" data-fgcolor="teal" ' +
                        'style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px; background-image: none; font-style: normal; font-variant-caps: normal; font-weight: bold; font-size: 18px; line-height: normal; font-family: Arial; text-align: center; color: rgb(0, 166, 90); padding: 0px; -webkit-appearance: none; background-position: initial initial; background-repeat: initial initial;"> ' +
                        '</div><div class="knob-label" style="color: white;">Handicap ' + (i + 1) + '</div></div>';


                node.innerHTML = '<div id="hole-' + current_hole + '-distances" class="box box-success" style="background-color: #2c3b41; border-top-color: #2c3b41; display: none">'
                    + '<div class="box-header with-border" style="width: 100%; text-align: center"><h3 class="box-title" style="text-align: center; font-size: 200%; color: ghostwhite">HOLE #' + current_hole + '</h3>'
                    + '<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>'
                    + '</div>'
                    + '<div class="box-body" style="background-color: #2c3b41; border-top-color: #2c3b41;">'
                    + box_color_input.innerHTML
                    + '</div>'
                    + '<div class="box-footer" style="background-color: #2c3b41; border-top-color: #2c3b41;">'
                    + '<button type="button" class="btn btn-default" onclick="'
                    + (current_hole === 1 ? '$.fn.hideMeShow(\'hole-1-distances\',\'Tee-Box-Color-Section\')' : (current_hole !== 18 ? '$.fn.HideMeShow(\'hole-' + current_hole + '-distances\',\'hole-' + (current_hole - 1) + '-distances\')' : null ))
                    + '"><< Back</button>'
                    + '<button ' + (current_hole === 18 ? 'type="submit"' : 'type="button"')
                    + ' class="btn btn-info pull-right" onclick="'
                    + (current_hole !== 18 ? '$.fn.validateDistance(\'' + current_hole + '\')' : '')
                    + '">'
                    + (current_hole !== 18 ? 'Next >>' : 'Submit') + '</button></div></div>';
                container.innerHTML += node.innerHTML;
            } while (current_hole++ < total_holes);

            $.fn.load_datepicker('#datepicker');
            $.fn.load_knob('.knob');                // were pre loading
            $.fn.load_timepicker('.timepicker');
        }
    });
</script>


<!-- Content Header (Page header) -->
<section class="content-header" style="color: #d9edf7">
    <h1>Add Course</h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite; "><i class="fa fa-paper"></i>Add Course</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content" id="content-pane">
    <form data-pjax class="form-horizontal" method="post" action="<?= SITE ?>AddCourse/" name="addCourse">
        <div id="alert"></div>

        <!-- Add Course Main Info -->

        <div class="box box-custom" id='CourseInfo'>

            <div class="box-header with-border" style="width: 100%; text-align: center">
                <h3 class="box-title" style="font-size: 200%">New Course</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="box-body">
                <!-- text input -->
                <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 ">
                    <!--Course Name -->
                    <div class="form-group col-xs-12 col-md-12" id="c_name">
                        <label for="c_name">Course</label>
                        <input type="text" class="form-control " placeholder="Course Name" name="c_name"
                               value="<?= $name ?? '' ?>">
                    </div>
                    <!-- Type of Course -->
                    <div class="form-group col-xs-12 col-md-12" id="c_access">
                        <div class="col-md-6">
                            <label for="course_type">Access</label>
                            <select id="course_type" name="c_access" class="form-control select2" style="width: 100%;"
                                    onchange="">
                                <option value="Public" <?= $access === 'Public' ? 'selected' : '' ?>>
                                    Public
                                </option>
                                <option value="Resort" <?= $access === 'Resort' ? 'selected' : '' ?>>
                                    Resort
                                </option>
                                <option value="Semi-private" <?= $access === 'Semi-private' ? 'selected' : '' ?>>
                                    Semi-private
                                </option>
                                <option value="Private" <?= $access === 'Private' ? 'selected' : '' ?>>
                                    Private
                                </option>
                            </select>
                        </div>
                        <!-- Number of Holes -->
                        <div class="col-md-6">
                            <label for="" id="c_style">Holes</label>
                            <select name="c_style" id="course_play" class="form-control select2" style="width: 100%;"
                                    onchange="">
                                <option value="18" <?= $style === '18 Hole Standard' ? 'selected' : '' ?>>
                                    18 Hole Standard
                                </option>
                                <option value="9" <?= $style === '9 Hole Standard' ? 'selected' : '' ?>>
                                    9 Hole Standard
                                </option>
                            </select>
                        </div>
                    </div><!-- /.form-group -->
                    <!-- /.row  for Select options -->
                    <br> <!-- I need my personal space :P -->
                    <!-- Phone Number -->
                    <div class="form-group col-xs-12 col-md-12" id="c_phone">
                        <label>Phone Number
                            <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                        </label>
                        <div class="input-group col-xs-12 col-md-12">
                            <div class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <input value="<?= $phone ?? '' ?>" type="text" name="c_phone"
                                   id="phone" class="form-control"
                                   data-inputmask='"mask": "(999) 999-9999"' data-mask>
                        </div>
                        <!-- /.input group -->
                    </div>

                    <!-- Street City Address input -->

                    <div class="form-group col-xs-12 col-md-12" id="c_street">
                        <label class="control-label" for="Street">Street</label>
                        <input value="<?= $street ?? '' ?>" name="c_street" type="text"
                               class="form-control" id="inputSuccess"
                               placeholder="Street Address">
                    </div>

                    <div class="form-group col-xs-12 col-md-12" id="c_city">
                        <label class="control-label" for="City">City</label>
                        <input value="<?= $city ?? '' ?>" type="text" class="form-control"
                               id="City" placeholder="City"
                               name="c_city">
                    </div>

                    <div class="form-group col-xs-12 col-md-12" id="c_state">
                        <label for="state">State</label>
                        <select id="state" name="c_state" class="form-control select2" style="width: 100%;">
                            <option selected="selected" value="<?= $state ?? '' ?>">
                                <?= $state ?? 'State Selection' ?>
                            </option>
                            <option value="Alabama">Alabama</option>
                            <option value="Alaska">Alaska</option>
                            <option value="California">California</option>
                            <option value="Delaware">Delaware</option>
                            <option value="Tennessee">Tennessee</option>
                            <option value="Texas">Texas</option>
                            <option value="Washington">Washington</option>
                        </select>
                    </div><!-- /.form-group -->
                    <div class="form-group col-xs-12 col-md-12" id="tee_boxes">
                        <label>Number of Tee Boxes</label>
                        <select class="form-control" name="tee_boxes">
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                            <option value="4">Four</option>
                            <option value="5">Five</option>
                        </select>
                    </div><!-- /.form-group -->
                    <div class="form-group col-xs-12 col-md-12" id="Handicap_number">
                        <label>Handicap</label>
                        <select class="form-control" name="Handicap_number">
                            <option value="0">None</option>
                            <option value="1">One Listed</option>
                            <option value="2">Mens and Womens Listed</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-12 col-md-12" id="pga_professional">
                        <label class="control-label" for="pga_professional">Course PGA Professional
                            <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                        </label>
                        <input value="<?= $pga_pro ?>" type="text" class="form-control" placeholder="Course PRO"
                               name="pga_professional">

                    </div>
                    <div class="form-group col-xs-12 col-md-12" id="course_website">
                        <label class="control-label" for="course_website">Course Website
                            <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                        </label>
                        <input value="<?= $course_website ?>" type="text" class="form-control"
                               placeholder="Course Website"
                               name="course_website">
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-default">Reset</button>
                <!-- button -->
                <button class="btn btn-info pull-right" type="button" onclick="$.fn.validateGeneral()">Next >></button>
            </div>

        </div>

        <!-- Add Tee Box Selection -->
        <div id="teebox-color-selection" class="col-xs-12"></div>
        <!-- Tee box distances will be here -->
        <div id="Tee_box_distances" class="col-sm-12"></div>

    </form>


</section>