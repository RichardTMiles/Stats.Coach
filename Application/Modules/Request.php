<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/13/17
 * Time: 4:28 AM
 */

namespace Modules;


class Request
{
    use Singleton;
    const Singleton = true;

    private $Cookie = [];

    public function __wakeup()
    {
        foreach ($this->Cookie as $key => $array)
            $this->setCookie( $key, $array[0], $array[1] );
        $this->Cookie = null;
    }
    
    ########################## Browser Storage #############################
    private function setCookie($key, $value = null, $time = 604800) // Week?
    {
        if (headers_sent()) return $this->Cookie[] = [ $key => [$value,$time]];
        session_regenerate_id( true );
        return setcookie( $key, $value, time() + $time, '/', $_SERVER['SERVER_NAME'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'), true );
    }

    ########################### Request data ###############################

    public function post(...$argv)
    {
        $this->storage = null;
        $closure = function ($key) {
            if (array_key_exists( $key, $_POST )) {
                $this->storage[] = $_POST[$key];
                $_POST[$key] = null;
            } else $this->storage[] = false;
        };
        if (count( $argv ) == 0 || !array_walk( $argv, $closure )) $this->storage = $_POST;
        return $this;
    }

    public function cookie(...$argv)
    {
        $this->storage = null;
        $cookie = function ($key) {
            if (array_key_exists( $key, $_COOKIE )) {
                $this->storage[] = htmlspecialchars( $_COOKIE[$key] );
                $_COOKIE[$key] = null;
            } else $this->storage[] = false;
        };
        if (count( $argv ) == 0 || !array_walk( $argv, $cookie )) $this->storage = $_COOKIE;
        return $this;
    }

    public function files(...$argv)
    {
        $this->storage = null;
        $closure = function ($key) {
            if (array_key_exists( $key, $_FILES )) {
                $this->storage[] = $_FILES[$key];
                $_FILES[$key] = null;
            } else $this->storage[] = false;
        };
        if (count( $argv ) == 0 || !array_walk( $argv, $closure )) $this->storage = $_FILES;
        return $this;
    }


    #private static $array;

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
        $arg = func_get_args();
        array_walk( $arg, function ($key) {
            if (array_key_exists( $key, $this->storage )) unset($this->storage[$key]);
        } );
        return $this;
    }

    /*
    private function flash()
    {
        $_SESSION['OLDRequest'][] = $this->storage;
        return $this;
    }

    private function old($key = null)
    {
        return (!empty($key) ? $_SESSION['OLDRequest'][$key] : $_SESSION['OLDRequest']);
    }
    */

    ########################## Validating ##############################
    public function clearCookies()
    {
        $only = array_keys( $this->storage );
        $this->storage = null;
        if (is_array( $only ))
            foreach ($only as $key => $value)
                $this->setCookie( $value );
        else $this->setCookie( $only );
    }


    public function phone()
    {
        return (preg_match( '#((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}#', $this->storage[0] ) ? $this->storage[0] : false);
    }

    public function email()
    {
        return filter_var( array_shift( $this->storage ), FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE );
    }

    public function regex($condition)
    {
        $only = $this->storage;
        $this->storage = null;
        $regex = function ($key) use ($condition) {
            return $this->storage[] = (preg_match( $condition, $key ) ? $key : false);
        };
        return (is_array( $only ) && array_walk( $only, $regex ) ?
            count( $this->storage ) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift( $regex( $only ) ));
    }

    public function value()
    {
        return count( $this->storage ) > 1 ? $this->storage : array_shift( $this->storage );
    }


    public function text()
    {
        return $this->regex('/([^\w])+/');
    }

    public function alnum()
    {
        $only = $this->storage;
        $this->storage = null;
        $alphaNumeric = function ($key) use ($only) {
            return $this->storage[] = (ctype_alnum( $key ) ? $key : false);
        };
        return (is_array( $only ) && array_walk( $only, $alphaNumeric ) ?
            count( $this->storage ) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift( $alphaNumeric( $only ) ));
    }

    public function int()
    {
        $only = $this->storage;
        $this->storage = null;
        $integer = function ($key) use ($only) {
            return $this->storage[] = (preg_match( "/([0-9])+/", $key ) ? $key : false);
        };
        return (is_array( $only ) && array_walk( $only, $integer ) ?
            count( $this->storage ) > 1 ? $this->storage : array_shift( $this->storage ) :
            array_shift( $integer( $only ) ));
    }


}