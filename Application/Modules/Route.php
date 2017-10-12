<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 1/17/17
 * Time: 11:48 AM
 */

namespace Modules;


use Model\Helpers\GlobalMap;
use Psr\Log\InvalidArgumentException;

class Route extends GlobalMap
{
    use Singleton;                // We use the add method function to bind the closure to the class

    public $uri;
    private $matched;             // a bool
    private $homeMethod;          // for the ->home() method
    private $structure;           // The MVC pattern is currently passes

    public function __construct(callable $structure = null)
    {
        parent::__construct();
        $this->structure = $structure;
        $this->uri = explode( '/', ltrim( urldecode( parse_url( trim( preg_replace( '/\s+/', ' ', $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) ), ' /' ) );
        $this->matched = false;
    }

    public function changeStructure(callable $struct): Route
    {
        $this->structure = $struct;
        return $this;
    }


    public function __destruct()
    {
        if ($this->matched || SOCKET) return null;

        if (is_callable( $this->homeMethod )) {
            $this->addMethod( 'default', $this->homeMethod );
            $restart = $this->methods['default'];
            return $restart();
        }

        // we can assume it is the mvc structure
        if (is_array( $this->homeMethod ) && is_callable( $structure = $this->structure ) &&
            count( $this->homeMethod ) >= (new \ReflectionFunction( $structure ))->getNumberOfRequiredParameters())
            return call_user_func_array( $structure, $this->homeMethod );

        $this->defaultRoute(true);

        return null;
    }

    public function signedIn(): Route
    {
        if ($this->matched || !$_SESSION['id']) {
            $clone = clone $this;
            $clone->matched = true;
            return $clone;
        }
        return $this;
    }

    public function signedOut(): Route
    {
        if ($this->matched || ($_SESSION['id'] && is_object( $this->user[$_SESSION['id']] ))) {
            $clone = clone $this;
            $clone->matched = true;
            return $clone;
        }
        return $this;
    }

    public function home($function = null)
    {
        if ($this->matched)
            return null;
        if (is_callable( $function ))
            $this->homeMethod = $function;
        elseif (is_callable( $this->storage ) || is_array( $this->storage ))
            $this->homeMethod = $this->storage;
    }

    public function match(string $toMatch, ...$argv): self     // TODO - rewrite this in REGEX
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
                    $this->matched = true;
                    $this->homeMethod = null;
                    $referenceVariables = [];

                    foreach ($variables as $key => $value) {
                        $GLOBALS[$key] = $value;
                        $referenceVariables[] = &$GLOBALS[$key];
                    }

                    if (is_callable( $argv[0] )) {
                        $this->addMethod( 'routeMatched', $argv[0] );
                        if (call_user_func_array( $this->methods['routeMatched'], $referenceVariables ) === false)
                            throw new \Error( 'Bad Closure Passed to Route::match()' );

                    } elseif (is_callable( $structure = $this->structure ) &&
                        (new \ReflectionFunction( $structure ))->getNumberOfRequiredParameters() <= count( $argv )) {
                        $argv[] = &$referenceVariables;
                        call_user_func_array( $structure, $argv );

                    } else throw new \InvalidArgumentException( 'Invalid closure or arguments' );

                    exit( 1 ); // Note that the application will break in the View::contents

                case '{': // this is going to indicate the start of a variable name

                    if (substr( $arrayToMatch[$i], -1 ) != '}')
                        throw new \InvalidArgumentException( 'Variable declaration must be rapped in brackets. ie `/{var}/ ' );

                    $variable = null;
                    $variable = rtrim( ltrim( $arrayToMatch[$i], '{' ), '}' );

                    if (substr( $variable, -1 ) == '?') {
                        $variable = rtrim( $variable, '?' );
                        $required = false;
                    } elseif ($required == false) // TODO - this should be true?
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




