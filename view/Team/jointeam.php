<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/10/17
 * Time: 1:53 AM
 */
global $teamCode;

?>

<!-- Content Header (Page header) -->
<section class="content-header" style="color: #d9edf7">
    <h1>Join a Team</h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite; "><i class="fa fa-paper"></i>Coach</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content" id="content-pane">
    <form data-pjax class="form-horizontal" method="post" action="<?= SITE ?>JoinTeam/" name="addCourse">
        <div id="alert"></div>

        <!-- Add Course Main Info -->
        <div class="box box-custom" id='CourseInfo' style="background-color: #2c3b41; border-top-color: #2c3b41;">

            <div class="box-header with-border" style="width: 100%; text-align: center">
                <h3 class="box-title" style="font-size: 200%; color: #ffffff;">Join a team</h3>
            </div>

            <div class="box-body" style="color: ghostwhite">

                <!-- text input -->
                <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 ">
                    <!--Course Name -->
                    <div class="form-group col-xs-12 col-md-12" id="teamCode">
                        <label for="teamCode">Please enter the team's code</label>
                        <input type="text" class="form-control " placeholder="Team Code" name="teamCode"
                               value="<?= $teamCode ?>">
                    </div>

                </div>
            </div>

            <div class="box-footer" style="background-color: #2c3b41; color: ghostwhite !important;">
                <button type="reset" class="btn btn-default">Reset</button>
                <!-- button -->
                <button class="btn btn-info pull-right" type="submit">Submit</button>
            </div>

        </div>


    </form>

</section>
