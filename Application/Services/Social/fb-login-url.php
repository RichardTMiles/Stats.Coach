<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/5/17
 * Time: 12:22 PM
 */

$fb = new Facebook\Facebook( [
    'app_id' => '1456106104433760', // Replace {app-id} with your app id
    'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
    'default_graph_version' => 'v2.2',
] );

$helper = $fb->getRedirectLoginHelper();

$permissions = [
    'public_profile', 'user_friends', 'email',
    'user_about_me', 'user_birthday',
    'user_education_history', 'user_hometown',
    'user_location', 'user_photos', 'user_friends'];           // Optional permissions

$loginUrl = $helper->getLoginUrl( 'https://stats.coach/Login/Facebook/', $permissions );    // TODO - make work

return $loginUrl;