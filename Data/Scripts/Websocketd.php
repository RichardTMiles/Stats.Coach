#!/usr/bin/php
<?php declare(ticks=1);
const SOCKET = true;

if (!file_exists('../../index.php') || !include_once '../../index.php')
    print "We could not locate the index" and die(1);

print 'HELL YA AND DIE' . PHP_EOL and die;

error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

# https://www.leaseweb.com/labs/2013/08/catching-signals-in-php-scripts/
pcntl_signal( SIGTERM, 'signalHandler' ); // Termination ('kill' was called')
pcntl_signal( SIGHUP, 'signalHandler' );  // Terminal log-out
pcntl_signal( SIGINT, 'signalHandler' );  // Interrupted ( Ctrl-C is pressed)

$fifoPath = SERVER_ROOT . 'Temp/' . $_SESSION['id'] . '.fifo';

if (file_exists( $fifoPath )) unlink( $fifoPath );

posix_mkfifo( $fifoPath, 0644 );

echo $user = get_current_user();

$fifoFile = \Carbon\Helpers\Pipe::named(SERVER_ROOT . 'Temp/' . $_SESSION['id'] . '.fifo');     // other users can notify us to update our application through this file

$stdin = fopen( 'php://stdin', 'r' );

echo "Socket Communication Started  
    USER :: " . get_current_user() . "
    PID :: " . getmypid() . "
    ID  :: " . $_SESSION['id'] . " 
    SOCKET :: " . SOCKET . PHP_EOL;


$request = (new class extends Carbon\Request
{
    public function is_json($string)
    {
        json_decode( $string );
        return (json_last_error() == JSON_ERROR_NONE);
    }
});


while (true) {
    $miss = 0;
    $handshake = 0;
    $readers = array($fifoFile, $stdin);
    if (($stream = stream_select( $readers, $writers, $except, 0, 15 )) === false):
        print "A stream error occurred\n";
        break;
    else :
        foreach ($readers as $input => $fd) {
            if ($fd == $stdin) {
                $string = $request->set( fgets( $stdin ) )->noHTML()->value();      // I think were going to make this a search function
                if ($string == 'exit') {
                    print "Application closed socket \n";
                    break;
                } elseif (!empty( $string ) && pcntl_fork() == 0) {
                    print "Fetch :: $string \n";
                    $_SERVER['REQUEST_URI'] = $string;
                    startApplication(  );
                    exit( 1 );
                } $handshake++;
            } elseif ($fd == $fifoFile) {
                $data = fread( $fifoFile, $bytes = 1024 );
                if (!empty( $data )) {
                    if (pcntl_fork() == 0) {
                        print "Update :: $data \n";
                        $_SERVER['REQUEST_URI'] = $data;
                        startApplication( $data );
                        exit( 1 );
                    }
                }
                $handshake++;
                print "Handshake\n";
            } else {
                print "Hits => $handshake";
                if ($handshake != 0):
                    $handshake = 0;
                    $miss = 1;
                elseif ($miss == 10):
                    exit( 1 );
                else: $miss++;
                    print "Misses => $miss\n";
                endif;
            }
        }
        sleep( 1 );
    endif;
}
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