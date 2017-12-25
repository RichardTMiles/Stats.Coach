<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 5/15/17
 * Time: 9:00 PM
 */

if (!session_id())
    session_start();

$fb = new Facebook\Facebook([
    'app_id' => FACEBOOK_APP_ID, // Replace {app-id} with your app id
    'app_secret' => FACEBOOK_APP_SECRET,
    'default_graph_version' => 'v2.2',
]);

$facebook_errors = function ($e) {
    \Carbon\Error\ErrorCatcher::generateErrorLog($e);
    \Carbon\Error\PublicAlert::danger('Facebook sent an invalid response.');
    startApplication(true);
};


if (isset($_GET['state'])) $_SESSION['FBRLH_state'] = $_GET['state'];
$helper = $fb->getRedirectLoginHelper();
// $helper->getPersistentDataHandler()->set( 'state', $_GET['state'] );


try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    $facebook_errors($e);
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    $facebook_errors($e);
    exit;
}

if (!isset($accessToken))
    $facebook_errors($helper);

// Logged in

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('1456106104433760'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');

$tokenMetadata->validateExpiration();

if (!$accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $facebook_errors($e);
    }
}

$_SESSION['fb_access_token'] = (string) $accessToken;

$response = [];
try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('/me?fields=id,email,cover,first_name,last_name,age_range,link,gender,locale,picture,timezone,updated_time,verified', "$accessToken");
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    $facebook_errors($e);

} catch (Facebook\Exceptions\FacebookSDKException $e) {
    $facebook_errors($e);
}

$user = $response->getGraphUser();

$GLOBALS['facebook'] = $user->all();

\Carbon\Request::changeURI(SITE. 'Facebook/');  // clear GET data.

return true;
// sortDump( $GLOBALS['facebook'] );
