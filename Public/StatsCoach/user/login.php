
<div class="login-box">
    <div class="login-logo">
        <a href="<?= SITE ?>" style="color: #ffffff; font-size: 150%"><b>Stats</b>.Coach</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body" style="background-color: #ECF0F1; color: #0c0c0c; border: medium">
        <p class="login-box-msg">Sign in to start your session</p>

        <div id="alert"></div>
        
        <?php if ($this->UserName == false): ?>
            <form action="<?= SITE ?>login/" method="post">
                <div class="form-group has-feedback">

                    <input type="text" class="form-control" name="username"
                           placeholder="Username" value="<?= (isset($_POST['username']) ? htmlentities( $_POST['username'] ) : null); ?>">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">

                    <input type="password" name="password" class="form-control" placeholder="Password">

                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="RememberMe" value="1"> Remember Me
                            </label>
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <button no-pjax type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div><!-- /.col -->
                </div>
            </form>


            <div class="social-auth-links text-center">
                <p>- OR -</p><a href="<?= $this->faceBookLoginUrl() ?>" class="btn btn-block btn-social btn-facebook btn-flat">
                    <i class="fa fa-facebook"></i> Sign in using Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat">
                    <i class="fa fa-google-plus"></i> Sign in using Google+</a>
            </div><!-- /.social-auth-links -->

            <br/>
            <div class="categories-bottom">
                <a class="no-pjax" href="<?= SITE . 'recover/'; ?>">Forgot password<br></a>
                <a href="<?= SITE . 'register/'; ?>" class="text-center">Register a new membership</a>
            </div>

        <?php else: ?>

            <!-- Automatic element centering -->
            <div class="lockscreen-wrapper">
                <!-- User name -->
                <div class="lockscreen-name" style="text-align: center; font-size: 200%"><b><?= $this->FullName ?></b></div>

                <!-- START LOCK SCREEN ITEM -->
                <div class="lockscreen-item">
                    <!-- lockscreen image -->
                    <div class="lockscreen-image">
                        <img src="<?= $this->UserImage ?>" alt="User Image">
                    </div>
                    <!-- /.lockscreen-image -->

                    <!-- lockscreen credentials (contains the form) -->
                    <form class="lockscreen-credentials" action="<?= SITE ?>login/" method="post">
                        <div class="input-group">
                            <input style="display: none" type="text" value="1" name="RememberMe">
                            <input style="display: none" type="text" class="form-control" name="username" placeholder="Username" value="<?= $this->UserName ?>">
                            <input type="password" name="password" class="form-control" placeholder="Password">

                            <div class="input-group-btn">
                                <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                            </div>
                        </div>
                    </form>
                    <!-- /.lockscreen credentials -->

                </div>
                <!-- /.lockscreen-item -->
                <div class="help-block text-center">
                    Enter your password to retrieve your session
                </div>
                <div class="text-center">
                    <a href="<?= SITE . 'login/clear/' ?>">Or sign in as a different user</a>
                </div>
                <div class="lockscreen-footer text-center">
                    Copyright &copy; 2014-2017 <b><a href="http://lilRichard.com" class="text-black">Richard Miles</a></b><br>
                    All rights reserved
                </div>
            </div>
            <!-- /.center -->
        <?php endif; ?>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
<script>  $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });</script>