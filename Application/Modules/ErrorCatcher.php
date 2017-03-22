<?php

namespace Modules;

use Psr\Singleton;

class ErrorCatcher
{
    use Singleton;

    private $report;

    public function __construct( )
    {

        if ( 0 == error_reporting () ) return null;

        // Open the file to get existing content
        ob_start( );
        print file_get_contents( ERROR_LOG ) . PHP_EOL;
        print $this->generateCallTrace( ) . PHP_EOL;
        $output = ob_get_contents( );
        ob_end_clean( );
        // Write the contents back to the file
        // file_put_contents( ERROR_LOG, $output );

        echo $output;

        // $this->redirect();
        die();
    }

    private function generateCallTrace()
    {
        $e = new \Exception( );
        
        ob_start( );
        $trace = explode( "\n", $e->getTraceAsString() );
        // reverse array to make steps line up chronologically
        $trace = array_reverse( $trace );
        array_shift( $trace ); // remove {main}
        array_pop( $trace ); // remove call to this method
        array_pop( $trace ); // remove call to this method

        $length = count( $trace );
        $result = array( );

        for ( $i = 0; $i < $length; $i++ ) {
            $result[] = ($i + 1) . ')' . substr( $trace[$i], strpos( $trace[$i], ' ' ) ) . PHP_EOL;
            print PHP_EOL;
            // replace '#someNum' with '$i)', set the right ordering
        }

        print "\t" . implode( "\n\t", $result );

        $output = ob_get_contents( );
        ob_end_clean( );
        return $this->report = $output;
    }

    public static function reporting($level = 0)
    {
        $error = function () { new ErrorCatcher(); };

        error_reporting( $level );
        ini_set( 'display_errors', ($level == 0 ? 0 : 1) );
        set_error_handler( $error );
        set_exception_handler( $error );
    }

    // http://php.net/manual/en/function.debug-backtrace.php

    private function redirect()
    {
        $errorPage = function () {
            // A call to you're home page should work here, but custom reporting would be better
            print '<meta http-equiv="refresh" content="0;url= ' . SITE_ROOT . '"/>';
            die();
        };

        $errorPage();
    }

}
