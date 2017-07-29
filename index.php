<?php

const ¶ = PHP_EOL;
const DS = DIRECTORY_SEPARATOR;

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

if (pathinfo( $_SERVER['REQUEST_URI'] , PATHINFO_EXTENSION) != null) {
    ob_start();
    echo $_SERVER['REQUEST_URI'];
    $report = ob_get_clean();
    $file = fopen(SERVER_ROOT . 'Data/Logs/Request/url_'.time().'.log' , "a");
    fwrite( $file, $report );
    fclose( $file );
    exit(1);    // A request has been made to an invalid file
}


// These files are required for the app to run. You must edit the Config file for your Servers
if (false == (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    false == (include SERVER_ROOT . 'Application/Configs/Config.php')     ||
    false == (include SERVER_ROOT . 'Application/Services/vendor/autoload.php')){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // Composer Autoloader
    exit(1);
}

$user = $team = $course = $tournaments = array();
Modules\Helpers\Reporting\ErrorCatcher::start();
Modules\Helpers\Serialized::start('user','team','course','tournaments');
// Pull theses from session, and store on shutdown


function startApplication($restart = false)
{
    if ($restart) {
        $_POST = [];
        global $user, $team, $course, $tournaments;
        if ($reset = ($restart === true))
            Modules\Helpers\Serialized::clear();
        Modules\Request::changeURI($reset ? '/' : $restart);
        Model\User::newInstance();      // This will reset the stats too.
        View\View::newInstance($reset);
        $restart = $reset;
    }

    Modules\Request::sendHeaders();     // Send ansy stored headers
    Model\User::getInstance();
    $view = View\View::getInstance();

    $route = new Modules\Route( function ($class, $method, &$argv = []) use ($restart, &$view) {
        $controller = "Controller\\$class";
        $model = "Model\\$class";

        if ($restart && array_key_exists( 'Modules/Singleton', class_uses($model, true)))
            $model::clearInstance();

        try {
            if (!empty($argv = call_user_func_array( [$controller::getInstance(), "$method"], $argv )))
                call_user_func_array( [$model::getInstance($argv), "$method"],  is_array($argv) ? $argv : [$argv]);
        } catch (Modules\Helpers\Reporting\PublicAlert $e){
        } finally { Modules\Helpers\Entities::verify(); };

        $view->content( $class, $method );
    });

    include SERVER_ROOT . 'Application/Bootstrap.php';
} 

startApplication();
