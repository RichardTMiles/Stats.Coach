<?php

$route->signedOut()->match( 'Login/{client?}/*',  'User', 'login' )->home();

$route->signedIn()->match( 'Home/*', 'Golf', 'golf' )->home();

$route->signedIn()->match( 'Logout/*', function () { Controller\User::logout(); } );                 // Logout

$route->signedIn()->match( 'PostScore/{state?}/{courseId?}/{boxColor?}/*', 'Golf', 'postScore' );  // PostScore $state

$route->signedOut()->match( 'Login/facebook/*', 'User', 'facebook' );                   // TODO - better client handling

$route->signedIn()->match( 'JoinTeam/', 'User', 'joinTeam');

$route->signedIn()->match( 'AddCourse/{state?}/*', 'Golf', 'AddCourse' );  // AddCourse

$route->signedOut()->match( 'Register/*', 'User', 'Register' );            // Register

$route->match( 'Activate/{email?}/{email_code?}/', 'User', 'activate' );   // Activate $email $email_code

$route->signedOut()->match( 'Recover/{userId?}/', 'User', 'Recover' );     // Recover $userId

$route->signedIn()->match( 'Profile/{userID?}/',  'User', 'profile' );     // Profile $user


$route->match('404/*', function () { View::contents('error','404error'); });

$route->match('500/*', function () { View::contents('error','500error'); });

$route->match( 'Privacy/*', function () { \View\View::contents( 'policy', 'privacypolicy' ); } );    // There is both a .php and .tpl.php

$route->match( 'Tests/*',                                               // This is how the view works
    function () {
            $view = \View\View::getInstance();
            ob_start();
            require_once SERVER_ROOT . 'Tests' . DS . 'recursiveSerializing.php';;
            $file = minify_html( ob_get_clean() );

            if ($view->ajaxActive()) echo $file;
            else $view->currentPage = base64_encode( $file );
            exit(1);
    }
);


