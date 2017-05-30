<?php
session_start();

define( 'DS', DIRECTORY_SEPARATOR );
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// These are required for the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false) {
    echo "Internal Server Error"; exit(1); }



new Psr\Autoload;                   //Controller\User::logout();
new Modules\ErrorCatcher;
View\View::newInstance();           // This will fire the users object
// TODO - I think there needs to be further seperation between the users class and view

$route = new Modules\Route( DEFAULT_LANDING_URI );

require SERVER_ROOT . 'Application/Bootstrap.php';


