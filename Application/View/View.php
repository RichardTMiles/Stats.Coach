<?php

namespace View;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

use Controller\User;
use Modules\Singleton;
use Modules\Request;

class  View
{
    use Singleton;
    const Singleton = true;
    
    public $currentPage;
    private $carryErrors;
    private $forceStoreContent;

    public function __wakeup()
    {
        if (!AJAX):      // an HTTP request
            $_POST = [];
            $this->__construct();                       // and reprocess the dependencies, wrapper is a global closure
        elseif (!empty($this->currentPage)):            // Implies AJAX && a page has already been rendered and stored
            echo base64_decode( $this->currentPage );   // The ajax page will be 64encoded before we store on the server
            $this->currentPage = false;
            self::clearInstance();                      // This will remove stored information on the server and delete its self reference
            exit(1);                                    // This is for the second inner AJAX request on first page load
        endif;                                          // this would mean we're requesting our second page through ajax ie not initial page request
    }

    public function __construct($forceWrapper = false)   // Send the content wrapper
    {
        if ($this->wrapper()) {
            Request::setHeader( "X-PJAX-Version: " . $_SESSION['X_PJAX_Version'] );
            if (!($forceWrapper || ($_SESSION['X_PJAX_Version'] != X_PJAX_VERSION)) && AJAX) return null;
            $_POST = [];
            ob_start();
            require(CONTENT_WRAPPER);   // Return the Template
            $template = ob_get_clean();
            echo (MINIFY_CONTENTS && (@include_once "minify.php")) ? minify_html( $template ) : $template;
            if ($forceWrapper):
                if (!empty($GLOBALS['alert'])) $this->carryErrors = $GLOBALS['alert']; // exit(1); 
                $this->forceStoreContent = true;
            endif;
        } elseif (AJAX) User::logout();  // This would only be executed it wrapper_requires_login = true and user logged out
        // if there it is an ajax request, the user must be logged in, or container must be true
    }


    public function wrapper() 
    {
        return (!WRAPPING_REQUIRES_LOGIN ?: $_SESSION['id']);
    }

    public static function contents(...$argv) {
        $self = static::getInstance();
        call_user_func_array( [$self, 'content' ], $argv );
    }

    public function content(...$argv) // Must be called through Singleton, must be private
    {
        switch (count($argv)) {
            case 2: $file = CONTENT_ROOT . strtolower( $argv[0] ) . DS . strtolower( $argv[1] ) . '.php';   //($this->user->user_id ? '.tpl.php' : '.php'));
                break;
            case 1:
                $file = @file_exists( $argv[0] ) ? $argv[0]  : CONTENT_ROOT . $argv[0];
                break;
            default: throw new \InvalidArgumentException();
        }

        if (file_exists( $file )) {
            ob_start();
            if (empty($GLOBALS['alert']) && !empty($GLOBALS['alert'] = $this->carryErrors))
                $this->carryErrors = null;
            include CONTENT_ROOT . 'alert/alerts.php'; // a little hackish when not using template file
            include_once $file;
            $file = ob_get_clean();                             // TODO minify_html()
            if (MINIFY_CONTENTS && (@include_once "minify.php"))
                $file = minify_html( $file );
            if ($this->forceStoreContent || (!AJAX && $this->wrapper())) {
                $this->currentPage = base64_encode( $file );
            } else echo $file;
            $this->forceStoreContent = false;
            exit(1);
        } else throw new \Exception( "$file does not exist" );  // TODO - throw 404 error
    }
    
    public function faceBookLoginUrl()
    {
        return (include SERVER_ROOT . 'Application/Services/Social/fb-login-url.php');
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
        return (isset( $GLOBALS[$variable] ) ? $GLOBALS[$variable] : null);
    }


}

