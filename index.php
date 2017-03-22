<?php
session_start();

error_reporting( E_ALL | E_STRICT );
ini_set( 'display_errors', 1 );

// Clock the runtime?
// $time_pre = microtime(true);
// $loadTime = (ceil((microtime(true) - $GLOBALS['time_pre'])/.0001) * .00001); // Seconds


// First step in debugging
function sortDump($mixed = 0) {
    echo '<pre>';
    var_dump(($mixed == 0 ? $GLOBALS : $mixed));
    echo '</pre>';
    die();
    // return null;
}


define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', dirname( __FILE__ ) . DS);

// These are required for the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false)
    echo "Internal Server Error";

// This instantiates Autoload and runs the first function call
$autoLoad = new Psr\Autoload;

$autoLoad->addNamespace( 'Psr',         '/Application/Standards' );
$autoLoad->addNamespace( 'Modules',     '/Application/Modules' );
$autoLoad->addNamespace( 'Controller',  '/Application/Controller' );
$autoLoad->addNamespace( 'Model',       '/Application/Model' );
$autoLoad->addNamespace( 'View',        '/Application/View' );
$autoLoad->addNamespace( 'App',         '/Application');
// Our common case first should handel the Tests Namespace

//$errorReporting = function() { new \Modules\ErrorCatcher; };
//set_error_handler( $errorReporting );
//set_exception_handler( $errorReporting );

// The application must return data if request is made with .pJax() (ajax)  } else { infinite loop; }
require SERVER_ROOT . 'Application/Bootstrap.php';


