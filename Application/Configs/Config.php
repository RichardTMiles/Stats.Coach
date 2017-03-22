<?php

################    Database    ####################
define ('DB_HOST', 'localhost');
define ('DB_NAME', 'StatsCoach'); // HomingDevice , StatsCoach
define ('DB_USER', 'tmiles199');
define ('DB_PASS', 'Huskies!99');

define( 'ERROR_LOG', SERVER_ROOT  . 'Data/Logs/Logs.php' );

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ?
        'https://' : 'http://') . $_SERVER['SERVER_NAME'];

if (strlen( $url ) <= 10) $url = null;    // For IDE Support

define( 'SITE_ROOT', $url . DS);          // The base URL


define( 'TEMPLATE_ROOT',  SERVER_ROOT . 'Public' . DS . 'AdminLTE' . DS);
define( 'TEMPLATE_PATH' , SITE_ROOT . 'Public' . DS . 'AdminLTE' . DS);

$url .= $_SERVER['REQUEST_URI'];         // Now the full url and uri
