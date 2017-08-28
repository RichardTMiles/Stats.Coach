#!/usr/bin/php
<?php

$pipe ='test.fifo';
$mode ='777';

while (!file_exists($pipe)) {
    sleep(3);
    // create the pipe
    #umask(0);
    #posix_mkfifo($pipe,$mode);
}


$value = "This is a testing signal";
$pipe = fopen("testpipe",'w');
fwrite($pipe, $value);

print " We Sent the message \n";

exit(1);



