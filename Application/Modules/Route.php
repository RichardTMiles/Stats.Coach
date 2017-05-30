<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/17/17
 * Time: 11:48 AM
 */

namespace Modules;

use Psr\Singleton;
use View\View;

class Route
{
    use Singleton;

    public $uri;
    private $matched;
    private $default;

    public function __construct($default = "Home/")
    {
        $this->matched = false;
        if (key_exists( 'REQUEST_URI', $_SERVER )) {
            $uri = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
            $uri = ltrim( $uri, '/' );
        
        } else if (empty($uri)) $uri = $default;
        
        // TODO - rethink the url and public opt
        if ($uri !== '/' && file_exists( SERVER_ROOT . '/public' . $uri )) {
            include(SERVER_ROOT . 'public/' . $uri);
            die();
        }
        $this->uri = explode( '/', strtolower( $uri ) );
    }

    public function home($function = null)
    {
        if ($this->matched == false) {
            if (is_callable( $function ))
                $this->default = $function;
            $this->default = $this->storage;
        }
    }
    
    public function __destruct()
    {
        if (!$this->matched && is_callable( $this->default )) {
            $this->addMethod( 'default', $this->default );
            $restart = $this->methods['default'];
            $this->default = true;
            $restart();
        }
    }

    public function match($toMatch, $closure)       // TODO - make someone rewrite this in REGX
    {
        if ($this->matched == true)
            return $this;

        if (!is_callable( $closure ))
            throw new \Exception;

        $this->storage = $closure;  // This is for home route function

        $uri = $this->uri;

        $arrayToMatch = explode( '/', strtolower( $toMatch ) );

        $pathLength = sizeof( $arrayToMatch );
        $uriLength = sizeof( $uri );

        if ($pathLength < $uriLength && substr( $toMatch, -1 ) != '*')
            return $this;

        $required = true;
        $variables = null;

        for ($i = 0; $i <= $pathLength; $i++) {

            // set up our ending condition
            if ($pathLength == $i || $arrayToMatch[$i] == null)
                $arrayToMatch[$i] = '*';

            switch ($arrayToMatch[$i][0]) {
                case  '*':
                    if (is_array( $variables )) {
                        foreach ($variables as $key => $value) {
                            $this->$key = $value;
                        }
                    } else {
                        $variables = array();
                    }

                    $this->matched = true;
                    $this->default = null;

                    $this->addMethod( 'routeMatched', $closure );

                    if (call_user_func_array( $this->methods['routeMatched'], $variables ) === false)
                        throw new \Error( 'Bad Closure Passed to Route::match()' );

                    return $this; // Note that the application will break in the View::contents

                case '{': // this is going to indicate the start of a variable name

                    if (substr( $arrayToMatch[$i], -1 ) != '}')
                        throw new \InvalidArgumentException;

                    $variable = null;

                    $variable = rtrim( ltrim( $arrayToMatch[$i], '{' ), '}' );

                    if (substr( $variable, -1 ) == '?') {
                        $variable = rtrim( $variable, '?' );
                        $required = false;
                    } elseif ($required == false)
                        return $this;


                    if ($variable == null)
                        throw new \Exception;

                    $value = null;
                    if (array_key_exists( $i, $uri ))
                        $value = $uri[$i];

                    if ($required == true && $value == null)
                        return $this;

                    $variables[$variable] = $value;
                    break;

                default:
                    if (!array_key_exists( $i, $uri ))
                        return $this;

                    if ($arrayToMatch[$i] != $uri[$i])
                        return $this;
            }
        }
        return $this;
    }
}




