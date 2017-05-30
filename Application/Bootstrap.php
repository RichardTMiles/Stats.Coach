<?php
// TODO - find how to make the godaddy stats.coach subdomain url works


$route->match( 'Facebook/*', function () {
    include SERVER_ROOT . 'Tests/fb-callback.php';
} );


$route->match( 'Tests/*', function ( ) {
    //Controller\User::protectPage(); ( new \Tests\Tests() );
    // include SERVER_ROOT . 'Tests/login.php';
    // Scripts\OpenSSL\NewKeys::generate();
    include SERVER_ROOT . 'Tests/login.php';
} );

$route->match( 'Login/{client?}/*', function ($client) {

    alert($client);
    // mvc( 'User', 'login', 'loggedOut' );
    exit(1); 
} );    // Login


$route->match( 'Home/*', function () { mvc( 'Golf', 'golf', 'protectPage' ); } )->home( );  // Home = golf -> golf


$route->match( 'PostScore/{state?}/*', function ($state) { mvc('Golf', 'postScore', 'protectPage'); } );    // PostScore $state


$route->match( 'AddCourse/{state?}/*', function ($state) { mvc( 'Golf', 'AddCourse', 'protectPage' ); } );    // AddCourse TODO - Make $state work


$route->match( 'Logout/*', function () { Controller\User::logout();} );    // Logout


$route->match( 'Register/*', function () {
    //sortDump();
    mvc( 'User', 'Register', 'loggedOut' ); } );    // Register


$route->match( 'Activate/{email?}/{email_code?}/', function ($email, $email_code) { ( new Model\User )->activate(); \View\View::contents( 'user', 'profile' ); } );    // Activate $email $email_code


$route->match( 'Recover/{userId?}/', function ($userId) { mvc( 'User', 'Recover', 'protectPage' ); } );    // Recover $userId


$route->match( 'Profile/{user?}/', function ($user) { mvc( 'User', 'profile', 'protectPage' ); } );    // Profile $user

