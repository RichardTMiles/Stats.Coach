<?php


$route->match( 'Tests/*',   // This 
    function () {
        $view = \View\View::getInstance();
        if ($view->ajaxActive()) {
            include SERVER_ROOT . 'Tests' . DS . 'sorecard.php';
            exit(1);
        }
        require_once SERVER_ROOT . 'Application' . DS . 'View' . DS . "minify.php";
        ob_start();
        require_once SERVER_ROOT . 'Tests' . DS . 'sorecard.php';
        $file = minify_html( ob_get_clean() );
        $view->currentPage = base64_encode( $file );
        exit(1);
    }
);

$route->signedIn()->match( 'Home/*', 'Golf', 'golf' )->home();          // Home = golf -> golf

$route->signedOut()->match( 'Login/{client?}/*', function ($client) {
    if ($client == "facebook") mvc( 'User', 'facebook' );
    else mvc( 'User', 'login' );
} );                    // Login

$route->signedIn()->match( 'Logout/*', function () {
    Controller\User::logout();
} );                       // Logout

$route->match( 'Privacy/*', function () {
    \View\View::contents( 'policy', 'privacypolicy' );
} );    // There is both a .php and .tpl.php


$route->signedIn()->match( 'PostScore/{state?}/{courseId?}/{boxColor?}/*',
    function ($state, $courseId, $boxColor) {
        mvc( 'Golf', 'postScore' );
    } );       // PostScore $state

$route->signedIn()->match( 'AddCourse/{state?}/*', function ($state) {
    mvc( 'Golf', 'AddCourse' );
} );      // AddCourse TODO - Make $state work

$route->signedOut()->match( 'Register/*', function () {
    mvc( 'User', 'Register' );
} );          // Register

$route->match( 'Activate/{email?}/{email_code?}/', function ($email, $email_code) {
    mvc( 'User', 'activate' );
} );    // Activate $email $email_code

$route->signedOut()->match( 'Recover/{userId?}/', function ($userId) {
    mvc( 'User', 'Recover' );
} );   // Recover $userId

$route->signedIn()->match( 'Profile/{userID?}/', function ($userID) {
    mvc( 'User', 'profile' );
} );    // Profile $user

