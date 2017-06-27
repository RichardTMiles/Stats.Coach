

    <header class="main-header">
        <!-- Logo -->
        <a href="<?=SITE_PATH?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>S</b>C</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Stats</b>.Coach</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 4 messages</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?=$this->user->user_profile_pic?>" class="img-circle" alt="User Image"/>
                                            </div>
                                            <h4>
                                                Support Team
                                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li><!-- end message -->
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="../../dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                                            </div>
                                            <h4>
                                                AdminLTE Design Team
                                                <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="../../dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                                            </div>
                                            <h4>
                                                Developers
                                                <small><i class="fa fa-clock-o"></i> Today</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="../../dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                                            </div>
                                            <h4>
                                                Sales Department
                                                <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="../../dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                                            </div>
                                            <h4>
                                                Reviewers
                                                <small><i class="fa fa-clock-o"></i> 2 days</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">See All Messages</a></li>
                        </ul>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 10 notifications</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the page and may cause
                                            design problems
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-red"></i> 5 new members joined
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-user text-red"></i> You changed your username
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">View all</a></li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Create a nice theme
                                                <small class="pull-right">40%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">40% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Some task I need to do
                                                <small class="pull-right">60%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">60% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Make beautiful transitions
                                                <small class="pull-right">80%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">80% Complete</span>
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
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?=$this->user->user_profile_pic?>" class="user-image" alt="User Image"/>
                            <span class="hidden-xs"><?=$this->user->user_full_name?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?=$this->user->user_profile_pic?>" class="img-circle" alt="User Image"/>
                                <p>
                                    <?=$this->user->user_full_name?> - <?=$this->user->user_sport?>
                                    <small>Member since <?= date( 'm/d/Y', $this->user->user_creation_date ) ?></small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?=SITE_PATH?>Profile/" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?=SITE_PATH?>Logout/" class="btn btn-default btn-flat">Sign out</a>
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
                    <img src="<?= $this->user->user_profile_pic ?>" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $this->user->user_first_name ?> <?= $this->user->user_last_name ?></p>
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
                            <a href="<?= SITE_PATH ?>" onclick="return Ajax(this)">
                                <i class="fa fa-circle-o"></i><?= $this->user->user_first_name ?> <?= $this->user->user_last_name ?>
                            </a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i>Timeline</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i> Teams <i class="fa fa-angle-left pull-right"></i></a>
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
                    <a href="<?= SITE_PATH ?>PostScore/" onclick="return Ajax(this);">
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
                        <li><a href="<?= SITE_PATH . 'Profile/' ?>" onclick="Ajax(this)"><i
                                    class="fa fa-circle-o"></i> <?= $this->user_first_name ?> <?= $this->user_last_name ?>
                            </a></li>
                        <li>
                            <a href="#">
                                Barbers Hill High School
                            </a>
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
                        <li><a href="<?= SITE_PATH ?>Profile/" onclick="return Ajax(this);"><i class="fa fa-circle-o"></i> Profile
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

