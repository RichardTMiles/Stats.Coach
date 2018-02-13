<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/10/17
 * Time: 1:53 AM
 */ ?>

<script>
    loadJS('<?=$this->versionControl(PUBLIC_FOLDER . "Golf/addcourse.js")?>');


    document.addEventListener("Carbon", (e) => {
        $.fn.load_datepicker('#datepicker');
        $.fn.load_knob('.knob');                // were pre loading
        $.fn.load_timepicker('.timepicker');
        $.fn.load_inputmask("[data-mask]")
        $.fn.load_select2(".select2");
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
                               value="<?= (isset($this->name) ? $this->name : "") ?>">
                    </div>
                    <!-- Type of Course -->
                    <div class="form-group col-xs-12 col-md-12" id="c_access">
                        <div class="col-md-6">
                            <label for="course_type">Access</label>
                            <select id="course_type" name="c_access" class="form-control select2" style="width: 100%;" onchange="">
                                <option value="Public" <?= (isset($this->access) && $this->access == "Public" ? "selected" : "") ?>>Public</option>
                                <option value="Resort" <?= (isset($this->access) && $this->access == "Resort" ? "selected" : "") ?>>Resort</option>
                                <option value="Semi-private" <?= (isset($this->access) && $this->access == "Semi-private" ? "selected" : "") ?>>
                                    Semi-private
                                </option>
                                <option value="Private" <?= (isset($this->access) && $this->access == "Private" ? "selected" : "") ?>>Private</option>
                            </select>
                        </div>
                        <!-- Number of Holes -->
                        <div class="col-md-6">
                            <label for="" id="c_style">Holes</label>
                            <select name="c_style" id="course_play" class="form-control select2" style="width: 100%;" onchange="">
                                <option value="18" <?= (isset($this->style) && $this->style == "18 Hole Standard" ? "selected" : "") ?>>18 Hole
                                    Standard
                                </option>
                                <option value="9" <?= (isset($this->style) && $this->style == "9 Hole Standard" ? "selected" : "") ?>>9 Hole
                                    Standard
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
                            <input value="<?= (isset($this->phone) ? $this->phone : "") ?>" type="text" name="c_phone" id="phone" class="form-control"
                                   data-inputmask='"mask": "(999) 999-9999"' data-mask>
                        </div>
                        <!-- /.input group -->
                    </div>

                    <!-- Street City Address input -->

                    <div class="form-group col-xs-12 col-md-12" id="c_street">
                        <label class="control-label" for="Street">Street</label>
                        <input value="<?= (isset($this->street) ? $this->street : "") ?>" name="c_street" type="text" class="form-control" id="inputSuccess"
                               placeholder="Street Address">
                    </div>

                    <div class="form-group col-xs-12 col-md-12" id="c_city">
                        <label class="control-label" for="City">City</label>
                        <input value="<?= (isset($this->city) ? $this->city : "") ?>" type="text" class="form-control" id="City" placeholder="City"
                               name="c_city">
                    </div>

                    <div class="form-group col-xs-12 col-md-12" id="c_state">
                        <label for="state">State</label>
                        <select id="state" name="c_state" class="form-control select2" style="width: 100%;">
                            <option selected="selected" value="<?= (isset($this->state) ? $this->state : "") ?>">
                                <?= (isset($this->state) ? $this->state : "State Selection") ?>
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
                        <input value="<?= $this->pga_pro ?>" type="text" class="form-control" placeholder="Course PRO"
                               name="pga_professional">

                    </div>
                    <div class="form-group col-xs-12 col-md-12" id="course_website">
                        <label class="control-label" for="course_website">Course Website
                            <a style="font-size: smaller; color: #9FAFD1;"> (Optional)</a>
                        </label>
                        <input value="<?= $this->course_website ?>" type="text" class="form-control" placeholder="Course Website"
                               name="course_website">

                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-default">Reset</button>
                <!-- button -->
                <button class="btn btn-info pull-right" type="button" onclick="validateGeneral()">Next >></button>
            </div>

        </div>

        <!-- Add Tee Box Selection -->
        <div id="teebox-color-selection" class="col-xs-12"></div>
        <!-- Tee box distances will be here -->
        <div id="Tee_box_distances" class="col-sm-12"></div>

    </form>


</section>