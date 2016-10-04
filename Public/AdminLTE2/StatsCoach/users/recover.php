<style>
    html {
        /* This image will be displayed fullscreen */
        background: url('http://www.sectornews.net/wp-content/uploads/2015/11/Cowboys-football.jpg') no-repeat center center;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;

        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: transparent;
    }
</style>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Recover Account</title>
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
<body class="hold-transition ">
<div class="login-box">
    <div class="login-logo">
        <a href="<?= SITE_ROOT . 'index.php'; ?>"><b>Stats</b>.Coach</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Recover Username & Password</p>

        <form action="<?= SITE_ROOT ?>users/recover/" method="post">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="email"
                       placeholder="Email" value="<?php if (isset($_POST['email'])) {
                    echo htmlentities( $_POST['email'] );
                } ?>">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Recover</button>
                </div><!-- /.col -->
            </div>
        </form>
        <br \>

        <?php
        if (empty($errors) === false) {
            foreach ($errors as $key => $val) {
                echo '<div class="categories-bottom"><p style="text-align:center;">';
                echo $val;
                echo '</p></div>';
            }
        }
        ?>

        <a href="<?= SITE_ROOT ?>">Already Have an account? Login Here</a><br>
        <a href="<?php echo SITE_ROOT . 'users/register/'; ?>" class="text-center">Register a new membership</a>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

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
