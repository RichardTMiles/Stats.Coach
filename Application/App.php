<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/25/18
 * Time: 3:20 AM
 */

namespace App;

use Carbon\Error\PublicAlert;
use Carbon\Route;
use Carbon\View;

abstract class App extends Route
{
    /**
     * App constructor. If no uri is set than
     * the Route constructor will execute the
     * defaultRoute method defined below.
     * @return callable
     * @throws \Mustache_Exception_InvalidArgumentException
     * @throws \Carbon\Error\PublicAlert
     */

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



    public
    function fullPage() : callable
    {
        return catchErrors(function (string $file) {
            return include APP_VIEW . $file;
        });
    }

    public
    function wrap()
    {
        return function (string $file): bool {
            return View::content(APP_VIEW . $file);
        };
    }

    public
    function MVC()
    {
        return function (string $class, string $method, array &$argv = []) {
            return MVC($class, $method, $argv);         // So I can throw in ->structure($route->MVC())-> anywhere
        };
    }

    public
    function events($selector = '')
    {
        return function ($class, $method, $argv) use ($selector) {
            global $alert, $json;

            if (false === $argv = CM($class, $method, $argv)) {
                return false;
            }

            if (!file_exists(SERVER_ROOT . $file = (APP_VIEW . $class . DS . $method . '.hbs'))) {
                $alert = 'Mustache Template Not Found ' . $file;
            }

            if (!\is_array($alert)) {
                $alert = array();
            }

            $json = array_merge($json, [
                'Errors' => $alert,
                'Event' => 'Controller->Model',   // This doesn't do anything.. Its just a mental note when I look at the json's in console (controller->model only)
                'Model' => $argv,
                'Mustache' => DS . $file,
                'Widget' => $selector
            ]);

            header('Content-Type: application/json'); // Send as JSON

            print PHP_EOL . json_encode($json) . PHP_EOL; // new line ensures it sends through the socket

            return true;
        };
    }
}
