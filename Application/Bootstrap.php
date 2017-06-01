<?php
// TODO - find how to make the godaddy stats.coach subdomain url works
/*
 * Routing a home function required default arguments be set to fuction
 * as expected.
 */

$route->match( 'Facebook/*', function () {
    alert("FACEBOOK");
    include SERVER_ROOT . 'Tests/fb-callback.php';
    exit(1);
} );



$route->match( 'Tests/*', function ( ) {
    //Controller\User::protectPage(); ( new \Tests\Tests() );
    // include SERVER_ROOT . 'Tests/login.php';
    // Scripts\OpenSSL\NewKeys::generate();
    include SERVER_ROOT . 'Tests/login.php';
} );


$route->match( 'Login/{client?}/*', function ($client = null) {
    alert("Login PAGE!!");
    mvc( 'User', 'login', 'loggedOut' );
    exit(1); 
} );    // Login


$route->match( 'Home/*', function () {
    alert("running home");
    mvc( 'Golf', 'golf', 'protectPage' ); } )->home( ); // Home = golf -> golf


$route->match( 'PostScore/{state?}/*', 
    function ($state) {
        alert("POSTSCORE");

        mvc('Golf', 'postScore', 'protectPage'); } );    // PostScore $state


$route->match( 'AddCourse/{state?}/*', 
    function ($state) { mvc( 'Golf', 'AddCourse', 'protectPage' ); } );    // AddCourse TODO - Make $state work


$route->match( 'Logout/*', 
    function () {

        alert("matched logout request");

        Controller\User::logout();} );    // Logout


$route->match( 'Register/*', function () { 
    mvc( 'User', 'Register', 'loggedOut' ); } );    // Register


$route->match( 'Activate/{email?}/{email_code?}/', 
    function ($email, $email_code) { 
        ( new Model\User )->activate(); 
        \View\View::contents( 'user', 'profile' ); } );    // Activate $email $email_code


$route->match( 'Recover/{userId?}/', 
    function ($userId) { mvc( 'User', 'Recover', 'protectPage' ); } );    // Recover $userId


$route->match( 'Profile/{user?}/', 
    function ($user) { mvc( 'User', 'profile', 'protectPage' ); } );    // Profile $user






