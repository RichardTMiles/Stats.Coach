<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/23/17
 * Time: 11:00 AM
 */

namespace Modules;

class Pipe
{
    public static function send(string $value, string $user)
    {
        try {
            $fifoPath = SERVER_ROOT . 'Temp/' . $user .'.fifo';

            if (!file_exists( $fifoPath ))
                return false;

            $fifo = fopen( $fifoPath, 'w' );
            fwrite( $fifo, $value, 1024 );
            fclose( $fifo );
        } catch (\ErrorException $e) {
            return false;
        }
        return true;
    }
}



