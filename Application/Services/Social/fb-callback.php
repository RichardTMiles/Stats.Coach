<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 5/15/17
 * Time: 9:00 PM
 */

if (!session_id())
    session_start();

$fb = new Facebook\Facebook( [
    'app_id' => '1456106104433760', // Replace {app-id} with your app id
    'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
    'default_graph_version' => 'v2.2',
] );

if (isset( $_GET['state'] ))
    $_SESSION['FBRLH_state'] = $_GET['state'];
$helper = $fb->getRedirectLoginHelper();
// $helper->getPersistentDataHandler()->set( 'state', $_GET['state'] );


try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    #startApplication(true);
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    #startApplication(true);

    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (!isset( $accessToken )) {
    if ($helper->getError()) {
        header( 'HTTP/1.0 401 Unauthorized' );
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header( 'HTTP/1.0 400 Bad Request' );
        echo 'Bad request';
    }
    exit;
}

// Logged in

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken( $accessToken );

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId( '1456106104433760' ); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');

$tokenMetadata->validateExpiration();

if (!$accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken( $accessToken );
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
        // startApplication(true);
        exit;
    }
}

$_SESSION['fb_access_token'] = (string)$accessToken;

try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get( '/me?fields=id,email,cover,first_name,last_name,age_range,link,gender,locale,picture,timezone,updated_time,verified', "$accessToken" );
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // startApplication(true);
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$user = $response->getGraphUser();

$GLOBALS['facebook'] = $user->all();


// sortDump( $GLOBALS['facebook'] );


// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');