<?php

use CarbonPHP\Error\PublicAlert;
use Model\Golf;
use Model\Messages;
use Model\User;
use Tables\carbon_user_notifications;
use Tables\carbon_user_tasks;
use Tables\carbon_users;

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


/**
 * @param bool $id
 * @param string $level
 * @return array
 * @throws PublicAlert
 */
function getUser($id = false, $level = 'All') : array
{
    global $user;

    if ($id === false) {
        throw new PublicAlert('No arguments to getUser.');
    }

    if (!is_array($user)) {
        $user = [];
    }

    if (!is_array($my = &$user[$id])) {          // || $reset  /  but this shouldn't matter
        $my = [];
    }

    /**
     * @noinspection NotOptimalIfConditionsInspection
     * @param array $options
     * @return void
     */
    $getUser = function ($options = []) use (&$my, $id, $level) {
        if (false === carbon_users::Get($my, empty($options) ? $id : null, $options) || empty($my)) {
            $_SESSION['id'] = false;
            throw new PublicAlert('Failed get to user restful api failed.');
        }
    };

    switch ($level) {
        case 'All':
            $getUser();
            break;
        case 'Profile':
            $getUser([
                'select' => [
                    carbon_users::USER_FIRST_NAME,
                    carbon_users::USER_LAST_NAME,
                    carbon_users::USER_ID,
                    carbon_users::USER_SPORT,
                    carbon_users::USER_PROFILE_PIC,
                    carbon_users::USER_COVER_PHOTO,
                    carbon_users::USER_ABOUT_ME,
                ],
                'where' => [
                    carbon_users::USER_ID => $id
                ],
                'pagination' => [
                    'limit' => 1
                ]
            ]);
            break;
        case 'Basic':
            $getUser([
                'select' => [
                    carbon_users::USER_FIRST_NAME,
                    carbon_users::USER_LAST_NAME,
                    carbon_users::USER_ID,
                    carbon_users::USER_SPORT,
                    carbon_users::USER_PROFILE_PIC,
                    carbon_users::USER_COVER_PHOTO,
                    carbon_users::USER_ABOUT_ME,
                ],
                'where' => [
                    carbon_users::USER_ID => $id
                ],
                'pagination' => [
                    'limit' => 1
                ]
            ]);
            break;
        default:
            throw new PublicAlert('Invalid option passed to getUser => ' . $level);

    }

    // todo check return of all rest api
    switch ($level) {
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'All':

            $my['notifications'] = [];

            carbon_user_notifications::Get($my['notifications'], null, [
                'where' => [
                    carbon_user_notifications::TO_USER_ID => $id,
                ]
            ]);

            $my['tasks'] = [];

            carbon_user_tasks::Get($my['tasks'], null, [
                'where' => [
                    carbon_user_tasks::USER_ID => $id
                ]
            ]);

            $my['navMessages'] = Messages::unreadMessages();

            $my['newMessages'] = count($my['navMessages']);

            $my['messages'] = [];


        /** @noinspection PhpMissingBreakStatementInspection */ case 'Profile':
            $my['followers'] = User::followers($_SESSION['id']);

            $my['followersCount'] = count($my['followers']);

            $my['following'] = User::following($_SESSION['id']);

            $my['followingCount'] = count($my['following']);

            // Im thinking its a faster op to foreach than database select again
            $my['friends'] = empty($my['following']) || empty($my['followers'])
                ? [] : array_intersect( $my['following'], $my['followers']);

        /** @noinspection PhpMissingBreakStatementInspection */
        case 'Basic':
            Golf::sessionStuff($my);
    }
    return $my;
}

return [
    'DATABASE' => [

        'DB_HOST' => APP_LOCAL ? '127.0.0.1' : '35.224.229.250',      // Host and Database get put here

        'DB_NAME' => 'StatsCoach',

        'DB_USER' => 'root',                 // User

        'DB_PASS' => APP_LOCAL ? 'password' : 'goldteamrules',      // Password goldteamrules

        'DB_BUILD' => SERVER_ROOT . '/config/buildDatabase.php',

        'REBUILD' => false                       // Initial Setup todo - remove this check
    ],

    'SITE' => [
        'URL' => 'stats.coach',    // Evaluated and if not the accurate redirect. Local php server okay. Remove for any domain

        'ROOT' => SERVER_ROOT,     // This was defined in our ../index.php

        'CACHE_CONTROL' => [
            'ico|pdf|flv|css|js' => 'Cache-Control: max-age=29030400, public',
            'jpg|jpeg|png|gif|swf|xml|txt|woff2|tff' => 'Cache-Control: max-age=604800, public',
            'html|htm|php|hbs' => 'Cache-Control: max-age=0, private, public',
        ],

        'CONFIG' => __FILE__,      // Send to sockets

        'TIMEZONE' => 'America/Phoenix',    //  Current timezone TODO - look up php

        'TITLE' => 'Stats • Coach',     // Website title

        'VERSION' => '0.0.0',       // Add link to semantic versioning

        'SEND_EMAIL' => 'no-reply@carbonphp.com',     // I send emails to validate accounts

        'REPLY_EMAIL' => 'support@carbonphp.com',

        'HTTP' => APP_LOCAL   // I assume that HTTP is okay by default
    ],

    'SESSION' => [

        'REMOTE' => true,                                    // Store the session in the SQL database

        'SERIALIZE' => [

        ],           // These global variables will be stored between session

        'CALLBACK' => function () {
            // optional variable $reset which would be true if a url is passed to startApplication()

            if ($_SESSION['id'] ?? ($_SESSION['id'] = false))
                getUser($_SESSION['id']);
        },
    ],

    /** TODO - finish building php websockets
     * https://certbot.eff.org/docs/using.html#where-are-my-certificates
     */
    'SOCKET' => [
        'WEBSOCKETD' => true,  // if you'd like to use web
        'PORT' => 8888,
        'DEV' => true,
        'SSL' => [
            'KEY' => '/var/www/stats.coach/privkey.pem',
            'CERT' => '/var/www/stats.coach/cert.pem'
        ]
    ],

    // ERRORS on point
    'ERROR' => [
        'LOCATION' => APP_ROOT . 'data' . DS . 'logs' . DS,

        'LEVEL' => E_ALL | E_STRICT,  // php ini level

        'STORE' => true,      // Database if specified and / or File 'LOCATION' in your system

        'SHOW' => true,       // Show errors on browser

        'FULL' => true        // Generate custom stacktrace will high detail - DO NOT set to TRUE in PRODUCTION
    ],

    'VIEW' => [
        'VIEW' => 'view/',  // This is where the MVC() function will map the HTML.PHP and HTML.HBS . See Carbonphp.com/mvc

        'WRAPPER' => 'layout/Wrapper.hbs',     // View::content() will produce this
    ],

    'MINIFY' => [
        'CSS' => [
            'OUT' => APP_ROOT . 'view/css/style.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css',
            APP_ROOT . 'node_modules/admin-lte/dist/css/AdminLTE.min.css',
            APP_ROOT . 'node_modules/admin-lte/dist/css/skins/_all-skins.min.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/iCheck/all.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/Ionicons/css/ionicons.min.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/bootstrap-slider/slider.css',
            APP_ROOT . 'node_modules/admin-lte/dist/css/skins/skin-green.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/select2/dist/css/select2.min.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/iCheck/flat/blue.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/morris.js/morris.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/pace/pace.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/jvectormap/jquery-jvectormap.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/bootstrap-daterangepicker/daterangepicker.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/timepicker/bootstrap-timepicker.css',
            APP_ROOT . 'node_modules/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/font-awesome/css/font-awesome.min.css',
            APP_ROOT . 'node_modules/admin-lte/bower_components/fullcalendar/dist/fullcalendar.min.css'
        ],
        'JS' => [
            'OUT' => APP_ROOT . 'view/js/javascript.js',
            APP_ROOT . 'node_modules/admin-lte/bower_components/jquery/dist/jquery.min.js',
            APP_ROOT . 'node_modules/jquery-pjax/jquery.pjax.js',
            CARBON_ROOT . 'view/mustache/Layout/mustache.js',
            CARBON_ROOT . 'helpers/Carbon.js',
            CARBON_ROOT . 'helpers/asynchronous.js',
            APP_ROOT . 'node_modules/jquery-form/src/jquery.form.js',
            APP_ROOT . 'node_modules/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js',
            APP_ROOT . 'node_modules/admin-lte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
            APP_ROOT . 'node_modules/admin-lte/bower_components/fastclick/lib/fastclick.js',
            APP_ROOT . 'node_modules/admin-lte/dist/js/adminlte.js',
        ],
    ]

];

