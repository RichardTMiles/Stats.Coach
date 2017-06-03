<?php

namespace View;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

use Facebook\Facebook;
use Controller\User;
use Psr\Singleton;

class View
{
    use Singleton;

    public $currentPage;

    public function __wakeup()
    {
        if (!$this->ajaxActive()):     // an HTTP request
            $this->__construct($this->wrapper());      // and reprocess the dependencies
        elseif (!empty($this->currentPage)):            // Implies AJAX && a page has already been rendered and stored
            echo base64_decode( $this->currentPage );   // . PHP_EOL . round((microtime( true ) - $GLOBALS['time_pre']), 6 );
            unset( $this->currentPage );
            exit(1);                      // This is for the second inner AJAX request on first page load
        endif;
        // this would mean we're requesting our second page through ajax
    }

    public function __construct($container = false)
    {
        if ($container) {
            if ($this->ajaxActive()) return null;

            ob_start();
            require_once(CONTENT_WRAPPER);
            $size = ob_get_length();
            echo $template = ob_get_clean(); // Return the Template
        }  elseif ($this->ajaxActive()) User::logout();
        // if there it is an ajax request, the user must be logged in, or container must be true
    }

    private function contents($class, $fileName) // Must be called through Singleton, must be private
    {
        
        $file = SERVER_ROOT . 'Public/StatsCoach/' . strtolower( $class ) . DS .
            strtolower( $fileName ) . ( ($loggedIn = User::getApp_id()) ? '.tpl.php' : '.php');

        if (file_exists( $file )) {
            ob_start();
            require_once $file;
            $file = ob_get_clean();
            if (!$this->ajaxActive() && (!WRAPPING_REQUIRES_LOGIN ?: $loggedIn))          // TODO - Logged in should be rethought
                $this->currentPage = base64_encode( $file );
            else echo $file;
        } else startApplication();          // restart, this usually means the user is trying to access a protected page when logged out
        exit(1);
    }

    public function ajaxActive()
    {
        return ((isset($_SERVER["HTTP_X_PJAX"]) && $_SERVER["HTTP_X_PJAX"]) ) || ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'));
    }
    
    public function faceBookLoginUrl() {
        $fb = new Facebook([
            'app_id' => '1456106104433760', // Replace {app-id} with your app id
            'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email', 'user_friends', 'public_profile'];           // Optional permissions

        $loginUrl = $helper->getLoginUrl('https://stats.coach/FaceBook/', $permissions);

        return htmlspecialchars( $loginUrl );
    }

    /**
     *  Given a file, i.e. /css/base.css, replaces it with a string containing the
     *  file's mtime, i.e. /css/base.1221534296.css.
     *
     * @param $file
     *  file to be loaded.  Must be an absolute path (i.e.
     *                starting with slash).
     * @return mixed  file to be loaded.
     */

    public function versionControl($file)
    {
        if(!file_exists( SERVER_ROOT . TEMPLATE_PATH . $file))
            return TEMPLATE_PATH . $file;

        $mtime = filemtime( SERVER_ROOT . TEMPLATE_PATH . $file);
        return TEMPLATE_PATH . preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    }

    public function __get($variable)
    {
        return (array_key_exists( $variable, $GLOBALS ) ? $GLOBALS[$variable] : null);
    }

    public function __sleep()
    {
        if (empty($this->currentPage)) {
            unset($_SESSION[__CLASS__]);
            return 0;
        }
        return array('currentPage');

    }
}

