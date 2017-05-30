<?php

namespace View;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

use Model\Helpers\UserRelay;
use Controller\User;
use Psr\Singleton;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;


class View
{
    use Singleton;

    public $currentPage;

    public function __wakeup()
    {
        // HTTP or AJAX?
        if (!$this->ajaxSupport()):     // an HTTP request
            $this->__construct();       // and reprocess the dependencies
        elseif (!empty($this->currentPage)):            // Implies AJAX && a page has already been rendered and stored
            echo base64_decode( $this->currentPage );   // . PHP_EOL . round((microtime( true ) - $GLOBALS['time_pre']), 6 );
            unset( $this->currentPage );
            exit(1);                      // This is for the second inner AJAX request on first page load
        endif;
        // this would mean we're requesting our second page through ajax
    }

    public function __construct($file = null)
    {
        $ajax = $this->ajaxSupport();

        if ($id = User::loggedIn()) {
            // If were logged in (ajax or not) we need to get the user data, which will be SESSION stored
            UserRelay::getInstance();

            if ($ajax) return null;
            
            ob_start();
            require_once(SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS . 'TopNav.php');
            $size = ob_get_length();
            echo $template = ob_get_clean(); // Return the Template
           
        }  elseif ($ajax) User::logout(); // if there it is an ajax request, the user must be logged in
    }

    private function contents($class, $fileName) // Must be called through Singleton, must be private
    {
        $file = SERVER_ROOT . 'Public/StatsCoach/' . strtolower( $class ) . DS .
            strtolower( $fileName ) . (($loggedIn = User::loggedIn()) ? '.tpl.php' : '.php');

        if (file_exists( $file )) {
            ob_start();
            require_once $file;
            $size = ob_get_length();    // Speed testing
            $file = ob_get_clean();
            if (!$this->ajax && $loggedIn)
                $this->currentPage = base64_encode( $file );
            else echo $file;
        } else echo('Template Files not found.');   // TODO - Throw exception
        exit(1);
    }

    public function ajaxSupport()
    {
        return $this->ajax = ((isset($_SERVER["HTTP_X_PJAX"]) && $_SERVER["HTTP_X_PJAX"]) ) || ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'));
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

    public function faceBookLoginUrl() {
        $fb = new Facebook([
            'app_id' => '1456106104433760',
            'app_secret' => 'c35d6779a1e5eebf7a4a3bd8f1e16026',
            'default_graph_version' => 'v2.4',
            'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token'] : '1456106104433760|c35d6779a1e5eebf7a4a3bd8f1e16026'
        ]);

        try {
            $response = $fb->get('/me?fields=id,name');
            $user = $response->getGraphUser();
            echo 'Name: ' . $user['name'];
            exit; //redirect, or do whatever you want
        } catch(FacebookResponseException $e) {
            //echo 'Graph returned an error: ' . $e->getMessage();
        } catch(FacebookSDKException $e) {
            //echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        return $helper->getLoginUrl('http://stats.coach/Facebook/', $permissions);
    }



    public function versionControl($file)
    {
        if(!file_exists( SERVER_ROOT . TEMPLATE_PATH . $file))
            return TEMPLATE_PATH . $file;

        $mtime = filemtime( SERVER_ROOT . TEMPLATE_PATH . $file);
        return TEMPLATE_PATH . preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    }

    public function __get($variable)
    {   // override Singleton's get function to prevent runtime errors, we want to output the name of the variable asap
        return (array_key_exists( $variable, $GLOBALS ) ? $GLOBALS[$variable] : null); // TODO- Catch and log these errors
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

