<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Stats | Coach</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    <!-- REQUIRED STYLE SHEETS -->
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>bootstrap/css/bootstrap.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>dist/css/skins/_all-skins.css">
    <!-- DataTables.Bootstrap -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/datatables/dataTables.bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>dist/css/skins/skin-green.css">
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/select2/select2.min.css">
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/morris/morris.css">

    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!--
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/daterangepicker/daterangepicker-bs3.css">
    -->
    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>
<style>
    .content-wrapper {
        /* This image will be displayed fullscreen
        /Public/StatsCoach/img/augusta-master.jpg
        http://site.rockbottomgolf.com/blog_images/Hole%2012%20-%20Imgur.jpg
        */
        background: url('/Public/StatsCoach/img/augusta-master.jpg') no-repeat center fixed;

        scroll-x /* Ensure the html element always takes up the full height of the browser window */ min-height: 100%;
        /* The Magic */
        background-size: cover;
    }

    body {
        background-color: transparent;
    }

</style>

<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-red layout-top-nav">
<div class="wrapper">

    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="<?= SITE_ROOT ?>" class="navbar-brand"><b>Stats</b>.Coach</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <!-- class="active" -->
                        <li>
                            <a href="#">Calendar<span class="sr-only">(current)</span></a></li>
                        <li>
                            <a href="<?=SITE_ROOT?>PostScore/">Post Score</a></li>
                        <li class="dropdown">
                            <a href="" class="dropdown-toggle" data-toggle="dropdown">Menu<span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Barbers Hill</a></li>
                                <li><a href="#">FPGA</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Join a Team</a></li>
                                <li class="divider"></li>
                                <li><a href="<?=SITE_ROOT?>AddCourse/">Add Course</a></li>
                            </ul>
                        </li>
                    </ul>
                    <!--
                    <form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
                        </div>
                    </form>
                    -->
                </div>
                <!-- /.navbar-collapse -->
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <!-- Menu toggle button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the messages -->
                                    <ul class="menu">
                                        <li><!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <!-- User Image -->
                                                    <img src="<?=TEMPLATE_PATH?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                                </div>
                                                <!-- Message title and timestamp -->
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <!-- The message -->
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <!-- end message -->
                                    </ul>
                                    <!-- /.menu -->
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li>
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
                                    <!-- Inner Menu: contains the notifications -->
                                    <ul class="menu">
                                        <li><!-- start notification -->
                                            <a href="#">
                                                <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                            </a>
                                        </li>
                                        <!-- end notification -->
                                    </ul>
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
                                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">20% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <!-- end task item -->
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
                                <img src="<?= $this->user_profile_pic ?>" class="user-image" alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><?= $this->user_full_name ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="<?=$this->user_profile_pic?>" class="img-circle" alt="User Image">

                                    <p>
                                        <?=$this->user_full_name?>
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="row">
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Followers</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Teams</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Friends</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?= SITE_ROOT ?>Profile/" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?= SITE_ROOT ?>Logout/" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-custom-menu -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </header>


    <!-- Full Width Column -->
    <div class="content-wrapper" style="opacity: .9; background-color: transparent;">
        <div class="container" id="ajax-content">
            <!-- Content Header (Page header) -->


            <!-- /.content -->
        </div>
        <!-- /.container -->
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer" style="">
        <div class="container">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2014-2016 <a href="http://lilRichard.com">Richard Miles</a>.</strong> All rights
            reserved.
        </div>
        <!-- /.container -->
    </footer>
</div>
<!-- ./wrapper -->


<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>

<!-- Background -->
<script src="<?= SITE_ROOT ?>Public/JavaScript/jquery.backstretch.min.js"></script>

<!-- Menu Options -->
<script src="<?= TEMPLATE_PATH ?>plugins/select2/select2.full.min.js"></script>


<script src="<?= TEMPLATE_PATH ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>plugins/fastclick/fastclick.min.js"></script>
<script src="<?= TEMPLATE_PATH ?>dist/js/app.min.js"></script>
<script src="<?=SITE_ROOT?>Public/Jquery-Pjax/jquery.pjax.js"></script>
<!-- AdminLTE for demo purposes -->
<script>
    $(function () {     // on Document Load
        // initial content

        $.pjax.reload('#ajax-content');

        // every href on 'a' element
        $(document).pjax('a', '#ajax-content');

    });
</script>


</body>
</html>
