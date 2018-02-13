<?php

use Carbon\Route;
use Carbon\View;

$url = new class extends Route
{
    public function defaultRoute()  // Sockets will not execute this
    {
        View::$forceWrapper = true; // this will hard refresh the wrapper

        if (!$_SESSION['id']):
            return $this->wrap()('User/login.php');  // don't change how wrap works, I know it looks funny
        else:
            return MVC('User', 'profile');
        endif;
    }

    public function fullPage()
    {
        return catchErrors(function (string $file) {
            return include APP_VIEW . $file;
        });
    }

    public function wrap()
    {
        return function (string $file) : bool {
            return View::content(APP_VIEW . $file);
        };
    }

    public function MVC()
    {
        return function (string $class, string $method, array &$argv = []) {
            return MVC($class, $method, $argv);         // So I can throw in ->structure($route->MVC())-> anywhere
        };
    }

    public function events()
    {
        return function ($class, $method, $argv) {
            global $alert, $json;

            if (false === $argv = CM($class, $method, $argv)){
                return false;
            }

            if (!file_exists(SERVER_ROOT . $file = (APP_VIEW . $class . DS . $method . '.hbs'))) {
                $alert = 'Mustache Template Not Found ' . $file;
            }

            if (!is_array($alert)) {
                $alert = array();
            }

            $json = array_merge($json, [
                'Errors' => $alert,
                'Event' => 'Controller->Model',   // This doesn't do anything.. Its just a mental note when I look at the json's in console (controller->model only)
                'Model' => $argv,
                'Mustache' => DS . $file
            ]);

            print PHP_EOL . json_encode($json) . PHP_EOL; // new line ensures it sends through the socket

            return true;
        };
    }
};

$url->structure($url->MVC());

if ((string)$url->match('Contact', 'Messages', 'Mail')) {
    return true;
}


################################### MVC
if (!$_SESSION['id']) {  // Signed out

    if ((string)$url->match('Login/*', 'User', 'login') ||
        (string)$url->match('Google/{request?}/*', 'User', 'google') ||
        (string)$url->match('Facebook/{request?}/*', 'User', 'facebook') ||
        (string)$url->match('Register/*', 'User', 'Register') ||           // Register
        (string)$url->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover')) {     // Recover $userId
        return true;
    }

} else {
    // Event
    if (((AJAX && !PJAX) || SOCKET) && (
            (string)$url->match('Search/{search}/', 'Search', 'all') ||
            (string)$url->match('Messages/', 'Messages', 'navigation') ||
            (string)$url->match('Messages/{user_uri}/', 'Messages', 'chat') ||    // chat box widget
            (string)$url->structure($url->events())->match('Follow/{user_id}/', 'User', 'follow') ||
            (string)$url->match('Unfollow/{user_id}/', 'User', 'unfollow'))) {
        return true;         // Event
    }

    // $url->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

    // $url->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);

    if (SOCKET) {
        return false;
    }                // Sockets only get json

    ################################### MVC
    $url->structure($url->MVC());

    ################################### Golf Stuff + User

    if ((string)$url->match('Profile/{user_uri?}/', 'User', 'profile') ||   // Profile $user
        (string)$url->match('Messages/*', 'Messages', 'messages') ||
        (string)$url->match('Home/*', 'Golf', 'golf') ||
        (string)$url->match('Golf/*', 'Golf', 'golf') ||
        (string)$url->match('Team/{team_id}/*', 'Team', 'team') ||
        (string)$url->match('Rounds/{user_uri?}/', 'Golf', 'rounds') ||
        (string)$url->match('JoinTeam/', 'Team', 'joinTeam') ||
        (string)$url->match('CreateTeam/', 'Team', 'createTeam') ||
        (string)$url->match('AddCourse/{state?}/*', 'Golf', 'AddCourse') ||
        (string)$url->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore') ||
        (string)$url->match('Logout/*', function () {
            Controller\User::logout();
        })) {
        return true;          // Logout
    }
}

return (string)$url->structure($url->MVC())->match('Activate/{email?}/{email_code?}/', 'User', 'activate') ||  // Activate $email $email_code
    (string)$url->structure($url->wrap())->match('404/*', 'Error/404error.php') ||
    (string)$url->match('500/*', 'Error/500error.php');

