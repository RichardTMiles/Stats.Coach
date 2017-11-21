<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="<?= SITE ?>Home/" class="navbar-brand"><b>Stats</b>.Coach</a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <!-- class="active" -->
                    <li>
                        <a href="<?= SITE ?>PostScore/">Post Score</a></li>
                    <li class="dropdown">
                        <a href="" class="dropdown-toggle" data-toggle="dropdown">Menu<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php if (isset( $my['teams'] )) foreach ($my['teams'] as $team_id) {
                                $myTeam = $this->team[$team_id];
                                echo '<li><a href="' . SITE . 'Team/' . $myTeam['team_code'] . '/">' . $myTeam['team_name'] . '</a></li>';
                            }
                            ?>
                            <li class="divider"></li>
                            <li><a href="<?= SITE ?>JoinTeam/">Join a Team</a></li>
                            <li class="divider"></li>
                            <li><a href="<?= SITE ?>AddCourse/">Add Course</a></li>
                        </ul>
                    </li>
                </ul>

                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
                    </div>
                </form>

            </div>
            <!-- /.navbar-collapse -->

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <?php
                include 'navbar-nav.php'
                ?>
            </div>
            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>


 