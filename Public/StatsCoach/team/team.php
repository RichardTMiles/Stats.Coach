<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:12 PM
 */
$team = $this->team[$this->team_id] or die(1);

?>
<!-- Content Header (Page header) -->
<section class="content-header" style="color: #d9edf7">
    <h1></h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite; "><i class="fa fa-paper"></i>Team</a></li>
    </ol>
</section>


<!-- Main content -->
<section class="content" id="content-pane">
    <div id="alert"></div>

    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-green">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= $team->photo ?>" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username"><?= $team->team_name ?></h3>
                            <h5 class="widget-user-desc"><a style="color: #0c0c0c"
                                                            href="<?= SITE . 'Profile/' . $this->user[$team->team_coach]->user_profile_uri ?>/"><?= $this->user[$team->team_coach]->user_full_name ?></a>
                            </h5>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li><a href="#">Team Code <span class="pull-right badge bg-blue"><?= $team->team_code ?></span></a></li>
                                <li><a href="#">Members <span class="pull-right badge bg-aqua"><?= count( $team->members ) ?></span></a></li>
                                <li><a href="#">Rounds <span class="pull-right badge bg-green">12</span></a></li>
                                <li><a href="#">Tournaments <span class="pull-right badge bg-red">842</span></a></li>
                                <li><a href="#">Strokes <span class="pull-right badge bg-red">842</span></a></li>
                                <li><a href="#">FFS <span class="pull-right badge bg-red">842</span></a></li>
                                <li><a href="#">PNR <span class="pull-right badge bg-red">842</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>

                <?php if ($team->team_coach == $_SESSION['id']) { ?>

                    <div class="col-md-12">
                        <div class="box box-widget">
                            <div class="box-body">

                                <form data-pjax class="form-horizontal" action="<?= SITE . 'Team/' . $team->team_id ?>" method="post"
                                      enctype="multipart/form-data">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" type="file" id="InputFile" name="FileToUpload">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">upload</button>
                            </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>


        </div>


</section>
