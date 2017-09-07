<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/23/17
 * Time: 11:00 AM
 */

namespace Modules\Helpers;

class Pipe
{
    public static function send(string $value, string $user)
    {
        if (Fork::safe()) {
            try {
                $fifoPath = SERVER_ROOT . 'Temp/' . $user . '.fifo';

                if (!file_exists( $fifoPath )) exit(1);

                posix_mkfifo( $fifoPath, 0644 );

                #sortDump(substr(sprintf('%o', fileperms($fifoPath)), -4) . PHP_EOL);

                $fifo = fopen( $fifoPath, 'r+' );
                fwrite( $fifo, $value, strlen($value) + 1 );
                fclose( $fifo );
            } catch (\Exception $e) {

            }
            exit(1);
        }
    }
}



