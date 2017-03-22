
<section class="content-header" style="color: #d9edf7">
    <h1>
        <?=$this->user_first_name ?>
        <small style="color: #d9edf7">Profile</small>
    </h1>
    <ol class="breadcrumb">
        <li ><a href="#"style="color: #d9edf7"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><?= $this->user_full_name ?></li>
        <li class="active" style="color: #d9edf7">Profile</li>
    </ol>
</section>

<!-- Main content -->

<section class="content">


    <div class="box box-widget widget-user">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-black" style="background: url('<?= TEMPLATE_PATH ?>dist/img/photo1.png') center center;">

            <h3 class="widget-user-username"></h3>
            <h5 class="widget-user-desc"></h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle" src="<?= $this->user_profile_pic ?>" alt="User Avatar">
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">2</h5>
                        <span class="description-text">Tournaments</span>
                    </div><!-- /.description-block -->

                </div><!-- /.col -->

                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">56</h5>
                        <span class="description-text">Rounds</span>
                    </div><!-- /.description-block -->

                </div><!-- /.col -->

                <div class="col-sm-4">
                    <div class="description-block">
                        <h5 class="description-header">6</h5>
                        <span class="description-text">HANDICAP</span>
                    </div><!-- /.description-block -->
                </div><!-- /.col -->
            </div><!-- /.row -->

        </div>
    </div>

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
                            <th>Drives</th>
                            <th>Fairways</th>
                            <th>putts</th>
                            <th>Strokes</th>
                            <th>Par</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php //foreach($rows as $row) { ?>
                        <tr>
                            <td><a href="#">November 4, 2015</a></td>
                            <td>Eagle Pointe</td>
                            <td>18</td>
                            <td>30</td>
                            <td>28</td>
                            <td>76</td>
                            <td>72</td>
                        </tr>
                        <tr>
                            <td><a href="#">November 1, 2015</a></td>
                            <td>Eagle Pointe</td>
                            <td>18</td>
                            <td>34</td>
                            <td>30</td>
                            <td>82</td>
                            <td>72</td>
                        </tr>
                        <tr>
                            <td><a href="#">october 22, 2015</a></td>
                            <td>Lake Park</td>
                            <td>16</td>
                            <td>34</td>
                            <td>30</td>
                            <td>80</td>
                            <td>74</td>
                        </tr>
                        <?php //}?>

                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="<?=SITE_ROOT?>PostScore/" class="btn btn-sm btn-info btn-flat pull-left">Post New Round</a>
                <a href="<?=SITE_ROOT?>" class="btn btn-sm btn-default btn-flat pull-right">View All Rounds</a>
            </div><!-- /.box-footer -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12" >
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-pencil"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Shots</span>
                    <span class="info-box-number">4,368</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12" >

            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-flag-checkered"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rounds Played</span>
                    <span class="info-box-number">56</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12" >

            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-tree"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Handicap</span>
                    <span class="info-box-number">6</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12" >

            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Last Round</span>
                    <span class="info-box-number">78</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
    </div>


</section><!-- /.content -->



