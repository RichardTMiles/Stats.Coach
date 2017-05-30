<?php

################    Reporting   ####################
ini_set( 'display_errors', 1 );
error_reporting( E_ALL | E_STRICT );

################    Database    ####################
define ('DB_HOST', 'miles.systems');
define ('DB_NAME', 'StatsCoach'); // HomingDevice , StatsCoach
define ('DB_USER', 'tmiles199');
define ('DB_PASS', 'Huskies!99');

define( 'ERROR_LOG', SERVER_ROOT  . 'Data/Logs/Logs.php' );

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ?
        'https://' : 'http://') . $_SERVER['SERVER_NAME'];

if (strlen( $url ) <= 10) $url = null;    // For IDE Support

define( 'SITE_ROOT', $url . DS);          // The base URL
define( 'TEMPLATE_ROOT',  SERVER_ROOT . 'Public' . DS . 'AdminLTE' . DS);
define( 'TEMPLATE_PATH' , DS . 'Public' . DS . 'AdminLTE' . DS);

$url .= $_SERVER['REQUEST_URI'];         // Now the full url and uri


define( 'DEFAULT_LANDING_URI', 'Login/' );


// Switch to html, utf-8
// More control is given in the .htaccess File
header( 'Cache-Control: must-revalidate' );  // valid for one day
header( 'Content-type: text/html; charset=utf-8' );



#################   Functions  #################

// First step in debugging
function sortDump($mixed = null)
{
    unset($_SERVER);
    echo '<pre>';
    var_dump( ($mixed === null ? $GLOBALS : $mixed) );
    echo '</pre>';
    die();
}

function alert($string = "Made it!")
{
    print "<script>alert('$string')</script>";
}

function mvc($class, $method, $access = false)
{
    if ($access != false) Controller\User::$access();
    $controller = "Controller\\$class";
    $model = "Model\\$class";
    
    if (( new $controller )->$method())
        ( new $model )->$method();

    \View\View::contents( $class, $method );

}

function restart()
{
    \View\View::newInstance();
    mvc( 'Golf', 'golf', 'protectPage');
    exit(1);
}

