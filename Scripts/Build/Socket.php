#!/usr/local/bin/php
<?php declare(ticks=1);

const SOCKET = true;
const WEBSOCKETD = true;        // you must have websocketd installed

include '..\..\index.php';


# this was originally created with websocketd written in go
# this is still here for users with PHP => 7.2  which is  thread unsafe

use Carbon\Helpers\Fork;
use \Carbon\Helpers\Pipe;
use \Carbon\Request;

/* We need a way to safely exit if the socket process is forced closed by websocketd
 * The signalHandler will attempt to unlink opened file descriptors after a force
 * close is issued
 */
function signalHandler($signal)
{
    print "Signal :: $signal\n";
    global $fifoPath, $fp;
    if (is_resource( $fp ))
        @fclose( $fp );
    if (file_exists( $fifoPath ))
        @unlink( $fifoPath );
    print "Safe Exit \n\n";
    exit( 1 );
}

# https://www.leaseweb.com/labs/2013/08/catching-signals-in-php-scripts/

pcntl_signal( SIGTERM, 'signalHandler' ); // Termination ('kill' was called') - exit, websocketd?

pcntl_signal( SIGHUP, 'signalHandler' );  // Terminal log-out

pcntl_signal( SIGINT, 'signalHandler' );  // Interrupted ( Ctrl-C is pressed)

$fifoFile = Pipe::named(SERVER_ROOT . 'Temp/' . $_SESSION['id'] . '.fifo');

$stdin = fopen( 'php://stdin', 'r' );      // were can take input through our socket via STDIN, open it as a file

$request = new Request;                                   // handles string validation

print 'Socket Active' . PHP_EOL;                          // This will get sent to the file descriptor, but will not send until ( ** 1 )

while (true) {  // loop.

    // if the named pipe is blocking then we know the user is active with each empty (data) received
    // this is the equivalent to the ready state, if the foreach is run and no descriptor is active
    // or has been `hit` via handshake, the socket will assume the user is offline.
    $miss = 0;
    $handshake = 0;

    $readers = array($fifoFile, $stdin);    // This must be reset each loop

    // poll the socket and named pipe for input. The socket is the users browser while the named pipe is our application.
    if (($stream = stream_select( $readers, $writers, $except, 0, 15 )) === false):
        print "A stream error occurred\n";
        break;

    else :
        // Readers will have only files with input available
        foreach ($readers as $input => $fd) {

            if ($fd == $stdin): // Socket

                $string = $request->set( fgets( $stdin ) )->noHTML()->value(); // validate, S'clean.

                if ($string == 'exit'):
                    print "Application closed socket \n";
                    exit( 2 );

                elseif (!empty( $string ) && Fork::safe()):
                    print "Socket :: $string \n";
                    $_SERVER['REQUEST_URI'] = $string;
                    startApplication( $string );
                    exit( 1 );

                endif;
                $handshake++;

            elseif ($fd == $fifoFile):  // Application.php
                // Application.php sends a request to update.
                $data = fread( $fifoFile, $bytes = 1024 );  // This will read multiple lines
                // we only send uri's to help with validation, and to update the applicable users session data
                if (!empty( $data )):
                    $data = explode( "\n", $data ); // separate uri's by newline.
                    foreach ($data as $i => $value) {
                        if (empty($value)) continue;
                        if (Fork::safe()):              // fork a request foreach uri
                            print "Update :: $value \n";
                            $_SERVER['REQUEST_URI'] = $value;
                            startApplication( $value );
                            exit( 1 );
                        endif;
                    }

                else:
                    print "Handshake \n";
                endif;
                $handshake++;

            else :
                // validate active socket
                print "Hits => $handshake";
                if ($handshake != 0):       // clear misses
                    $handshake = 0;
                    $miss = 1;

                elseif ($miss == 10):       // 10 misses !!?!?
                    exit( 2 );

                else: $miss++;              // Nothing active, hu?
                    print "Miss => $miss\n";
                endif;
            endif;
        }
        sleep( 1 );     // Keep it off the processor stack
    endif;
}

exit( 1 );  // call signalHandler()