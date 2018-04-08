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
     * @throws \Carbon\Error\PublicAlert
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

        parent::__construct($structure);
    }


    /**
     * @param null $uri
     * @return bool
     * @throws \Mustache_Exception_InvalidArgumentException
     * @throws \Carbon\Error\PublicAlert
     */
    public function startApplication($uri = null)
    {
        static $count;

        if (empty($count)) {
            $count = 0;
        }

         $count++;

        $this->userSettings();          // Update the current user

        if (null !== $uri) {
            $this->changeURI($uri);
        } else {
            if (empty($this->uri[0])) {
                if (SOCKET) {
                    throw new PublicAlert('$_SERVER["REQUEST_URI"] MUST BE SET IN SOCKET REQUESTS');
                }
                $this->matched = true;
                return $this->defaultRoute();
            }
        }

        $this->structure($this->MVC());

        if ($this->match('Contact', 'Messages', 'Mail')()) {
            return true;
        }


        ################################### MVC
        if (!$_SESSION['id']) {  // Signed out
            if ($this->match('Login/*', 'User', 'login')() ||
                $this->match('oAuth/{service}/{request?}/*', 'User', 'oAuth')() ||
                $this->match('Register/*', 'User', 'Register')() ||           // Register
                $this->match('Recover/{user_email?}/{user_generated_string?}/', 'User', 'recover')()) {     // Recover $userId
                return true;
            }


        } else {
            // Event
            if (((AJAX && !PJAX) || SOCKET) && (
                     $this->match('Search/{search}/', 'Search', 'all')() ||
                     $this->match('Messages/', 'Messages', 'navigation')() ||
                     $this->match('Messages/{user_uri}/', 'Messages', 'chat')() ||    // chat box widget
                     $this->structure($this->events())->match('Follow/{user_id}/', 'User', 'follow')() ||
                     $this->match('Unfollow/{user_id}/', 'User', 'unfollow')())) {
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

            if ( $this->match('Profile/{user_uri?}/', 'User', 'profile')() ||   // Profile $user
                 $this->match('Messages/*', 'Messages', 'messages')() ||
                 $this->match('Home/*', 'Golf', 'golf')() ||
                 $this->match('Golf/*', 'Golf', 'golf')() ||
                 $this->match('Team/{team_id}/*', 'Team', 'team')() ||
                 $this->match('Rounds/{user_uri?}/', 'Golf', 'rounds')() ||
                 $this->match('JoinTeam/', 'Team', 'joinTeam')() ||
                 $this->match('CreateTeam/', 'Team', 'createTeam')() ||
                 $this->match('AddCourse/{state?}/*', 'Golf', 'AddCourse')() ||
                 $this->match('PostScore/{state?}/{course_id?}/{boxColor?}/*', 'Golf', 'postScore')() ||
                 $this->match('Logout/*', function () {
                    User::logout();
                })) {
                return true;          // Logout
            }
        }

        return  $this->structure($this->MVC())->match('Activate/{email?}/{email_code?}/', 'User', 'activate')() ||  // Activate $email $email_code
             $this->structure($this->wrap())->match('404/*', 'Error/404error.php')() ||
             $this->match('500/*', 'Error/500error.php')();


    }
}