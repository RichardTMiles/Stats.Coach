<?php

/* This is the Bootstrap, or back-bone of the site.
 *
 * Another way to look at the bootstrap in an MVC is to
 * use it as a router. Were going to look at the Url of
 * to determine what the user is requesting access to, then
 * we will see if he/she is allowed to access it.
 *
 * We do this by making sure the the user is logged in,
 * aka $_Session['id'] is set...
 *
 * Then well will break apart the url into variables. The
 * variables in the url will, more that likely, include the
 * requested class-name and function within that class
*/

namespace App;

use \App\Modules\Application\UserProtect;
use \App\Modules\Models\UserRelay;
use \App\Modules\Application\Error;


// Abstracted class, Simplify the Run - Create the needed service layers
abstract class ApplicationController
{
    public $data;

    public function __construct($data)
    {            // Sorted / Validated $_GET
        $this->data = $data;
        unset($data);
        return $this->{$this->data['function']}();   // Run the requested function
    }

    protected function pushModel($data)
    {    // This should only be used in the controllers
        $model = '\App\Models\\' . $data['class'];
        return new $model( $data );
    }
}

// Abstracted class, Simplify the Run - Create the needed service layers
abstract class ApplicationModel
{
    protected $UserRelay;
    protected $data;

    public function __construct($data)
    {            // Sorted / Validated $_GET
        extract( $data );
        unset($data);
        $this->UserRelay = new UserRelay;
        if (isset($_SESSION['id'])) {
            extract( $this->UserRelay->profileData( $_SESSION['id'] ) ); // Will return all data into var
            $user_id = $_SESSION['id'];
        }
        $this->data = compact( array_keys( get_defined_vars() ) );
        return $this->{$this->data['function']}();   // Run the requested function
    }
}

/**
 * Hold Data Variable and our Magic Methods
 * __set    __isset     __get
 * RequireFile function
 */
abstract class ApplicationView
{
    public $data;

    protected function requireFile($file)
    {
        if (file_exists( $file ) == true) { // Must Do == True, file_exist doesn't return on false
            require $file;
            return true;
        }
        // TODO - Build and error class / model
        $trace = debug_backtrace();
        trigger_error(
            'Include Error:: ' . $file . '<br />' .
            ' requested from ' . $trace[0]['file'] . '<br />' .
            ' on line ' . $trace[0]['line'] . '<br />',
            E_USER_NOTICE );

        return false;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name)
    {
        if (array_key_exists( $name, $this->data )) {
            return $this->data[$name];
        }
        //Trace should be logged - set up later
        $trace = debug_backtrace();
        trigger_error(
            ' Undefined property via __get(): ' . $name . '<br />' .
            ' in ' . $trace[0]['file'] . '<br />' .
            ' on line ' . $trace[0]['line'] . '<br />',
            E_USER_NOTICE );
        return null;
    }
}

// Our Bootstrap, Lets Sort our url then create our route
class Bootstrap
{

    // Stats.Coach / $class / $function / $parameter / $unique / $id /


    public $class;
    public $function;
    public $parameter;
    public $unique;
    public $id;

    // For our log file is needed
    public $error;

    public function __construct()
    {

        $this->cleanUrl();
        $userProtect = new UserProtect();

        // Lets start by checking our $_SESSION['id'] to see if were logged in
        if ($userProtect->logged_in() == false) {

            // Find out what the logged out user wants
            // There restricted to the users class, and select functions in this code

            if (!empty($this->class) && ($this->class) == "Users" && !empty($this->function)) {
                // Validation Switch:: if we're logged out we can only view these pages
                switch ($this->function) {
                    case 'login':
                        $this->function = "login";
                        break;
                    case 'recover':
                        $this->function = "recover";
                        break;
                    case 'activate':
                        $this->function = "activate";
                        break;
                    case 'register':
                        $this->function = "register";
                        break;
                    default:
                        $this->function = "login";
                }
            } else {

                $this->class = 'Users';
                $this->function = "login";

            }
            $nameSpaced = 'App\Controllers\Users';  // Set the full name space of the redirecting Class
            // Namespace = Full Name and Location (PSR-4)
        } else {    // Logged in

            // TODO - Make more dynamic when other sports added

            if (empty($this->class)) $this->class = "Golf";               // The default logged-in page

            if (empty($this->function)) $this->function = strtolower( $this->class );

            $nameSpaced = 'App\Controllers\\' . $this->class;

        }
        $this->createController( $nameSpaced );
    }


    public function cleanUrl()
    {    // You should comprehend whats going on here before looking at __Construct();
        // These will be evaluated on in this class, so we can just set and move on.
        if (isset($_GET['controller'])) $this->class = ucfirst( strtolower( $_GET['controller'] ) );
        if (isset($_GET['action'])) $this->function = strtolower( $_GET['action'] );

        // For evaluating later....
        if (isset($_GET['parameter'])) {
            $this->parameter = strtolower( $_GET['parameter'] );
        } else {
            $this->parameter = null;
        }

        if (isset($_GET['unique'])) {
            $this->unique = strtolower( $_GET['unique'] );
        } else {
            $this->unique = null;
        }

        if (isset($_GET['id'])) {
            $this->id = strtolower( $_GET['id'] );
        } else {
            $this->id = null;
        }

    }

    public function pushData()
    {
        $error = $this->error;
        $class = $this->class;
        $function = $this->function;
        $parameter = $this->parameter;
        $unique = $this->unique;
        $id = $this->id;

        return (compact( array_keys( get_defined_vars() ) ));
    }


    public function createController($class)
    {

        //does the class exist?
        if (class_exists( $class )) {
            $parents = class_parents( $class );
            //does the class inherit from the BaseController class?
            if (in_array( 'App\ApplicationController', $parents )) {
                //does the requested class contain the requested action as a method?
                if (method_exists( $class, $this->function )) {
                    //Return basically hands off Everything with the needed variables stored from the URL
                    return new $class( $this->pushData() );

                } else {
                    $this->error = "BAD Method Error";
                    //bad controller error
                    return new Error( $this->pushData() );
                }
            } else {
                $this->error = "BAD Parent Error";
                //bad controller error
                return new Error( $this->pushData() );
            }
        } else {
            $this->error = "BAD Class Error";
            //bad controller error
            return new Error( $this->pushData() );
        }
    }
}

