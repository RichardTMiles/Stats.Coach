<?php
session_start();

define( 'DS', DIRECTORY_SEPARATOR );
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// These are required for  the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false
) {
    echo "Internal Server Error";
    exit(1);
}


new Psr\Autoload;                  //Controller\User::logout(); exit(1);
// new Modules\ErrorCatcher;


alert("new Request");

function startApplication()
{
    alert("startApplication");

    $userStatus = \Controller\User::getApp_id();

    $wrapper = function () use ($userStatus) {
        return (!WRAPPING_REQUIRES_LOGIN ?: \Model\User::ajaxLogin_Support( $userStatus ));
    };

    View\View::clearInstance();
    View\View::getInstance( $wrapper() );

    $route = new Modules\Route(
        function () { mvc( 'User', 'login' ); } ,         // default logged out
        function () { mvc( 'Golf', 'golf' ); } ,         // default logged in
        $userStatus );


    alert('bootstrapping');

    require SERVER_ROOT . 'Application/Bootstrap.php';

    alert( "exit index" );

    exit(1);
}

startApplication();
