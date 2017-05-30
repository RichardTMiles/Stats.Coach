<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/13/17
 * Time: 4:28 AM
 */

namespace Modules;


use Controller\User;
use Psr\Singleton;

class Request
{
    use Singleton;

    public function __construct()
    {
        // Ajax makes life a little hard when pressing the back button
        // Backing into a previous post state is a thing is a problem..
        if (!array_key_exists("username", $_POST) || !User::loggedIn()) return;
        $_POST["username"] = null;
        $_POST["password"] = null;
        unset($_POST);
    }

    public function is($type)
    {
        $type = 'is_' . strtolower( $type );
        if (function_exists( $type )) return $type( $this->storage );
        throw new \Exception;
    }

    private function has($key)
    {
        return array_key_exists( $key, $this->storage );
    }

    public function except()
    {
        array_walk( func_get_args(), function ($key) {
            if (array_key_exists( $key, $this->storage )) unset($this->storage[$key]);
        } );
    }

    private function flash()
    {
        $_SESSION['OLDRequest'][] = $this->storage;
    }

    private function old($key = null)
    {
        return (!empty($key) ? $_SESSION['OLDRequest'][$key] : $_SESSION['OLDRequest']);
    }

    ########################## Building the storage ########################
    private function post()
    {
        $only = func_get_args();
        $this->storage = null;
        $post = function ($key) {
            if (array_key_exists( $key, $_POST )) {
                $this->storage[] = $_POST[$key];
                $_POST[$key] = null;
            } else $this->storage[] = false;
        }; // the unset function isn't behaving nicely w/ Ajax :: so = null
        if (count( $only ) == 0 || !array_walk( $only, $post )) $this->storage = $_POST;
    }

    private function cookie($key = null)
    {
        $only = func_get_args();
        $this->storage = null;
        $cookie = function ($key) {
            if (array_key_exists( $key, $_COOKIE )) {
                $this->storage[] = $_COOKIE[$key];
                $_COOKIE[$key] = null;
            } else $this->storage[] = false;
        }; // the unset function isn't behaving nicely w/ Ajax :: so = null
        if (count( $only ) == 0 || !array_walk( $only, $cookie )) $this->storage = $_COOKIE;
    }

    private function files($key = null)
    {
        $only = func_get_args();
        $this->storage = null;
        $file = function ($key) {
            if (array_key_exists( $key, $_FILES )) {
                $this->storage[] = $_FILES[$key];
                $_FILES[$key] = null;
            } else $this->storage[] = false;
        }; // the unset function isn't behaving nicely w/ Ajax :: so = null
        if (count( $only ) == 0 || !array_walk( $only, $file )) $this->storage = $_FILES;
    }

    ########################## Validating ##############################
    public function phone()
    {
        return (preg_match( '#((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}#', $this->storage[0] ) ? $this->storage[0] : false);
    }

    private function email()
    {
        return filter_var( array_shift($this->storage), FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE );
    }

    public function regex($condition)
    {
        $only = $this->storage;
        $this->storage = null;
        $regex = function ($key) use ($condition) {
            return $this->storage[] = (preg_match( $condition, $key ) ? $key : false); };
        return (is_array( $only ) && array_walk( $only, $regex ) ?
            count($this->storage) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift($regex( $only )));
    }

    private function value()
    {
        $only = $this->storage;
        $this->storage = null;
        $value = function ($key) use ($only) {
            return $this->storage[] = $key; };
        return (is_array( $only ) && array_walk( $only, $value ) ?
            count($this->storage) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift($value( $only )));
    }

    public function alnum()
    {
        $only = $this->storage;
        $this->storage = null;
        $alphaNumeric = function ($key) use ($only) {
            return $this->storage[] = (ctype_alnum( $key ) ? $key : false); };
        return (is_array( $only ) && array_walk( $only, $alphaNumeric ) ? 
            count($this->storage) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift($alphaNumeric( $only )));
    }

    public function int()
    {
        $only = $this->storage;
        $this->storage = null;
        $integer = function ($key) use ($only) {
            return $this->storage[] = (preg_match( "/([0-9])+/", $key ) ? $key : false); };
        return (is_array( $only ) && array_walk( $only, $integer ) ?
            count($this->storage) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift($integer( $only )));
    }


}