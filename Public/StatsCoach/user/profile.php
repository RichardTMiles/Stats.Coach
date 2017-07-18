<script>
    $(document).on('submit', 'form[data-pjax]', function (event) {
        $.pjax.submit(event, '#pjax-container')
    })
</script>

<!-- Content Header (Page header) -->

<section class="content-header" style="color: ghostwhite">
    <h1>
        <?= $this->user->user_full_name ?>
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
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="<?= $this->user->user_profile_pic ?>" alt="User profile picture">
                    <h3 class="profile-username text-center"><?= $this->user->user_full_name ?></h3>
                    <p class="text-muted text-center">Software Engineer</p>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Followers</b> <a class="pull-right">1,322</a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b> <a class="pull-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>Friends</b> <a class="pull-right">13,287</a>
                        </li>
                    </ul>
                    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <div class="col-md-9">
            <div class="row">
                <div class="col-md-auto">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">Profile</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <dl class="dl-horizontal">
                                <dt>About Me</dt>
                                <dd><?=$this->user->user_about_me?></dd>
                                <dt>Birthday</dt>
                                <dd><?=$this->user->birthday?></dd>
                                <dt>Education History</dt>
                                <dd><?=$this->user->user_education_history?></dd>
                                <dt>Mutual Friends</dt>
                                <dd><?=$this->user->user_about_me?>
                                </dd>
                            </dl>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>

                <?php if (!empty($this->user->rounds) && is_array( $this->user->rounds )) { ?>
                    <div class="col-md-auto">
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
                                                <th>Fairways</th>
                                                <th>Greens</th>
                                                <th>Putts</th>
                                                <th>Par</th>
                                                <th>Strokes</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($this->user->rounds as $key => $stats) { ?>
                                                <tr>
                                                    <td><?= date( 'm/d/Y', $stats->creation_date ) ?></td>
                                                    <td><?= $stats->course_name ?></td>
                                                    <td><?= $stats->score_total_ffs ?></td>
                                                    <td><?= $stats->score_total_gnr ?></td>
                                                    <td><?= $stats->score_total_putts ?></td>
                                                    <td><?= $stats->par_tot ?></td>
                                                    <td><?= $stats->score_total ?></td>
                                                </tr>
                                            <?php } ?>

                                            </tbody>
                                        </table>
                                    </div><!-- /.table-responsive -->
                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix">
                                    <a href="<?= SITE ?>PostScore/" class="btn btn-sm btn-info btn-flat pull-left">Post New Round</a>
                                    <a href="<?= SITE ?>" class="btn btn-sm btn-default btn-flat pull-right">View All Rounds</a>
                                </div><!-- /.box-footer -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div><!-- /.row -->

</section>


<!-- /.content -->