<?php

use Carbon\Route;

use Carbon\View;


$route = Route::setInstance(new class extends Route        // Start the route with the structure of the default route const
{
    public function defaultRoute($run = false): void
    {
        if (SOCKET) return;

        if ($run || empty($this->uri[0])):
            $this->matched = true;
            if (!$_SESSION['id']):
                MVC('User', 'login');
                exit(1);
            else:
                MVC('Golf', 'golf');
                exit(1);
            endif;
        endif;
    }
});

$route->defaultRoute(false);    // false means don't run unless the url is null

$mvc = function (...$argv) {
    call_user_func_array('MVC', $argv);
    exit(1);
};

$json = function (...$argv) {
    call_user_func('Mustache', $argv);
    exit(1);
};

$route->structure($mvc);  // Event for empty closure & lambdas


if (!$_SESSION['id']):

    $route->match('Login/{client?}/*', 'User', 'login');

    $route->match('Facebook/*', 'User', 'facebook');

    $route->match('Register/*', 'User', 'Register');            // Register

    $route->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover');     // Recover $userId

    exit(1);

else:

    if (SOCKET || AJAX):
        $route->structure($json);  // Event closure

        $route->match('Messages/', 'messages/nav-messages', ['widget' => '#NavMessages']);

        $route->match('Messages/{user_uri?}/',
            function ($user_uri = false) use ($view, $json) {
                global $user_id; // for later..
                $user_id = \Tables\Users::user_id_from_uri($user_uri) or die(1); // if post isset we can assume an add

                if (!empty($_POST) && !empty(($string = (new \Carbon\Request)->post('message')->noHTML()->value())))
                    Tables\Messages::add($this->user[$user_id], $user_id, $string);// else were grabbing content (json, html, etc)

                Tables\Messages::get($this->user[$user_id], $user_id);

                return $json('messages/messages');
            });

        $route->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

        $route->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);

        if (SOCKET) return null;  // Sockets only get json

        $route->structure($mvc);                // Load the mvc lambda
    endif;

    $route->match('Home/*', 'Golf', 'golf');

    $route->match('Profile/{user_uri?}/', 'User', 'profile');     // Profile $user

    $route->match('CreateTeam/', 'Team', 'createTeam');

    $route->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore');  // PostScore $state

    $route->match('JoinTeam/', 'Team', 'joinTeam');

    $route->match('Team/{team_id}/*', 'Team', 'team');

    $route->match('Rounds/{user_uri?}/', 'Golf', 'rounds');

    $route->match('AddCourse/{state?}/*', 'Golf', 'AddCourse');  // AddCourse

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
    View::contents('policy', 'privacypolicy');
});

$route->match('Tests/*',                                // This is how the view works
    function () use ($view) {
        if (AJAX) {
            require_once SERVER_ROOT . 'Tests/index.php';;
            $view->currentPage = null;
        }
        exit(1);
    }
);


// What now?
