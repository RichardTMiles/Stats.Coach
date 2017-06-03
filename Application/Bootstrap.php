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


$route->match( 'Facebook/*', function () {
    alert("FACEBOOK");
    include SERVER_ROOT . 'Tests/fb-callback.php';
    exit(1);
} );


$route->match( 'Tests/*',    function () { include SERVER_ROOT . 'Tests/login.php'; } );


$route->signedOut()->match( 'Login/{client?}/*', function ($client = null) { mvc( 'User', 'login' ); } );    // Login


$route->signedIn()->match( 'PostScore/{state?}/*', function ($state) { mvc('Golf', 'postScore'); } );    // PostScore $state


$route->signedIn()->match( 'AddCourse/{state?}/*', function ($state) { mvc( 'Golf', 'AddCourse'); } );    // AddCourse TODO - Make $state work


$route->match( 'Logout/*',  function () { Controller\User::logout(); } );    // Logout


$route->match( 'Register/*', function () { mvc( 'User', 'Register'); } );    // Register


$route->match( 'Activate/{email?}/{email_code?}/', 
    function ($email, $email_code) { 
        ( new Model\User )->activate(); 
        \View\View::contents( 'user', 'profile' ); } );    // Activate $email $email_code


$route->match( 'Recover/{userId?}/', 
    function ($userId) { mvc( 'User', 'Recover' ); } );    // Recover $userId


$route->match( 'Profile/{user?}/', 
    function ($user) { mvc( 'User', 'profile' ); } );    // Profile $user






