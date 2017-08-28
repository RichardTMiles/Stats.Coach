#!/usr/local/bin/php
<?php declare(ticks=1);

const SOCKET = true;
require_once 'index.php';

# https://www.leaseweb.com/labs/2013/08/catching-signals-in-php-scripts/
pcntl_signal( SIGTERM, 'signalHandler' ); // Termination ('kill' was called')
pcntl_signal( SIGHUP, 'signalHandler' );  // Terminal log-out
pcntl_signal( SIGINT, 'signalHandler' );  // Interrupted ( Ctrl-C is pressed)

$fifoPath = SERVER_ROOT . 'Temp/' . $_SESSION['id'] .'.fifo';

if (!file_exists( $fifoPath )) posix_mkfifo( $fifoPath, 0600 );

if (pcntl_fork() == 0) {   // child
    $file = fopen( $fifoPath, "w" );
    sleep( 1 );
    exit( 0 );
} else {
    $fifoFile = fopen( $fifoPath, 'r' );
}
$stdin = fopen( 'php://stdin', 'r' );

echo "COMMUNICATION STARTED\n PID :: " . getmypid() . "\n ID  :: " . $_SESSION['id'] . PHP_EOL;

while (true) {
    $readers = array($fifoFile, $stdin);
    if (($stream = stream_select( $readers, $writers, $except, 0, 15 )) === false) {
        print "A stream error occurred\n";
        break;
    }
    foreach ($readers as $input => $fd) {
        if ($fd == $stdin) {
            $line = fgets( $stdin );      // I think were going to make this a search function
            // TODO - search in app
            if ($line == 'exit') {
                print 'Now Exiting' . PHP_EOL;
                return 0;
            }
             print "You sent :: $line \n";
        } elseif ($fd == $fifoFile) {
            $data = fread( $fifoFile, $bytes = 124 );
            if (!empty( $data )) {
                print $data . PHP_EOL;
            }
        }
    }
    sleep( 1 );
}

function signalHandler($signal)
{
    print "Signal :: $signal\n";
    global $fifoPath, $fp;
    if (is_resource($fp))
        @fclose( $fp );
    @unlink( $fifoPath );
    print "Safe Exit \n\n";
    exit( 1 );
}