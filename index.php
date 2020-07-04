<?php
#phpinfo() and exit;

const DS = DIRECTORY_SEPARATOR; // All folder constants end in a trailing slash /

define('APP_ROOT', __DIR__ . DS);  // Set our root folder for the application

const SERVER_ROOT = APP_ROOT;        // I would like to change to only using APP_ROOT soon

// Template
const COMPOSER = 'vendor/';

const TEMPLATE = COMPOSER . 'almasaeed2010/adminlte/';   // I learned That URLS need `/` not `DS`

// Facebook
const FACEBOOK_APP_ID = '1456106104433760';

const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';

if (false === include APP_ROOT . 'vendor/autoload.php') {
    // Load the autoload() for composer dependencies located in the Services folder
    print '<h1>try running >> composer run rei</h1>' and die;
    // Composer autoload
}

(new CarbonPHP\CarbonPHP(APP_ROOT . 'config' . DS . 'config.php'))(new App\StatsCoach);

return true;

