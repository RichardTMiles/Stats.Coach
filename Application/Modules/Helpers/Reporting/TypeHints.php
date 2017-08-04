<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 8:01 PM
 */

namespace Psr;

/* http://php.net/manual/en/language.oop5.typehinting.php
 *
 * TODO - Make this work 
 *
 *
 * Enable type hinting in php < 7.0.0
 */

class TypeHints
{
    private static $TYPEHINT_PCRE = '/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (\w+) given/';

    private static $Typehints = array(
        'boolean'   => 'is_bool',
        'integer'   => 'is_int',
        'float'     => 'is_float',
        'string'    => 'is_string',
        'resrouce'  => 'is_resource'
    );

    private function __Constrct() {}

    public static function initializeHandler()
    {
        set_error_handler('\Psr\TypeHints::handleTypehint', E_RECOVERABLE_ERROR);
        return TRUE;
    }

    private static function getTypehintedArgument($ThBackTrace, $ThFunction, $ThArgIndex, &$ThArgValue)
    {

        foreach ($ThBackTrace as $ThTrace)
        {

            // Match the function; Note we could do more defensive error checking.
            if (isset($ThTrace['function']) && $ThTrace['function'] == $ThFunction)
            {

                $ThArgValue = $ThTrace['args'][$ThArgIndex - 1];

                return TRUE;
            }
        }

        return FALSE;
    }

    public static function handleTypehint($ErrLevel, $ErrMessage)
    {

        if ($ErrLevel == E_RECOVERABLE_ERROR)
        {

            if (preg_match(static::$TYPEHINT_PCRE, $ErrMessage, $ErrMatches))
            {

                list($ErrMatch, $ThArgIndex, $ThClass, $ThFunction, $ThHint, $ThType) = $ErrMatches;

                if (isset(self::$Typehints[$ThHint]))
                {

                    $ThBacktrace = debug_backtrace();
                    $ThArgValue  = NULL;

                    if (self::getTypehintedArgument($ThBacktrace, $ThFunction, $ThArgIndex, $ThArgValue))
                    {

                        if (call_user_func(self::$Typehints[$ThHint], $ThArgValue))
                        {

                            return TRUE;
                        }
                    }
                }
            }
        }

        return FALSE;
    }
}

TypeHints::initializeHandler();