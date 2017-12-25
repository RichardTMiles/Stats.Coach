<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 10/29/17
 * Time: 11:14 AM
 */

$session_callback = function ($reset) {
    if ($_SESSION['id'] ?? $_SESSION['id'] = false) {
        global $user;

        if (!is_array($user)) $user = [];

        if (!is_array($me = &$user[$_SESSION['id']] ?? false)) {          // || $reset  /  but this shouldn't matter
            Tables\Users::all($me, $_SESSION['id']);
            Tables\Users::sport($me,  $_SESSION['id']);
            Tables\Followers::get($me,  $_SESSION['id']);
            Tables\Messages::all($me,  $_SESSION['id']);
        }
    }
};

// Tables that require a unique identifier
const USER = 1;
const USER_FOLLOWERS = 2;
const USER_NOTIFICATIONS = 3;
const USER_MESSAGES = 4;
const USER_TASKS = 5;
const TEAMS = 6;
const TEAM_MEMBERS = 7;
const GOLF_TOURNAMENTS = 8;
const GOLF_ROUNDS = 9;
const GOLF_COURSE = 10;
const ENTITY_COMMENTS = 11;
const ENTITY_PHOTOS = 12;


// Template
const COMPOSER = 'Data' . DS . 'vendor' . DS;
const TEMPLATE = COMPOSER . 'almasaeed2010' . DS . 'adminlte' . DS;

// Facebook
const FACEBOOK_APP_ID = '1456106104433760';
const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';

// Google
const GOOGLE_APP_ID = '500127309864-0eandu3etp0purtgsv7umhpsbhqhvjf1.apps.googleusercontent.com';
const GOOGLE_APP_SECRET = 'OfHL_1PAiHB2tlDLwZGcVO4S';


return [
    'SITE' => [
        'URL' => 'stats.coach',

        'ROOT' => SERVER_ROOT,

        'CONFIG' => __FILE__,

        'TIMEZONE' => 'America/Phoenix',

        'TITLE' => 'Stats.Coach',

        'VERSION' => '1.1.7',

        'SEND_EMAIL' => 'Support@Stats.Coach',

        'REPLY_EMAIL' => 'Richard@Miles.Systmes',

        'BOOTSTRAP' => 'Application/Route.php',

        'HTTP' => (bool)true,
    ],

    'SERIALIZE' => [ 'user', 'team', 'course', 'tournament'],

    'SESSION' => [
        'REMOTE' => (bool)true,

        'PATH' => (string)SERVER_ROOT . 'Data/Sessions',

        'CALLBACK' => $session_callback,
    ],


    'SOCKET' => [
        'WEBSOCKETD' => true,
        'PORT' => 8888,
        'DEV' => true,
        'SSL' => [
            'KEY' => '/Users/richardmiles/sites/ssl/stats/websocket/domainkey.txt',
            'CERT' => '/Users/richardmiles/sites/ssl/stats/websocket/domain-crt.txt'
        ]
    ],

    'ERROR' => [
        'LEVEL' => (int)E_ALL,

        'LOCATION' => (string)SERVER_ROOT . 'Data/Logs/',

        'STORE' => (bool)true,

        'SHOW' => (bool)true,

        'FULL' => (bool)true
    ],

    'VIEW' => [
        'VIEW' => 'Application/View/',

        'WRAPPER' => 'StatsCoach.php',
    ],

    'DATABASE' => [
        'DB_HOST' => '127.0.0.1',

        'DB_NAME' => 'StatsCoach',

        'DB_USER' => 'root',

        'DB_PASS' => 'Huskies!99',

        'INITIAL_SETUP' => false                       // no tables
    ],

    'AUTOLOAD' => [                                     // 'Carbon' => '',
        'View' => SERVER_ROOT . 'Application/View',

        'Tables' => SERVER_ROOT . 'Application/Tables',

        'Controller' => SERVER_ROOT . 'Application/Controller',

        'Model' => SERVER_ROOT . 'Application/Model',

        'App' => SERVER_ROOT . 'Application'
    ]
];

