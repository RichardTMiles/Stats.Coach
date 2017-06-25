
<script>
    $(document).on('submit', 'form[data-pjax]', function(event) {
        $.pjax.submit(event, '#pjax-container')
    })
</script>

<!-- Content Header (Page header) -->

<section class="content-header" style="color: ghostwhite">
    <h1>
        User Profile
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#" style="color: ghostwhite">Examples</a></li>
        <li class="active" style="color: ghostwhite">User profile</li>
    </ol>
    <p>
    </p>
</section>

<!-- Main content -->

<section class="content" >
    <div id="alert"></div>

    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="<?=$this->user->user_profile_pic?>" alt="User profile picture">
                    <h3 class="profile-username text-center"><?=$this->user->user_full_name?></h3>
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

        <div class="col-md-9 col-sm-12 col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Latest Rounds</h3>
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
                                <td><a href="#">october 22, 2015</a></td>
                                <td>Lake Park</td>
                                <td>16</td>
                                <td>34</td>
                                <td>30</td>
                                <td>80</td>
                                <td>74</td>
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
                                <td><a href="#">November 4, 2015</a></td>
                                <td>Eagle Pointe</td>
                                <td>18</td>
                                <td>30</td>
                                <td>28</td>
                                <td>76</td>
                                <td>72</td>
                            </tr>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <a href="<?=SITE_PATH. "PostScore/"?>" class="btn btn-sm btn-info btn-flat pull-left">Post New Round</a>
                    <a href="javascript::;" class="btn btn-sm btn-default btn-flat pull-right">View All Rounds</a>
                </div><!-- /.box-footer -->
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Info / Settings -->
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#AboutMe" data-toggle="tab" aria-expanded="true">About Me</a></li>
                    <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Settings</a></li>
                </ul>
                <div class="tab-content">
                    <!-- About Me -->
                    <div class="tab-pane active" id="AboutMe">
                        <!-- Post -->
                        <div class="post">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <i class="fa fa-text-width"></i>
                                    <h3 class="box-title">Description Horizontal</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <dl class="dl-horizontal">
                                        <dt>Description lists</dt>
                                        <dd>A description list is perfect for defining terms.</dd>
                                        <dt>&nbsp;</dt>
                                        <dd>&nbsp;</dd>
                                        <dt>Euismod</dt>
                                        <dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec
                                            elit.
                                        </dd>
                                        <dd>Donec id elit non mi porta gravida at eget metus.</dd>
                                        <dt>&nbsp;</dt>
                                        <dd>&nbsp;</dd>
                                        <dt>Malesuada porta</dt>
                                        <dd>Etiam porta sem malesuada magna mollis euismod.</dd>
                                        <dt>&nbsp;</dt>
                                        <dd>&nbsp;</dd>
                                        <dt>Felis euismod semper eget lacinia</dt>
                                        <dd>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut
                                            fermentum massa justo sit amet risus.
                                        </dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <!-- /.post -->
                    </div><!-- /.tab-pane -->

                    <!-- SETTINGS TAB -->
                    <div class="tab-pane" id="settings">
                        <!-- Form Start -->
                        <form class="form-horizontal" action="<?=SITE_PATH?>Profile/" method="post" enctype="multipart/form-data">


                            <div class="form-group col-md-12">

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <img class="profile-user-img img-responsive img-circle" src="<?=$this->user->user_profile_pic ?>"
                                             alt="User profile picture">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputFile" class="col-sm-3 control-label">File input</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" id="InputFile" name="FileToUpload">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">First Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="inputName"
                                               placeholder="<?= $this->user->user_first_name ?>" name="first_name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">Last Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="inputName"
                                               placeholder="<?=$this->user->user_last_name ?>" name="first_name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" id="inputEmail"
                                               placeholder="<?= $this->user->user_email ?>" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">Username</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="inputName" disabled="disabled"
                                               placeholder="<?= $this->user->user_username ?>" name="username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputSkills" class="col-sm-3 control-label">Password</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="inputSkills"
                                               placeholder="Protected" name="password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputGender" class="col-sm-3 control-label">Gender</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="inputGender" name="gender">
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputExperience" class="col-sm-3 control-label">Biography</label>
                                    <div class="col-sm-8">
                                    <textarea class="form-control" id="inputExperience"
                                              placeholder="Experience"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="1"> I agree to the <a href="#">terms and
                                                conditions</a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div><!-- /.nav-tabs-custom -->
        </div><!-- /.col -->
    </div><!-- /.row -->

</section>


<!-- /.content -->