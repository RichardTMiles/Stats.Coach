<!DOCTYPE html>
<html>
<?php

include_once SERVER_ROOT . PUBLIC_FOLDER . 'StatsCoach/Head.php';
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav" style="background: transparent">
<div class="wrapper" style="background: transparent">

    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="<?= site ?>" class="navbar-brand"><b>Stats</b>Coach</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">


                    <form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
                        </div>
                    </form>


                </div>
                <!-- /.navbar-collapse -->
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="active"><a href="<?=site?>login/">Login <span class="sr-only">(current)</span></a></li>


                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">Register</span>

                            </a>

                        </li>
                    </ul>
                </div>
                <!-- /.navbar-custom-menu -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </header>
    <!-- Full Width Column -->
    <div class="content-wrapper" style="background: transparent">
        <div class="container">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1 style="color: ghostwhite">
                    Top Navigation
                    <small style="color: ghostwhite">Example 2.0</small>
                </h1>
                <ol class="breadcrumb" style="color: ghostwhite">
                    <li><a href="#" style="color: ghostwhite"><i class="fa fa-dashboard" style="color: ghostwhite"></i> Home</a></li>
                    <li><a href="#" style="color: ghostwhite">Layout</a></li>
                    <li class="active" style="color: ghostwhite">Top Navigation</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6">

                        <div class="callout callout-danger">
                            <p>Add the layout-top-nav class to the body tag to get this layout. This feature can also be
                                used
                                with a
                                sidebar! So use this class if you want to remove the custom dropdown menus from the
                                navbar
                                and
                                use regular
                                links instead.</p>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <h4>Warning!</h4>

                            <p>The construction of this layout differs from the normal one. In other words, the HTML
                                markup of
                                the navbar
                                and the content will slightly differ than that of the normal layout.</p>
                        </div>

                    </div>
                </div>


                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Blank Box</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-2 col-md-offset-5">
                        <button type="button" class="btn btn-block btn-info btn-sm">Info</button>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.4.0
            </div>
            <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
            reserved.
        </div>
        <!-- /.container -->
    </footer>
</div>
<!-- ./wrapper -->

<?php
include_once SERVER_ROOT . PUBLIC_FOLDER . 'StatsCoach/Styles.php';
include_once SERVER_ROOT . PUBLIC_FOLDER . 'StatsCoach/Scripts.php';
?>

</body>
</html>

