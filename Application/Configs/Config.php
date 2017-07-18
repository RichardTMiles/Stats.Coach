<?php

const ¶ = PHP_EOL."\t";

const SITE_TITLE = 'Stats Coach';
const SITE_VERSION = '0.4.0';

if (!isset($_SESSION['X_PJAX_Version']))
    $_SESSION['X_PJAX_Version'] = SITE_VERSION;

define( 'X_PJAX_VERSION' , $_SESSION['X_PJAX_Version']);

################    Reporting   ####################
date_default_timezone_set('America/Phoenix');
ini_set( 'display_errors', 1 );
error_reporting( E_ALL | E_STRICT );
define( 'MINIFY_CONTENTS', false );

################    Database    ####################
/**
 * The following constants are used by the Database
 * Which uses a MYSQL database with a PDO wrapper
 *
 * @constant DB_HOST The databases Host i.e. localhost
 * @constant DB_NAME The name of the location on the database
 * @constant DB_USER The user name if required
 * @constant DB_PASS The users password if applicable
 *
 */
define( 'DB_HOST', 'miles.systems' );
define( 'DB_NAME', 'StatsCoach' );
define( 'DB_USER', 'tmiles199' );
define( 'DB_PASS', 'Huskies!99' );


// This will get the current url on the server, note its capable of HTTP and HTTPS
define( 'URL' , (isset($_SERVER['SERVER_NAME']) ?
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ?
        'https://' : 'http://') . $_SERVER['SERVER_NAME'] : null), true);

define( 'URI', ltrim( urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ), '/' ), true);


################# Application Paths ########################
/**
 * The following constants MUST be used wherever applicable
 * The template and dependence path
 *
 * @constant SITE_ROOT          The current url is automaticaly produced
 * @constant VENDOR             Composers Vendor File
 * @constant ERROR_LOG          Path to the runtime errors file, this can be turned on or off in the Error Class
 * @constant CONTENT_ROOT       Location for the sites template files .tpl.php or .php
 * @constant CONTENT_WRAPPER    Location of the outer content wrapper from <html></html>
 * @constant TEMPLATE_ROOT      Path to the a pre-built template. If template is custom built enter SERVER_ROOT
 * @constant TEMPLATE_PATH      Path to the template for public use i.e. relative path for .css includes
 * @constant WRAPPING_REQUIRES_LOGIN  Bool  If the template wrapper is dependant apon being logged in.
 */
define( 'SITE',             url . DS , true);    // http(s)://example.com/  - do not change
define( 'CONTENT',          DS . 'Public/StatsCoach' . DS );
define( 'VENDOR',           DS . 'Application/Services/vendor' . DS );
define( 'TEMPLATE',         VENDOR  . 'almasaeed2010/adminlte' . DS ); // TEMPLATE HTML FILES PLUGIN HERE
define( 'ERROR_LOG',        SERVER_ROOT . 'Data/Logs/Log_'. time() .'_'.session_id().'.php' );
define( 'VENDOR_ROOT',      SERVER_ROOT . 'Application/Services/vendor' . DS );
define( 'TEMPLATE_ROOT',    VENDOR_ROOT . 'almasaeed2010/adminlte' . DS );
define( 'CONTENT_ROOT',     SERVER_ROOT . 'Public/StatsCoach' . DS );
define( 'CONTENT_WRAPPER',  SERVER_ROOT . 'Application/View/StatsCoach.php' );

const DEFAULT_LOGGED_OUT_MVC  = [ 'User' => 'login' ];      // must be lower?
const DEFAULT_LOGGED_IN_MVC   = [ 'Golf' => 'golf'  ];
define( 'WRAPPING_REQUIRES_LOGIN', false );                 // I use the same headers every where

// More cache control is given in the .htaccess File
header( 'Content-type: text/html; charset=utf-8' );
header( 'Cache-Control: must-revalidate' );


#################   Functions  ######################
/**
 * This will run the application in an MVC style.
 * The classes will be defined in the the first param while
 * the method inside the second param.
 *
 * The flow of the application is
 *
 *  `Controller` -> `Model` -> `View`
 *
 * The controller should work with the request class to help validate all data
 * received from the user. If all data is preceived to be valid (by type) then
 * we should return true or a value which will evaluate to true with (==) double equal,
 * else false.
 *
 * The model will only be fetched and method executed if the previous controller
 * returns true (or mixed). The mixed value will be passed to the models constructor and method.
 * This will communicate with the database (if applicable) to
 * further validate, update, or fetch information.
 *
 * If no errors have been raised then the view will be executed. We created a self
 * stored instance in the index to handle the request throughout the application. This
 * allows us to call the view->contents() method statically even though it is defined as
 * a private method.
 *
 * Singletons functionality in the view ensures constructor is called and reset
 * when needed & in conjunction to any sterilized data also present.
 *
 * We keep the contents function private and call this way to allow frontend developers
 * to include global valuables using `$this->`
 *
 * @param $class string Name of the class within the controller and model folder
 *  and a folder name in the CONTENT_ROOT
 *
 * @param $method string Name of the method in the above parameter, and file name
 *  for the template.
 *
 * @return void The View->contents() procedure will exit(1)
 */


/**
 * This will run the Application using the above configuration options.
 *
 * This will attempt to load HTTP requests in a V-MVC style sending the outer wrapper to the browser
 * and storing the content in the session.
 *
 * Ajax requests will attempt to load saved content in the session, otherwise it will run the application in
 * an MVC style, skipping the initial content load.
 *
 * @param bool $restart if set to true the application will clear the current uri essentially restarting the application
 *
 * @param callable $default_logged_out When logged out, closure run on null uri application exit with no content sent
 *
 * @param callable $default_logged_in When logged in, closure run on null uri application exit with no content sent
 *
 * @throws Exception
 */


##################   DEV Tools   #################
// This will cleanly print the var_dump function and kill the execution of the application

/**
 * This will cleanly print the var_dump function and kill the execution of the application.
 *
 * This function is for development purposes. The function accepts one value to printed on
 * the browser. If the value passes is empty or null the function will print all variables
 * in the $GLOBAL scope.
 *
 * @param mixed $mixed Will be run throught the var_dump function.
 *
 * @return die(1);
 */
function sortDump(...$mixed)
{
    $mixed=(count($mixed) == 1 ? array_pop( $mixed ) : $mixed );
    $view = \View\View::getInstance();
    ob_start();
    echo '<pre>';
    debug_zval_dump( $mixed?:$GLOBALS );
    echo '</pre><br>####################### VAR DUMP ######################<br><pre>';
    var_dump( $mixed );
    echo '</pre><br><br><pre>';
    echo "################## BACK TRACE ###################\n";
    var_dump( debug_backtrace( ) );
    echo '</pre>';
    $report = ob_get_clean();
    if ($view->ajaxActive()) echo $report;
    else $view->currentPage = base64_encode( $report );
    exit(1);
}

/**
 * This ports the javascript alert function to work in PHP. Note output is sent to the browser
 *
 * @param string $string will be placed in the javascript alert function.
 *
 * @return null
 */
function alert($string = "Stay woke.")
{
    print "<script>alert('$string')</script>";
}
// http://php.net/manual/en/debugger.php
function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}





