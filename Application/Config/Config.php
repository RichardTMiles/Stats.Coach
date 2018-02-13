<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 10/29/17
 * Time: 11:14 AM
 *
 * Tables that require a unique identifier,
 * I use this for tags in the carbon_tag
 * data table.
 */
const USER = 1;
const USER_FOLLOWERS = 2;
const USER_NOTIFICATIONS = 3;
const USER_MESSAGES = 4;
const USER_TASKS = 5;
const ENTITY_COMMENTS = 6;
const ENTITY_PHOTOS = 7;
const TEAMS = 8;
const TEAM_MEMBERS = 9;
const GOLF_TOURNAMENTS = 10;
const GOLF_ROUNDS = 11;
const GOLF_COURSE = 12;

// Template
const COMPOSER = 'Data' . DS . 'Vendors' . DS;
const TEMPLATE =  COMPOSER . 'almasaeed2010' . DS . 'adminlte' . DS;

// Facebook
const FACEBOOK_APP_ID = '1456106104433760';
const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';

// Google
const GOOGLE_APP_ID = '';
const GOOGLE_APP_SECRET = '';

return [
    'DATABASE' => [

        'DB_DSN' =>  APP_LOCAL ? 'mysql:host=127.0.0.1;dbname=StatsCoach;' : 'mysql:host=35.224.229.250;dbname=StatsCoach;',      // Host and Database get put here

        'DB_USER' => 'root',                 // User

        'DB_PASS' =>  APP_LOCAL ? 'Huskies!99' : 'goldteamrules',          // Password goldteamrules

        'DB_BUILD' => SERVER_ROOT . 'Application/Config/buildDatabase.php',

        'REBUILD' => false                       // Initial Setup todo - remove this check
    ],

    'SITE' => [
        'URL' => 'stats.coach',    // Evaluated and if not the accurate redirect. Local php server okay. Remove for any domain

        'ROOT' => SERVER_ROOT,     // This was defined in our ../index.php

        'ALLOWED_EXTENSIONS' => 'png|jpg|gif|jpeg|bmp|icon|js|css|woff|woff2|map|hbs|eotv',     // File ending in these extensions will be served

        'CONFIG' => __FILE__,      // Send to sockets

        'TIMEZONE' => 'America/Phoenix',    //  Current timezone TODO - look up php

        'TITLE' => 'Root • Prerogative',      // Website title

        'VERSION' => '0.0.0',       // Add link to semantic versioning

        'SEND_EMAIL' => 'no-reply@carbonphp.com',     // I send emails to validate accounts

        'REPLY_EMAIL' => 'support@carbonphp.com',

        'BOOTSTRAP' => 'Application/Bootstrap.php',     // This file is executed when the startApplication() function is called

        'HTTP' => true   // I assume that HTTP is okay by default
    ],

    'SESSION' => [
        'REMOTE' => true,             // Store the session in the SQL database

        'SERIALIZE' => [ 'user' ],           // These global variables will be stored between session

        'CALLBACK' => function () {         // optional variable $reset which would be true if a url is passed to startApplication()

            if ($_SESSION['id'] ?? ($_SESSION['id'] = false)) {

                #return $_SESSION['id'] = false;

                global $user;

                if (!is_array($user)) {
                    $user = [];
                }

                if (!is_array($me = &$user[$_SESSION['id']])) {          // || $reset  /  but this shouldn't matter

                    $me = [];

                    Table\Users::All($me, $_SESSION['id']);

                    $stats = 'Model\\' . $me['user_sport'] ?? '';

                    if (class_exists($stats)) {
                        $interfaces = class_implements($stats);
                        if (\in_array('Model\Helpers\iSport', $interfaces, true)) {
                            $me = (new $stats)->stats($me, $_SESSION['id']);
                        }
                    }

                    Table\Followers::All($me,  $_SESSION['id']);

                    Table\Messages::All($me,  $_SESSION['id']);
                }
            }
        },
    ],

    /*          TODO - finish building php websockets
    'SOCKET' => [
        'WEBSOCKETD' => false,  // if you'd like to use web
        'PORT' => 8888,
        'DEV' => true,
        'SSL' => [
            'KEY' => '',
            'CERT' => ''
        ]
    ],  */


    // ERRORS on point
    'ERROR' => [
        'LEVEL' =>  E_ALL | E_STRICT,  // php ini level

        'STORE' =>  true,      // Database if specified and / or File 'LOCATION' in your system

        'SHOW' =>  true,       // Show errors on browser

        'FULL' => true        // Generate custom stacktrace will high detail - DO NOT set to TRUE in PRODUCTION
    ],

    'VIEW' => [
        'VIEW' => 'Application/View/',  // This is where the MVC() function will map the HTML.PHP and HTML.HBS . See Carbonphp.com/mvc

        'WRAPPER' => 'StatsCoach.php',     // View::content() will produce this
    ],

];

