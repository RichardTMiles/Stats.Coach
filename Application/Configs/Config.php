<?php

################    Reporting   ####################
ini_set( 'display_errors', 1 );
error_reporting( E_ALL | E_STRICT );

################    Database    ####################
define ('DB_HOST', 'miles.systems');
define ('DB_NAME', 'StatsCoach'   ); // HomingDevice , StatsCoach
define ('DB_USER', 'tmiles199'    );
define ('DB_PASS', 'Huskies!99'   );


$url = (isset($_SERVER['SERVER_NAME']) ?
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ?
        'https://' :
        'http://') . $_SERVER['SERVER_NAME'] : null);


#################   Template ########################
define( 'SITE_ROOT',       $url .   DS );          // The base URL
define( 'ERROR_LOG',       SERVER_ROOT . 'Data'   . DS . 'Logs'       . DS . 'Logs.php'  );
define( 'CONTENT_ROOT',    SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS );
define( 'CONTENT_WRAPPER', CONTENT_ROOT. 'TopNav.php');
define( 'TEMPLATE_ROOT',   SERVER_ROOT . 'Public' . DS . 'AdminLTE'   . DS);
define( 'TEMPLATE_PATH' ,           DS . 'Public' . DS . 'AdminLTE'   . DS);
define( 'DEFAULT_LANDING_URI',           'Login/' );
define( 'WRAPPING_REQUIRES_LOGIN',          true  );


// More cache control is given in the .htaccess File
header( 'Cache-Control: must-revalidate' );  // valid for one day
header( 'Content-type: text/html; charset=utf-8' );


#################   Functions  ######################
function mvc($class, $method)
{
    $controller = "Controller\\$class";
    $model = "Model\\$class";

    if (( new $controller )->$method())
        ( new $model )->$method();

    View\View::contents( $class, $method );    // this will exit(1);

}

function startApplication()
{
    $userStatus = Controller\User::getApp_id();

    $wrapper = $GLOBALS['closures']['wrapper'] = function () use ($userStatus) {
        return (!WRAPPING_REQUIRES_LOGIN ?: Model\User::ajaxLogin_Support( $userStatus )); };

    View\View::clearInstance();
    View\View::getInstance( $wrapper() );

    // This will clear the uri, so if we must restart it will be with `default` options
    $route = new Modules\Route(
        function () { mvc( 'User', 'login' ); } ,         // default logged out
        function () { mvc( 'Golf', 'golf' ); } ,         // default logged in
        $userStatus );
    
    require SERVER_ROOT . 'Application/Bootstrap.php';

    exit(1);
}


##################   DEV Tools   #################
function sortDump($mixed = null)
{
    unset($_SERVER);
    echo '<pre>';
    var_dump( ($mixed === null ? $GLOBALS : $mixed) );
    echo '</pre>';
    die(0);
}

function alert($string = "Made it!")
{
    print "<script>alert('$string')</script>";
}




