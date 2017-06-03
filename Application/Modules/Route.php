<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/17/17
 * Time: 11:48 AM
 */

namespace Modules;

use Model\User;
use Psr\Singleton;
use View\View;

class Route
{
    use Singleton;

    public $uri;
    private $matched;
    private $homeMethod;
    private $signedStatus;
    private $default_Signed_Out;
    private $default_Signed_In;

    public function __construct(callable $default_Signed_Out, callable $default_Signed_In = null, $signedStatus = false)
    {
        if (key_exists( 'REQUEST_URI', $_SERVER )) {
            $uri = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
            $_SERVER['REQUEST_URI'] = null;   // so I can restart the class to the default path TODO - this may b the pr
            $uri = ltrim( $uri, '/' );
        }

        if (empty($uri)) {
            $this->matched = true;  // I dont think this is needed how im using it, but my be required for fututre builds
            return ($signedStatus ? $default_Signed_In() : $default_Signed_Out());
        }
        $this->matched = false;
        $this->default_Signed_Out = $default_Signed_Out;
        $this->default_Signed_In = $default_Signed_In;
        $this->signedStatus = $signedStatus;

        $this->uri = explode( '/', strtolower( $uri ) );
    }
    
    public function signedIn() {
        if ($this->signedStatus != true && $this->matched != true)
            $this->matched = "PUSH";
        return $this;
    }
    
    public function signedOut() {
        if ($this->signedStatus != false && $this->matched != true)
            $this->matched = "PUSH";
        return $this;
    }

    public function home($function = null)
    {
        if ($this->matched == false) {
            if (is_callable( $function ))
                $this->homeMethod = $function;
            elseif (is_callable( $this->storage ))
                $this->homeMethod = $this->storage;
        }
    }
    
    public function __destruct()
    {
        if ($this->matched) return;

        if (!is_callable( $this->homeMethod ))
            $this->homeMethod = ($this->signedStatus ? $this->default_Signed_In : $this->default_Signed_Out);

        $this->addMethod( 'default', $this->homeMethod );
        $restart = $this->methods['default'];
        $restart();

    }

    public function match($toMatch, callable $closure)       // TODO - make someone rewrite this in REGX
    {
        if ($this->matched === true)
            return $this;

        $this->storage = null;

        if ($this->matched === "PUSH") {
            $this->matched = false;
            return $this;
        }
         
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
                    $this->homeMethod = null;

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




