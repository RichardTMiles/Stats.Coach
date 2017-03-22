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
                self = self.style.visibility = "hidden";
                return;
            }
        }

        while (self.nodeName != 'TR') {
            self = self.parentElement;
        }

        do {
            self = self.nextSibling;
        } while (self.nodeName != 'TR');

        self.style.display = '';
    }

    function HideMeShow(hide, show) {
        document.getElementById(hide).style.display = "none";
        document.getElementById(show).style.display = "block";
    }


    // This works don't change
    $(document).on('submit', 'form[data-pjax]', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#ajax-content')
    });

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

    <form data-pjax class="form-horizontal" method="post">

        <!-- Add Course Main Info -->
        <div class="box box-custom" id='CourseInfo' style="display: block; background-color: #2c3b41; border-top-color: #2c3b41;">

            <div class="box-body">
                <div class="row">
                    <div class="box-body">

                        <!-- I'm a dumb ass... Form Data -->
                        <!-- text input -->
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 ">

                                <!--Course Name -->
                                <div class="form-group">
                                    <label>Course</label>
                                    <input type="text" class="form-control" placeholder="Course Name" name="course_name">
                                </div>
                                <!-- Type  &&  Style -->
                                <div class="row">
                                    <!-- Type of Course -->
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label for="course_type">Access</label>
                                            <select id="course_type" name="course_type" class="form-control select2" style="width: 100%;" onchange="">
                                                <option selected="selected" disabled>Course Type</option>
                                                <option value="Alabama">Public</option>
                                                <option value="Alaska">Resort</option>
                                                <option value="California">Semi-private</option>
                                                <option value="Delaware">Private</option>
                                            </select>
                                        </div>

                                        <!-- Number of Holes -->
                                        <div class="col-md-6">
                                            <label for="">Style</label>
                                            <select class="form-control select2" style="width: 100%;" onchange="">
                                                <option selected="selected" disabled>Course Play</option>
                                                <option value="9-hole">9 Hole Standard</option>
                                                <option value="18-hole">18 Hole Standard</option>
                                                <option value="Approach">Approach</option>
                                                <option value="Executive">Executive</option>
                                            </select>
                                        </div>
                                    </div><!-- /.form-group -->
                                </div>
                                <!-- /.row  for Select options -->

                                <br> <!-- I need my personal space :P -->

                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label>Phone Number
                                        <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <input type="text" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                                    </div>
                                    <!-- /.input group -->
                                </div>

                                <!-- Street City Address input -->
                                <div class="form-group has-success">
                                    <label class="control-label" for="Street"><i class="fa fa-check"></i>Street</label>
                                    <input type="text" class="form-control" id="inputSuccess" placeholder="Street Address">
                                    <span class="help-block">Help block with success</span>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="City">City</label>
                                    <input type="text" class="form-control" id="City" placeholder="City">
                                </div>

                                <div class="form-group">
                                    <label>State</label>
                                    <select class="form-control select2" style="width: 100%;" onclick="Courses()">
                                        <option selected="selected" value="">
                                            State Selection
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer" style="background-color: #2c3b41; color: ghostwhite !important;"><a href="<?= SITE_ROOT ?>">
                    <button type="button" class="btn btn-default">Cancel</button>
                </a>
                <button type="button" class="btn btn-info pull-right" onclick="HideMeShow('CourseInfo','ScoreCard')">Next >></button>
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
                            <td><input type="number" name="par_1" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_2" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_3" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_4" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_5" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_6" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_7" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_8" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_9" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_10" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_11" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_12" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_13" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_14" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_15" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_16" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_17" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="par_18" min="1" max="1000" class="" id="" placeholder="#"></td>
                        </tr>

                        <!-- Tee 1-->
                        <tr>
                            <td>
                                <select class="form-control select2" style="width: 100%;"
                                        name="tee_1_color" onchange="fancyColors(this)">
                                    <option selected="selected" disabled>Add Tee Box</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td><input type="number" name="tee_1_1" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_2" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_3" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_4" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_5" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_6" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_7" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_8" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_9" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_10" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_11" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_12" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_13" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_14" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_15" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_16" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_17" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_1_18" min="50" max="2000" class="" id="" placeholder="#"></td>
                        </tr>

                        <tr style="display: none;">
                            <td><select class="form-control select2" style="width: 100%;"
                                        name="tee_2_color" onchange="fancyColors(this)">
                                    <option selected="selected" value="none" disabled>Add Tee Box</option>
                                    <option value="none">None</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select></td>
                            <td><input type="number" name="tee_2_1" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_2" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_3" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_4" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_5" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_6" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_7" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_8" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_9" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_10" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_11" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_12" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_13" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_14" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_15" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_16" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_17" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_2_18" min="50" max="2000" class="" id="" placeholder="#"></td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" name="tee_3_color" style="width: 100%;" onchange="fancyColors(this)">
                                    <option selected="selected" disabled>Add Tee Box</option>
                                    <option value="none">None</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="tee_3_1" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_2" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_3" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_4" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_5" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_6" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_7" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_8" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_9" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_out" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_10" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_11" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_12" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_13" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_14" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_15" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_16" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_17" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_18" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_in" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                            <td>
                                <input type="number" name="tee_3_total" min="50" max="1000" class="" id="" placeholder="#">
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" style="width: 100%;" name="tee_4_color"
                                        onchange="fancyColors(this)">
                                    <option selected="selected" disabled>Add Tee Box</option>
                                    <option value="none">None</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select>
                            </td>
                            <td><input type="number" name="tee_4_1" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_2" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_3" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_4" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_5" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_6" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_7" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_8" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_9" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_10" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_11" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_12" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_13" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_14" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_15" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_16" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_17" min="50" max="2000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_4_18" min="50" max="2000" class="" id="" placeholder="#"></td>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td>
                                <select class="form-control select2" style="width: 100%;" value="tee_1_color" onchange="fancyColors(this)">
                                    <option selected="selected" disabled>Add Tee Box</option>
                                    <option value="none">None</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="White">White</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Black">Black</option>
                                </select></td>
                            <td><input type="number" name="tee_5_1" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_2" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_3" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_4" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_5" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_6" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_7" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_8" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_9" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_10" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_11" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_12" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_13" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_14" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_15" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_16" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_17" min="13" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="tee_5_18" min="13" max="1000" class="" id="" placeholder="#"></td>
                        </tr>

                        <tr style="background-color: ghostwhite">
                            <td>
                                <select class="form-control select2" style="width: 100%;" onchange="showSibling(this)" value="hc_1_type">
                                    <option value="Handicap" selected="selected">Handicap</option>
                                    <option value="Men">Men's Handicap</option>
                                    <option value="none">Not Listed</option>
                                </select></td>
                            <td><input type="number" name="hc_1_1" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_2" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_3" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_4" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_5" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_6" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_7" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_8" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_9" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_10" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_11" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_12" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_13" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_14" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_15" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_16" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_17" min="1" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_1_18" min="1" max="1000" class="" id="" placeholder="#"></td>
                        </tr>

                        <tr style="display: none; background-color: ghostwhite">
                            <td>
                                <select class="form-control select2" style="width: 100%;" onchange="" name="hc_2_type">
                                    <option value="women" selected="selected">Women's Handicap</option>
                                    <option value="none">Not Listed</option>

                                </select>
                            </td>
                            <td><input type="number" name="hc_2_1" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_2" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_3" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_4" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_5" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_6" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_7" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_8" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_9" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_10" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_11" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_12" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_13" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_14" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_15" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_16" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_17" min="50" max="1000" class="" id="" placeholder="#"></td>
                            <td><input type="number" name="hc_2_18" min="50" max="1000" class="" id="" placeholder="#"></td>
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