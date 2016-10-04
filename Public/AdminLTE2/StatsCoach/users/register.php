<style>
    html {
        /* This image will be displayed fullscreen */
        background: url('http://www.sbs.com.au/cyclingcentral/sites/sbs.com.au.cyclingcentral/files/velodrome_4000_getty.jpg') no-repeat center center;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;

        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: transparent;
    }

    .login-page, .register-page {
        background-color: transparent;
    }
</style>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Registration Page</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?php echo TEMPLATE_PATH ?>bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo TEMPLATE_PATH ?>dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo TEMPLATE_PATH ?>plugins/iCheck/square/blue.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<style>
    body {
        background-color: transparent;
    }
</style>
<body class="hold-transition">
<div class="register-box">
    <div class="register-logo">
        <a href=""><b>STATS</b>.Coach</a>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">Register a new membership</p>
        <form action="http://stats.coach/users/register/" method="post">

            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="First Name" name="firstname"
                       value="<?php if (isset($_POST['firstname'])) echo htmlentities( $_POST['firstname'] ); ?>">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Last Name" name="lastname"
                       value="<?php if (isset($_POST['lastname'])) echo htmlentities( $_POST['lastname'] ); ?>">
                <span class="glyphicon glyphicon-console form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Email" name="email"
                       value="<?php if (isset($_POST['email'])) echo htmlentities( $_POST['email'] ); ?>">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Username" name="username"
                       value="<?php if (isset($_POST['username'])) echo htmlentities( $_POST['username'] ); ?>">
                <span class="glyphicon glyphicon-knight form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password2">
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            </div>


            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> I agree to the <a href="#">terms</a>
                        </label>
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                </div><!-- /.col -->
            </div>
        </form>

        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using Google+</a>
        </div>

        <a href="<?= SITE_ROOT ?>" class="text-center">I already have a membership</a>
    </div><!-- /.form-box -->
</div><!-- /.register-box -->

<!-- jQuery 2.1.4 -->
<script src="<?= TEMPLATE_PATH ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= TEMPLATE_PATH ?>bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?= TEMPLATE_PATH ?>plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
