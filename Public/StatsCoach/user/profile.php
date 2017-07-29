<?php $user = $this->user[$this->id ?: $_SESSION['id']];

?>

<!-- Content Header (Page header) -->

<section class="content-header" style="color: ghostwhite">
    <h1>
        <?= $user->user_first_name . ' ' . $user->user_last_name ?>
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
                    <img class="profile-user-img img-responsive img-circle" src="<?= $user->user_profile_picture ?>" alt="User profile picture">
                    <h3 class="profile-username text-center">
                        <?= $user->user_first_name . ' ' . $user->user_last_name ?>
                    </h3>
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


            <?php if($user->user_id != $_SESSION['id']) { ?>

            <!-- DIRECT CHAT SUCCESS -->
            <div class="box box-success direct-chat direct-chat-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Direct Chat</h3>

                    <div class="box-tools pull-right">
                        <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages">3</span>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle"
                                data-original-title="Contacts">
                            <i class="fa fa-comments"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages">
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                            <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-left">Alexander Pierce</span>
                                <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
                            </div>
                            <!-- /.direct-chat-info -->
                            <img class="direct-chat-img" src="" alt="Message User Image"><!-- /.direct-chat-img -->
                            <div class="direct-chat-text">
                                Is this template really for free? That's unbelievable!
                            </div>
                            <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                            <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-right">Sarah Bullock</span>
                                <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-info -->
                            <img class="direct-chat-img" src="" alt="Message User Image"><!-- /.direct-chat-img -->
                            <div class="direct-chat-text">
                                You better believe it!
                            </div>
                            <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                    </div>
                    <!--/.direct-chat-messages-->

                    <!-- Contacts are loaded here -->
                    <div class="direct-chat-contacts">
                        <ul class="contacts-list">
                            <li>
                                <a href="#">
                                    <img class="contacts-list-img" src="" alt="User Image">

                                    <div class="contacts-list-info">
                            <span class="contacts-list-name">
                              Count Dracula
                              <small class="contacts-list-date pull-right">2/28/2015</small>
                            </span>
                                        <span class="contacts-list-msg">How have you been? I was...</span>
                                    </div>
                                    <!-- /.contacts-list-info -->
                                </a>
                            </li>
                            <!-- End Contact Item -->
                        </ul>
                        <!-- /.contatcts-list -->
                    </div>
                    <!-- /.direct-chat-pane -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <form action="#" method="post">
                        <div class="input-group">
                            <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-success btn-flat">Send</button>
                      </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-footer-->
            </div>
            <!--/.direct-chat -->

            <? } ?>

        </div>

        <div class="col-md-9">
            <div class="row">

                <!-- Display User Info -->
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
                                <dd><?= $user->user_about_me ?></dd>
                                <br>
                                <dt>Birthday</dt>
                                <dd><?= $user->user_birthday ?></dd>
                                <br>
                                <dt>Education History</dt>
                                <dd><?= $user->user_education_history ?></dd>
                                <br>
                                <dt>Mutual Friends</dt>
                                <dd><?= !empty($user->user_facebook_id) ? $user->user_facebook_id : 'Connect to Facebook'; ?></dd>
                            </dl>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.user info -->


                <?php
                // Show settings or chat box
                if ($user->user_id != $_SESSION['id']) // Chat box
                { ?>
                    <!-- Chat box -->
                    <div class="box box-success">
                        <div class="box-header ui-sortable-handle" style="cursor: move;">
                            <i class="fa fa-comments-o"></i>

                            <h3 class="box-title">Chat</h3>

                            <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Status">
                                <div class="btn-group" data-toggle="btn-toggle">
                                    <button type="button" class="btn btn-default btn-sm active"><i class="fa fa-square text-green"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-square text-red"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;">
                            <div class="box-body chat" id="chat-box" style="overflow: hidden; width: auto; height: 250px;">
                                <!-- chat item -->
                                <div class="item">
                                    <img src="" alt="user image" class="online">

                                    <p class="message">
                                        <a href="#" class="name">
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                                            Mike Doe
                                        </a>
                                        I would like to meet you to discuss the latest news about
                                        the arrival of the new theme. They say it is going to be one the
                                        best themes on the market
                                    </p>
                                    <div class="attachment">
                                        <h4>Attachments:</h4>

                                        <p class="filename">
                                            Theme-thumbnail-image.jpg
                                        </p>

                                        <div class="pull-right">
                                            <button type="button" class="btn btn-primary btn-sm btn-flat">Open</button>
                                        </div>
                                    </div>
                                    <!-- /.attachment -->
                                </div>
                                <!-- /.item -->
                                <!-- chat item -->
                                <div class="item">
                                    <img src="<?= $user->user_profile_picture ?>" alt="user image" class="offline">

                                    <p class="message">
                                        <a href="#" class="name">
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:15</small>
                                            Alexander Pierce
                                        </a>
                                        I would like to meet you to discuss the latest news about
                                        the arrival of the new theme. They say it is going to be one the
                                        best themes on the market
                                    </p>
                                </div>
                                <!-- /.item -->
                                <!-- chat item -->
                                <div class="item">
                                    <img src="" alt="user image" class="offline">

                                    <p class="message">
                                        <a href="#" class="name">
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:30</small>
                                            Susan Doe
                                        </a>
                                        I would like to meet you to discuss the latest news about
                                        the arrival of the new theme. They say it is going to be one the
                                        best themes on the market
                                    </p>
                                </div>
                                <!-- /.item -->
                            </div>
                            <div class="slimScrollBar"
                                 style="background-color: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 184.9112426035503px; background-position: initial initial; background-repeat: initial initial;"></div>
                            <div class="slimScrollRail"
                                 style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; background-color: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px; background-position: initial initial; background-repeat: initial initial;"></div>
                        </div>
                        <!-- /.chat -->
                        <div class="box-footer">
                            <div class="input-group">
                                <input class="form-control" placeholder="Type message...">

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box (chat box) -->
                <?php } else { ?>

                    <div class="col-md-auto">
                        <!-- Horizontal Form -->
                        <div class="box box-info" id="ProfileSettings">
                            <div class="box-header with-border">
                                <h3 class="box-title">Profile Settings</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->

                            <!-- Form Start -->
                            <form data-pjax class="form-horizontal" action="<?= SITE ?>Profile/" method="post" enctype="multipart/form-data">

                                <div class="box-body">
                                    <div class="form-group col-md-12">

                                        <div class="form-group">
                                            <label for="exampleInputFile" class="col-sm-3 control-label">Profile Picture</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="file" id="InputFile" name="FileToUpload">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="first" class="col-sm-3 control-label">First Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="first"
                                                       placeholder="<?= $user->user_first_name ?>" name="first_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lastName" class="col-sm-3 control-label">Last Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="lastName"
                                                       placeholder="<?= $user->user_last_name ?>" name="last_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lastName" class="col-sm-3 control-label">Birthday:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input name="datepicker" type="text" class="form-control pull-right" id="datepicker"
                                                           value="<?= $user->user_birthday ?>">
                                                </div>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group <?=$user->user_email_confirmed == 0 ? 'has-error' : 'has-success' ?>">
                                            <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="email" class="form-control" id="inputEmail"
                                                       placeholder="<?= $user->user_email ?>" name="email">
                                                <span class="help-block"><?=$user->user_email_confirmed == 1? 'Email Verified' : 'Please check this email to activate your account!' ?></span>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="username" class="col-sm-3 control-label">Username</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="username" disabled="disabled"
                                                       placeholder="<?= $user->user_username ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-sm-3 control-label">Password</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="password"
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
                                                <textarea name="about_me" class="form-control" id="inputExperience" placeholder="Experience"></textarea>
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

                                </div>

                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="reset" class="btn btn-default">Reset</button>
                                    <button name="terms" type="submit" class="btn btn-danger pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                    <?php
                }

                if (!empty($user->rounds) && is_array( $user->rounds )) { ?>
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

                                            <?php foreach ($user->rounds as $stats) {
                                                echo '<tr>
                                                    <td>' . date( 'm/d/Y', $stats->score_date ) . "</td>
                                                    <td> $stats->course_name </td>
                                                    <td> $stats->score_total_ffs </td>
                                                    <td> $stats->score_total_gnr </td>
                                                    <td> $stats->score_total_putts </td>
                                                    <td> $stats->par_tot </td>
                                                    <td> $stats->score_total </td>";
                                            } ?>

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


<script>
    var loading = '<!-- Loading (remove the following to stop the loading)--><div class="overlay"><i class="fa fa-refresh fa-spin"></i></div><!-- end loading -->';

    $(document).on('submit', 'form[data-pjax]', function (event) {
        $(event.target).closest('box').append(loading);
        $.pjax.submit(event, '#ajax-content')
    });

    $(function () {
        //Date picker
        $('#datepicker').datepicker({autoclose: true});
    });

</script>


<!-- /.content -->