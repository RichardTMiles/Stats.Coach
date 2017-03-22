<?php

namespace View;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

use Model\Helpers\UserRelay;
use Controller\User;
use Psr\Singleton;


class View
{
    use Singleton;

    public $currentPage;

    public function __wakeup()
    {
        if (!$this->ajaxSupport()):
            unset($this->currentPage);  // If the page is refreshed completely we need to resend template
            $this->__construct();       // and reprocess the dependencies
        elseif (!empty($this->currentPage)):
            // In theory this should be the responce of every first request
            echo base64_decode( $this->currentPage );
            unset($this->currentPage);
            die();
        endif;
    }

    public function __construct($file = null)
    {
        $ajax = $this->ajaxSupport();

        if ($id = User::loggedIn()) {

            // If were logged in (ajax or not) we need to get the user data, which will be SESSION stored
            UserRelay::userProfile( $id );

            if ($ajax) return null;

            // Turn off caching
            header( 'Expires: Thu, 21 Jul 1977 07:30:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );

            // Switch to html, utf-8
            header( 'Content-type: text/html; charset=utf-8' );

            //require_once(SERVER_ROOT . 'Public' . DS . 'StatsCoach'. DS . 'AdminLTE.php');
            require_once(SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS . 'TopNav.php');

        } elseif ($ajax) User::logout(); // if there it is an ajax request, the user must be logged in
    }

    private function contents($class, $fileName) // This has to
    {
        $class = strtolower( $class );
        $fileName = strtolower( $fileName );

        $ext = ($loggedIn = User::loggedIn()) ? '.tpl.php' : '.php';

        if (file_exists( SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS . $class . DS . $fileName . $ext ) == true) {
            ob_start();
            require_once SERVER_ROOT . 'Public' . DS . 'StatsCoach' . DS . $class . DS . $fileName . $ext;
            $currentPage = ob_get_clean();

            if (!$this->ajax && $loggedIn) {
                $this->currentPage = base64_encode( $currentPage );

            } else echo $currentPage;


        } else echo('Template Files not found.');

    }

    public function ajaxSupport()
    {
        // is this an AJAX request
        return $this->ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
    }


    public function __get($variable)
    {
        // override Singleton to prevent runtime errors, we want to output the name of the variable asap
        return (array_key_exists( $variable, $GLOBALS ) ? $GLOBALS[$variable] : $variable);
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

