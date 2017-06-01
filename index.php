<?php
session_start();

define( 'DS', DIRECTORY_SEPARATOR );
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// These are required for the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false) {
    echo "Internal Server Error"; exit(1); }


new Psr\Autoload;                  //Controller\User::logout(); exit(1);
// new Modules\ErrorCatcher;


function startApplication()
{
    alert("startApplication");

    View\View::newInstance(
        (!WRAPPING_REQUIRES_LOGIN ?: \Model\Helpers\UserRelay::ajaxLogin_Support( \Controller\User::getApp_id() ) ));  // This will fire the users object if logged in

    $route = new Modules\Route( DEFAULT_LANDING_URI );
    
    alert("Bootstap");
    
    require SERVER_ROOT . 'Application/Bootstrap.php';

    exit(1);
}







startApplication();

