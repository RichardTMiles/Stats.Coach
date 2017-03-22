<?php


View\View::getInstance();

// This will be the default view 'URL' if no route is found or if on an IDE
$route = new Modules\Route( 'Home/' );

function mvc($class, $method)
{
    $controller = "Controller\\$class";
    ( new $controller )->$method();

    $model = "Model\\$class";
    ( new $model )->$method();

    /* We dont want to send the template twice, so the best way to manage
     * this dynamic relation is a single instance of the view
     * */
    \View\View::contents( $class, $method );

}   // V - MVC

$route->match( 'Tests/*',
    function () {
        Controller\User::protectPage();
        ( new \Tests\Tests() );
    } );    // Tests


$route->match( 'Login/*',
    function () {
        Controller\User::loggedOut();
        mvc( 'User', 'login' );
    } );    // Login


$route->match( 'Home/', function () {
    Controller\User::protectPage();
    \View\View::contents( 'golf', 'golf' );
} )->home();  // Home = golf -> golf


$route->match( 'PostScore/{state?}/*', function ($state) {
        Controller\User::protectPage();
        if ($_POST != null || !empty($state)) ( new \Model\Golf() )->PostScore( $state );

        // die();

        \View\View::contents( 'golf', 'postscore' );
    } );    // PostScore $state


$route->match( 'AddCourse/*',
    function () {
        Controller\User::protectPage();
        mvc( 'Golf', 'AddCourse' );
    } );    // AddCourse


$route->match( 'Logout/*',
    function () {
        Controller\User::logout();
    } );    // Logout


$route->match( 'Register/*',
    function () {
        Controller\User::loggedOut();
        mvc( 'User', 'Register' );
    } );    // Register


$route->match( 'Activate/{email?}/{email_code?}/', function ($email, $email_code) {
        ( new Model\User )->activate();
        \View\View::contents( 'user', 'profile' );
    } );    // Activate $email $email_code


$route->match( 'Recover/{userId?}/', function ($userId) {
        Controller\User::protectPage();
        mvc( 'User', 'Recover' );
    } );    // Recover $userId


$route->match( 'Profile/{user?}/', function ($user) {
        Controller\User::protectPage();
        \View\View::contents( 'user', 'profile' );
    } );    // Profile $user






