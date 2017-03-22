<?php
/* Create a new class called Bcrypt */
/*
 *
*/

namespace Modules\Helpers;

class Bcrypt
{
    private static $rounds = 16;

    /* Gen Salt */
    private static function genSalt()
    {
        /* GenSalt */
        $string = str_shuffle( mt_rand() );
        $salt = uniqid( $string, true );

        /* Return */
        return $salt;
    }

    /* Gen Hash */
    public static function genHash($password)
    {
        if (CRYPT_BLOWFISH != 1)
            throw new \Exception( "Bcrypt is not supported on this server, please see the following to learn more: http://php.net/crypt" );

        /* Explain '$2y$' . $this->rounds . '$' */
        /* 2y selects bcrypt algorithm */
        /* $this->rounds is the workload factor */
        /* GenHash */
        $hash = crypt( $password, '$2y$' . self::$rounds . '$' . self::genSalt() );
        /* Return */
        return $hash;
    }

    /* Verify Password */
    public static function verify($password, $existingHash)
    {
        /* Hash new password with old hash */
        $hash = crypt( $password, $existingHash );

        /* Do Hashs match? */
        if ($hash === $existingHash) {
            return true;
        } else {
            return false;
        }
    }
}