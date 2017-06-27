<?php


$route->match( 'Tests/*',                                               // This is how the view works
    function () {
        $view = \View\View::getInstance();
        if ($view->ajaxActive()) {
            include SERVER_ROOT . 'Tests' . DS . 'randomHex.php';
            exit(1);    }
        require_once SERVER_ROOT . 'Application' . DS . 'View' . DS . "minify.php";
        ob_start();
        require_once SERVER_ROOT . 'Tests' . DS . 'randomHex.php';
        $file = minify_html( ob_get_clean() );
        $view->currentPage = base64_encode( $file );
        exit(1);
    }
);

$route->signedIn()->match( 'Home/*', 'Golf', 'golf' )->home();          // Home = golf -> golf

$route->signedOut()->match( 'Login/facebook/*', 'User', 'facebook' );

$route->signedOut()->match( 'Login/{client?}/*',  'User', 'login' );                                   // Login

$route->signedIn()->match( 'Logout/*', function () { Controller\User::logout(); } );                 // Logout

$route->match( 'Privacy/*', function () { \View\View::contents( 'policy', 'privacypolicy' ); } );    // There is both a .php and .tpl.php

$route->signedIn()->match( 'PostScore/{state?}/{courseId?}/{boxColor?}/*',  'Golf', 'postScore' );  // PostScore $state

$route->signedIn()->match( 'AddCourse/{state?}/*', 'Golf', 'AddCourse' );  // AddCourse

$route->signedOut()->match( 'Register/*', 'User', 'Register' );            // Register

$route->match( 'Activate/{email?}/{email_code?}/', 'User', 'activate' );   // Activate $email $email_code

$route->signedOut()->match( 'Recover/{userId?}/', 'User', 'Recover' );     // Recover $userId

$route->signedIn()->match( 'Profile/{userID?}/',  'User', 'profile' );     // Profile $user

