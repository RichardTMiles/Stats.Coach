<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/25/18
 * Time: 3:29 AM
 */

namespace App;

use Controller\User;

class Bootstrap extends App
{
    /**
     * @param null $uri
     * @return bool
     * @throws \Carbon\Error\PublicAlert
     */
    public function __invoke($uri = null)
    {
        if (null !== $uri) {
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