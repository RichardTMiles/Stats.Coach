<?php


/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:12 PM
 */

$myTeam = $this->team[$this->team_id] or die( 1 );

$rounds = $tournaments = $strokes = $FFS = $GIR = $putts = 0;

foreach ($myTeam['members'] as $an => $id) {
    $an = $this->user[$id]['stats'] ?? false;
    if (!$an) continue;
    $rounds += $an['stats_rounds'];
    $tournaments += $an['stats_tournaments'];
    $strokes += $an['stats_strokes'];
    $FFS = $an['stats_ffs'];
    $GIR = $an['stats_gnr'];
    $putts = $an['stats_putts'];
}

$imTheCoach = $myTeam['team_coach'] == $_SESSION['id'];

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
                    <!-- Team Info -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-green">
                            <div class="widget-user-image">
                                <img class="img-circle"
                                     src="<?= SITE . ((!empty( $myTeam['team_photo'] ) && ($myTeam['photo'][$myTeam['team_photo']] ?? false) && ($photo = $myTeam['photo'][$myTeam['team_photo']]['photo'] ?? false)) ? $photo : "Data/Uploads/Pictures/Defaults/team-icon.png") ?>"
                                     alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username"><?= $myTeam['team_name'] ?></h3>
                            <h5 class="widget-user-desc">
                                <i class="fa fa-fw fa-coffee"></i>
                                <a style="color: #ffffff" href="<?= SITE . 'Profile/' . $this->user[$myTeam['team_coach']]['user_profile_uri'] ?>/"><?= $this->user[$myTeam['team_coach']]['user_full_name'] ?></a></h5>
                            <?php if($imTheCoach): ?>
                            <h6 class="widget-user-desc" onclick="Carbon($.fn.startApplication('Team/'))"><i class="fa fa-fw fa-cog"></i>Team Settings</h6>
                            <?php endif; ?>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li><a href="">Team Code <span class="pull-right badge bg-blue"><?= $myTeam['team_code'] ?></span></a></li>
                                <li><a onclick="$.fn.startApplication('Team/<?= $myTeam['team_id'] ?>/Members/')">Members <span
                                                class="pull-right badge bg-aqua"><?= count( $myTeam['members'] ) ?></span></a></li>
                                <li><a href="#">Rounds <span class="pull-right badge bg-green"><?=$rounds?></span></a></li>
                                <li><a href="#">Tournaments <span class="pull-right badge bg-red"><?=$tournaments?></span></a></li>
                                <li><a href="#">Strokes <span class="pull-right badge bg-red"><?=$strokes?></span></a></li>
                                <li><a href="#">FFS <span class="pull-right badge bg-red"><?=$FFS?></span></a></li>
                                <li><a href="#">PNR <span class="pull-right badge bg-red"><?=$GIR?></span></a></li>
                                <li><a href="#">Putts <span class="pull-right badge bg-red"><?=$putts?></span></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-team -->
                </div>

                <?php if ($imTheCoach) { ?>

                    <div class="col-md-12">
                        <div class="box box-widget">
                            <div class="box-body">

                                <form data-pjax class="form-horizontal" action="<?= SITE . 'Team/' . $myTeam['team_id'] ?>" method="post"
                                      enctype="multipart/form-data">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" type="file" id="InputFile" name="FileToUpload">
                                        <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Upload team photo</button>
                            </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>



            </div>

        </div>
        <div class="col-md-8">
            <!-- User team member -->
            <?php foreach ($myTeam['members'] as $an => $id) {
            $obj = $this->user[$id];

            ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <a href="<?=SITE. 'Profile/'. ($obj['user_profile_uri'] ?: $id) ?>/">
                            <?= $obj['user_first_name'] . ' ' . $obj['user_last_name'] ?>
                        </a>
                    </h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Add Goal</a></li>
                                <li><a href="#">Edit Goals</a></li>
                                <li><a href="#">Send Message</a></li>
                                <li><a href="#">View All Rounds</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Remove Team Member</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-black"
                                     style="background: url('<?= $obj['user_cover_photo'] ?>') center center;">
                                    <h3 class="widget-user-username"></h3>
                                    <h5 class="widget-user-desc"></h5>
                                </div>
                                <div class="widget-user-image">
                                    <img class="img-circle" src="<?= $obj['user_profile_pic'] ?>" alt="User Avatar">
                                </div>

                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">12</h5>
                                                <span class="description-text">FFS</span>
                                            </div><!-- /.description-block -->

                                        </div><!-- /.col -->

                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">3</h5>
                                                <span class="description-text">Rounds</span>
                                            </div><!-- /.description-block -->

                                        </div><!-- /.col -->

                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header">16</h5>
                                                <span class="description-text">GNR</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->

                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <p class="text-center">
                                <strong>Goal Completion</strong>
                            </p>

                            <div class="progress-group">
                                <span class="progress-text">Rounds this month</span>
                                <span class="progress-number"><b>160</b>/200</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Hours on Range</span>
                                <span class="progress-number"><b>10</b>/400</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-red" style="width: 03%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Handicap</span>
                                <span class="progress-number"><b>3</b>/6</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-green" style="width: 50%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Send Inquiries</span>
                                <span class="progress-number"><b>250</b>/500</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-yellow" style="width: 80%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./box-body -->
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                                <h5 class="description-header">230</h5>
                                <span class="description-text">Rounds</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                                <h5 class="description-header">32</h5>
                                <span class="description-text">Putts</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                                <h5 class="description-header">Profit</h5>
                                <span class="description-text">TOTAL PROFIT</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block">
                                <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                                <h5 class="description-header">1200</h5>
                                <span class="description-text">GOAL COMPLETIONS</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-footer -->
            </div>
        </div>

        <?php } ?>

</section>

