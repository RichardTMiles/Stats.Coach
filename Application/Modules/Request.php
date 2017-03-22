<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/13/17
 * Time: 4:28 AM
 */

namespace Modules;


use Psr\Singleton;

class Request
{
    use Singleton;

    private $all;


    public function __toString()
    {
        return $this->storage;
    }

    // $_POST; $_COOKIE; $_ENV; $_FILES; $_GET; $_REQUEST; $_SERVER; $_SESSION;

    private function all( )
    {
        return $this->all = array_merge($_POST, $_COOKIE, $_ENV, $_FILES, $_GET, $_REQUEST, $_SERVER, $_SESSION);
    }

    public function has( $key )
    {
        return array_key_exists( $key , $this->store );
    }

    public function only()
    {
        $only[] = array_walk( func_get_args(), function ($key) {
            if (array_key_exists( $key, $this->store )) {
                return $this->store[$key];
            }
            throw new \Exception;
        });
        $this->store = &$only;

    }

    public function except( )
    {
        array_walk( func_get_args(), function ($key) {
            if (array_key_exists( $key, $this->store )) {
                unset( $this->store[$key] );
            }
        });
    }

    private function flash( )
    {
        $_SESSION['old'][] = $this->store;
    }

    private function old( $key = null )
    {
        return ($key != null ? $_SESSION['old'][$key] : $_SESSION['old']);
    }

    public function value( $key = null )
    {
        $key = ($key != null ? $this->storage[$key] : $this->storage);
        $this->storage = null;
        return $key;
    }

    public function removeSpecial()
    {

        $clean = function ($string) {
            $buffer = null;
            $string = strtolower( $string );
            for ($i = 0; $i < strlen( $string ); $i++) {
                $ascii = ord( $string[$i] );
                if (($ascii >= 0 && $ascii <= 47) || ($ascii >= 127)) {
                    $buffer .= "";
                } else {
                    $buffer .= $string[$i];
                }
            }
            $this->store = $buffer;
        };

       if ($this->is( 'array' )) {
           foreach ($this->storage as $key => $value) {
               $this->storage = call_user_func_array ( $clean, $this->store[$key] );
           }
       } else {
           $clean($this->storage);
       }

        return $this->storage;
    }

    public function is($type)
    {
        $type = 'is_' . strtolower( $type );
        if (function_exists( $type ))
            return $type( $this->storage );
        throw new \Exception;
    }

    private function post( $key = null )
    {
        $this->storage = ($key === null || !array_key_exists( $key, $_POST ) ? false : $_POST[$key]);
    }

    private function email()
    {
        return filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    }
    
    public function alnum()
    {
        return (ctype_alnum($this->storage)? $this->storage : false);
    }

    
    private function get( $key = null, $default = null )
    {
        if ($key === null) {
            $this->storage = &$_GET;
        } elseif (key_exists( $key, $_GET )) {
            $this->storage = &$_GET[$key];
        } elseif ($default != null) {
            $_GET[$key] = $default;
            $this->storage = &$_GET[$key];
        }
    }

    private function server( $key = null )
    {
        if ($key === null)
            $this->storage = &$_SERVER;
        $this->storage = &$_SERVER[$key];
    }

    private function session( $key = null )
    {
        if ($key === null)
            $this->storage = &$_SESSION;
        $this->storage = &$_SESSION[$key];
    }

    private function request( $key = null )
    {
        if ($key === null)
            $this->storage = &$_REQUEST;
        $this->storage = &$_REQUEST[$key];
    }

    private function env( $key = null )
    {
        if ($key === null)
            $this->store = &$_ENV;
        $this->store = &$_ENV[$key];
    }

    private function cookie( $key = null )
    {
        if ($key === null)
            $this->store = &$_COOKIE;
        $this->store = &$_COOKIE[$key];
    }

    private function files( $key = null )
    {
        if ($key === null)
            $this->store = &$_FILES;
        $this->store = &$_FILES[$key];
    }


}