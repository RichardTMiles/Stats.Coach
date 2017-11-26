<?php

use Carbon\Route;
use Carbon\View;





$route = new class extends Route
{
    public function defaultRoute()
    {
        if (!$_SESSION['id']):
            MVC('user', 'login');
        else:
            MVC('golf', 'golf');
        endif;
        exit(1);
    }
};

$json = function ($path, $options) {
    Mustache($path, $options);
    exit(1);
};

$route->structure($mvc = function (string $class, string $method, array &$argv = []) {
    MVC($class,$method,$argv) and exit(1);
});  // Event for empty closure & lambdas


if (!$_SESSION['id']):

    $route->match('Login/*', 'User', 'login');

    $route->match('Facebook/{request}/*', 'User', 'facebook');

    $route->match('Register/*', 'User', 'Register');            // Register

    $route->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover');     // Recover $userId

    exit(1);

else:   // logged in

    if (SOCKET || AJAX):
        $route->structure($json);               // Event closure

        $route->match('Messages/', 'messages/nav-messages', ['widget' => '#NavMessages']);

        $route->match('Messages/{user_uri?}/',
            function ($user_uri = false) use ($json) {
                global $user_id;                                                        // for later..
                $user_id = \Tables\Users::user_id_from_uri($user_uri) or die(1);        // if post isset we can assume an add

                if (!empty($_POST) && !empty(($string = (new \Carbon\Request)->post('message')->noHTML()->value())))
                    Tables\Messages::add($this->user[$user_id], $user_id, $string);     // else were grabbing content (json, html, etc)

                Tables\Messages::get($this->user[$user_id], $user_id);

                return $json('messages/messages');
            });

        $route->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

        $route->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);

        if (SOCKET) return null;                // Sockets only get json

        $route->structure($mvc);                // Load the mvc lambda
    endif;

    $route->match('Home/*', 'Golf', 'golf');

    $route->match('Golf/*', 'Golf', 'golf');

    $route->match('Profile/{user_uri?}/', 'User', 'profile');           // Profile $user

    $route->match('CreateTeam/', 'Team', 'createTeam');

    $route->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore');  // PostScore $state

    $route->match('JoinTeam/', 'Team', 'joinTeam');

    $route->match('Team/{team_id}/*', 'Team', 'team');

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
        if (AJAX) {
            require_once SERVER_ROOT . 'Tests/index.php';;
        }
        exit(1);
    }
);



// What now?
