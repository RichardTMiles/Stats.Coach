<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/2/17
 * Time: 8:54 AM
 */

namespace Modules\Helpers;


class Fork
{
    public function __construct(callable $lambda, $argv = null)
    {
        if (self::safe()) {
            call_user_func_array($lambda, $argv);
            exit(1);
        }
    }


    private static function build()
    {
        define('FORK', TRUE);
    }
    public static function deamon()
    {
        if ($pid = pcntl_fork()) return 0;     // Parent
        elseif ($pid < 0) throw new \Exception('Failed to fork');
        self::build();

        fclose(STDIN);  // Close all of the standard
        fclose(STDOUT); // file descriptors as we
        fclose(STDERR); // are running as a daemon.

        register_shutdown_function(function () { session_abort(); posix_kill(posix_getpid(), SIGHUP); exit(1); });

        return 1;
    }


    public static function safe()
    {
        if ($pid = pcntl_fork())
            return 0;     // Parent
        elseif ($pid < 0) throw new \Exception('Failed to fork');
        self::build();
        // fclose(STDIN); -- unset
        register_shutdown_function(function () { session_abort(); posix_kill(posix_getpid(), SIGHUP); exit(1); });
        return 1;
    }

}