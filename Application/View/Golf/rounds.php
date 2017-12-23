<?php

global $user_id;

$my = $this->user[$user_id ?: $_SESSION['id']];

?>


<section class="content-header" style="color: ghostwhite">
    <h1>
        <?= $my['user_first_last'] ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active" style="color: ghostwhite"><a href="#" style="color: ghostwhite">Profile</a></li>
    </ol>
    <p>
    </p>
</section>
<!-- Main content -->

<section class="content">
    <div id="alert"></div>

    <div class="row">
        <div class="col-md-auto">
            <div class="box box-widget widget-user">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Latest Scores</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Course</th>
                                    <th>Fairways</th>
                                    <th>Greens</th>
                                    <th>Putts</th>
                                    <th>Par</th>
                                    <th>Strokes</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php if (!empty( $my['rounds'] ) && is_array( $my['rounds'] )) {
                                    foreach ($my['rounds'] as $stats) {
                                        print "<tr>
                                                    <td> ".date( 'm/d/Y', $stats['score_date'] )."</td>
                                                    <td> {$stats['course_name']} </td>
                                                    <td> {$stats['score_total_ffs']} </td>
                                                    <td> {$stats['score_total_gnr']} </td>
                                                    <td> {$stats['score_total_putts']} </td>
                                                    <td> {$stats['par_tot']} </td>
                                                    <td> {$stats['score_total']} </td>";
                                    }
                                } ?>

                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <a href="<?= SITE ?>PostScore/" class="btn btn-sm btn-info btn-flat pull-left">Post New Round</a>
                    </div><!-- /.box-footer -->
                </div>
            </div>
        </div>
    </div>
</section>





