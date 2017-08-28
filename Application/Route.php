<?php


$route->signedOut()->match( 'Login/{client?}/*', 'User', 'login' )->home();
$route->signedOut()->match( 'Facebook/*', 'User', 'facebook' );

$route->signedIn()->match( 'Home/*', 'Golf', 'golf' )->home();

$route->match( 'Logout/*', function () {
    Controller\User::logout();
} );   // Logout

$route->signedIn()->match( 'PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore' );  // PostScore $state

$route->signedIn()->match( 'JoinTeam/', 'Team', 'joinTeam' );

$route->signedIn()->match( 'Team/{team_id}/*', 'Team', 'team' );

$route->signedIn()->match( 'CreateTeam/', 'Team', 'createTeam' );

# $route->signedIn()->match( 'Settings/', 'User', 'settings');

$route->signedIn()->match( 'AddCourse/{state?}/*', 'Golf', 'AddCourse' );  // AddCourse

$route->signedOut()->match( 'Register/*', 'User', 'Register' );            // Register

$route->match( 'Activate/{email?}/{email_code?}/', 'User', 'activate' );   // Activate $email $email_code

$route->signedOut()->match( 'Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover' );     // Recover $userId

$route->signedIn()->match( 'Profile/{user_uri?}/', 'User', 'profile' );     // Profile $user

$route->match( '404/*', function () {
    \View\View::contents( 'error', '404error' );
} );

$route->match( '500/*', function () {
    \View\View::contents( 'error', '500error' );
} );

$route->match( 'Privacy/*', function () {
    \View\View::contents( 'policy', 'privacypolicy' );
} );    // There is both a .php and .tpl.php

$route->match( 'Tests/*',                                               // This is how the view works
    function () use ($view) {
        if (AJAX) {
            require_once SERVER_ROOT . 'Tests/index.php';;
            $view->currentPage = null;
        }
        exit( 1 );
    }
);



