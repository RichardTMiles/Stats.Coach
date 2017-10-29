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

return [
    'SITE' => [
        'URL' => 'stats.coach',

        'ROOT' => SERVER_ROOT,

        'TIMEZONE' => 'America/Phoenix',

        'TITLE' => 'CarbonPHP',

        'VERSION' => '1.0.01',

        'SEND_EMAIL' => 'notice@example.com',

        'REPLY_EMAIL' => 'support@example.com',

        'BOOTSTRAP' => 'Application/Route.php',

        'HTTP' => (bool) true,
    ],

    'SESSION' => [
        'PATH' => (string) SERVER_ROOT . 'Data/Session/',

        'REMOTE' => (bool) false,

        'CALLBACK' => $session_callback,
    ],

    'ERROR' => [
        'LEVEL' => (int)E_ALL | E_STRICT,

        'LOCATION' => (string) 'Data/Logs/Error.txt',

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

        'View' => '/Application/View',

        'Tables' => '/Application/Services',

        'Controller' => '/Application/Controller',

        'Model' => '/Application/Model',

        'App' => '/Application'
    ]
];

