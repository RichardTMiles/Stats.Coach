<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 3/8/17
 * Time: 4:15 PM
 */

// This is the return for the ajax call below
if ($this->ajax) {
    // This assumes the page is already called, and an inner AJAX is called

}
?>

<script>

    function fancyColors(self) {

        // If none, remove
        if (self.options[self.selectedIndex].value == 'none') {
            self = self.closest('TR');
            self = self.style.visibility = "hidden";
            return;
        }

        var b = self.closest('td');
        // hackish
        b.style.backgroundColor = self.options[self.selectedIndex].value;
        b.style.opacity = '.5';

        showSibling(b.closest('tr'));
    }

    function showSibling(self) {

        if (self.nodeName != 'TR') {    // for a request that didnt come from fancy colors
            if (self.options[self.selectedIndex].value == 'none') {
                while (self.nodeName != 'TR') {
                    self = self.parentElement;
                }
                self = self.style.visibility = "none";
                return;
            }
        }

        while (self.nodeName != 'TR') {
            self = self.parentElement;
        } do {
            self = self.nextSibling;
        } while (self.nodeName != 'TR');

        self.style.display = '';
    }

    function HideMeShow(hide, show) {
        document.getElementById(hide).style.display = "none";
        document.getElementById(show).style.display = "block";
    }

    /* validate course information */
    function validateP2() {
        e = false;
        var Form = document.forms["addCourse"], j;
        for (var i = 1; i < 19; i++) {
            if (Form["par_"+i].value == null || Form["par_"+i].value == "") {
                $("#par_"+i).css("background-color", "aqua"); e = true;
                alert("par_"+i);
            } else $("#par_"+i).css("background-color", "green");

            if (i<5) {
                if (Form["tee_"+i+"_color"].value != null && Form["tee_"+i+"_color"].value != "" && Form["tee_"+i+"_color"].value != "none") {
                    for (j = 1; j < 19; j++) {
                        if (Form["tee_" + i + "_" + j].value == null || Form["tee_" + i + "_" + j].value == "") {
                            $("#tee_" + i + "_" + j).css("background-color", "aqua");
                            e = true; alert("tee_" + i + "_" + j);
                        } else $("#tee_" + i + "_" + j).css("background-color", "green");
                    }
                }

                if ( i < 3) {
                    // TODO - HC 1 type is invalid
                    if (Form["hc_"+i+"_type"].value != null && Form["hc_"+i+"_type"].value != "" && Form["hc_"+i+"_type"].value != "none") {
                        for (j = 1; j < 19; j++) {
                            if (Form["hc_" + i + "_" + j].value == null || Form["hc_" + i + "_" + j].value == "") {
                                $("#hc_" + i + "_" + j).css("background-color", "aqua");
                                e = true;alert("hc_" + i + "_" + j);
                            } else $("#hc_" + i + "_" + j).css("background-color", "green");
                        }
                    }
                }
            }
        }
        if (e) {
            alert("Please Fill All Required Field");
            return false;
        } return true;
    }

    /* validate course data <int> */
    function validateP1() {

        var Form = document.forms["addCourse"];
        var fields = ["c_name", "c_access", "c_style", "c_street", "c_city", "c_state"],
            lengthF = fields.length, e = false;
        for (var i = 0; i < lengthF; i++) {
            if (Form[fields[i]].value == null || Form[fields[i]].value == "") {
                $("#" + fields[i]).removeClass("has-success").addClass("has-error");
                e = true;
            } else $("#" + fields[i]).removeClass("has-error").addClass("has-success");
        }
        if (e) {
            alert("Please Fill All Required Field");
            return false;
        } HideMeShow('CourseInfo', 'ScoreCard'); // on success
        return true;
    }

    // This works don't change
    $(document).on('submit', 'form[data-pjax]', function (event) {
        event.preventDefault();
        if (validateP2()) $.pjax.submit(event, '#ajax-content') });

    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        //Money Euro
        $("[data-mask]").inputmask();
    });

</script>


<!-- Content Header (Page header) -->
<section class="content-header" style="color: #d9edf7">
    <h1>Add Course
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite; "><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#" style="color: ghostwhite;">Forms</a></li>
        <li class="active" style="color: ghostwhite;">Advanced Elements</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Its a big form -->

    <form data-pjax class="form-horizontal" method="post" action="<?=SITE_ROOT?>AddCourse/" name="addCourse">

        <!-- Add Course Main Info -->
        <div class="box box-custom" id='CourseInfo' style="display: block; background-color: #2c3b41; border-top-color: #2c3b41;">

            <div class="box-body">
                <div class="row">
                    <div class="box-body" style="color: ghostwhite">

                        <!-- I'm a dumb ass... Form Data -->
                        <!-- text input -->
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 ">

                                <!--Course Name -->
                                <div class="form-group" id="c_name">
                                    <label>Course</label>
                                    <input type="text" class="form-control" placeholder="Course Name" name="c_name" value="<?=(isset($this->name)?$this->name:"")?>">
                                </div>
                                <!-- Type  &&  Style -->
                                <div class="row">
                                    <!-- Type of Course -->


                                    <!--  var fields = ["c_name", "c_access", "c_style", "c_phone", "c_street", "c_city", "c_state"],
                                        <i class="fa fa-check"></i> -->

                                    <div class="form-group" id="c_access">
                                        <div class="col-md-6">
                                            <label for="course_type">Access</label>
                                            <select id="course_type" name="c_access" class="form-control select2" style="width: 100%;" onchange="">
                                                <option value="Public" <?=(isset($this->access) && $this->access == "Public" ? "selected":"")?>>Public</option>
                                                <option value="Resort" <?=(isset($this->access) && $this->access == "Resort" ? "selected":"")?>>Resort</option>
                                                <option value="Semi-private" <?=(isset($this->access) && $this->access == "Semi-private" ? "selected":"")?>>Semi-private</option>
                                                <option value="Private" <?=(isset($this->access) && $this->access == "Private" ? "selected":"")?>>Private</option>
                                            </select>
                                        </div>


                                        <!-- Number of Holes -->

                                        <div class="col-md-6">
                                            <label for="" id="c_style">Style</label>
                                            <select name="c_style" id="course_play" class="form-control select2" style="width: 100%;" onchange="" >
                                                <option value="18-hole" <?=(isset($this->style) && $this->style == "18 Hole Standard" ? "selected":"")?>>18 Hole Standard</option>
                                                <option value="9-hole" <?=(isset($this->style) && $this->style == "9 Hole Standard" ? "selected":"")?>>9 Hole Standard</option>
                                                <option value="Executive" <?=(isset($this->style) && $this->style == "Executive" ? "selected":"")?>>Executive</option>
                                                <option value="Approach" <?=(isset($this->style) && $this->style == "Approach" ? "selected":"")?>>Approach</option>
                                            </select>
                                        </div>
                                    </div><!-- /.form-group -->
                                </div>
                                <!-- /.row  for Select options -->

                                <br> <!-- I need my personal space :P -->

                                <!-- Phone Number -->
                                <div class="form-group" id="c_phone">
                                    <label>Phone Number
                                        <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <input value="<?=(isset($this->phone) ? $this->phone : "")?>" type="text" name="c_phone" id="phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                                    </div>
                                    <!-- /.input group -->
                                </div>


                                <!-- Street City Address input -->
                                <div class="form-group" id="c_street">
                                    <label class="control-label" for="Street">Street</label>
                                    <input value="<?=(isset($this->street) ? $this->street : "")?>" name="c_street" type="text" class="form-control" id="inputSuccess" placeholder="Street Address">
                                </div>

                                <div class="form-group" id="c_city">
                                    <label class="control-label" for="City">City</label>
                                    <input value="<?=(isset($this->city) ? $this->city : "")?>" type="text" class="form-control" id="City" placeholder="City" name="c_city">
                                </div>

                                <div class="form-group" id="c_state">
                                    <label for="state">State</label>
                                    <select id="state" name="c_state" class="form-control select2" style="width: 100%;" onclick="Courses()">
                                        <option selected="selected" value="<?=(isset($this->state) ? $this->state : "")?>">
                                            <?=(isset($this->state) ? $this->state : "State Selection")?>
                                        </option>
                                        <option value="Alabama" >Alabama</option>
                                        <option value="Alaska">Alaska</option>
                                        <option value="California">California</option>
                                        <option value="Delaware">Delaware</option>
                                        <option value="Tennessee">Tennessee</option>
                                        <option value="Texas">Texas</option>
                                        <option value="Washington">Washington</option>
                                    </select>
                                </div><!-- /.form-group -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer" style="background-color: #2c3b41; color: ghostwhite !important;"><a href="<?= SITE_ROOT ?>">
                    <button type="button" class="btn btn-default">Cancel</button>
                </a>
                <button type="button" class="btn btn-info pull-right" onclick="validateP1()">Next >></button>
            </div>

        </div>
        <!-- Add Score Card Information -->
        <div class="box box-custom" id='ScoreCard' style="display: none; background-color: #2c3b41; border-top-color: #2c3b41; color: ghostwhite !important;">
            <div class="box-header">
                <h3 class="box-title" style="color: ghostwhite;">Course Statistics</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <br>
                <!-- /.box-header -->
                <div class="box-body" style="overflow-y: scroll !important; background-color: white; color: #080808; border-radius: 15px;">
                    <table class="table table-bordered" style="font-size: large">
                        <tbody>

                        <!-- The Main TITLES -->
                        <tr style="background-color: #00a65a;">
                            <th style="min-width: 90px;">Hole</th>
                            <th style="min-width: 60px;">1</th>
                            <th style="min-width: 60px;">2</th>
                            <th style="min-width: 60px;">3</th>
                            <th style="min-width: 60px;">4</th>
                            <th style="min-width: 60px;">5</th>
                            <th style="min-width: 60px;">6</th>
                            <th style="min-width: 60px;">7</th>
                            <th style="min-width: 60px;">8</th>
                            <th style="min-width: 60px;">9</th>
                            <th style="min-width: 60px;">10</th>
                            <th style="min-width: 60px;">11</th>
                            <th style="min-width: 60px;">12</th>
                            <th style="min-width: 60px;">13</th>
                            <th style="min-width: 60px;">14</th>
                            <th style="min-width: 60px;">15</th>
                            <th style="min-width: 60px;">16</th>
                            <th style="min-width: 60px;">17</th>
                            <th style="min-width: 60px;">18</th>
                        </tr>

                        <!-- Par -->
                        <tr style="background-color: ghostwhite;">
                            <td style="text-align: center;">
                                PAR
                            </td>
                            <td><input value="<?=(isset($this->par1) ? $this->par1 : "")?>" type="number" name="par_1" min="1" max="1000" class="" id="par_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par2) ? $this->par2 : "")?>" type="number" name="par_2" min="1" max="1000" class="" id="par_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par3) ? $this->par3 : "")?>" type="number" name="par_3" min="1" max="1000" class="" id="par_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par4) ? $this->par4 : "")?>" type="number" name="par_4" min="1" max="1000" class="" id="par_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par5) ? $this->par5 : "")?>" type="number" name="par_5" min="1" max="1000" class="" id="par_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par6) ? $this->par6 : "")?>" type="number" name="par_6" min="1" max="1000" class="" id="par_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par7) ? $this->par7 : "")?>" type="number" name="par_7" min="1" max="1000" class="" id="par_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par8) ? $this->par8 : "")?>" type="number" name="par_8" min="1" max="1000" class="" id="par_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par9) ? $this->par9 : "")?>" type="number" name="par_9" min="1" max="1000" class="" id="par_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par10) ? $this->par10 : "")?>" type="number" name="par_10" min="1" max="1000" class="" id="par_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par11) ? $this->par11 : "")?>" type="number" name="par_11" min="1" max="1000" class="" id="par_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par12) ? $this->par12 : "")?>" type="number" name="par_12" min="1" max="1000" class="" id="par_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par13) ? $this->par13 : "")?>" type="number" name="par_13" min="1" max="1000" class="" id="par_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par14) ? $this->par14 : "")?>" type="number" name="par_14" min="1" max="1000" class="" id="par_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par15) ? $this->par15 : "")?>" type="number" name="par_15" min="1" max="1000" class="" id="par_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par16) ? $this->par16 : "")?>" type="number" name="par_16" min="1" max="1000" class="" id="par_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par17) ? $this->par17 : "")?>" type="number" name="par_17" min="1" max="1000" class="" id="par_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->par18) ? $this->par18 : "")?>" type="number" name="par_18" min="1" max="1000" class="" id="par_18" placeholder="#"></td>
                        </tr>


                        <!-- Tee 1-->
                        <tr>
                            <td>
                                <select class="form-control select2" style="width: 100%;" name="tee_1_color" id="tee_1_color" onchange="fancyColors(this)">
                                    <option selected="selected" value="<?=(isset($this->tee_1_color) ? $this->tee_1_color : "none");?>">
                                                <?=(isset($this->tee_1_color) ? $this->tee_1_color : "Add Tee Box")?></option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td><input value="<?=(isset($this->teeBox1[0]) ? $this->teeBox1[0] : "")?>" type="number" name="tee_1_1" min="50" max="2000" class="" id="tee_1_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[1]) ? $this->teeBox1[1] : "")?>" type="number" name="tee_1_2" min="50" max="2000" class="" id="tee_1_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[2]) ? $this->teeBox1[2] : "")?>" type="number" name="tee_1_3" min="50" max="2000" class="" id="tee_1_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[3]) ? $this->teeBox1[3] : "")?>" type="number" name="tee_1_4" min="50" max="2000" class="" id="tee_1_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[4]) ? $this->teeBox1[4] : "")?>" type="number" name="tee_1_5" min="50" max="2000" class="" id="tee_1_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[5]) ? $this->teeBox1[5] : "")?>" type="number" name="tee_1_6" min="50" max="2000" class="" id="tee_1_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[6]) ? $this->teeBox1[6] : "")?>" type="number" name="tee_1_7" min="50" max="2000" class="" id="tee_1_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[7]) ? $this->teeBox1[7] : "")?>" type="number" name="tee_1_8" min="50" max="2000" class="" id="tee_1_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[8]) ? $this->teeBox1[8] : "")?>" type="number" name="tee_1_9" min="50" max="2000" class="" id="tee_1_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[9]) ? $this->teeBox1[9] : "")?>" type="number" name="tee_1_10" min="50" max="2000" class="" id="tee_1_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[10]) ? $this->teeBox1[10] : "")?>" type="number" name="tee_1_11" min="50" max="2000" class="" id="tee_1_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[11]) ? $this->teeBox1[11] : "")?>" type="number" name="tee_1_12" min="50" max="2000" class="" id="tee_1_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[12]) ? $this->teeBox1[12] : "")?>" type="number" name="tee_1_13" min="50" max="2000" class="" id="tee_1_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[13]) ? $this->teeBox1[13] : "")?>" type="number" name="tee_1_14" min="50" max="2000" class="" id="tee_1_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[14]) ? $this->teeBox1[14] : "")?>" type="number" name="tee_1_15" min="50" max="2000" class="" id="tee_1_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[15]) ? $this->teeBox1[15] : "")?>" type="number" name="tee_1_16" min="50" max="2000" class="" id="tee_1_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[16]) ? $this->teeBox1[16] : "")?>" type="number" name="tee_1_17" min="50" max="2000" class="" id="tee_1_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox1[17]) ? $this->teeBox1[17] : "")?>" type="number" name="tee_1_18" min="50" max="2000" class="" id="tee_1_18" placeholder="#"></td>
                        </tr>

                        <!-- Tee 2 -->
                        <tr style="display: none;">
                            <td><select class="form-control select2" style="width: 100%;" name="tee_2_color" id="tee_2_color" onchange="fancyColors(this)" onload="fancyColors(this)">
                                    <option selected="selected" value="<?=(isset($this->tee_2_color) ? $this->tee_2_color : "none");?>">
                                        <?=(isset($this->tee_2_color) ? $this->tee_2_color : "Add Tee Box")?></option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select></td>
                            <td><input value="<?=(isset($this->teeBox2[0]) ? $this->teeBox2[0] : "")?>" type="number" name="tee_2_1" min="50" max="2000" class="" id="tee_2_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[1]) ? $this->teeBox2[1] : "")?>" type="number" name="tee_2_2" min="50" max="2000" class="" id="tee_2_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[2]) ? $this->teeBox2[2] : "")?>" type="number" name="tee_2_3" min="50" max="2000" class="" id="tee_2_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[3]) ? $this->teeBox2[3] : "")?>" type="number" name="tee_2_4" min="50" max="2000" class="" id="tee_2_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[4]) ? $this->teeBox2[4] : "")?>" type="number" name="tee_2_5" min="50" max="2000" class="" id="tee_2_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[5]) ? $this->teeBox2[5] : "")?>" type="number" name="tee_2_6" min="50" max="2000" class="" id="tee_2_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[6]) ? $this->teeBox2[6] : "")?>" type="number" name="tee_2_7" min="50" max="2000" class="" id="tee_2_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[7]) ? $this->teeBox2[7] : "")?>" type="number" name="tee_2_8" min="50" max="2000" class="" id="tee_2_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[8]) ? $this->teeBox2[8] : "")?>" type="number" name="tee_2_9" min="50" max="2000" class="" id="tee_2_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[9]) ? $this->teeBox2[9] : "")?>" type="number" name="tee_2_10" min="50" max="2000" class="" id="tee_2_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[10]) ? $this->teeBox2[10] : "")?>" type="number" name="tee_2_11" min="50" max="2000" class="" id="tee_2_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[11]) ? $this->teeBox2[11] : "")?>" type="number" name="tee_2_12" min="50" max="2000" class="" id="tee_2_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[12]) ? $this->teeBox2[12] : "")?>" type="number" name="tee_2_13" min="50" max="2000" class="" id="tee_2_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[13]) ? $this->teeBox2[13] : "")?>" type="number" name="tee_2_14" min="50" max="2000" class="" id="tee_2_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[14]) ? $this->teeBox2[14] : "")?>" type="number" name="tee_2_15" min="50" max="2000" class="" id="tee_2_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[15]) ? $this->teeBox2[15] : "")?>" type="number" name="tee_2_16" min="50" max="2000" class="" id="tee_2_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[16]) ? $this->teeBox2[16] : "")?>" type="number" name="tee_2_17" min="50" max="2000" class="" id="tee_2_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox2[17]) ? $this->teeBox2[17] : "")?>" type="number" name="tee_2_18" min="50" max="2000" class="" id="tee_2_18" placeholder="#"></td>
                        </tr>

                        <!-- Tee 3 -->
                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" name="tee_3_color" id="tee_3_color" style="width: 100%;" onchange="fancyColors(this)">
                                    <option selected="selected" value="<?=(isset($this->tee_3_color) ? $this->tee_1_color : "none");?>">
                                        <?=(isset($this->tee_3_color) ? $this->tee_3_color : "Add Tee Box")?></option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td><input value="<?=(isset($this->teeBox3[1]) ? $this->teeBox3[1] : "")?>" type="number" name="tee_3_1" min="50" max="1000" class="" id="tee_3_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[2]) ? $this->teeBox3[2] : "")?>" type="number" name="tee_3_2" min="50" max="1000" class="" id="tee_3_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[3]) ? $this->teeBox3[3] : "")?>" type="number" name="tee_3_3" min="50" max="1000" class="" id="tee_3_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[4]) ? $this->teeBox3[4] : "")?>" type="number" name="tee_3_4" min="50" max="1000" class="" id="tee_3_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[5]) ? $this->teeBox3[5] : "")?>" type="number" name="tee_3_5" min="50" max="1000" class="" id="tee_3_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[6]) ? $this->teeBox3[6] : "")?>" type="number" name="tee_3_6" min="50" max="1000" class="" id="tee_3_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[7]) ? $this->teeBox3[7] : "")?>" type="number" name="tee_3_7" min="50" max="1000" class="" id="tee_3_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[8]) ? $this->teeBox3[8] : "")?>" type="number" name="tee_3_8" min="50" max="1000" class="" id="tee_3_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[9]) ? $this->teeBox3[9] : "")?>" type="number" name="tee_3_9" min="50" max="1000" class="" id="tee_3_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[10]) ? $this->teeBox3[10] : "")?>" type="number" name="tee_3_10" min="50" max="1000" class="" id="tee_3_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[11]) ? $this->teeBox3[11] : "")?>" type="number" name="tee_3_11" min="50" max="1000" class="" id="tee_3_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[12]) ? $this->teeBox3[12] : "")?>" type="number" name="tee_3_12" min="50" max="1000" class="" id="tee_3_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[13]) ? $this->teeBox3[13] : "")?>" type="number" name="tee_3_13" min="50" max="1000" class="" id="tee_3_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[14]) ? $this->teeBox3[14] : "")?>" type="number" name="tee_3_14" min="50" max="1000" class="" id="tee_3_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[15]) ? $this->teeBox3[15] : "")?>" type="number" name="tee_3_15" min="50" max="1000" class="" id="tee_3_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[16]) ? $this->teeBox3[16] : "")?>" type="number" name="tee_3_16" min="50" max="1000" class="" id="tee_3_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[17]) ? $this->teeBox3[17] : "")?>" type="number" name="tee_3_17" min="50" max="1000" class="" id="tee_3_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox3[18]) ? $this->teeBox3[18] : "")?>" type="number" name="tee_3_18" min="50" max="1000" class="" id="tee_3_18" placeholder="#"></td>
                        </tr>

                        <!-- tee 4 -->
                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" style="width: 100%;" name="tee_4_color" id="tee_4_color" onchange="fancyColors(this)">
                                    <option selected="selected" value="<?=(isset($this->tee_4_color) ? $this->tee_4_color : "none");?>">
                                        <?=(isset($this->tee_4_color) ? $this->tee_4_color : "Add Tee Box")?></option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td><input value="<?=(isset($this->teeBox4[1]) ? $this->teeBox4[1] : "")?>" type="number" name="tee_4_1" min="50" max="2000" class="" id="tee_4_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[2]) ? $this->teeBox4[2] : "")?>" type="number" name="tee_4_2" min="50" max="2000" class="" id="tee_4_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[3]) ? $this->teeBox4[3] : "")?>" type="number" name="tee_4_3" min="50" max="2000" class="" id="tee_4_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[4]) ? $this->teeBox4[4] : "")?>" type="number" name="tee_4_4" min="50" max="2000" class="" id="tee_4_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[5]) ? $this->teeBox4[5] : "")?>" type="number" name="tee_4_5" min="50" max="2000" class="" id="tee_4_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[6]) ? $this->teeBox4[6] : "")?>" type="number" name="tee_4_6" min="50" max="2000" class="" id="tee_4_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[7]) ? $this->teeBox4[7] : "")?>" type="number" name="tee_4_7" min="50" max="2000" class="" id="tee_4_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[8]) ? $this->teeBox4[8] : "")?>" type="number" name="tee_4_8" min="50" max="2000" class="" id="tee_4_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[9]) ? $this->teeBox4[9] : "")?>" type="number" name="tee_4_9" min="50" max="2000" class="" id="tee_4_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[10]) ? $this->teeBox4[10] : "")?>" type="number" name="tee_4_10" min="50" max="2000" class="" id="tee_4_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[11]) ? $this->teeBox4[11] : "")?>" type="number" name="tee_4_11" min="50" max="2000" class="" id="tee_4_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[12]) ? $this->teeBox4[12] : "")?>" type="number" name="tee_4_12" min="50" max="2000" class="" id="tee_4_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[13]) ? $this->teeBox4[13] : "")?>" type="number" name="tee_4_13" min="50" max="2000" class="" id="tee_4_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[14]) ? $this->teeBox4[14] : "")?>" type="number" name="tee_4_14" min="50" max="2000" class="" id="tee_4_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[15]) ? $this->teeBox4[15] : "")?>" type="number" name="tee_4_15" min="50" max="2000" class="" id="tee_4_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[16]) ? $this->teeBox4[16] : "")?>" type="number" name="tee_4_16" min="50" max="2000" class="" id="tee_4_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[17]) ? $this->teeBox4[17] : "")?>" type="number" name="tee_4_17" min="50" max="2000" class="" id="tee_4_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox4[18]) ? $this->teeBox4[18] : "")?>" type="number" name="tee_4_18" min="50" max="2000" class="" id="tee_4_18" placeholder="#"></td>
                            </td>
                        </tr>

                        <!-- tee 5 -->
                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" style="width: 100%;"  name="tee_5_color" id="tee_5_color" onchange="fancyColors(this)">
                                    <option selected="selected" value="<?=(isset($this->tee_5_color) ? $this->tee_1_color : "none");?>">
                                        <?=(isset($this->tee_5_color) ? $this->tee_5_color : "Add Tee Box")?></option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select></td>
                            <td><input value="<?=(isset($this->teeBox5[1]) ? $this->teeBox5[1] : "")?>" type="number" name="tee_5_1" min="13" max="1000" class="" id="tee_5_1" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[2]) ? $this->teeBox5[2] : "")?>" type="number" name="tee_5_2" min="13" max="1000" class="" id="tee_5_2" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[3]) ? $this->teeBox5[3] : "")?>" type="number" name="tee_5_3" min="13" max="1000" class="" id="tee_5_3" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[4]) ? $this->teeBox5[4] : "")?>" type="number" name="tee_5_4" min="13" max="1000" class="" id="tee_5_4" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[5]) ? $this->teeBox5[5] : "")?>" type="number" name="tee_5_5" min="13" max="1000" class="" id="tee_5_5" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[6]) ? $this->teeBox5[6] : "")?>" type="number" name="tee_5_6" min="13" max="1000" class="" id="tee_5_6" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[7]) ? $this->teeBox5[7] : "")?>" type="number" name="tee_5_7" min="13" max="1000" class="" id="tee_5_7" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[8]) ? $this->teeBox5[8] : "")?>" type="number" name="tee_5_8" min="13" max="1000" class="" id="tee_5_8" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[9]) ? $this->teeBox5[9] : "")?>" type="number" name="tee_5_9" min="13" max="1000" class="" id="tee_5_9" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[10]) ? $this->teeBox5[10] : "")?>" type="number" name="tee_5_10" min="13" max="1000" class="" id="tee_5_10" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[11]) ? $this->teeBox5[11] : "")?>" type="number" name="tee_5_11" min="13" max="1000" class="" id="tee_5_11" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[12]) ? $this->teeBox5[12] : "")?>" type="number" name="tee_5_12" min="13" max="1000" class="" id="tee_5_12" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[13]) ? $this->teeBox5[13] : "")?>" type="number" name="tee_5_13" min="13" max="1000" class="" id="tee_5_13" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[14]) ? $this->teeBox5[14] : "")?>" type="number" name="tee_5_14" min="13" max="1000" class="" id="tee_5_14" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[15]) ? $this->teeBox5[15] : "")?>" type="number" name="tee_5_15" min="13" max="1000" class="" id="tee_5_15" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[16]) ? $this->teeBox5[16] : "")?>" type="number" name="tee_5_16" min="13" max="1000" class="" id="tee_5_16" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[17]) ? $this->teeBox5[17] : "")?>" type="number" name="tee_5_17" min="13" max="1000" class="" id="tee_5_17" placeholder="#"></td>
                            <td><input value="<?=(isset($this->teeBox5[18]) ? $this->teeBox5[18] : "")?>" type="number" name="tee_5_18" min="13" max="1000" class="" id="tee_5_18" placeholder="#"></td>
                        </tr>

                        <tr style="background-color: ghostwhite">
                            <td style="text-align: center;">
                                Handicap
                            </td>
                            <td><input type="number" name="hc_1_1" min="1" max="1000" class="" id="hc_1_1" placeholder="#"></td>
                            <td><input type="number" name="hc_1_2" min="1" max="1000" class="" id="hc_1_2" placeholder="#"></td>
                            <td><input type="number" name="hc_1_3" min="1" max="1000" class="" id="hc_1_3" placeholder="#"></td>
                            <td><input type="number" name="hc_1_4" min="1" max="1000" class="" id="hc_1_4" placeholder="#"></td>
                            <td><input type="number" name="hc_1_5" min="1" max="1000" class="" id="hc_1_5" placeholder="#"></td>
                            <td><input type="number" name="hc_1_6" min="1" max="1000" class="" id="hc_1_6" placeholder="#"></td>
                            <td><input type="number" name="hc_1_7" min="1" max="1000" class="" id="hc_1_7" placeholder="#"></td>
                            <td><input type="number" name="hc_1_8" min="1" max="1000" class="" id="hc_1_8" placeholder="#"></td>
                            <td><input type="number" name="hc_1_9" min="1" max="1000" class="" id="hc_1_9" placeholder="#"></td>
                            <td><input type="number" name="hc_1_10" min="1" max="1000" class="" id="hc_1_10" placeholder="#"></td>
                            <td><input type="number" name="hc_1_11" min="1" max="1000" class="" id="hc_1_11" placeholder="#"></td>
                            <td><input type="number" name="hc_1_12" min="1" max="1000" class="" id="hc_1_12" placeholder="#"></td>
                            <td><input type="number" name="hc_1_13" min="1" max="1000" class="" id="hc_1_13" placeholder="#"></td>
                            <td><input type="number" name="hc_1_14" min="1" max="1000" class="" id="hc_1_14" placeholder="#"></td>
                            <td><input type="number" name="hc_1_15" min="1" max="1000" class="" id="hc_1_15" placeholder="#"></td>
                            <td><input type="number" name="hc_1_16" min="1" max="1000" class="" id="hc_1_16" placeholder="#"></td>
                            <td><input type="number" name="hc_1_17" min="1" max="1000" class="" id="hc_1_17" placeholder="#"></td>
                            <td><input type="number" name="hc_1_18" min="1" max="1000" class="" id="hc_1_18" placeholder="#"></td>
                        </tr>

                        <tr style="background-color: ghostwhite">
                            <td>
                                <select class="form-control select2" style="width: 100%;" onchange="" name="hc2" id="hc2">
                                    <option value="none" selected>Not Listed</option>
                                    <option value="women">Women's Handicap</option>
                                </select>
                            </td>
                            <td><input type="number" name="hc_2_1" min="50" max="1000" class="" id="hc_2_1" placeholder="#"></td>
                            <td><input type="number" name="hc_2_2" min="50" max="1000" class="" id="hc_2_2" placeholder="#"></td>
                            <td><input type="number" name="hc_2_3" min="50" max="1000" class="" id="hc_2_3" placeholder="#"></td>
                            <td><input type="number" name="hc_2_4" min="50" max="1000" class="" id="hc_2_4" placeholder="#"></td>
                            <td><input type="number" name="hc_2_5" min="50" max="1000" class="" id="hc_2_5" placeholder="#"></td>
                            <td><input type="number" name="hc_2_6" min="50" max="1000" class="" id="hc_2_6" placeholder="#"></td>
                            <td><input type="number" name="hc_2_7" min="50" max="1000" class="" id="hc_2_7" placeholder="#"></td>
                            <td><input type="number" name="hc_2_8" min="50" max="1000" class="" id="hc_2_8" placeholder="#"></td>
                            <td><input type="number" name="hc_2_9" min="50" max="1000" class="" id="hc_2_9" placeholder="#"></td>
                            <td><input type="number" name="hc_2_10" min="50" max="1000" class="" id="hc_2_10" placeholder="#"></td>
                            <td><input type="number" name="hc_2_11" min="50" max="1000" class="" id="hc_2_11" placeholder="#"></td>
                            <td><input type="number" name="hc_2_12" min="50" max="1000" class="" id="hc_2_12" placeholder="#"></td>
                            <td><input type="number" name="hc_2_13" min="50" max="1000" class="" id="hc_2_13" placeholder="#"></td>
                            <td><input type="number" name="hc_2_14" min="50" max="1000" class="" id="hc_2_14" placeholder="#"></td>
                            <td><input type="number" name="hc_2_15" min="50" max="1000" class="" id="hc_2_15" placeholder="#"></td>
                            <td><input type="number" name="hc_2_16" min="50" max="1000" class="" id="hc_2_16" placeholder="#"></td>
                            <td><input type="number" name="hc_2_17" min="50" max="1000" class="" id="hc_2_17" placeholder="#"></td>
                            <td><input type="number" name="hc_2_18" min="50" max="1000" class="" id="hc_2_18" placeholder="#"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box-footer" style="background-color: #2c3b41; color: ghostwhite !important;">
                <button type="button" class="btn btn-default" onclick="HideMeShow('ScoreCard', 'CourseInfo');"><< Back</button>
                <button type="submit" class="btn btn-info pull-right">Submit</button>
            </div>
        </div>
    </form>

</section><!-- /.content -->