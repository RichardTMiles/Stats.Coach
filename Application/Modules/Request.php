<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/13/17
 * Time: 4:28 AM
 */

namespace Modules;

use Modules\Interfaces\MagicMethods;

abstract class Request implements MagicMethods
{
    private $storage = array();

    ########################## Manual Input ################################
    public function set(...$argv)
    {
        $this->storage = $argv;
        return $this;
    }

    ########################## Session Storage #############################
    public static function sendHeaders()
    {
        if (isset($_SESSION['Cookies']) && is_array( $_SESSION['Cookies'] ))
            foreach ($_SESSION['Cookies'] as $key => $array) static::setCookie( $key, $array[0], $array[1] );

        if (isset($_SESSION['Headers']) && is_array( $_SESSION['Headers'] ))
            foreach ($_SESSION['Headers'] as $value) static::setHeader( $value );

        unset($_SESSION['Cookies'], $_SESSION['Headers']);
    }

    public static function setCookie($key, $value = null, $time = 604800) // Week?
    {
        if (headers_sent()) $_SESSION['Cookies'][] = [$key => [$value, $time]];
        else return setcookie( $key, $value, time() + $time, '/', $_SERVER['SERVER_NAME'], (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'), true );
    }

    public function clearCookies()
    {
        $only = array_keys( $this->storage );
        $this->storage = null;
        if (is_array( $only ))
            foreach ($only as $key => $value)
                static::setCookie( $value );
        else static::setCookie( $only );
    }       // Supporting function to setCookie

    public static function setHeader($string)
    {
        if (headers_sent()) $_SESSION['Headers'][] = $string;
        else return header( $string, true );
    }

    public static function changeURI($string)
    {
        $_SERVER['REQUEST_URI'] = $string;
        return static::setHeader( "X-PJAX-URL: " . SITE . $string );
    }


    ########################### Request Data ###############################
    private function request($argv, &$array, $noHTML = false)
    {

        $this->storage = null;
        $closure = function ($key) use ($noHTML, &$array) {
            if (array_key_exists( $key, $array )) {
                $this->storage[] = $noHTML ? htmlspecialchars( $array[$key] ) : $array[$key];
                $array[$key] = null;
            } else $this->storage[] = false;
        };
        if (count( $argv ) == 0 || !array_walk( $argv, $closure )) $this->storage = $array;
        return $this;
    }

    public function post(...$argv)
    {
        return $this->request( $argv, $_POST );
    }

    public function cookie(...$argv)
    {
        return $this->request( $argv, $_COOKIE, true );
    }

    public function files(...$argv)
    {
        return $this->request( $argv, $_FILES, true );
    }


    ##########################  Storage Shifting  #########################
    public function base64_decode()
    {
        $array = [];
        $lambda = function ($key) use (&$array) {
            $array[] = base64_decode( $key, true );
        };
        if (is_array( $this->storage )) array_walk( $this->storage, $lambda );
        elseif ($this->storage != null) $lambda( $this->storage );
        $this->storage = $array;
        return $this;
    }

    public function has($key)
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

    ########################## Validating    ##############################
    public function is($type)
    {
        $type = 'is_' . strtolower( $type );
        if (function_exists( $type )) return $type( $this->storage );
        throw new \InvalidArgumentException( 'no valid function is_$type' );
    }

    public function regex($condition)
    {
        if (empty($this->storage)) return false;

        $array = [];
        $regex = function ($key) use ($condition, &$array) {
            return $array[] = (preg_match( $condition, $key ) ? $key : false);
        };

        return (array_walk( $this->storage, $regex ) ?
            count( $array ) == 1 ? array_shift( $array ) : $array :
            array_shift( $regex( $this->storage ) ));
    }   // Match a pcal regex expression

    public function int($min = null, $max = null)   // inclusive max and min
    {
        if ($this->storage == null) return false;

        $array = [];
        $integer = function ($key) use (&$array, $min, $max)
        {
            if (($key = intval( $key )) === false) return $array[] = false;
            if ($max !== null) $key = ($key <= $max ? $key : false);
            if ($min !== null) $key = ($key >= $min ? $key : false);
            return $array[] = $key;
        };

        return (array_walk( $this->storage, $integer ) ?
            (count( $array ) == 1 ? array_shift( $array ) : $array) :
            false);
    }

    public function float()
    {
        if ($this->storage == null) return false;

        $array = [];
        $lambda = function ($key) use (&$array) {
            return $array[] = floatval( $key );
        };

        return (array_walk( $this->storage, $lambda ) ?
            (count( $array ) == 1 ? array_shift( $array ) : $array) :
            false);
    }

    public function alnum()
    {
        if ($this->storage == null) return false;

        $array = [];
        $alphaNumeric = function ($key) use (&$array) {
            return $array[] = (ctype_alnum( $key ) ? $key : false);
        };

        return (array_walk( $this->storage, $alphaNumeric ) ?
            (count( $array ) == 1 ? array_shift( $array ) : $array) :
            array_shift( $alphaNumeric( $only ) ));
    }           // One word alpha numeric

    public function text()
    {
        return $this->regex( '/([^\w])+/' );
    }            // Multiple word alpha numeric

    public function phone()
    {
        return (preg_match( '#((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}#', $this->storage[0] ) ? $this->storage[0] : false);
    }

    public function email()
    {
        if (empty($this->storage)) return false;
        return filter_var( array_shift( $this->storage ), FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE );
    }

    public function website()
    {
        $array = [];
        $lambda = function ($key) use (&$array) {
            $array[] = filter_var( $key, FILTER_VALIDATE_URL );
        };
        return (array_walk( $this->storage, $lambda ) ?
            (count( $array ) == 1 ? array_shift( $array ) : $array) :
            array_shift( $lambda( $this->storage ) ));
    }

    public function value()
    {
        return count( $this->storage ) == 1 ? array_shift( $this->storage ) : $this->storage;
    }


}