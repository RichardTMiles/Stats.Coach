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



return [
    'DATABASE' => [

        'DB_HOST' => APP_LOCAL ? '127.0.0.1' : '35.224.229.250',      // Host and Database get put here

        'DB_NAME' => 'StatsCoach',

        'DB_USER' => 'root',                 // User

        'DB_PASS' => APP_LOCAL ? 'admin' : 'goldteamrules',      // Password goldteamrules

        'DB_BUILD' => SERVER_ROOT . '/config/buildDatabase.php',

        'REBUILD' => 0                 // Initial Setup todo - remove this check
    ],

    'SITE' => [
        'URL' => 'stats.coach',    // Evaluated and if not the accurate redirect. Local php server okay. Remove for any domain

        'ROOT' => SERVER_ROOT,     // This was defined in our ../index.php

        'CACHE_CONTROL' => [
            'ico|pdf|flv' => 'Cache-Control: max-age=29030400, public',
            'jpg|jpeg|png|gif|swf|xml|txt|css|js|woff2|tff' => 'Cache-Control: max-age=604800, public',
            'html|htm|php|hbs' => 'Cache-Control: max-age=0, private, public',
        ],

        'CONFIG' => __FILE__,      // Send to sockets

        'TIMEZONE' => 'America/Phoenix',    //  Current timezone TODO - look up php

        'TITLE' => 'Stats • Coach',     // Website title

        'VERSION' => '0.0.0',       // Add link to semantic versioning

        'SEND_EMAIL' => 'no-reply@carbonphp.com',     // I send emails to validate accounts

        'REPLY_EMAIL' => 'support@carbonphp.com',

        'HTTP' => true   // I assume that HTTP is okay by default
    ],

    'SESSION' => [

        'REMOTE' => true,                                    // Store the session in the SQL database

        'SERIALIZE' => [

        ],           // These global variables will be stored between session

        'CALLBACK' => function () {         // optional variable $reset which would be true if a url is passed to startApplication()

            if ($_SESSION['id'] ?? ($_SESSION['id'] = false)) {

                #return $_SESSION['id'] = false;

                global $user;

                if (!is_array($user)) {
                    $user = [];
                }

                if (!is_array($my = &$user[$id = $_SESSION['id']])) {          // || $reset  /  but this shouldn't matter
                    $my = [];

                    /** @noinspection NotOptimalIfConditionsInspection */
                    if (false === Tables\carbon_users::Get($my, $_SESSION['id'], []) ||
                        empty($my)) {
                        $_SESSION['id'] = false;
                        \CarbonPHP\Error\PublicAlert::danger('Failed to user.');
                    }

                    if (empty($user[$id]['user_profile_pic']) || $user[$id]['user_profile_pic'] === null) {
                        $user[$id]['user_profile_pic'] = '/view/img/Carbon-red.png';
                    }

                    // todo check return of all rest api
                    \Model\Golf::sessionStuff($my);

                    Tables\carbon_user_followers::Get($my['followers'], null, [
                        'where' => [
                            'follows_user_id' => $_SESSION['id']
                        ]
                    ]);

                    $my['followersCount'] = count($my['followers']);

                    $my['following'] = [];

                    Tables\carbon_user_followers::Get($my['following'], $_SESSION['id'], []);

                    $my['followingCount'] = count($my['following']);

                    Tables\carbon_user_messages::Get($my['messages'], null, [
                        'where' => [
                            'to_user_id' => $_SESSION['id']
                        ]
                    ]);
                }
            }
        },
    ],

    /*          TODO - finish building php websockets          */
    'SOCKET' => [
        'WEBSOCKETD' => false,  // if you'd like to use web
        'PORT' => 8888,
        'DEV' => true,
        'SSL' => [
            'KEY' => '',
            'CERT' => ''
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

        'WRAPPER' => 'Layout/Wrapper.hbs',     // View::content() will produce this
    ],

    'MINIFY' => [
        'CSS' => [
            'OUT' => APP_ROOT . 'view/css/style.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css',
            APP_ROOT .'node_modules/admin-lte/dist/css/AdminLTE.min.css',
            APP_ROOT .'node_modules/admin-lte/dist/css/skins/_all-skins.min.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
            APP_ROOT .'node_modules/admin-lte/plugins/iCheck/all.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/Ionicons/css/ionicons.min.css',
            APP_ROOT .'node_modules/admin-lte/plugins/bootstrap-slider/slider.css',
            APP_ROOT .'node_modules/admin-lte/dist/css/skins/skin-green.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/select2/dist/css/select2.min.css',
            APP_ROOT .'node_modules/admin-lte/plugins/iCheck/flat/blue.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/morris.js/morris.css',
            APP_ROOT .'node_modules/admin-lte/plugins/pace/pace.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/jvectormap/jquery-jvectormap.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/bootstrap-daterangepicker/daterangepicker.css',
            APP_ROOT .'node_modules/admin-lte/plugins/timepicker/bootstrap-timepicker.css',
            APP_ROOT .'node_modules/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/font-awesome/css/font-awesome.min.css',
            APP_ROOT .'node_modules/admin-lte/bower_components/fullcalendar/dist/fullcalendar.min.css'
        ],
        'JS' => [
            'OUT' => APP_ROOT . 'view/js/javascript.js',
            APP_ROOT .'node_modules/admin-lte/bower_components/jquery/dist/jquery.min.js',
            APP_ROOT .'node_modules/jquery-pjax/jquery.pjax.js',
            CARBON_ROOT .'view/mustache/Layout/mustache.js',
            CARBON_ROOT .'helpers/Carbon.js',
            CARBON_ROOT .'helpers/asynchronous.js',
            APP_ROOT .'node_modules/jquery-form/src/jquery.form.js',
            APP_ROOT .'node_modules/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js',
            APP_ROOT .'node_modules/admin-lte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
            APP_ROOT .'node_modules/admin-lte/bower_components/fastclick/lib/fastclick.js',
            APP_ROOT .'node_modules/admin-lte/dist/js/adminlte.js',
        ],
    ]

];

