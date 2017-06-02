<?php
session_start();

define( 'DS', DIRECTORY_SEPARATOR );
define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// These are required for  the app to run. PHP-Standards
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/Singleton.php') == false ||
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false) {
    echo "Internal Server Error";
    exit(1);
}


new Psr\Autoload;                  //Controller\User::logout(); exit(1);
new Modules\ErrorCatcher;

startApplication();
