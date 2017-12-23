<?php // This page is dynamic for any user

global $user_id;

$my = $my ?? $this->user[$_SESSION['id']];

$myAccountBool = empty($user_id) || $user_id === $_SESSION['id'];

$profile = (!$myAccountBool ? $this->user[$user_id] : $my);

?>

<!-- Content Header (Page header) -->

<section class="content-header" style="color: ghostwhite">
    <h1>
        <?= $profile['user_first_last'] ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active" style="color: ghostwhite"><a href="#" style="color: ghostwhite">Profile</a></li>
    </ol>
    <p></p>
</section>
<!-- Main content -->

<section class="content">
    <div id="alert"></div>
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary" data-widget="">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle"
                         src="<?= $profile['user_profile_pic'] ?>" alt="User profile picture">
                    <h3 class="profile-username text-center">
                        <?= $profile['user_first_last'] ?>
                    </h3>
                    <p class="text-muted text-center">Golfer</p>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Followers</b>
                            <a class="pull-right"><?= count($profile['followers']) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b>
                            <a class="pull-right"><?= count($profile['following']) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Rounds</b><a class="pull-right"><?= $profile['stats']['stats_rounds'] ?></a>
                        </li>
                    </ul>
                    <?php if (!$myAccountBool) {
                        $following = in_array($user_id, $my['following']); ?>
                        <a style="display: <?= ($following) ? 'none' : 'block' ?>"
                           onclick="follow()"
                           class="btn btn-primary btn-block" id="FollowUser"><b>Follow :)</b></a>
                        <a style="display: <?= ($following) ? 'block' : 'none' ?>"
                           onclick="unfollow()"
                           class="btn btn-success btn-block" id="UnfollowUser"><b>Unfollow :(</b></a>
                        <script>
                            function follow () {
                                $.fn.startApplication('<?= SITE ?>Follow/<?= $user_id ?>/');
                                document.getElementById('FollowUser').style.display = 'none';
                                document.getElementById('UnfollowUser').style.display = 'block';
                            }
                            function unfollow () {
                                $.fn.startApplication('<?= SITE ?>Unfollow/<?= $user_id ?>/');
                                document.getElementById('FollowUser').style.display = 'block';
                                document.getElementById('UnfollowUser').style.display = 'none';
                            }
                        </script>
                    <?php } else { ?>
                        <a href="<?= SITE ?>Messages/" class="btn btn-success btn-block">Messages</a>
                    <?php } ?>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <?php if (!$myAccountBool) print '<div class="box box-success direct-chat direct-chat-success"></div>'; ?><!-- DIRECT CHAT SUCCESS -->

            <?php
            dump($my);
            ?>

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
                                <dd><?= $profile['user_about_me'] ?></dd>
                                <br>
                                <dt>Birthday</dt>
                                <dd><?= $profile['user_birthday'] ?></dd>
                                <br>
                                <dt>Education History</dt>
                                <dd><?= $profile['user_education_history'] ?></dd>
                                <br>
                                <dt>Mutual Friends</dt>
                                <dd><?= $profile['user_facebook_id'] ?? 'Connect to Facebook'; ?></dd>
                            </dl>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.user info -->
                <?php if ($myAccountBool) { ?>
                    <div class="col-md-auto">
                        <!-- Horizontal Form -->
                        <div class="box box-info" id="ProfileSettings">
                            <div class="box-header with-border">
                                <h3 class="box-title">Profile Settings</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                                class="fa fa-remove"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form data-pjax class="form-horizontal" action="<?= SITE ?>Profile/" method="post"
                                  enctype="multipart/form-data">

                                <div class="box-body">
                                    <div class="form-group col-md-12">

                                        <div class="form-group">
                                            <label for="exampleInputFile" class="col-sm-3 control-label">Profile
                                                Picture</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="file" id="InputFile"
                                                       name="FileToUpload">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="first" class="col-sm-3 control-label">First Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="first"
                                                       placeholder="<?= $profile['user_first_name'] ?>"
                                                       name="first_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lastName" class="col-sm-3 control-label">Last Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="lastName"
                                                       placeholder="<?= $profile['user_last_name'] ?>" name="last_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lastName" class="col-sm-3 control-label">Birthday:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input name="datepicker" type="text" class="form-control pull-right"
                                                           id="datepicker"
                                                           value="<?= $profile['user_birthday'] ?>">
                                                </div>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group <?= $profile['user_email_confirmed'] == 0 ? 'has-error' : 'has-success' ?>">
                                            <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="email" class="form-control" id="inputEmail"
                                                       placeholder="<?= $profile['user_email'] ?>" name="email">
                                                <span
                                                        class="help-block"><?= $profile['user_email_confirmed'] == 1 ?
                                                        'Email Verified' :
                                                        'Please check this email to activate your account!' ?></span>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="username" class="col-sm-3 control-label">Username</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="username"
                                                       disabled="disabled"
                                                       placeholder="<?= $profile['user_username'] ?>">
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
                                            <label for="inputExperience" class="col-sm-3 control-label">About Me</label>
                                            <div class="col-sm-8">
                                                <textarea name="about_me" class="form-control" id="inputExperience"
                                                          placeholder="<?= $profile['user_about_me'] ?>"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <div class="checkbox">
                                                <label>
                                                    <input name='Terms' type="checkbox" value="1"> I agree to the
                                                    <a href="<?= SITE ?>Privacy/">terms and conditions</a>
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
                    <?php    // TODO - place timeline
                }

                if (!empty($profile['rounds']) && is_array($profile['rounds'])
                    && is_array($profile['rounds'][0] ?? false) && !empty($profile['rounds'][0])) { ?>
                    <div class="col-md-auto">
                        <div class="box box-widget widget-user">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Latest Scores</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i></button>
                                        <button class="btn btn-box-tool" data-widget="remove"><i
                                                    class="fa fa-times"></i></button>
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

                                            <?php foreach ($profile['rounds'] as $stats) {
                                                echo '<tr>
                                                    <td>' . date('m/d/Y', $stats['score_date']) . "</td>
                                                    <td> {$stats['course_name']} </td>
                                                    <td> {$stats['score_total_ffs']} </td>
                                                    <td> {$stats['score_total_gnr']} </td>
                                                    <td> {$stats['score_total_putts']} </td>
                                                    <td> {$stats['par_tot']} </td>
                                                    <td> {$stats['score_total']} </td></tr>";
                                            } ?>

                                            </tbody>
                                        </table>
                                    </div><!-- /.table-responsive -->
                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix">
                                    <a href="<?= SITE ?>PostScore/" class="btn btn-sm btn-info btn-flat pull-left">Post
                                        New Round</a>
                                    <a href="<?= SITE . 'Rounds/' . $user_id ?>/"
                                       class="btn btn-sm btn-default btn-flat pull-right">View All Rounds</a>
                                </div><!-- /.box-footer -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div><!-- /.row -->
</section>

<script>Carbon((e) => {
        $.fn.load_datepicker('#datepicker');
        <?php if (!$myAccountBool):?>
        $.fn.startApplication('<?= SITE . 'Messages/' . $user_id ?>/');
        <?php endif; ?>
    })</script>

<!-- /.content -->

