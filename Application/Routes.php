<?php

use Modules\Route;      // Uri path matching

use Modules\Error\ErrorCatcher; // This should prevent internal errors from being thrown

use Modules\Error\PublicAlert;  // This will safely print bootstrap styled alerts on the page for user to see

use Modules\Entities;   // A database abstraction method to prevent cyclic patterns

use Modules\View;

$ajax_but_signed_out = function () {
    \Controller\User::logout();
};

// Wrap a closure in try {} catch ()
$catch = function (callable $closure): callable {
    return function (...$argv) use ($closure) {
        try {
            call_user_func_array($closure, $argv);
        } catch (InvalidArgumentException $e) {
            ErrorCatcher::generateErrorLog($e);
            PublicAlert::danger('A fatal has occurred. We have logged this issue and we will investigate soon. Please contact us if problems persist.');
        } catch (TypeError $e) {
            ErrorCatcher::generateErrorLog($e);
            PublicAlert::danger('Developers make mistakes, and you found a big one. We\'ve logged this event and will be investigating soon.'); // TODO - Change what is logged
        } finally {
            return Entities::verify();     // Check that all database commit chains have finished successfully, otherwise attempt to remove
        }
    };
};

// Sends Json array to browser
$mustache = $catch(function ($path, $options = array()) {

    global $json;   // It's best to leave the array empty before this function call, but the option is left open..

    $file = SERVER_ROOT . "Public/StatsCoach/Mustache/$path.php";
    if (file_exists($file) && is_array($file = include $file))
        $json = array_merge(
            is_array($json) ? $json : [], $file);

    $json = array_merge(
        is_array($json) ? $json : [],            // Easy Error Catching
        array('UID' => $_SESSION['id'],
            'Mustache' => SITE . "Public/StatsCoach/Mustache/$path.mst"));

    $json = array_merge(
        (is_array($json) ? $json : []),               // Easy Error Catching - dont mess up
        (is_array($options) ? $options : []));       // Options Trumps all

    print json_encode($json) . PHP_EOL;
});

// Controller -(true?)> Model -(final)> View();
$mvc = function ($class, $method, &$argv = []) use (&$view, $catch) {
    $controller = "Controller\\$class";
    $model = "Model\\$class";

    $run = function ($class, $argv) use ($method) {
        return call_user_func_array([new $class, "$method"],
            is_array($argv) ? $argv : [$argv]);
    };

    $catch(function () use ($run, $controller, $model, $argv) {
        if (!empty($argv = $run($controller, $argv))) $run($model, $argv);
    })();

    // This could cache or send
    $view->content($class, $method);  // but will exit(1);
};

$route = new class extends Route        // Start the route with the structure of the default route const
{
    public $mvc;

    public function defaultRoute($run = false) : void
    {   //use ($mvc)
        if (!is_callable($this->mvc)) return;

        $mvc = $this->mvc;

        if ($run || $this->uri[0] == null) {
            if (!$_SESSION['id'])
                $mvc('User', 'login');
            else
                $mvc('Golf', 'golf');
        }
    }
};

$route->mvc = $mvc;


$route->defaultRoute(false);
## test

if (!$_SESSION['id'] ?? false || PJAX)
    goto ROUTES;

$route->changeStructure($mustache);  // Event closure

##################### Events = returns ( JSON(Mustache) | Console(Mixed) ) ##################################

$route->match('Messages/', 'messages/nav-messages', ['widget' => '#NavMessages']);

$route->match('Messages/{user_uri?}/',
    function ($user_uri = false) use ($view, $mustache) {
        global $user_id; // for later..
        $user_id = \Tables\Users::user_id_from_uri($user_uri) or die(1); // if post isset we can assume an add

        if (!empty($_POST) && !empty(($string = (new \Modules\Request)->post('message')->noHTML()->value())))
            Tables\Messages::add($this->user[$user_id], $user_id, $string);// else were grabbing content (json, html, etc)

        Tables\Messages::get($this->user[$user_id], $user_id);

        return $mustache('messages/messages');
    });

$route->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

$route->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);

if (SOCKET) return null;                        // Sockets shouldn't get HTML

ROUTES: # ROUTES = return HTML ##################################

$route->changeStructure($mvc);                // Load the mvc lambda

$route->signedOut()->match('Login/{client?}/*', 'User', 'login')->home();

$route->signedOut()->match('Facebook/*', 'User', 'facebook');

$route->signedIn()->match('Home/*', 'Golf', 'golf')->home();

$route->match('Logout/*', function () {
    Controller\User::logout();
});          // Logout

$route->signedIn()->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore');  // PostScore $state

$route->signedIn()->match('JoinTeam/', 'Team', 'joinTeam');

$route->signedIn()->match('Team/{team_id}/*', 'Team', 'team');

$route->signedIn()->match('Rounds/{user_uri?}/', 'Golf', 'rounds');

$route->signedIn()->match('CreateTeam/', 'Team', 'createTeam');

# $route->signedIn()->match( 'Settings/', 'User', 'settings');

$route->signedIn()->match('AddCourse/{state?}/*', 'Golf', 'AddCourse');  // AddCourse

$route->signedOut()->match('Register/*', 'User', 'Register');            // Register

$route->match('Activate/{email?}/{email_code?}/', 'User', 'activate');   // Activate $email $email_code

$route->signedOut()->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover');     // Recover $userId

$route->signedIn()->match('Profile/{user_uri?}/', 'User', 'profile');     // Profile $user

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
