<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/17/17
 * Time: 11:48 AM
 */

namespace Modules;


class Route
{
    use Singleton;

    public $uri;
    private $matched;             // a bool
    private $homeMethod;          // for the ->home() method
    private $structure;           // The MVC pattern is currently passes

    public function __construct(callable $structure)
    {
        $this->structure = $structure;
        $this->uri = explode( '/', ltrim( urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ), ' /' ) );
        $this->matched = true;
        if (empty($this->uri[0]))
            $this->defaultRoute();
        else
            $this->matched = false;
    }

    public function defaultRoute()
    {
        $mvc = $this->structure;
        $default = ($_SESSION['id'] && is_object( $this->user[$_SESSION['id']] ) ? DEFAULT_LOGGED_IN_MVC : DEFAULT_LOGGED_OUT_MVC);
        if (is_array( $default ))
            $mvc( $default['Class'], $default['Method'] );
    }

    public function __destruct()
    {
        if ($this->matched)
            return null;

        if (is_callable( $this->homeMethod )) {
            $this->addMethod( 'default', $this->homeMethod );
            $restart = $this->methods['default'];
            return $restart();
        }

        if (is_array( $this->homeMethod ) && count( $this->homeMethod ) >= 2 && is_callable( $mvc = $this->structure )) {
            return $mvc( $this->homeMethod['Class'], $this->homeMethod['Method'] );
        }

        $this->defaultRoute();
    }

    public function signedIn()
    {
        if ($this->matched || !$_SESSION['id']) {
            $clone = clone $this;
            $clone->matched = true;
            return $clone;
        }
        return $this;
    }

    public function signedOut()
    {
        if ($this->matched || ($_SESSION['id'] && is_object( $this->user[$_SESSION['id']] ))) {
            $clone = clone $this;
            $clone->matched = true;
            return $clone;
        }
        return $this;
    }

    public function home($function = null)  // TODO - I dont think this is working correctly
    {
        if ($this->matched)
            return null;
        if (is_callable( $function ))
            $this->homeMethod = $function;
        elseif (is_callable( $this->storage ) || is_array( $this->storage ))
            $this->homeMethod = $this->storage;
    }

    public function match($toMatch, ...$argv)       // TODO - make someone rewrite this in REGX
    {
        if ($this->matched === true)
            return $this;

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
                    $referenceVariables = [];
                    foreach ($variables as $key => $value) {
                        $GLOBALS[$key] = $value;
                        $referenceVariables[] = &$GLOBALS[$key];
                    }

                    $this->matched = true;
                    $this->homeMethod = null;

                    if (is_callable( $argv[0] )) {
                        $this->addMethod( 'routeMatched', $argv[0] );
                        if (call_user_func_array( $this->methods['routeMatched'], $referenceVariables ) === false)
                            throw new \Error( 'Bad Closure Passed to Route::match()' );

                    } elseif (count( $argv ) == 2) {
                        $structure = $this->structure;
                        $argv[] = &$referenceVariables;
                        call_user_func_array( $structure, $argv );
                        exit(1);
                    } else throw new \InvalidArgumentException( 'Are we passing a valid structure?' );
                    return $this; // Note that the application will break in the View::contents

                case '{': // this is going to indicate the start of a variable name

                    if (substr( $arrayToMatch[$i], -1 ) != '}')
                        throw new \InvalidArgumentException( 'Variable declaration must be rapped in brackets. ie `/{var}/ ' );

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

                    if (strtolower( $arrayToMatch[$i] ) != strtolower( $uri[$i] ))
                        return $this;
            }
        }
        return $this;
    }
}




