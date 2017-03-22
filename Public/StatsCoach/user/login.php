<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stats | Coach</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/iCheck/square/blue.css">

    <!-- backStretch -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script src="<?= SITE_ROOT ?>Public/JavaScript/jquery.backstretch.min.js">
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<style>
    html {
        /* This image will be displayed fullscreen */
        background: url('http://site.rockbottomgolf.com/blog_images/Hole%2012%20-%20Imgur.jpg') no-repeat center fixed;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;

        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: transparent;
    }
</style>
<body class="body hold-transition" id="body">

<div class="login-box">
    <div class="login-logo">
        <p><a href="<?=SITE_ROOT?>" style="color: #d9edf7"><b>Stats</b>.Coach</a></p>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <!-- form action="index.php?controller=user&action=verify_signin&id=run" method="post" -->
        <form action="<?= SITE_ROOT?>login/" method="post">
            <div class="form-group has-feedback">

                <input type="text" class="form-control" name="username"
                       placeholder="Username" value="<?=(isset($_POST['username']) ? htmlentities( $_POST['username'] ): null ); ?>">
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
                            <input type="checkbox"> Remember Me
                        </label>
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
                </div><!-- /.col -->
            </div>
        </form>


        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
        </div><!-- /.social-auth-links -->

        <br/>
        <div class="categories-bottom">
            <p style="text-align:center";>
                <?=(isset($this->alert) ? $this->alert : null);?>
            </p>
            <br>
                <a href="<?= SITE_ROOT . 'recover/'; ?>">Forgot password
            <br>
            <a href="<?=SITE_ROOT . 'register/'; ?>" class="text-center">Register a new membership</a>

        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="<?=TEMPLATE_PATH ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?=TEMPLATE_PATH ?>bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="<?=TEMPLATE_PATH ?>plugins/iCheck/icheck.min.js"></script>
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
