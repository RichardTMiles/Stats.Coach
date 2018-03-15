<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/25/18
 * Time: 3:29 AM
 */

namespace App;

use Carbon\Error\PublicAlert;
use Carbon\View;
use Controller\User;

class Bootstrap extends App
{
    /**
     * Bootstrap constructor. Places basic variables
     * in our json response that will be needed by many pages.
     * @param null $structure
     */
    public function __construct($structure = null)
    {
        global $json;

        $json = array();
        $json['SITE'] = SITE;
        $json['HTTP'] = HTTP;
        $json['HTTPS'] = HTTPS;
        $json['SOCKET'] = SOCKET;
        $json['AJAX'] = AJAX;
        $json['PJAX'] = PJAX;
        $json['SITE_TITLE'] = SITE_TITLE;
        $json['APP_VIEW'] = APP_VIEW;
        $json['TEMPLATE'] = TEMPLATE;
        $json['COMPOSER'] = COMPOSER;
        $json['X_PJAX_Version'] = &$_SESSION['X_PJAX_Version'];
        $json['FACEBOOK_APP_ID'] = FACEBOOK_APP_ID;

        $this->userSettings();  // This is the current user state, if the user logs in or changes account types this will need to be refreshed

        parent::__construct($structure);
    }

    public function userSettings() {
        global $user, $json;

        // If the user is signed in we need to get the
        if ($_SESSION['id'] ?? false) {
            $json['me'] = $GLOBALS['user'][$_SESSION['id']];
            $json['signedIn'] = true;
            $json['nav-bar'] = '';
            $json['user-layout'] = 'class="wrapper" style="background: rgba(0, 0, 0, 0.7)"';

            //
            $mustache = function ($path) {      // This is our mustache template engine implemented in php, used for rendering user content
                global $json;
                static $mustache;
                if (empty($mustache)) {
                    $mustache = new \Mustache_Engine();
                }
                if (!file_exists($path)) {
                    print "<script>Carbon(() => $.fn.bootstrapAlert('Content Buffer Failed ($path), Does Not Exist!', 'danger'))</script>";
                }
                return $mustache->render(file_get_contents($path), $json);
            };

            switch ($user[$_SESSION['id']]['user_type'] ?? false) {
                case 'Athlete':
                    $json['body-layout'] = 'hold-transition skin-blue layout-top-nav';
                    $json['header'] = $mustache(APP_ROOT . APP_VIEW . 'Layout/AthleteLayout.hbs');
                    break;
                case 'Coach':
                    $json['body-layout'] = 'skin-green fixed sidebar-mini sidebar-collapse';
                    $json['header'] = $mustache(APP_ROOT . APP_VIEW . 'Layout/CoachLayout.hbs');
                    break;
                default:
                    throw new PublicAlert('No user type found!!!!');
            }
        } else {
            $json['body-layout'] = 'stats-wrap';
            $json['user-layout'] = 'class="container" id="pjax-content"';
        }
    }


    public function defaultRoute()
    {
        // Sockets will not execute this
        View::$forceWrapper = true; // this will hard refresh the wrapper

        if (!$_SESSION['id']):
            return MVC('User', 'login');
        else:
            return MVC('Golf', 'golf');
        endif;
    }

    /**
     * @param null $uri
     * @return bool
     * @throws \Carbon\Error\PublicAlert
     */
    public function __invoke($uri = null)
    {
        if (null !== $uri) {
            $this->userSettings();
            $this->changeURI($uri);
        }

        $this->structure($this->MVC());

        if ((string)$this->match('Contact', 'Messages', 'Mail')) {
            return true;
        }

        ################################### MVC
        if (!$_SESSION['id']) {  // Signed out

            if ((string)$this->match('Login/*', 'User', 'login') ||
                (string)$this->match('oAuth/{service}/{request?}/*', 'User', 'oAuth') ||
                (string)$this->match('Register/*', 'User', 'Register') ||           // Register
                (string)$this->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover')) {     // Recover $userId
                return true;
            }

        } else {
            // Event
            if (((AJAX && !PJAX) || SOCKET) && (
                    (string)$this->match('Search/{search}/', 'Search', 'all') ||
                    (string)$this->match('Messages/', 'Messages', 'navigation') ||
                    (string)$this->match('Messages/{user_uri}/', 'Messages', 'chat') ||    // chat box widget
                    (string)$this->structure($this->events())->match('Follow/{user_id}/', 'User', 'follow') ||
                    (string)$this->match('Unfollow/{user_id}/', 'User', 'unfollow'))) {
                return true;         // Event
            }

            // $url->match('Notifications/*', 'notifications/notifications', ['widget' => '#NavNotifications']);

            // $url->match('tasks/*', 'tasks/tasks', ['widget' => '#NavTasks']);


            $this->structure($this->events('#course'))->match('PostScore/{state}/', 'Golf', 'coursesByState');

            if (SOCKET) {
                return false;
            }                // Sockets only get json

            ################################### MVC
            $this->structure($this->MVC());

            ################################### Golf Stuff + User

            if ((string)$this->match('Profile/{user_uri?}/', 'User', 'profile') ||   // Profile $user
                (string)$this->match('Messages/*', 'Messages', 'messages') ||
                (string)$this->match('Home/*', 'Golf', 'golf') ||
                (string)$this->match('Golf/*', 'Golf', 'golf') ||
                (string)$this->match('Team/{team_id}/*', 'Team', 'team') ||
                (string)$this->match('Rounds/{user_uri?}/', 'Golf', 'rounds') ||
                (string)$this->match('JoinTeam/', 'Team', 'joinTeam') ||
                (string)$this->match('CreateTeam/', 'Team', 'createTeam') ||
                (string)$this->match('AddCourse/{state?}/*', 'Golf', 'AddCourse') ||
                (string)$this->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore') ||
                (string)$this->match('Logout/*', function () {
                    User::logout();
                })) {
                return true;          // Logout
            }
        }

        return (string)$this->structure($this->MVC())->match('Activate/{email?}/{email_code?}/', 'User', 'activate') ||  // Activate $email $email_code
            (string)$this->structure($this->wrap())->match('404/*', 'Error/404error.php') ||
            (string)$this->match('500/*', 'Error/500error.php');


    }
}