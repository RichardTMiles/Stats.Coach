
<div class="login-box">
    <div class="login-logo">
        <a href="<?= SITE . 'index.php'; ?>"><b>Stats</b>.Coach</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Recover Username & Password</p>

        <form action="<?= SITE ?>users/recover/" method="post">
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

        <div id="alert"></div>


        <a href="<?= SITE ?>">Already Have an account? Login Here</a><br>
        <a href="<?php echo SITE . 'users/register/'; ?>" class="text-center">Register a new membership</a>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
