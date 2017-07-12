<?php
define( 'DS', DIRECTORY_SEPARATOR );

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

session_save_path(SERVER_ROOT . 'Data' . DS . 'Sessions');    // Manually Set where the Users Session Data is stored

ini_set('session.gc_probability', 1);               // Clear any lingering session data in default locations

switch(pathinfo( $_SERVER['REQUEST_URI'] , PATHINFO_EXTENSION)) {
    case 'css': case 'js': case 'php': case 'jpg': case 'png': exit(1);    // A request has been made to an invalid file
    default: continue;
}

session_start();    // Receive the session id from the users Cookies (browser) and load variables stored on the server

// These are required for  the app to run. You must edit the Config file for your Servers
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Modules/Singleton.php') == false ||             // Trait that defines magic methods for session and application portability
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    (include SERVER_ROOT . 'Application/Services/vendor/autoload.php') == false){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // Composer Autoloader
    exit(1);
}

new Modules\ErrorCatcher(1);

function startApplication($restart = false)
{
    if ($restart) {
        Model\User::newInstance();                   // This will reset the stats too.
        View\View::newInstance($restart === true);   // Un-sterilize and call the wake up fn if possible
        $_SERVER['REQUEST_URI'] = ($restart === true ? $_POST = '/' : $restart);
    }

    $user = Model\User::getInstance();
    $view = View\View::getInstance();
    
    $route = new Modules\Route( function ($class, $method) use ($restart, $view) {
        $controller = "Controller\\$class";
        $model = "Model\\$class";

        if ($restart === true) {
            $controller::clearInstance();
            $model::clearInstance();
        }

        $controller = $controller::getInstance();   // debating to clear the instance
        if (($argv = $controller->$method()) !== false) {
            $model = $GLOBALS[($class = strtolower( $class ))] = $model::getInstance( $argv );
            call_user_func_array( [$model, "$method"], (is_array($argv)?$argv:[$argv]) );
        }

        // Relies on the Singleton Trait - content is private but uses attempts to use the current instance
        $view->contents( $class, $method );    // this will exit(1) on success
    });
    

    include SERVER_ROOT . 'Application/Bootstrap.php';


} 

startApplication();
