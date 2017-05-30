<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 5/15/17
 * Time: 8:59 PM
 */


$fb = new Facebook\Facebook([
    'app_id' => '1456106104433760',
    'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
    'default_graph_version' => 'v2.4',
    'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token'] : '1456106104433760|c35d6779a1e5eebf7a4a3bd8f1e16026'
]);

try {
    $response = $fb->get('/me?fields=id,name');
    $user = $response->getGraphUser();
    echo 'Name: ' . $user['name'];
    exit; //redirect, or do whatever you want
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    //echo 'Graph returned an error: ' . $e->getMessage();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    //echo 'Facebook SDK returned an error: ' . $e->getMessage();
}

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes'];
$loginUrl = $helper->getLoginUrl('http://stats.coach/login/facebook/', $permissions);
echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
