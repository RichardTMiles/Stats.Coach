<?php
define( 'DS', DIRECTORY_SEPARATOR );

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );  // Set our root folder for the application

session_save_path(SERVER_ROOT . 'Data' . DS . 'Sessions');    // Manually Set where the Users Session Data is stored

ini_set('session.gc_probability', 1);               // Clear any lingering session data in default locations

session_start();                // Receive the session id from the users Cookies (browser) and load variables stored on the server


// gc_disable();        -- Due to my paranoia, I have this for testing errors
// register_shutdown_function(function () { gc_enable(); });

// These are required for  the app to run. You must edit the Config file for your Servers
if ((include SERVER_ROOT . 'Application/Configs/Config.php') == false ||
    (include SERVER_ROOT . 'Application/Modules/Singleton.php') == false ||           // Trait that defines magic methods for session and application portability
    (include SERVER_ROOT . 'Application/Standards/AutoLoad.php') == false ||            // PSR4 Autoloader, with common case first added for namespace = currentDir
    (include SERVER_ROOT . 'Application/Services/vendor/autoload.php') == false){       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                                       // These file locations will not change.
    exit(1);
}

// Setting the following parameter to one or zero will turn on the Log file and attempt to catch all errors that slip through
new Modules\ErrorCatcher(1); // This Error Catching system will store any errors on the servers log file defined in the config file
// The current catching system actually blows.. need to remake



startApplication();
