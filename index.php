<?php

const DS = DIRECTORY_SEPARATOR;
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

// Is this an https request to a file
if (!defined('SOCKET') && pathinfo( $_SERVER['REQUEST_URI'] , PATHINFO_EXTENSION) != null) {
    if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
        echo include SERVER_ROOT . 'robots.txt';
        exit(1);
    }
    ob_start();
    echo $_SERVER['REQUEST_URI'];
    $report = ob_get_clean();
    $file = fopen(SERVER_ROOT . 'Data/Logs/Request/url_'.time().'.log' , "a");
    fwrite( $file, $report );
    fclose( $file );
    exit(0);    // A request has been made to an invalid file
}

// These files are required for the app to run. You must edit the Config file for your Servers
if (false == (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    false == (include SERVER_ROOT . 'Application/Configs/Config.php')     ||
    false == (include SERVER_ROOT . 'Application/Services/vendor/autoload.php')){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // Composer Autoloader
    exit(3);
}

Modules\Error\ErrorCatcher::start();

Modules\Request::sendHeaders();     // Send any stored headers

$user = $team = $course = $tournaments = array();   // For clarity

Modules\Helpers\Serialized::start('user','team','course','tournaments');  // Pull theses from session, and store on shutdown

function startApplication($restart = false)
{
    static $count = 0;

    if ($restart) {
        $_POST = [];
        if ($reset = ($restart === true)) 
            Modules\Helpers\Serialized::clear();
        Modules\Request::changeURI($reset ? '/' : $restart);    // dynamically using pjax and headers
        $user = Model\User::newInstance();                      // This will reset the stats too.
        $view = View\View::newInstance($reset);
        $restart = $reset;
    }

    $user = $user ?? Model\User::getInstance();     // if(AJAX && $_SESSION['id']) sortDump( $GLOBALS );
    $view = $view ?? View\View::getInstance();
    
    $mvc = function ($class, $method, &$argv = []) use ($restart, &$view) {
        $controller = "Controller\\$class";
        $model = "Model\\$class";

        try {
            if (!empty($argv = call_user_func_array( [$controller::getInstance(), "$method"], $argv )))
                call_user_func_array( [$model::getInstance($argv), "$method"],  is_array($argv) ? $argv : [$argv]);
        } catch (Modules\Error\PublicAlert $e) {
        } catch (TypeError $e) {
            \Modules\Error\PublicAlert::danger( $e->getMessage() ); // TODO - Change logging
        } finally { Modules\Helpers\Entities::verify(); };

        $view->content( $class, $method );
    };

    $route = new Modules\Route( $mvc );

    if (!$count++) include SERVER_ROOT . 'Application/Events.php';

    include SERVER_ROOT . 'Application/Route.php';
} 

if (!SOCKET) startApplication();

