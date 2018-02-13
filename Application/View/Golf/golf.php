<?php

global $user, $course;

$my = $user[$_SESSION['id']];

?>
<section class="content-header" style="color: #d9edf7">
    <h1>
        <?= $my['user_first_name'] ?>
        <small style="color: #d9edf7">Profile</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: #d9edf7"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><?= $my['user_first_last'] ?></li>
        <li class="active" style="color: #d9edf7">Profile</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div id="alert"></div>

    <div class="box box-widget widget-user">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-black" style="background: url('<?= $my['user_cover_photo'] ?>') center center;">
            <h3 class="widget-user-username"></h3>
            <h5 class="widget-user-desc"></h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle" src="<?= $my['user_profile_pic'] ?>" alt="User Avatar">
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><?= $my['stats']['stats_ffs'] ?></h5>
                        <span class="description-text">Fairways on First Shot</span>
                    </div><!-- /.description-block -->

                </div><!-- /.col -->

                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header"><?= $my['stats']['stats_rounds'] ?></h5>
                        <span class="description-text">Rounds</span>
                    </div><!-- /.description-block -->

                </div><!-- /.col -->

                <div class="col-sm-4">
                    <div class="description-block">
                        <h5 class="description-header"><?= $my['stats']['stats_gnr'] ?></h5>
                        <span class="description-text">Greens in Regulation</span>
                    </div><!-- /.description-block -->
                </div><!-- /.col -->
            </div><!-- /.row -->

        </div>
    </div>

    <?php if (!empty( $my['rounds'] ) && is_array( $my['rounds'] ) && is_array( $my['rounds'][0] ?? false ) && !empty($my['rounds'][0])) {

        ?>
        <div class="box box-widget widget-user">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Latest Scores</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Fairway</th>
                                <th>Green</th>
                                <th>Putts</th>
                                <th>Par</th>
                                <th>Strokes</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($my['rounds'] as $key => $round) { ?>
                                <tr>
                                    <td><?= date( 'm/d/Y', $round['score_date'] ) ?></td>
                                    <td><?= $round['course_name'] ?></td>
                                    <td><?= $round['score_total_ffs'] ?></td>
                                    <td><?= $round['score_total_gnr'] ?></td>
                                    <td><?= $round['score_total_putts'] ?></td>
                                    <td><?= $course[$round['course_id']]['course_par_tot'] ?></td>
                                    <td><?= $round['score_total'] ?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <a href="<?= SITE ?>PostScore/" class="btn btn-sm btn-info btn-flat pull-left">Post New Round</a>
                    <a href="<?= SITE ?>Rounds/" class="btn btn-sm btn-default btn-flat pull-right">View All Rounds</a>
                </div><!-- /.box-footer -->
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-pencil"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Shots</span>
                    <span class="info-box-number"><?= $my['stats']['stats_strokes'] ?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">

            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-flag-checkered"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rounds Played</span>
                    <span class="info-box-number"><?= $my['stats']['stats_rounds'] ?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">

            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-tree"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Handicap</span>
                    <?php // TODO - the handicap system ?>
                    <span class="info-box-number"><?= ($my['stats']['stats_handicap'] < 6 ? "You must play at least 6 rounds" : $this->golf->stats_handicap) ?></span>

                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">

            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Last Round</span>
                    <span class="info-box-number"><?= $my['rounds'][0]['score_total'] ?? 'We\'re waiting.' ?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
    </div>


</section><!-- /.content -->





