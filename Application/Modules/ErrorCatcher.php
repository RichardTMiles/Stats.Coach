<?php

namespace Modules;

// http://php.net/manual/en/function.debug-backtrace.php

use View\View;

class ErrorCatcher
{
    private $report;

    public function __construct( $active = true )
    {
        if (!$active) return;
        $closure = function () { $this->generateErrorLog(); };
        set_error_handler($closure);
        set_exception_handler($closure);
    }

    private function generateErrorLog()
    {
        if ( 0 == error_reporting () ) return null;

        // Open the file to get existing content
        $file = fopen(ERROR_LOG , "w");
        ob_start( );
        print PHP_EOL. date( 'D, d M Y H:i:s' , time());
        print $this->generateCallTrace( ) . PHP_EOL;
        echo $output = ob_get_contents( );
        ob_end_clean( );
        // Write the contents back to the file
        fwrite( $file, $output );
        fclose( $file );
        // startApplication(true);
        View::contents('error','500error');
    }

    // 

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


    // TODO - add java tags/ make redirect work
    private function redirect()
    {
        print '<meta http-equiv="refresh" content="0;url= ' . SITE_PATH . '"/>';
        die(0);
    }

}
