<?php
/*
    This is ths starting point for our Stats.Coach/. Each time you press
    refresh in your browser, or click on a link to our site
    you will be redirected through this file. This frame work designed to
    be a 'pure MVC' written in php.

    We start the website/framework process by declaring some standard
    error preferences.
*/

session_start();    // Start the session

error_reporting( E_ALL | E_STRICT );

ini_set( 'display_errors', 1 );

define( 'DS', DIRECTORY_SEPARATOR );

define( 'SERVER_ROOT', dirname( __FILE__ ) . DS );

// Use this for error checking
function sort_dump($variable)
{
    echo "<pre>";
    var_dump( $variable );
    echo "</pre>";
    exit();
}


/*
    This is the only file includes done manually through out our site due the the implementation of
    an auto loading system. I stick to the 'PHP Standard Recommendation's or PSR-4 for auto loading classes,
    and I also choose to manually include our config file.

    I could error check in one line, but it would be a dynamic expression to large for PHP-Storm
    .. and it looks nicer this way
*/

if (!require_once SERVER_ROOT . 'Application/Configs/config.php') {
    echo "Index Fatal Error.";
    die();
}
if (!require_once SERVER_ROOT . 'Application/Services/Psr4AutoloaderClass.php') {
    echo "Index Fatal Error.";
    die();
}

/*  Now that the Psr4 Auto loader Class is included lets initialise it
 *  by using the function register();
 *
 *  I set the namespaces to closely match the actual file path. I feel
 *  this is the best practice for simplicity. To learn more about PSR-4
 *  see the actual class file.
 */


$autoLoad = new \App\Services\Psr4AutoloaderClass;
$autoLoad->register();
$autoLoad->addNamespace( 'App', SERVER_ROOT . 'Application/' );
$autoLoad->addNamespace( 'App\Modules', SERVER_ROOT . 'Application/Modules/Application/' );
$autoLoad->addNamespace( 'App\Modules', SERVER_ROOT . 'Application/Modules/Models/' );
$autoLoad->addNamespace( 'View', SERVER_ROOT . 'Application/Views/' );
$autoLoad->addNamespace( 'ALITE', SERVER_ROOT . 'Public/AdminLTE2/StatsCoach/' );


/*
 * We have all the basics initialised, so we can move on to our bootstrap.
 * All we need to do is 'create' the class using the proper namespace using
 * the 'new' language construct.
 *
 * Using magic methods such as "__Construct();", we can completely hand-off
 * responsibilities from each unit (Controller, Model, View) while remaining
 * completely independent (stopping 2 way communication).
 *
 * In other words, each time a new class is called we will never again use
 * the class initiating, calling, it.
 */


new \App\Bootstrap();



