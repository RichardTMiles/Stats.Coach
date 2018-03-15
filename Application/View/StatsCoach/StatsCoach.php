<!DOCTYPE html>
<html>
<?php

global $user, $team;

include_once APP_ROOT . APP_VIEW . 'Layout/Head.php';

$userType = ($user[$_SESSION['id']]['user_type'] ?? false);

switch ($userType) {
    case 'Athlete':
        $layout = 'hold-transition skin-green layout-top-nav';
        break;
    case 'Coach':
        $layout = 'skin-green fixed sidebar-mini sidebar-collapse';
        break;
    default:
        $layout = 'stats-wrap';
}

?>
<!-- Full Width Column -->
<body class="<?= $layout ?>" style="background-color: transparent">
<div <?= (($userType === 'Athlete' || $userType === 'Coach') ? 'class="wrapper" style="background: rgba(0, 0, 0, 0.7)"' : 'class="container" id="pjax-content"') ?>>

    <?php

    if (($_SESSION['id'] ?? false) && is_array($user[$_SESSION['id']] ?? false)) {
        $my = $user[$_SESSION['id']];
        if ($userType === 'Coach') {
            require_once APP_ROOT . APP_VIEW . 'Layout/CoachLayout.php';
        } else {
            require_once APP_ROOT . APP_VIEW . 'Layout/AthleteLayout.php';
        }

        ?>

        <script>
            Carbon(() => {

                let $menu = $('li');

                let activity = function () {
                    $("li a").filter(function () {
                        $menu.removeClass('active');
                        return this.href === location.href.replace(/#.*/, "");
                    }).parent().addClass("active");
                };

                activity();

                $menu.click(function () {
                    $menu.removeClass('active');
                    $(this).addClass('active');
                });

                $('#mytitle').click(function () {
                    $menu.removeClass('active');
                });
            })
        </script>

        <div class="content-wrapper" style="background: transparent">
            <div class="container">
                <div id="alert"></div>
                <!-- content -->
                <div id="pjax-content">
                    <?= \Carbon\View::$bufferedContent ?>
                </div>
                <!-- /.content -->
            </div>
            <div class="clearfix"></div>
            <!-- /.container -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer" style="">
            <div class="container">
                <div class="pull-right hidden-xs">
                    <a href="<?= SITE ?>Privacy/">Privacy Policy</a> <b>Version</b> <?= SITE_VERSION ?>
                </div>
                <strong>Copyright &copy; 2014-2017 <a href="http://lilRichard.com">Stats Coach</a>.</strong>
                <!--script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script-->
            </div>
            <!-- /.container -->
        </footer>

        <?php
    } else {
        print \Carbon\View::$bufferedContent;
    } ?>
</div>
<?php
include_once APP_ROOT . APP_VIEW . 'Layout/Styles.php';
include_once APP_ROOT . APP_VIEW . 'Layout/Scripts.php';
?>
</body>
</html>
