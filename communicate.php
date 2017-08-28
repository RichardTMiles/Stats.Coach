#!/usr/local/bin/php
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/27/17
 * Time: 10:00 PM
 */

$fifoPath = __DIR__ . '/Temp/1.fifo';

print $fifoPath . PHP_EOL;

if (!file_exists($fifoPath)) {
    print "User not active \n\n";
    exit(0);
}

$fifo = fopen( $fifoPath, 'w' );

$data = "Richard Miles \n";
fwrite($fifo, $data, 1024);

echo "Done \n\n";
