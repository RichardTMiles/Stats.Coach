<?php

define( 'SITE_TITLE', 'Stats Coach' );

################    Reporting   ####################
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
$url = (isset($_SERVER['SERVER_NAME']) ?
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ?
        'https://' :
        'http://') . $_SERVER['SERVER_NAME'] : null);


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
define( 'SITE_PATH', $url . DS );          // http(s)://example.com/  - do not change
define( 'VENDOR', SERVER_ROOT . 'Application' . DS . 'Services' . DS . 'vendor' . DS );
define( 'ERROR_LOG', SERVER_ROOT . 'Data' . DS . 'Logs' . DS . 'Logs.php' );
define( 'CONTENT_ROOT', SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS );
define( 'CONTENT_PATH', SITE_PATH . 'Public' . DS . 'StatsCoach' . DS );
define( 'CONTENT_WRAPPER', CONTENT_ROOT . 'AthleteLayout.php' );
define( 'TEMPLATE_ROOT', VENDOR . 'almasaeed2010' . DS . 'adminlte' . DS );
define( 'TEMPLATE_PATH', DS . 'Application/Services/vendor/almasaeed2010/adminlte' . DS ); // TEMPLATE HTML FILES PLUGIN HERE
define( 'WRAPPING_REQUIRES_LOGIN', true );
define( 'DEFAULT_LOGGED_IN_URI', 'home/' );       // must be lower?
define( 'DEFAULT_LOGGED_OUT_URI', 'login/' );


// More cache control is given in the .htaccess File
header( 'Cache-Control: must-revalidate' );
header( 'Content-type: text/html; charset=utf-8' );


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
function mvc($class, $method)
{
    $controller = "Controller\\$class";
    $model = "Model\\$class";

    $controller::clearInstance();
    $controller = $controller::getInstance();   // debating to clear the instance

    if (($argv = $controller->$method()) !== false) {

        sortDump($model);

        $model::clearInstance();
        $model = $model::getInstance( $argv );
        $model->$method( $argv );
    }

    // Relies on the Singleton Trait - content is private but uses attempts to use the current instance
    View\View::contents( $class, $method );    // this will exit(1) on success
}


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
function startApplication($restart = false, callable $default_logged_out = null, callable $default_logged_in = null)
{

    // $_SESSION['id'] = null; exit(1);
    // sortDump(Model\User::ajaxLogin_Support());
    if ($restart) Model\User::clearInstance();
    $GLOBALS['user'] = $user = Model\User::clearInstance(Model\User::ajaxLogin_Support());
    $app_id = $user->user_id;


    $wrapper = $GLOBALS['closures']['wrapper'] = function () use ($app_id) {
        return (!WRAPPING_REQUIRES_LOGIN ?: $app_id); };

    if ($restart || $_SERVER['REQUEST_URI'] == null) {
        $_POST = null;
        $_SERVER['REQUEST_URI'] = ($restart === true ?
            ($user->user_id ? DEFAULT_LOGGED_IN_URI : DEFAULT_LOGGED_OUT_URI) :
            ($restart ?: null));
    }

    View\View::clearInstance();             // This will help us remove any stored templates if restarted
    View\View::getInstance( $wrapper() );   // Un-sterilize and call the wake up fn if possible
    // or construct and send the users content wrapper if not an ajax request

    // This will clear the uri, so if we must restart it will be with `default` options
    $route = new Modules\Route(
        $default_logged_out,                  // default logged out accepts Closure
        $default_logged_in,                   // default logged in  accepts Closure
        $user->user_id);           // Signned in?


    require SERVER_ROOT . 'Application/Bootstrap.php';

    exit(1);  // If this is reached the route destructor should execute the home method if valid,
    // closures provided, or startApplication(true) which replaces the given uri
}

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
    unset($_SERVER);
    echo '<pre>';
    if (count($mixed) == 1) {
        debug_zval_dump( $mixed[0] );
        echo '</pre><br><br><pre>';

    } else {
        var_dump( (count( $mixed ) == 0 ? $GLOBALS : $mixed) );
        echo '</pre><br><br><pre>';
    }

    var_dump(debug_backtrace());

    echo '</pre>';
    die(1);         // note minify.php is dependant of $_SERVER.. TODO - ?
}

/**
 * This ports the javascript alert function to work in PHP. Note output is sent to the browser
 *
 * @param string $string will be placed in the javascript alert function.
 *
 * @return null
 */
function alert($string = "Made it!")
{
    print "<script>alert('$string')</script>";
}






