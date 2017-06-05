<?php
session_start();

define( 'DS', DIRECTORY_SEPARATOR );
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// These are required for  the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false ||
    (include SERVER_ROOT . 'Application/Services/vendor/autoload.php') == false){
    echo "Internal Server Error";
    exit(1);
}


new Psr\Autoload;
# new Modules\ErrorCatcher;


##################   DEV Tools   #################
function sortDump($mixed = null)
{
    unset($_SERVER);
    echo '<pre>';
    var_dump( ($mixed === null ? $GLOBALS : $mixed) );
    echo '</pre>';
    die(1);
}

function alert($string = "Made it!")
{
    print "<script>alert('$string')</script>";
}

#################### Run Stats Coach ###############
startApplication();
