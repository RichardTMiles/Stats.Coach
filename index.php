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
    print '<h1>Fuck, Composer Failed.</h1>' and die;
    // Composer autoload
}



try {
    $app = CarbonPHP\CarbonPHP::make(APP_ROOT . 'config' . DS . 'config.php');
} catch (\Throwable $e) {
    /** @noinspection ForgottenDebugOutputInspection */
    APP_LOCAL and print_r($e->getMessage());
    print '<h1>Fuck, CarbonPHP [C6] Failed. It\'s likely you just need to run "composer install".</h1>';
    exit(1);
}

/**
 * At one point I returned the invocation of $app to show that
 * the application will not exit on completion, but rather return
 * back to this index file. This means you can still execute code
 * after $app(); I stopped returning the __invoke() because if false
 * is returned here, the index will re-execute.
 * This turns very bad quickly.
 */


CarbonPHP\CarbonPHP::run(\App\StatsCoach::class);

return true;

