<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 5/15/17
 * Time: 9:00 PM
 */



$fb = new Facebook\Facebook([
    'app_id' => '1456106104433760', // Replace {app-id} with your app id
    'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
    'default_graph_version' => 'v2.4',
    'default_access_token' => '1456106104433760|c35d6779a1e5eebf7a4a3bd8f1e16026'
]);


$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    //echo 'Graph returned an error: ' . $e->getMessage();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    //echo 'Facebook SDK returned an error: ' . $e->getMessage();
}

if (isset($accessToken)) { 
    // Logged in!
    $_SESSION['facebook_access_token'] = (string) $accessToken;
} elseif ($helper->getError()) {
    // The user denied the request
}
// header('Location: index.php');


echo $_SESSION['facebook_access_token'];


echo "\n\n\n\n\n";
sortDump();