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
const COMPOSER = 'vendor/';
const TEMPLATE = COMPOSER . 'almasaeed2010/adminlte/';   // I learned That URLS need `/` not `DS`

// Facebook
const FACEBOOK_APP_ID = '1456106104433760';
const FACEBOOK_APP_SECRET = 'c35d6779a1e5eebf7a4a3bd8f1e16026';

function urlFacebook($request = null)
{
    try {
        $fb = new Facebook\Facebook([
            'app_id' => FACEBOOK_APP_ID,            // Replace {app-id} with your app id
            'app_secret' => FACEBOOK_APP_SECRET,
            'default_graph_version' => 'v2.12',
            'http_client_handler' => 'stream',      // better compatibility
        ]);


        if (isset($_GET['state'])) {
            $_SESSION['FBRLH_state'] = $_GET['state'];
        }

        $helper = $fb->getRedirectLoginHelper();


        #if (isset($_SESSION['facebook_access_token'])) {
        #    $accessToken = $_SESSION['facebook_access_token'];
        #} else {
        $accessToken = $helper->getAccessToken();
        #}
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    }

    if (null !== $accessToken) {
        if (isset($_SESSION['facebook_access_token'])) {
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        } else {
            // getting short-lived access token
            $_SESSION['facebook_access_token'] = (string)$accessToken;
            // OAuth 2.0 client handler
            $oAuth2Client = $fb->getOAuth2Client();
            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
            $_SESSION['facebook_access_token'] = (string)$longLivedAccessToken;
            // setting default access token to be used in script
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
        // redirect the user back to the same page if it has "code" GET variable
        #if (isset($_GET['code'])) {
        ##   header('Location: ./');
        #}
        // getting basic info about user

        try {
            $profile_request = $fb->get('/me?fields=name,first_name,last_name,email,gender,cover,picture', $_SESSION['facebook_access_token']);
            $profile = $profile_request->getGraphNode()->asArray();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            // redirecting user back to app login page
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
    } else {
        try {
            // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
            return $helper->getLoginUrl(SITE . 'oAuth/Facebook/' . $request . DS, [
                'public_profile', 'user_friends', 'email',
                'user_birthday',
                'user_hometown',
                'user_location', 'user_photos', 'user_friends']);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }

    \CarbonPHP\Request::changeURI(SITE . 'oAuth/Facebook/?much=love');  // clear GET data.

    return array(
        'id' => $profile['id'],
        'first_name' => $profile['first_name'] ?? '',
        'last_name' => $profile['last_name'] ?? '',
        'email' => $profile['email'] ?? '',
        'gender' => $profile['gender'] ?? '',
        'picture' => $profile['picture']['url'] ?? '',
        'cover' => $profile['cover']['source'] ?? '',
    );

}

/**
 * @param null $request
 * @return array|string
 * @throws Google_Exception
 * @throws \CarbonPHP\Error\PublicAlert
 */
function urlGoogle($request = null)
{
    //Call Google API
    $client = new Google_Client();
    $client->setApplicationName('Stats.Coach');
    $client->setAuthConfig(APP_ROOT . 'config/gAuth.json');

    if ($request !== null) {
        $request .= DS;
        $client->setRedirectUri('https://stats.coach/oAuth/Google/' . $request);
    }

    $client->setIncludeGrantedScopes(true);   // incremental auth
    $client->addScope('profile');
    $client->addScope('email');

    $google = new Google_Service_Oauth2($client);


    if (!isset($_GET['code'])) {
        return $client->createAuthUrl();
    }

    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if ($accessToken) {

        $client->setAccessToken($accessToken);

        //Get user profile data from google
        $gpUserProfile = $google->userinfo->get();

        //Insert or update user data to the database
        return array(
            'id' => $gpUserProfile['id'],
            'first_name' => $gpUserProfile['given_name'],
            'last_name' => $gpUserProfile['family_name'],
            'email' => $gpUserProfile['email'],
            'gender' => $gpUserProfile['gender'],
            'locale' => $gpUserProfile['locale'],
            'picture' => $gpUserProfile['picture'],
            'link' => $gpUserProfile['link']
        );
    }
    throw new \CarbonPHP\Error\PublicAlert('failed to get access token');
}


return [
    'DATABASE' => [

        'DB_DSN' => APP_LOCAL ? 'mysql:host=127.0.0.1;dbname=StatsCoach;' : 'mysql:host=35.224.229.250;dbname=StatsCoach;',      // Host and Database get put here

        'DB_USER' => 'root',                 // User

        'DB_PASS' => APP_LOCAL ? 'goldteamrules' : 'goldteamrules',          // Password goldteamrules

        'DB_BUILD' => SERVER_ROOT . '/config/buildDatabase.php',

        'REBUILD' => false                       // Initial Setup todo - remove this check
    ],

    'SITE' => [
        'URL' => 'stats.coach',    // Evaluated and if not the accurate redirect. Local php server okay. Remove for any domain

        'ROOT' => SERVER_ROOT,     // This was defined in our ../index.php

        'CACHE_CONTROL' => [
            'ico|pdf|flv' => 'Cache-Control: max-age=29030400, public',
            'jpg|jpeg|png|gif|swf|xml|txt|css|js|woff2|tff|map' => 'Cache-Control: max-age=604800, public',
            'html|htm|php|hbs' => 'Cache-Control: max-age=0, private, public',
        ],

        'CONFIG' => __FILE__,      // Send to sockets

        'TIMEZONE' => 'America/Phoenix',    //  Current timezone TODO - look up php

        'TITLE' => 'Stats • Coach',     // Website title

        'VERSION' => '0.0.0',       // Add link to semantic versioning

        'SEND_EMAIL' => 'no-reply@carbonphp.com',     // I send emails to validate accounts

        'REPLY_EMAIL' => 'support@carbonphp.com',

        'BOOTSTRAP' => 'Application/Bootstrap.php',     // This file is executed when the startApplication() function is called

        'HTTP' => true   // I assume that HTTP is okay by default
    ],

    'SESSION' => [

        'REMOTE' => true,             // Store the session in the SQL database

        'SERIALIZE' => ['user', 'team', 'course'],           // These global variables will be stored between session

        'CALLBACK' => function () {         // optional variable $reset which would be true if a url is passed to startApplication()

            if ($_SESSION['id'] ?? ($_SESSION['id'] = false)) {

                global $user;

                #return $_SESSION['id'] = false;

                if (!is_array($user)) {
                    $user = [];
                }

                if (!is_array($my = &$user[$_SESSION['id']])) {          // || $reset  /  but this shouldn't matter
                    $my = [];
                    if (false === Table\carbon_users::Get($my, $_SESSION['id'], []) ||
                        empty($my)) {
                        $_SESSION['id'] = false;
                        \CarbonPHP\Error\PublicAlert::danger('Failed to user.');
                    }

                    /*$my['stats'] = [];
                    Table\golf_stats::Get($my['stats'], $_SESSION['id'], []);

                    $my['teams'] = [];
                    Table\carbon_teams::Get($my['teams'], $_SESSION['id'], []);

                    $my['followers'] = [];
                    Table\user_followers::Get($my['followers'], $_SESSION['id'], []);

                    $my['messages'] = [];
                    Table\user_messages::Get($my['messages'], $_SESSION['id'], []);*/

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
        'LEVEL' => E_ALL | E_STRICT,  // php ini level

        'STORE' => true,      // Database if specified and / or File 'LOCATION' in your system

        'SHOW' => true,       // Show errors on browser

        'FULL' => true        // Generate custom stacktrace will high detail - DO NOT set to TRUE in PRODUCTION
    ],

    'VIEW' => [
        'VIEW' => 'view/',  // This is where the MVC() function will map the HTML.PHP and HTML.HBS . See Carbonphp.com/mvc

        'WRAPPER' => 'StatsCoach/Wrapper.hbs',     // View::content() will produce this
    ],

];

