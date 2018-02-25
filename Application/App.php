<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/25/18
 * Time: 3:20 AM
 */

namespace App;

use Carbon\Route;
use Carbon\View;

class App extends Route
{
    public function defaultRoute()  // Sockets will not execute this
    {
        View::$forceWrapper = true; // this will hard refresh the wrapper

        if (!$_SESSION['id']):
            return $this->wrap()('User/login.php');  // don't change how wrap works, I know it looks funny
        else:
            return MVC('Golf', 'golf');
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

    public function events($selector = '')
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
}
