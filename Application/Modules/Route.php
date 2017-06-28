<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/17/17
 * Time: 11:48 AM
 */

namespace Modules;

use Psr\Singleton;

class Route
{
    use Singleton;

    public $uri;
    private $matched;
    private $homeMethod;
    private $signedStatus;
    private $default_Signed_Out;
    private $default_Signed_In;
    private $structure;

    public function __construct($signedStatus = false, callable $structure, callable $default_Signed_Out = null, callable $default_Signed_In = null)
    {
        if (key_exists( 'REQUEST_URI', $_SERVER ))
            $uri = ltrim( urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ) , '/' );

        if (empty($uri)) {
            $this->matched = true;
            if ($signedStatus) {
                if (is_callable( $default_Signed_In )) $default_Signed_In();
            } elseif (is_callable( $default_Signed_Out )) $default_Signed_Out();
        }

        $this->matched = false;
        $this->default_Signed_Out = $default_Signed_Out;
        $this->default_Signed_In = $default_Signed_In;
        $this->signedStatus = $signedStatus;
        $this->structure = $structure;
        $this->uri = explode( '/', strtolower( $uri ) );
    }

    public function signedIn()
    {
        if ($this->signedStatus != true && $this->matched != true)
            $this->matched = "PUSH";
        return $this;
    }

    public function signedOut()
    {
        if ($this->signedStatus != false && $this->matched != true)
            $this->matched = "PUSH";
        return $this;
    }

    public function home($function = null)
    {
        if ($this->matched == false) {
            if (is_callable( $function ))
                $this->homeMethod = $function;
            elseif (is_callable( $this->storage ) || (is_array( $this->storage ) && count( $this->storage ) == 2))
                $this->homeMethod = $this->storage;
        }
    }

    public function __destruct()        //TODO- make work with new structure
    {
        if ($this->matched) return;

        if (is_array( $this->homeMethod ) && count( $this->homeMethod ) == 2)
            if (is_callable( $mvc = $this->structure)) $mvc( $this->storage[0], $this->storage[1] );

        if (!is_callable( $this->homeMethod ))
            $this->homeMethod = ($this->signedStatus ? $this->default_Signed_In : $this->default_Signed_Out);

        if (is_callable( $this->homeMethod )) {
            $this->addMethod( 'default', $this->homeMethod );
            $restart = $this->methods['default'];
            $restart();
        } else startApplication(true);
    }

    public function match($toMatch, ...$argv)       // TODO - make someone rewrite this in REGX
    {
        if ($this->matched === true)
            return $this;

        $this->storage = null;

        if ($this->matched === "PUSH") {
            $this->matched = false;
            return $this;
        }

        $this->storage = $argv;  // This is for home route function

        $uri = $this->uri;

        $arrayToMatch = explode( '/', $toMatch );

        $pathLength = sizeof( $arrayToMatch );
        $uriLength = sizeof( $uri );

        if ($pathLength < $uriLength && substr( $toMatch, -1 ) != '*')
            return $this;

        $required = true;
        $variables = array();

        for ($i = 0; $i <= $pathLength; $i++) {

            // set up our ending condition
            if ($pathLength == $i || $arrayToMatch[$i] == null)
                $arrayToMatch[$i] = '*';

            switch ($arrayToMatch[$i][0]) {
                case  '*':
                    foreach ($variables as $key => $value)
                        $this->{$key} = $value;

                    $this->matched = true;
                    $this->homeMethod = null;

                    if(is_callable( $argv[0] )) {
                        $this->addMethod( 'routeMatched', $argv[0] );
                        if (call_user_func_array( $this->methods['routeMatched'], $variables ) === false)
                            throw new \Error( 'Bad Closure Passed to Route::match()' );
                    } elseif (count( $argv ) == 2) {
                        $structure = $this->structure;
                        $structure( $argv[0], $argv[1] );
                    } else throw new \InvalidArgumentException;

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

                    if (strtolower($arrayToMatch[$i]) != $uri[$i])
                        return $this;
            }
        }
        return $this;
    }
}




