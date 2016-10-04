<?php

namespace App\Views;

use App\ApplicationView;

class AdminLTE extends ApplicationView
{
    public function __construct($data)
    {


        // Sets up our Magic Methods
        $this->data = $data;        // For our template / Output buffer

        // Render the main output/content page and its variables
        ob_start();
        $this->requireFile( SITE_STRIPPED . strtolower( $this->class ) . '/' . $this->function . '.tpl.php' );
        $output = ob_get_contents();
        ob_end_clean();

        // If its Ajax don't return the Template
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
            echo $output;
            exit();
        }

        // Turn off caching
        header( 'Expires: Thu, 21 Jul 1977 07:30:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );

        // Switch to html, utf-8
        header( 'Content-type: text/html; charset=utf-8' );

        /**
         * Just a general note, the background image is stored in adminLTE.css :: .layout-boxed
         * List all css includes
         */
        $css_incl['bootstrap_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'bootstrap/css/bootstrap.min.css">';
        $css_incl['DataTables_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/datatables/dataTables.bootstrap.css">';
        $css_incl['font_awesome_css'] = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">';
        $css_incl['Ion_icons_css'] = '<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">';
        $css_incl['AdminLTE_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'dist/css/AdminLTE.css">';
        $css_incl['AdminLTE_skin_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'dist/css/skins/_all-skins.css">';
        $css_incl['Select2_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/select2/select2.min.css">';
        $css_incl['iCheck_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/iCheck/flat/blue.css">';
        $css_incl['Morris_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/morris/morris.css">';
        $css_incl['jvectormap_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/jvectormap/jquery-jvectormap-1.2.2.css">';
        $css_incl['Date_Picker_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/datepicker/datepicker3.css">';
        $css_incl['Daterange_picker_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/daterangepicker/daterangepicker-bs3.css">';
        $css_incl['wysihtml5_css'] = '<link rel="stylesheet" href="' . TEMPLATE_PATH . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">';

        // List all javascript includes
        $js_incl['jQuery'] = '<script src="' . TEMPLATE_PATH . 'plugins/jQuery/jQuery-2.1.4.min.js"></script>';
        $js_incl['bootstrap'] = '<script src="' . TEMPLATE_PATH . 'bootstrap/js/bootstrap.min.js"></script>';
        $js_incl['DataTables'] = '<script src="' . TEMPLATE_PATH . 'plugins/datatables/jquery.dataTables.min.js"></script>';
        $js_incl['DataTables_Bootstrap'] = '<script src="' . TEMPLATE_PATH . 'plugins/datatables/dataTables.bootstrap.min.js"></script>';
        $js_incl['InputMasks_1'] = '<script src="' . TEMPLATE_PATH . 'plugins/input-mask/jquery.inputmask.js"></script>';
        $js_incl['InputMasks_2'] = '<script src="' . TEMPLATE_PATH . 'plugins/input-mask/jquery.inputmask.date.extensions.js"></script>';
        $js_incl['InputMasks_3'] = '<script src="' . TEMPLATE_PATH . 'plugins/input-mask/jquery.inputmask.extensions.js"></script>';
        $js_incl['Select2'] = '<script src="' . TEMPLATE_PATH . 'plugins/select2/select2.full.min.js"></script>';
        $js_incl['slimScroll'] = '<script src="' . TEMPLATE_PATH . 'plugins/slimScroll/jquery.slimscroll.min.js"></script>';
        $js_incl['fastclick'] = '<script src="' . TEMPLATE_PATH . 'plugins/fastclick/fastclick.min.js"></script>';
        $js_incl['appmin'] = '<script src="' . TEMPLATE_PATH . 'dist/js/app.min.js"></script>';
        // <!-- AdminLTE for demo purposes -->
        $js_incl['demo'] = '<script src="' . TEMPLATE_PATH . 'dist/js/demo.js"></script>';


        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title>Stats Coach<?php if (isset($this->pageTitle)) echo " | $this->pageTitle"; ?></title>

            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <script>
                var AdminLTEOptions = {
                    //Add slimscroll to navbar menus
                    //This requires you to load the slimscroll plugin
                    //in every page before app.js
                    navbarMenuSlimscroll: true,
                    navbarMenuSlimscrollWidth: "3px", //The width of the scroll bar
                    navbarMenuHeight: "200px", //The height of the inner menu
                    //General animation speed for JS animated elements such as box collapse/expand and
                    //sidebar treeview slide up/down. This options accepts an integer as milliseconds,
                    //'fast', 'normal', or 'slow'
                    animationSpeed: 'normal',
                    //Sidebar push menu toggle button selector
                    sidebarToggleSelector: "[data-toggle='offcanvas']",
                    //Activate sidebar push menu
                    sidebarPushMenu: true,
                    //Activate sidebar slimscroll if the fixed layout is set (requires SlimScroll Plugin)
                    sidebarSlimScroll: false,
                    //Enable sidebar expand on hover effect for sidebar mini
                    //This option is forced to true if both the fixed layout and sidebar mini
                    //are used together
                    sidebarExpandOnHover: false,
                    //BoxRefresh Plugin
                    enableBoxRefresh: true,
                    //Bootstrap.js tooltip
                    enableBSToppltip: true,
                    BSTooltipSelector: "[data-toggle='tooltip']",
                    //Enable Fast Click. Fastclick.js creates a more
                    //native touch experience with touch devices. If you
                    //choose to enable the plugin, make sure you load the script
                    //before AdminLTE's app.js
                    enableFastclick: true,
                    //Control Sidebar Options
                    enableControlSidebar: true,
                    controlSidebarOptions: {
                        //Which button should trigger the open/close event
                        toggleBtnSelector: "[data-toggle='control-sidebar']",
                        //The sidebar selector
                        selector: ".control-sidebar",
                        //Enable slide over content
                        slide: true
                    },
                    //Box Widget Plugin. Enable this plugin
                    //to allow boxes to be collapsed and/or removed
                    enableBoxWidget: true,
                    //Box Widget plugin options
                    boxWidgetOptions: {
                        boxWidgetIcons: {
                            //Collapse icon
                            collapse: 'fa-minus',
                            //Open icon
                            open: 'fa-plus',
                            //Remove icon
                            remove: 'fa-times'
                        },
                        boxWidgetSelectors: {
                            //Remove button selector
                            remove: '[data-widget="remove"]',
                            //Collapse button selector
                            collapse: '[data-widget="collapse"]'
                        }
                    },
                    //Direct Chat plugin options
                    directChat: {
                        //Enable direct chat by default
                        enable: true,
                        //The button to open and close the chat contacts pane
                        contactToggleSelector: '[data-widget="chat-pane-toggle"]'
                    },
                    //Define the set of colors to use globally around the website
                    colors: {
                        lightBlue: "#3c8dbc",
                        red: "#f56954",
                        green: "#006a31",
                        aqua: "#00c0ef",
                        yellow: "#f39c12",
                        blue: "#0073b7",
                        navy: "#001F3F",
                        teal: "#39CCCC",
                        olive: "#3D9970",
                        lime: "#01FF70",
                        orange: "#FF851B",
                        fuchsia: "#F012BE",
                        purple: "#8E24AA",
                        maroon: "#D81B60",
                        black: "#222222",
                        gray: "#d2d6de"
                    },
                    //The standard screen sizes that bootstrap uses.
                    //If you change these in the variables.less file, change
                    //them here too.
                    screenSizes: {
                        xs: 480,
                        sm: 768,
                        md: 992,
                        lg: 1200
                    }
                };
            </script>

            <!-- REQUIRED STYLE SHEETS -->
            <?php foreach ($css_incl as $key => $value) {
                echo $value;
            } ?>


            <!-- REQUIRED JS SCRIPTS -->
            <?php foreach ($js_incl as $key => $value) {
                echo $value;
            } ?>

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

            <!-- Ajax (Asynchronous JavaScript and XML) -->
            <script>
                window.onpopstate = function (event) {
                    $(".content-wrapper").load(document.location, function (data) {
                        $(".content-wrapper").html(data).fadeIn(1500).delay(6000);
                    });
                };


                function Ajax(link) {
                    /*
                     var isChromium = window.chrome,
                     winNav = window.navigator,
                     vendorName = winNav.vendor,
                     isOpera = winNav.userAgent.indexOf("OPR") > -1,
                     isIEedge = winNav.userAgent.indexOf("Edge") > -1,
                     isIOSChrome = winNav.userAgent.match("CriOS");

                     if(isIOSChrome){
                     // is Google Chrome on IOS
                     } else if(isChromium !== null && isChromium !== undefined && vendorName === "Google Inc." && isOpera == false && isIEedge == false) {
                     // is Google Chrome
                     } else {
                     // not Google Chrome
                     if (link != '#' && link != '') {

                     event.preventDefault();
                     history.pushState(null, '', link.href);
                     var content = $(".content-wrapper");
                     content.load(link.href, function (data) {
                     content.html(data).fadeIn(1500).delay(6000); });

                     }
                     }
                     */
                }
            </script>
        </head>


        <body class="hold-transition skin-green layout-boxed sidebar-mini sidebar-collapse">
        <div class="wrapper" style="opacity: .9;">

            <!-- Main Header -->
            <header class="main-header">

                <!-- Logo -->
                <a href="<?= SITE_ROOT ?>" class="logo" onclick="return Ajax(this);">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>S</b>.C</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>Stats</b>.Coach</span>
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu">
                                <!-- Menu toggle button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-envelope-o"></i>
                                    <span class="label label-success">1</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 1 message(s)</li>
                                    <li>
                                        <!-- inner menu: contains the messages -->
                                        <ul class="menu">
                                            <li><!-- start message -->
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <!-- User Image -->
                                                        <img src="<?= SITE_STRIPPED ?>dist/img/user2-160x160.jpg"
                                                             class="img-circle" alt="User Image">
                                                    </div>
                                                    <!-- Message title and timestamp -->
                                                    <h4>
                                                        Support Team
                                                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                    </h4>
                                                    <!-- The message -->
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li><!-- end message -->
                                        </ul><!-- /.menu -->
                                    </li>
                                    <li class="footer"><a href="#">See All Messages</a></li>
                                </ul>
                            </li><!-- /.messages-menu -->
                            <!-- Notifications Menu -->
                            <li class="dropdown notifications-menu">
                                <!-- Menu toggle button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning">10</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 10 notifications</li>
                                    <li>
                                        <div style="height: auto !important;">
                                            <!-- Inner Menu: contains the notifications -->
                                            <ul class="menu">
                                                <li><!-- start notification -->
                                                    <a href="#">
                                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                                    </a>
                                                </li><!-- end notification -->
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="footer"><a href="#">View all</a></li>
                                </ul>
                            </li>
                            <!-- Tasks Menu -->
                            <li class="dropdown tasks-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-flag-o"></i>
                                    <span class="label label-danger">9</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 9 tasks</li>
                                    <li>
                                        <!-- Inner menu: contains the tasks -->
                                        <ul class="menu">
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <!-- Task title and progress text -->
                                                    <h3>
                                                        Design some buttons
                                                        <small class="pull-right">20%</small>
                                                    </h3>
                                                    <!-- The progress bar -->
                                                    <div class="progress xs">
                                                        <!-- Change the css width attribute to simulate progress -->
                                                        <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                             role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                             aria-valuemax="100">
                                                            <span class="sr-only">20% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li><!-- end task item -->
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <a href="#">View all tasks</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="<?= SITE_ROOT . $this->user_profile_pic ?>" class="user-image"
                                         alt="User Image">
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><span
                                        class="hidden-xs"><?= $this->user_first_name ?> <?= $this->user_last_name ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img src="<?= SITE_ROOT . $this->user_profile_pic ?>" class="img-circle"
                                             alt="User Image">
                                        <p>
                                            <?= $this->user_first_name ?> <?= $this->user_last_name ?> - Golf
                                            <small>Member Since <?= date( 'm/d/Y', $this->user_creation_date ) ?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Followers</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">News</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Friends</a>
                                        </div>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= SITE_ROOT ?>users/profile/"
                                               class="btn btn-default btn-flat" onclick="return Ajax(this);">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= SITE_ROOT ?>users/logout/" class="btn btn-default btn-flat">Logout</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>

            </header>

            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar" style="height: auto;">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?= SITE_ROOT . $this->user_profile_pic ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p><?= $this->user_first_name ?> <?= $this->user_last_name ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search...">
                              <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                                    <i class="fa fa-search"></i>
                                </button>
                              </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="header">MAIN NAVIGATION</li>

                        <li class="active treeview">
                            <a href="#">
                                <i class="fa fa-dashboard"></i> <span>Overview</span> <i
                                    class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="active">
                                    <a href="<?= SITE_ROOT ?>" onclick="return Ajax(this)">
                                        <i class="fa fa-circle-o"></i><?= $this->user_first_name ?> <?= $this->user_last_name ?>
                                    </a>
                                </li>
                                <li><a href="#"><i class="fa fa-circle-o"></i>Timeline</a></li>
                                <li>
                                    <a href="#"><i class="fa fa-circle-o"></i> Teams <i
                                            class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="#"><i class="fa fa-circle-o"></i> Barbers Hill High School</a></li>
                                        <li><a href="#"><i class="fa fa-circle-o"></i> NPGA South East Boys</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="#">
                                <i class="fa fa-calendar"></i> <span>Event Schedule</span>
                                <small class="label pull-right bg-red">3</small>
                            </a>
                        </li>


                        <li>
                            <a href="<?= SITE_ROOT ?>Golf/PostScore/" onclick="return Ajax(this);">
                                <i class="fa fa-edit"></i> <span>Post Scores</span>
                            </a>
                        </li>


                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-pie-chart"></i>
                                <span>Full Analytics</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="#"><i
                                            class="fa fa-circle-o"></i> <?= $this->user_first_name ?> <?= $this->user_last_name ?>
                                    </a></li>
                                <li>
                                    <a href="#">Barbers Hill High School <i
                                            class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="#"><i class="fa fa-circle-o"></i> Madilyn Miles</a></li>
                                        <li><a href="#"><i class="fa fa-circle-o"></i> Morgan Miles </a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-angle-left pull-right"></i> NPGA South East Boys</a>
                                    <ul class="treeview-menu">
                                        <li><a href="#"><i class="fa fa-circle-o"></i> Bond, James</a></li>
                                        <li><a href="#"><i class="fa fa-circle-o"></i> Stirling, Scott </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>


                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-table"></i> <span>Tournaments</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="#"><i class="fa fa-circle-o"></i> Vanessa Close </a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Join Tournament</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Past Results</a></li>
                            </ul>
                        </li>

                        <!-- Messages -->

                        <li>
                            <a href="#">
                                <i class="fa fa-envelope"></i> <span>Messages</span>
                                <small class="label pull-right bg-yellow">1</small>
                            </a>
                        </li>

                        <!-- Drills -->

                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i> <span>Drills</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="#"><i class="fa fa-circle-o"></i> Putting</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Approach</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Accuracy</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Distance</a></li>
                            </ul>
                        </li>


                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-laptop"></i>
                                <span>Account Overview</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?= SITE_ROOT ?>users/profile/" onclick="return Ajax(this);"><i class="fa fa-circle-o"></i> Profile
                                        Settings</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Tournament Finder</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Create Team</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Join Team</a></li>
                            </ul>
                        </li>


                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-share"></i> <span>MultiSport</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="#"><i class="fa fa-circle-o"></i> Coming Soon</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Basketball</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Volleyball</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Soccer</a></li>


                            </ul>
                        </li>


                        <li><a href="#"><i class="fa fa-book"></i> <span>Documentation</span></a></li>


                        <li class="header">2016 Overall Leaderboard</li>
                        <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Individual</span></a></li>
                        <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Team Standings</span></a></li>
                        <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Division</span></a></li>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" style="opacity: 1.0;"> <!-- Content Header (Page header) --> <!-- Main content -->
                <?= $output ?>
            </div>
            <!-- /.content-wrapper -->

            <!-- Main Footer -->
            <footer class="main-footer">
                <!-- To the right -->
                <div class="pull-right hidden-xs">
                    Analysing Excellence
                </div>
                <!-- Default to the left -->
                <strong>Copyright &copy; 2016 <a href="#">Stats Coach</a>.</strong> All rights reserved.
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-light">
                <!-- Create the tabs -->
                <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                    <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a>
                    </li>
                    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane active" id="control-sidebar-home-tab">
                        <h3 class="control-sidebar-heading">Recent Activity</h3>
                        <ul class="control-sidebar-menu">
                            <li>
                                <a href="javascript::;">
                                    <i class="menu-icon fa fa-birthday-cake bg-red"></i>
                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
                                        <p>Will be 23 on April 24th</p>
                                    </div>
                                </a>
                            </li>
                        </ul><!-- /.control-sidebar-menu -->

                        <h3 class="control-sidebar-heading">Tasks Progress</h3>
                        <ul class="control-sidebar-menu">
                            <li>
                                <a href="javascript::;">
                                    <h4 class="control-sidebar-subheading">
                                        Custom Template Design
                                        <span class="label label-danger pull-right">70%</span>
                                    </h4>
                                    <div class="progress progress-xxs">
                                        <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                                    </div>
                                </a>
                            </li>
                        </ul><!-- /.control-sidebar-menu -->

                    </div><!-- /.tab-pane -->
                    <!-- Stats tab content -->
                    <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div><!-- /.tab-pane -->
                    <!-- Settings tab content -->
                    <div class="tab-pane" id="control-sidebar-settings-tab">
                        <form method="post">
                            <h3 class="control-sidebar-heading">General Settings</h3>
                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Report panel usage
                                    <input type="checkbox" class="pull-right" checked>
                                </label>
                                <p>
                                    Some information about this general settings option
                                </p>
                            </div><!-- /.form-group -->
                        </form>
                    </div><!-- /.tab-pane -->
                </div>
            </aside><!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>

        </div><!-- ./wrapper -->

        </body>
        </html>


    <?php }
} ?>