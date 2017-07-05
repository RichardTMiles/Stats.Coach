<?php

namespace View;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

use Controller\User;
use Modules\Singleton;

class View
{
    use Singleton;
    const Singleton = true;

    public $currentPage;

    public function __wakeup()
    {
        if (!($this->ajax = $this->ajaxActive())):      // an HTTP request
            $this->__construct();                       // and reprocess the dependencies, wrapper is a global closure
        elseif (!empty($this->currentPage)):            // Implies AJAX && a page has already been rendered and stored
            echo base64_decode( $this->currentPage );   // . PHP_EOL . round((microtime( true ) - $GLOBALS['time_pre']), 6 );
            unset($this->currentPage);
            self::clearInstance();
            exit(1);                                    // This is for the second inner AJAX request on first page load
        endif;                                          // this would mean we're requesting our second page through ajax
    }

    public function __construct($sendWrapper = false)
    {
        $this->restart = $sendWrapper;
        if ($this->wrapper()) {
            if (!$sendWrapper && $this->ajax = $this->ajaxActive()) return null;
            require_once "minify.php";
            ob_start();
            require(CONTENT_WRAPPER);   // Return the Template
            $template = ob_get_clean();
            echo MINIFY_CONTENTS ? minify_html( $template ) : $template;
        } elseif ($this->ajaxActive()) User::logout();  // This would only be executed it wrapper_requires_login = true and user logged out
        // if there it is an ajax request, the user must be logged in, or container must be true
    }

    public function wrapper() 
    {
        return (!WRAPPING_REQUIRES_LOGIN ?: $this->user->user_id);
    }

    private function contents($class, $fileName) // Must be called through Singleton, must be private
    {
        $file = (CONTENT_ROOT . strtolower( $class ) . DS . strtolower( $fileName ) . ($this->user->user_id ? '.tpl.php' : '.php'));

        if (file_exists( $file )) {
            ob_start();
            include $file;
            include CONTENT_ROOT . 'alert' . DS . 'alerts.tpl.php'; // a little hackish when not using template file
            echo '<div class="clearfix"></div>';
            $file = ob_get_clean();         // minify_html()

            //if (MINIFY_CONTENTS && (@include_once "minify.php"))
              //  $file = minify_html( $file );

            if ($this->restart || (($this->restart || !$this->ajaxActive()) && (!WRAPPING_REQUIRES_LOGIN ?: $this->user->user_id))) {
                $this->currentPage = base64_encode( $file );
            } else echo $file;

        } else throw new \Exception( "$file does not exist" );  // TODO - throw 404 error
        // startApplication( true );
        // restart, this usually means the user is trying to access a protected page when logged out
        exit(1);
    }

    private function ajaxActive()
    {
        return $this->ajax = (isset($_GET['_pjax']) || (isset($_SERVER["HTTP_X_PJAX"]) && $_SERVER["HTTP_X_PJAX"])) || ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'));
    }

    public function faceBookLoginUrl()
    {
        return (include SERVER_ROOT . 'Application' . DS . 'Services' . DS . 'Social' . DS . 'fb-login-url.php');
    }

    public function googleLoginUrl()
    {

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
        if (file_exists( $absolute = SERVER_ROOT . $file )) $file = DS . $file;
        elseif (file_exists( $absolute = VENDOR_ROOT . $file )) $file = VENDOR . $file;
        elseif (file_exists( $absolute = TEMPLATE_ROOT . $file )) $file = TEMPLATE . $file;
        elseif (file_exists( $absolute = CONTENT_ROOT . $file ))  $file = CONTENT  . $file;
        $control = @filemtime( $absolute );
        return ($control ? preg_replace( '{\\.([^./]+)$}', "." . $control . ".\$1", $file ) : DS . $file );
    }

    public function __get($variable)
    {
        return (array_key_exists( $variable, $GLOBALS ) ? $GLOBALS[$variable] : null);
    }


}

