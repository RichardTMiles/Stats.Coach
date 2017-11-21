<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 10/29/17
 * Time: 11:14 AM
 */

$session_callback = function ($reset) {
    if ($_SESSION['id']) {
        global $user;

        if (!is_array($user)) $user = [];

        if (!is_array($user[$_SESSION['id']] ?? false))            // || $reset  /  but this shouldn't matter
            Tables\Users::all($user[$_SESSION['id']], $_SESSION['id']);
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
const FACEBOOK_APP_ID = '1456106104433760';
const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';

return [
    'SITE' => [
        'URL' => 'stats.coach',

        'ROOT' => SERVER_ROOT,

        'TIMEZONE' => 'America/Phoenix',

        'TITLE' => 'Stats.Coach',

        'VERSION' => '1.1.4',

        'SEND_EMAIL' => 'Support@Stats.Coach',

        'REPLY_EMAIL' => 'Richard@Miles.Systmes',

        'BOOTSTRAP' => 'Application/Route.php',

        'HTTP' => (bool) true,
    ],

    'SERIALIZE' => [ 'user' , 'team' , 'course', 'tournament' ],

    'SESSION' => [
        'REMOTE' => (bool) true,

        'PATH' => (string) SERVER_ROOT . 'Data/Sessions/',

        'CALLBACK' => $session_callback,
    ],

    'ERROR' => [
        'LEVEL' => (int)E_ALL | E_STRICT,

        'LOCATION' => (string) SERVER_ROOT . 'Data/Logs/',

        'STORE' => (bool) true,

        'SHOW' => (bool) true,

        'FULL' => (bool) true
    ],

    'VIEW' => [
        'WRAPPER' => 'Public/StatsCoach.php',

        'MUSTACHE' => 'Application/View/Mustache/',
    ],

    'DATABASE' => [
        'DB_HOST' => '127.0.0.1',

        'DB_NAME' => 'StatsCoach',

        'DB_USER' => 'root',

        'DB_PASS' => 'Huskies!99',

        'INITIAL_SETUP' => false,                       // no tables
    ],

    'AUTOLOAD' => [                                     // 'Carbon' => '',
        'View' => SERVER_ROOT . 'Application/View',

        'Tables' => SERVER_ROOT . 'Application/Tables',

        'Controller' => SERVER_ROOT . 'Application/Controller',

        'Model' => SERVER_ROOT . 'Application/Model',

        'App' => SERVER_ROOT . 'Application'
    ]
];

