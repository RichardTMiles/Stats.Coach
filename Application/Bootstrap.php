<?php

$route = new Modules\Route( $mvc );

$route->signedOut()->match( 'Login/{client?}/*',  'User', 'login' )->home();

$route->signedIn()->match( 'Home/*', 'Golf', 'golf' )->home();

// AJAX REQUEST
$route->signedIn()->match( 'Messages/{user_uri?}/', function (string $user_uri = null) use ($user) {
        $id = $user->user_id_from_uri($user_uri);
    
         // if post isset we can assume an add
        if (!empty($_POST) && AJAX) \Model\Helpers\Messages::add( $this->user[$id], $id, (new class extends \Modules\Request{})->post( 'message' )->text() );

        // else were grabbing content (json, html,etc)
        if (empty($action)) dump( \Model\Helpers\Messages::get( $this->user[$id], $id ) );
        return 1;
    } );


$route->match( 'Logout/*', function () { Controller\User::logout(); } );   // Logout

$route->signedIn()->match( 'PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore' );  // PostScore $state

$route->signedIn()->match( 'JoinTeam/', 'Team', 'joinTeam');

$route->signedIn()->match( 'Team/{team_id}/*', 'Team', 'team');

$route->signedIn()->match( 'CreateTeam/', 'User', 'createTeam');

# $route->signedIn()->match( 'Settings/', 'User', 'settings');

$route->signedIn()->match( 'AddCourse/{state?}/*', 'Golf', 'AddCourse' );  // AddCourse

$route->signedOut()->match( 'Register/*', 'User', 'Register' );            // Register

$route->match( 'Activate/{email?}/{email_code?}/', 'User', 'activate' );   // Activate $email $email_code

$route->signedOut()->match( 'Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover' );     // Recover $userId

$route->signedIn()->match( 'Profile/{user_uri?}/',  'User', 'profile' );     // Profile $user

$route->match('404/*', function () { \View\View::contents('error','404error'); });

$route->match('500/*', function () { \View\View::contents('error','500error'); });

$route->match( 'Privacy/*', function () { \View\View::contents( 'policy', 'privacypolicy' ); } );    // There is both a .php and .tpl.php

$route->match( 'Tests/*',                                               // This is how the view works
    function () {
            $view = \View\View::getInstance();
            ob_start();
            require_once SERVER_ROOT . 'Tests/index.php';;
            $file = ob_get_clean();
            if (AJAX) echo $file;
            else $view->currentPage = base64_encode( $file );
            exit(1);
    }
);



