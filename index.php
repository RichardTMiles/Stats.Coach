<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/3/17
 * Time: 11:16 PM
 *
 * // Let the list of carbon_php dependencies
 *
 *
 *
 * Let it be known the basic commands of IntelliJ
 *
 * Jump to function definition:     (Command + click)
 *
 */

const DS = DIRECTORY_SEPARATOR;

define('SERVER_ROOT', dirname(__FILE__) . DS);  // Set our root folder for the application

// These files are required for the app to run. You must edit the Config file for your Servers


if (false == (include SERVER_ROOT . 'Application/Modules/Carbon.php')) {       // Load the autoload() for composer dependencies located in the Services folder
    echo "Internal Server Error";                                              // Composer autoload
    exit(3);
}


// Tables that require a unique identifier
const USER = 0;
const USER_FOLLOWERS = 1;
const USER_MESSAGES = 3;
const USER_TASKS = 4;
const TEAMS = 5;
const TEAM_MEMBERS = 6;
const GOLF_TOURNAMENTS = 7;
const GOLF_ROUNDS = 8;
const GOLF_COURSE = 9;
const ENTITY_COMMENTS = 10;
const ENTITY_PHOTOS = 11;

const FACEBOOK_APP_ID = '1456106104433760';
const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';


$session_callback = function ($reset) {
    if ($_SESSION['id']) {
        global $user;

        if (!is_array($user)) $user = [];

        if (!is_object($user[$_SESSION['id']] ?? false))            // || $reset  /  but this shouldn't matter
            Tables\Users::all($user[$_SESSION['id']], $_SESSION['id']);

        Model\Helpers\Events::refresh($user[$_SESSION['id']], $_SESSION['id']);
    }
};


CarbonPHP([

    'REPORTING' => [
        'LEVEL' => E_ALL | E_STRICT,
        'STORE' => true,
        'PRINT' => true,
        'FULL' => true
    ],

    'ROUTES' => 'Application/Routes.php',

    'URL' => 'stats.coach',

    'SITE_TITLE' => 'Stats Coach',

    'SITE_VERSION' => 'Beta 1',

    'SYSTEM_EMAIL' => 'Support@Stats.Coach',

    'REPLY_EMAIL' => 'RichardMiles2@my.unt.edu',

    'SERIALIZE' => [        // These are application specific
        'user',
        'team',
        'course',
        'tournaments'
    ],

    'MINIFY_CONTENTS' => false,

    'USERS' => true,

    'SESSION_UPDATE_CALLBACK' => $session_callback,

    'WRAPPING_REQUIRES_LOGIN' => false,

    'AJAX_OUT' => 'null',

    'CONTENT' => 'Public/StatsCoach/',

    'CONTENT_WRAPPER' => 'Public/StatsCoach.php',

    'TEMPLATE' => 'Data/vendor/almasaeed2010/adminlte/',

    'VENDOR' => 'Data/vendor/',

    'SOCKET' => false,      //[ 'port' => 8080, ]

    'HTTP' => true,

    'HTTPS' => true,

    'AJAX' => true,

    'PJAX' => true,

    'DB_HOST' => 'miles.systems',

    'DB_NAME' => 'StatsCoach',

    'DB_USER' => 'tmiles199',

    'DB_PASS' => 'Huskies!99',

    'AUTOLOAD' => [
        'View' => '/Application/View',
        'Tables' => '/Application/Services',
        'Modules' => '/Application/Modules',
        'Controller' => '/Application/Controller',
        'Model' => '/Application/Model',
        'App' => '/Application'
    ]

])();




