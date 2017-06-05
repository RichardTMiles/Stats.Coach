<?php
// TODO - find how to make the godaddy stats.coach subdomain url works
/*
 * Routing a home function required default arguments be set to fuction
 * as expected.
 */



$route->signedIn()->match( 'Home/*', function () { mvc( 'Golf', 'golf' ); } )->home();                    // Home = golf -> golf


$route->match( 'AdminLTE/*', function () {
    include TEMPLATE_ROOT . 'pages/layout/boxed.html';
    exit(1);
} );


$route->match( 'Privacy/*', function () { \View\View::contents('policy','privacypolicy'); } );


$route->match( 'Tests/*',    function () { include SERVER_ROOT . 'Tests/login.php'; } );


$route->signedOut()->match( 'Login/{client?}/*', function ($client) {
    if ($client == "facebook") include SERVER_ROOT . 'Application/Services/Facebook/callback.php';
    mvc( 'User', 'login' ); } );    // Login


$route->signedIn()->match( 'PostScore/{state?}/*', function ($state) { mvc('Golf', 'postScore'); } );    // PostScore $state


$route->signedIn()->match( 'AddCourse/{state?}/*', function ($state) { mvc( 'Golf', 'AddCourse'); } );    // AddCourse TODO - Make $state work


$route->match( 'Logout/*',  function () {
    Controller\User::logout(); } );    // Logout


$route->signedOut()->match( 'Register/*', function () { mvc( 'User', 'Register'); } );    // Register


$route->match( 'Activate/{email?}/{email_code?}/', function ($email, $email_code) {
        ( new Model\User )->activate();
        \View\View::contents( 'user', 'profile' ); } );    // Activate $email $email_code


$route->signedOut()->match( 'Recover/{userId?}/', function ($userId) { mvc( 'User', 'Recover' ); } );    // Recover $userId


$route->signedIn()->match( 'Profile/{user?}/', function ($user) { mvc( 'User', 'profile' ); } );    // Profile $user






