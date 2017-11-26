<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 *
 *
 * */


const DS = DIRECTORY_SEPARATOR;

define('SERVER_ROOT', dirname(__FILE__) . DS);  // Set our root folder for the application

// These files are required for the app to run. You must edit the Config file for your Servers

if (false == (include SERVER_ROOT . 'Data/vendor/autoload.php')) {       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                             // Composer autoload
    exit(3);
}

Carbon\Carbon::Application(include_once("Application/Configs/Config.php"))();

