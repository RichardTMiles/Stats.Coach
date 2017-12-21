<?php

use Carbon\Route;
use Carbon\View;

$route = new class extends Route
{
    public function defaultRoute()
    {
        if (SOCKET) print "Fuck we shouldn't be here!\n" and die(1);

        if (!$_SESSION['id']): MVC('user', 'login');
        else: MVC('golf', 'golf');
        endif;
        exit(1);
    }
};


$route->structure($mvc = function (string $class, string $method, array &$argv = []) {
    MVC($class, $method, $argv) and exit(1);
});  // Event for empty closure & lambdas


################################### MVC

if (!$_SESSION['id']):  // Signed out

    $route->match('Login/*', 'User', 'login');

    $route->match('Google/{request?}/*', 'User', 'google');

    $route->match('Facebook/{request?}/*', 'User', 'facebook');

    $route->match('Register/*', 'User', 'Register');            // Register

    $route->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover');     // Recover $userId

    exit(1);

else:   // logged in

    ################################### Dynamically load content with Mustache
    if (SOCKET || (AJAX && !PJAX)):

        $route->match('Search/{search}/', 'Search', 'all');

        $route->match('Messages/', 'Messages', 'navigation');

        $route->match('Messages/{user_uri}/', 'Messages' , 'chat' );    // chat box widget

        $route->match('Follow/{user_id}/', 'User', 'follow');           // Event

        $route->match('Unfollow/{user_id}/', 'User', 'unfollow');         // Event

        // $route->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

        // $route->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);

        if (SOCKET) return null;                // Sockets only get json

        $route->structure($mvc);                // Load the mvc lambda
    endif;

    ################################### MVC


    $route->match('Home/*', 'Golf', 'golf');

    $route->match('Golf/*', 'Golf', 'golf');

    $route->match('Profile/{user_uri?}/', 'User', 'profile');           // Profile $user

    $route->match('CreateTeam/', 'Team', 'createTeam');

    $route->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore');  // PostScore $state

    $route->match('JoinTeam/', 'Team', 'joinTeam');

    $route->match('Team/{team_id}/*', 'Team', 'team');

    $route->match('Messages/*', 'Messages', 'messages');

    $route->match('Rounds/{user_uri?}/', 'Golf', 'rounds');

    $route->match('AddCourse/{state?}/*', 'Golf', 'AddCourse');         // AddCourse

    $route->match('Logout/*', function () {
        Controller\User::logout();
    });          // Logout
endif;

$route->match('Activate/{email?}/{email_code?}/', 'User', 'activate');   // Activate $email $email_code

$route->match('404/*', function () {
    View::contents('error', '404error');
});

$route->match('500/*', function () {
    View::contents('error', '500error');
});

$route->match('Privacy/*', function () {
    View::contents(SERVER_ROOT . 'Public/policy/privacypolicy.php');
});

$route->match('Tests/*',                                // This is how the view works
    function () {
        require_once SERVER_ROOT . 'Tests/index.php';
        exit(1);
    }
);



// What now?
