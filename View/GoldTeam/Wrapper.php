<!DOCTYPE html>
<html>
<?php
include_once SERVER_ROOT . APP_VIEW . 'Layout/Head.php';
$logged_in = $_SESSION['id'] ?? false;

$customer = $_SESSION['table'] ?? false;

$body = $logged_in || $customer ? 'skin-red sidebar-mini' : 'hold-transition skin-blue-light layout-top-nav fixed';

?>
<!-- Full Width Column -->
<body class="<?= $body ?>">

<div class="wrapper" style="background-color: rgba(0, 0, 0, 0.7)">

    <?php

    /***
    if ($logged_in || $customer) {
        if ($customer) {
            include APP_ROOT . APP_VIEW . 'Layout' . DS . 'Customer.php';
        } else {
            include APP_ROOT . APP_VIEW . 'Layout' . DS . 'logged-in-layout.php';
        } //else {
            //include APP_ROOT . APP_VIEW . 'Layout' . DS . '';
        //}



    } else {
        include APP_ROOT . APP_VIEW . 'Layout' . DS . 'logged-out-layout.php';
    }
     *
     */

    include APP_ROOT . APP_VIEW . 'Layout' . DS . 'logged-in-layout.php';
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background: transparent">
        <!--  style="background: transparent"  Add this to use the backstretch fn-->
        <div id="alert"></div>
        <!-- content -->
        <div class="col-md-offset-1 col-md-10">


            <div id="pjax-content">
                <?= \Carbon\View::$bufferedContent ?? '' ?>
            </div>
        </div>
        <!-- /.content -->
        <div class="clearfix"></div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer bg-black" style="border: none;">
        <div class="container">
            <div class="pull-right hidden-xs">
                <a href="<?= SITE ?>Privacy/" class="text-primary">Privacy Policy</a> <b>Version</b> <?= SITE_VERSION ?>
            </div>
            <strong>Copyright &copy; 2014-2017 <a href="<?= SITE ?>"><span
                            class="text-primary">Gold Team</span></a>.</strong>
            <!--script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script-->
        </div>
        <!-- /.container -->
    </footer>

    <?php
    include_once SERVER_ROOT . APP_VIEW . 'Layout/Styles.php';
    include_once SERVER_ROOT . APP_VIEW . 'Layout/Scripts.php';
    ?>
</body>
</html>
