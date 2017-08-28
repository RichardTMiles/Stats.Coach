<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/10/17
 * Time: 3:30 PM
 */


$fp = stream_socket_client("tcp://www.stats.coach:42000", $errno, $errstr, 5);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
    while (!feof($fp)) {
        echo fgets($fp, 1024);
    }
    fclose($fp);
}