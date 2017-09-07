<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 *
 * Let it be known the basic commands of IntelliJ
 *
 * Jump to function definition:     (Command + click)
 *
 */

const DS = DIRECTORY_SEPARATOR;
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

// These files are required for the app to run. You must edit the Config file for your Servers
if (false == (include SERVER_ROOT . 'Application/Configs/Config.php')) {       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                              // Composer autoload
    exit( 3 );
}

use Modules\Session;
use Tables\Users;
use Modules\Route;
use Modules\Helpers\Serialized;
use Modules\Error\PublicAlert;
use Modules\Request;
use Modules\Helpers\Entities;

Serialized::clear();

function startApplication($restartURI = false)
{
    static $view;                                           // TODO - to statics hold objects

    global $user, $team, $course, $tournaments;

    if ($restartURI) {                                      // This will always be se in a socket
        $_POST = [];
        Request::changeURI( $restartURI ?: '/' );    // Dynamically using pjax + headers
        if ( $restartURI === true )                                // This should stop the socket
            Serialized::clear();
    }

    #$_SESSION['id'] = 1;

    Session::update();


    $view = new View\View( $restartURI === true );      // use set headers to determine what view to load

    #sortDump($user);

    $mustache = function ($path, $options = array()) {
        global $json;
        $json = array_merge( is_array( $json ) ? $json : [], is_array( $options ) ? $options : [] );
        $file = SERVER_ROOT . "Public/StatsCoach/Mustache/$path.php";
        if (file_exists( $file ) && is_array( $options = include $file ))
            $json = array_merge( $json, $options );

        $json = array_merge( $json, array(
            'UID' => $_SESSION['id'],
            'Mustache' => SITE . "Public/StatsCoach/Mustache/$path.mst") );

        print json_encode( $json ) . PHP_EOL;
    };

    $mvc = function ($class, $method, &$argv = []) use (&$view) {
        $controller = "Controller\\$class";
        $model = "Model\\$class";

        $run = function ($class, $argv) use ($method) {
            return call_user_func_array( [new $class, "$method"],
                is_array( $argv ) ? $argv : [$argv] );
        };

        try {

            if (!empty( $argv = $run( $controller, $argv ) )) $run( $model, $argv );

        } catch (PublicAlert $e) {
        } catch (TypeError $e) {
            PublicAlert::danger( $e->getMessage() ); // TODO - Change what is logged
        } finally {
            Entities::verify();
        };

        $view->content( $class, $method );
    };

    $route = new Route( $mvc );    // Start the route with the structure of the default route const

    $route->changeStructure( $mustache );   // Switch to the event closure

    include SERVER_ROOT . 'Application/Events.php';

    if (SOCKET) return 1;

    $route->changeStructure( $mvc );

    include SERVER_ROOT . 'Application/Routes.php';
}

if (!SOCKET) startApplication();

