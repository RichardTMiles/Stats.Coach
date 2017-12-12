#!/usr/local/bin/php
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/27/17
 * Time: 10:00 PM
 */

$fifoPath = dirname(__DIR__) . '/Data/Temp/333eafb18a4.fifo';

print $fifoPath . PHP_EOL;

if (!file_exists($fifoPath)) {
    print "User not active \n\n";
    exit(0);
}
posix_mkfifo( $fifoPath, 0644 );

$fifo = fopen( $fifoPath, 'r+' );

$data = "Richard Miles \n";

fwrite($fifo, $data, 1024);

echo "Done \n\n";
