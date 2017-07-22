<?php switch(pathinfo( $_SERVER['REQUEST_URI'] , PATHINFO_EXTENSION)) {
    case 'css': case 'js': case 'php': case 'jpg': case 'png': exit(1);    // A request has been made to an invalid file
    default: }

const DS = DIRECTORY_SEPARATOR;

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

session_save_path(SERVER_ROOT . 'Data/Sessions');    // Manually Set where the Users Session Data is stored

ini_set('session.gc_probability', 1);               // Clear any lingering session data in default locations

session_start();    // Receive the session id from the users Cookies (browser) and load variables stored on the server

// These are required for  the app to run. You must edit the Config file for your Servers
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Modules/Singleton.php') == false ||             // Trait that defines magic methods for session and application portability
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    (include SERVER_ROOT . 'Application/Services/vendor/autoload.php') == false){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // Composer Autoloader
    exit(1);
}

Modules\Helpers\Reporting\ErrorCatcher::start();


function startApplication($restart = false)
{
    global $user, $team, $course;

    if ($restart) {
        $_POST = [];
        if ($reset = ($restart === true))
            $user = $team = $course = null;
        Model\User::newInstance();      // This will reset the stats too.
        View\View::newInstance($reset);
        Modules\Request::changeURI($reset ? '/' : $restart);
        $restart = $reset;
    }

    Modules\Request::sendHeaders();     // Send any stored headers
    
    
    Model\User::getInstance();


    View\View::getInstance();


    $route = new Modules\Route( function ($class, $method, &$argv = []) use ($restart) {
        $controller = "Controller\\$class";
        $model = "Model\\$class";

        if ($restart) if (array_key_exists( 'Modules/Singleton', class_uses($model, true)))
            $model::clearInstance();

        try {
            if (!empty($argv = call_user_func_array( [$controller::getInstance(), "$method"], $argv )))
                call_user_func_array( [$model::getInstance($argv), "$method"],  is_array($argv) ? $argv : [$argv]);
        } catch (\Modules\Helpers\Reporting\PublicAlert $e){

        } finally {
            \Modules\Helpers\DataFetch::verify();
        };
        View\View::getInstance()->content( $class, $method );
    });

    include SERVER_ROOT . 'Application/Bootstrap.php';

} 

startApplication();
