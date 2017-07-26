<?php switch(pathinfo( $_SERVER['REQUEST_URI'] , PATHINFO_EXTENSION)) {
    case 'css': case 'js': case 'php': case 'jpg': case 'png': exit(1);    // A request has been made to an invalid file
    default: }

const DS = DIRECTORY_SEPARATOR;

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

session_save_path(SERVER_ROOT . 'Data/Sessions');    // Manually Set where the Users Session Data is stored

ini_set('session.gc_probability', 1);               // Clear any lingering session data in default locations

session_start();    // Receive the session id from the users Cookies (browser) and load variables stored on the server

// These files are required for the app to run. You must edit the Config file for your Servers
if (false == (include SERVER_ROOT . 'Application/Configs/Config.php') ||
    false == (include SERVER_ROOT . 'Application/Modules/Helpers/Serialized.php') ||
    false == (include SERVER_ROOT . 'Application/Modules/Singleton.php')  ||             // Trait that defines magic methods for session and application portability
    false == (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    false == (include SERVER_ROOT . 'Application/Services/vendor/autoload.php')){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // Composer Autoloader
    exit(1);
}

Modules\Helpers\Reporting\ErrorCatcher::start();
Modules\Helpers\Serialized::start('user','team','course','tournaments');

$ajax = (isset($_GET['_pjax']) || (isset($_SERVER["HTTP_X_PJAX"]) && $_SERVER["HTTP_X_PJAX"])) ||
    ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'));

function startApplication($restart = false)
{
    if ($restart) {
        $_POST = [];
        global $user, $team, $course, $tournaments;
        if ($reset = ($restart === true))
            Modules\Helpers\Serialized::clear();
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

        if ($restart && array_key_exists( 'Modules/Singleton', class_uses($model, true)))
            $model::clearInstance();

        try {
            if (!empty($argv = call_user_func_array( [$controller::getInstance(), "$method"], $argv )))
                call_user_func_array( [$model::getInstance($argv), "$method"],  is_array($argv) ? $argv : [$argv]);

        } catch (\Modules\Helpers\Reporting\PublicAlert $e){
        } finally { \Modules\Helpers\DataFetch::verify(); };

        View\View::getInstance()->content( $class, $method );
    });

    include SERVER_ROOT . 'Application/Bootstrap.php';
} 

startApplication();
